<?php

class FilesController extends AppController {
    public $helpers = array('Html', 'Form', 'Session');
    public $components = array('Session', 'RequestHandler');

    public function setSyncJp() {
        $security = $this->request->data['ms'];
        if (md5("owksdf*7hsdfsdf") != $security) {
            $this->set('data', array("error" => 1, "msg" => $security));
            return;
        }
        $image_id = intval($this->request->data['id']);
        $this->File->setSyncJP($image_id);
        $data = array("ok" => 1, "image" => $image_id);
        $this->set('data', $data);
    }

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('setSyncJp');
    }

}

?>
