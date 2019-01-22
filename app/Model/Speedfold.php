<?php

App::uses('AppModel', 'Model');
App::uses('CakeSession', 'Model/Datasource');

class Speedfold extends AppModel {

    public $useTable = 'Images_Folds';
    public $hasMany = array(
        'Image' => array(
            'className' => 'Image',
            'foreignKey' => 'fold_id',
            'order' => 'Image.modified'
        )
    );
    public $order = 'Speedfold.modified DESC';

    public function beforeFind($queryData) {
        $this->unbindModel(array('hasMany' => array('Image')));
        $queryData['conditions']['Speedfold.status'] = isset($queryData['conditions']['Speedfold.status']) ? $queryData['conditions']['Speedfold.status'] : 1;
        //  $queryData['conditions']['Speedfold.maker_id'] = isset($queryData['conditions']['Speedfold.maker_id']) ? $queryData['conditions']['Speedfold.maker_id'] : CakeSession::read('Auth.User.m_maker_id');
        return $queryData;
    }

    public function is_has_child($parent_id) {
        $num = $this->find('count', array('conditions' => array('Speedfold.parent_id' => $parent_id)));
        return $num > 0 ? true : false;
    }

    public function set_has_child($id, $flag = null) {
        if (!isset($flag) || !in_array($flag, array(1, 0))) {
            $flag = $this->is_has_child($id) ? 1 : 0;
        }

        $this->read(null, $id);
        $this->set('has_child', $flag);
        $this->save();
    }

    public function is_exists_fold($parent_id, $fold_name) {
        $num = $this->find('count', array('conditions' => array("Speedfold.maker_id" => CakeSession::read('Auth.User.m_maker_id'), 'Speedfold.parent_id' => $parent_id, 'Speedfold.name' => trim($fold_name))));
        return $num > 0 ? true : false;
    }

    public function is_has_images($id = null) {
        if (!isset($id))
            throw new NotFoundException(__('Invalid Fold'));

        $num = $this->Image->find('count', array('conditions' => array('Image.fold_id' => $id,"Speedfold.maker_id" => CakeSession::read('Auth.User.m_maker_id'))));
        return $num > 0 ? true : false;
    }

    public function is_empty_fold($id = null) {
        if (!isset($id))
            throw new NotFoundException(__('Invalid Fold'));
        return !$this->is_has_child($id) && !$this->is_has_images($id);
    }

    public function delete_fold($id) {
        if (!isset($id))
            throw new NotFoundException(__('Invalid Fold'));
        if (!$this->read(null, $id)) {
            throw new NotFoundException(__('Not Found'));
        }
        $this->set('status', 0);
        $this->save();
        return true;
    }

    public function recur_del_fold($id) {
        if (!isset($id))
            return 0;        
        if ($this->is_has_images($id)) {
            return 0;
        } elseif ($this->is_empty_fold($id)) {
            $this->delete_fold($id);
            return 1;
        } else {
            $a_f = 1;
            $sub_folds = $this->find('all', array('conditions' => array('parent_id' => $id)));  
            foreach ($sub_folds as $fold) {
                $res = $this->recur_del_fold($fold['Speedfold']['id']);
                $a_f = $a_f && $res;
            }
            if ($a_f) {
                $this->delete_fold($id);
            }
           
            return $a_f;           
        }
    }

    public function getFoldCover($id) {
          if (!isset($id)) return false;
          $fold_cover_img = $this->Image->field('uri',array('fold_id' =>$id, 'is_cover'=>1),"in_order DESC");
          if(!$fold_cover_img){
              $fold_cover_img = $this->Image->field('uri',array('fold_id' =>$id),"in_order DESC");
          }
          if(!$fold_cover_img){
              return "";
          }
          return  $fold_cover_img;
    }

     public function getFoldTrack($id) {
        if (!isset($id))
            return array();

        $fold = $this->findById($id);
        $position = preg_split("#/|//#", $fold['Speedfold']['position']); 
        $folds = $this->find('all', array('conditions' => array('Speedfold.id' => $position), 'order' => 'Speedfold.level'));
        return $folds;
    }
    
    public function is_sub($target_id, $moving_id){ 
         if (!isset($target_id) || !$moving_id){
            throw new NotFoundException(__('Invalid Fold'));
         }

         if($target_id == $moving_id){
             return true;
         }
        $num = $this->find('count', array('conditions' => array('Speedfold.position like ' => '%/' . $moving_id . '/%/' . $target_id . '/%')));
        return $num > 0 ? true : false;
    }
    
    public function getRootFold(){
        $fold = array("Speedfold"=>array("name"=>"根目录", "position"=>"", "level"=>0));
        return $fold;
    }
    public function parse_album_path($path){
        if($path =='')
            throw new NotFoundException("相册路径不对");
        
        $m = trim(substr($path, strpos($path, "=")+1), ":"); 
        $names = explode(":", $m);
        $album_id = $this->field('id',array('name' =>$names[count($names)-1],"level"=>count($names), "maker_id" => CakeSession::read('Auth.User.m_maker_id')),"");
        return $album_id;
    }
}

?>
