<?php

class SpeedfoldsController extends AppController {

    public $helpers = array('Html', 'Form', 'Session');
    public $components = array('Session', 'RequestHandler');
    public $findMethods = array('available' => true);

    public function beforeRender() {
        $this->layout = 'myimages';
    }

    public function index() {
        
    }

    public function search($pid = null) { 
        if (!isset($pid)) {
            $pid = $this->request->query['id'];
        }

        if (!isset($pid)) {
            throw new NotFoundException("没有相册id提供");
        }

        if(!isset($this->request->params['named']['page'])){
            $this->request->params['named']['page'] = $this->request->data['page'];
        }
        
         if($this->Speedfold->is_has_images($pid)){
            $this->redirect(array('controller' => 'Images','action' => 'search', $pid,"page:".$this->request->params['named']['page'], '.json'));
         }
        $conditions = array("Speedfold.maker_id" => $this->Session->read('Auth.User.m_maker_id'));
        
        $fold = $this->Speedfold->findById($pid);
        $grand_parent_id = 0;
        $album_name = "";
        if ($fold){
             $grand_parent_id = $fold['Speedfold']['parent_id'];
             $album_name = $fold['Speedfold']['name'];
        }
           
        $conditions['Speedfold.parent_id'] = $pid;
        $is_paging = isset($this->request->data['is_paging']) ? $this->request->data['is_paging'] : false;
        
        
        if ($is_paging) {
            $this->paginate = array('limit' => 24, 'conditions' => $conditions);
            $folds = $this->paginate('Speedfold');
            foreach ($folds as $key => $fold) {
                $folds[$key]['Speedfold']['cover'] = $this->Speedfold->getFoldCover($fold['Speedfold']['id']);
            }
            $data = array('folds' => $folds, 'pid' => $pid,"album_name"=>$album_name, 'ppid' => $grand_parent_id, "listType"=>'folds',"page"=>$this->request->named['page']);
            $data['track'] = $this->Speedfold->getFoldTrack($pid);
            $this->set('data', $data);
        } else {
            $folds = $this->Speedfold->find('all', array('conditions' => $conditions));
            if ($this->request->query['type'] == 'l') {
                $this->format_for_tree($folds);
            } else {
                $this->set('folds', $folds);
                $this->set('_serialize', array('folds'));
            }
        }
    }
    
    private function format_for_tree(&$folds) {
        $tree = array();
        if (!is_array($folds) || empty($folds))
            return false;
        foreach ($folds as $fold) {
            $tmp = array('data' => $fold['Speedfold']['name'],
                'attr' => array('id' => $fold['Speedfold']['id'])
            );
            if ($fold['Speedfold']['has_child']) {
                $tmp['state'] = 'closed';
                $tmp['attr']['sub'] = 'y';
            }
            $tree[] = $tmp;
        }

        echo json_encode($tree);
        exit(1);
    }

    public function add() {
        $parent_id = $this->request->data['pid'];
        $name = $this->request->data['name'];
        if ($this->Speedfold->is_exists_fold($parent_id, $name)) {
            echo json_encode(array('err' => 1, 'msg' => "该目录存在相同相册名 [$name]，请选择其他的相册名。"));
            exit();
        }
        $data = array(
            'parent_id' => $parent_id,
            'name' => $name,
            'maker_id' => $this->Session->read('Auth.User.m_maker_id'),
            'imgz_s' => 1,
            'imgj_s' => 0,
            'status' => 1,
            'has_child' => 0
        );
        $this->Speedfold->save($data);
        $fold_id = $this->Speedfold->id;
        if ($parent_id == 0) {
            $data = array("position" => "/" . $fold_id . "/", "level" => 1);
        } else {
            $this->Speedfold->set_has_child($parent_id, 1);
            $fold_parent = $this->Speedfold->findById($parent_id);
            $data = array("position" => $fold_parent['Speedfold']['position'] . "/" . $fold_id . "/", "level" => intval($fold_parent['Speedfold']['level']) + 1);
        }
        $this->Speedfold->read(null, $fold_id);
        $this->Speedfold->set($data);
        $this->Speedfold->save();
        $fold = array('id' => $fold_id, 'name' => $name);



        echo json_encode(array('err' => 0, 'fold' => $fold));
        exit();
    }

    public function delete() {

        $fold_ids = $this->request->data['ids'];
        $res = array();

        foreach ($fold_ids as $id) {
            $t = $this->Speedfold->recur_del_fold($id);
            $res[] = array('id' => $id, 'err' => $t ? 0 : 1);
        }
        echo json_encode($res);
        exit();
    }

    public function move() {
        $ids = $this->request->data['fold_ids'];
        $target_id = $this->request->data['target_id'];

        if ($target_id == 0) {
            $target_fold = $this->Speedfold->getRootFold();
        } else {
            $target_fold = $this->Speedfold->findById($target_id);
        }
        if ($this->Speedfold->is_has_images($target_id)) {
            echo json_encode(array("err" => 1, "msg" => "相册 [" . $target_fold['Speedfold']['name'] . "] 已经存在图片，不能移动相册到该目录，操作无法进行，请先删除目标目录里图片！"));
            exit();
        }
        $res = array();
        $this->Speedfold->unbindModel(array('hasMany' => array('Image')));
        $updated_flag = 0;
        $parent_id = null;
        foreach ($ids as $id) {
            if ($this->Speedfold->is_sub($target_id, $id)) {
                $res[] = array('id' => $id, 'err' => 1, 'msg' => '不能移到子目录或自身');
                continue;
            }

            $fold = $this->Speedfold->read(null, $id);
            if (!isset($parent_id))
                $parent_id = $fold['Speedfold']['parent_id'];

            if ($parent_id == $target_id) {
                echo json_encode(array("err" => 1, "msg" => "相册 [" . $target_fold['Speedfold']['name'] . "] 已经存在该目录，操作取消！"));
                exit();
            }
            $data = array("parent_id" => $target_id,
                "position" => $target_fold['Speedfold']['position'] . '/' . $this->Speedfold->id . '/',
                "level" => intval($target_fold['Speedfold']['level']) + 1
            );
            $this->Speedfold->set($data);
            $this->Speedfold->save();
            $updated_flag = 1;
            $res[] = array('id' => $id, 'err' => 0);
        }

        if ($updated_flag) {
            $this->Speedfold->set_has_child($target_id, 1);
            $this->Speedfold->set_has_child($parent_id);
        }
        echo json_encode(array('err' => 2, 'msg' => $res));
        exit();
    }

    public function beforeFilter() {
        parent::beforeFilter();
        //  $this->Auth->allow();
    }

    protected function _findAvailable($state, $query, $results = array()) {
        
    }

}

?>
