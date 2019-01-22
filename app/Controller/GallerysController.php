<?php

class GallerysController extends AppController {

    public $uses = array('Speedfold', 'Image');
    public $components = array('RequestHandler');

    public function index() {
        $recipes = $this->Post->find('all', array('limit' => 1));
        $this->set(array(
            'recipes' => $recipes,
            '_serialize' => array('recipes')
        ));
    }

    public function view($id) {
        $recipe = $this->Recipe->findById($id);
        $this->set(array(
            'recipe' => $recipe,
            '_serialize' => array('recipe')
        ));
    }

    public function edit($id) {
        $this->Recipe->id = $id;
        if ($this->Recipe->save($this->request->data)) {
            $message = 'Saved';
        } else {
            $message = 'Error';
        }
        $this->set(array(
            'message' => $message,
            '_serialize' => array('message')
        ));
    }

    public function delete($id) {
        if ($this->Recipe->delete($id)) {
            $message = 'Deleted';
        } else {
            $message = 'Error';
        }
        $this->set(array(
            'message' => $message,
            '_serialize' => array('message')
        ));
    }

    public function search() {
        $post_data = json_decode(file_get_contents("php://input"), true);
        if ($post_data['type'] == 'fold') {
            return $this->getFolds($post_data['cons']);
        } elseif ($post_data['type'] == 'image') {
            return $this->getImages($post_data['cons']);
        }
    }

    public function getFolds($cons = null) {
        if (!$cons)
            throw new NotFoundException(__('invalid fold'));

        $limit = isset($cons['limit']) ? $cons['limit'] : 30;

        switch ($cons['order']) {
            case 1:
                $order = array("Speedfold.created" => 'desc');
                break;
            case -1:
                $order = array("Speedfold.created" => 'asc');
                break;
            case 2:
                $order = array("Speedfold.name" => 'desc');
                break;
            case -2:
                $order = array("Speedfold.name" => 'asc');
                break;
            default:
                $order = array("Speedfold.created" => 'desc');
        }

        $this->request->params['named']['page'] = isset($cons['page']) ? $cons['page'] : 1;

        $parent_id = isset($cons['parent_id']) && $cons['parent_id'] ? $cons['parent_id'] : 0;
        $conditions = array('Speedfold.maker_id' => $cons['maker_id'], 'Speedfold.parent_id' => $parent_id);

        if (isset($cons['m_space_folder.keyworld']) && $cons['m_space_folder.keyworld']) {
            $conditions['Speedfold.name like'] = "%" . urlencode($cons['m_space_folder.keyworld']) . "%";
        }
        $this->Speedfold->unbindModel(array('hasMany' => array('Image')));

        $this->paginate = array('limit' => $limit, 'order' => $order, 'conditions' => $conditions);

        $folds = $this->paginate('Speedfold');

        $data = array('folds' => $folds, 'ppid' => 0);
        $data['track'] = $this->Speedfold->getFoldTrack($parent_id);
        $this->set('data', $data);
    }

    public function getImages($cons = null) {
        if (!$cons)
            throw new NotFoundException(__('invalid image'));
        $limit = isset($cons['limit']) ? $cons['limit'] : 30;
        $order = array("Image.created" => "desc");
        switch ($cons['order']) {
            case 1:
                $order = array("Image.created" => 'desc');
                break;
            case -1:
                $order = array("Image.created" => 'asc');
                break;
            case 2:
                $order = array("Image.title" => 'desc');
                break;
            case -2:
                $order = array("Image.title" => 'asc');
                break;
            default:
                $order = array("Image.created" => 'desc');
        }
        if ($cons['order'] == 2) {
            $order = array("Image.title" => 'desc');
        }

        $this->request->params['named']['page'] = isset($cons['page']) ? $cons['page'] : 1;
        $conditions = array('Image.maker_id' => $cons['maker_id'], 'Image.fold_id' => $cons['m_space_image.m_space_image_m_space_folder_id']);

        if (isset($cons['m_space_image.keyword']) && $cons['m_space_image.keyword']) {
            $conditions['Image.title like'] = "%" . urlencode($cons['m_space_image.keyword']) . "%";
        }


        $this->paginate = array('limit' => $limit, 'order' => $order, 'conditions' => $conditions);

        $folds = $this->paginate('Image');

        $data = array('images' => $folds, 'ppid' => 0);
        $data['track'] = $this->Speedfold->getFoldTrack($cons['m_space_image.m_space_image_m_space_folder_id']);
        $this->set('data', $data);
    }

    public function showPageImages() {
        $jsonp_callback = $this->request->query['callback'];
        $search_images_id = $this->request->query['ids'];
        $conditions = array('Image.id' => $search_images_id);
        $this->Image->unbindModel(array('belongsTo' => array('Speedfold')));
        $res = $this->Image->find("all", array("conditions" => $conditions));
        $images = array();
        foreach ($res as $img) {
            $images[$img['Image']['id']] = $img['Image']['uri'];
        }
        $this->set('jsonp_callback', $jsonp_callback);
        $this->set('data', $images);
    }

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow();
    }

}

?>
