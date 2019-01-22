<?php

class ImagesController extends AppController {

    public $helpers = array('Html', 'Form', 'Session');
    public $components = array('Session', 'RequestHandler');
    public $paginate = array(
        'limit' => 24
    );

    public function beforeRender() {
        $this->layout = 'myimages';
    }

    public function index() {
        $this->set('album_id', 0);
        $this->set('page', isset($this->request->params['page']) && intval($this->request->params['page'])>0  ? intval($this->request->params['page']) : 1 );
    }

    public function search_album() { 
        $home = $this->request->params['home'];
        $path = $this->request->params['path'];
        $album_id = $this->Image->Speedfold->parse_album_path($path);
        $this->set('album_id', $album_id);
        $this->set('page', $this->request->params['page'] ? $this->request->params['page'] : 1 );
    }

    public function search($fold_id) {
        if (!isset($fold_id))
            throw new NotFoundException(__('Invalid fold id'));
        $fold = $this->Image->Speedfold->findById($fold_id);
        $grand_parent_id = 0;
        if ($fold) {
            $grand_parent_id = $fold['Speedfold']['parent_id'];
            $album_name = $fold['Speedfold']['name'];
        }

        $data = array();
        $this->paginate = array(
            'limit' => 24,
            'conditions' => array('Image.fold_id' => $fold_id, 'Image.maker_id' => $this->Session->read('Auth.User.m_maker_id'))
        );
        $this->Image->unbindModel(array('belongsTo' => array('Speedfold')));
        $data['images'] = $this->paginate('Image');
        $data['album_name'] = $album_name;
        $data['pid'] = $fold['Speedfold']['id'];
        $data['page'] = $this->request->named['page'];
        $data['ppid'] = $grand_parent_id;
        $data['track'] = $this->Image->Speedfold->getFoldTrack($fold_id);
        $data['listType'] = 'imgs';
        $this->set('data', $data);
    }

    public function delete() {
        $img_del_ids = $this->request->data['ids'];
        $fold_id = $this->request->data['fold_id'];
        $res = array();
        foreach ($img_del_ids as $id) {
            $this->Image->delete_image($id);
            $res[] = array('err' => 0, 'id' => $id);
        }
        echo json_encode($res);
    }

    public function move() {
        $ids = $this->request->data['images'];
        $fold_id = $this->request->data['target_id'];
        $fold = $this->Image->Speedfold->findById($fold_id);
        if ($this->Image->Speedfold->is_has_child($fold_id)) {
            echo json_encode(array("err" => 1, "msg" => "相册 [" . $fold['Speedfold']['name'] . "] 不能存放图片，该目录已经存在子目录！"));
            exit();
        }

        $fields = array("Image.fold_id" => $fold_id);
        $conditions = array(
            "Image.status" => 1,
            "Image.maker_id" => $this->Session->read("Auth.User.m_maker_id"),
            "Image.id" => $ids
        );
        $this->Image->unbindModel(array('belongsTo' => array('Speedfold')));
        $this->Image->updateAll($fields, $conditions);

        echo json_encode(array('err' => 0, 'images' => $ids, 'msg' => "图片成功移动"));
        exit();
    }

    public function setSyncJp() {
        $security = $this->request->data['ms'];
        if (md5("owksdf*7hsdfsdf") != $security) {
            $this->set('data', array("error" => 1, "msg" => $security));
            return;
        }
        $image_id = intval($this->request->data['id']);
        $this->Image->setSyncJP($image_id);
        $data = array("ok" => 1, "image" => $image_id);
        $this->set('data', $data);
    }
    
    public function beforeFilter() {
        parent::beforeFilter();
         $this->Auth->allow('setSyncJp');
    }

}

?>
