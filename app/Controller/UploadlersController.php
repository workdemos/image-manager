<?php

class UploadlersController extends AppController {

    public $components = array('RequestHandler', 'Session', 'Upload');
    public $uses = array('Image','Speedfold');

    public function send() {
        $mode = $this->request->data['mode'] ? $this->request->data['mode'] : 'null';
        $fold_id = $this->request->data['fold_id'];

        $files = $this->Upload->post($mode); 

        foreach ($files as $k => $file) {
            if ( !isset($file->error)) {
                $data = array(
                    'title' => $file->ori_name,
                    'size' => $file->size,
                    'uri' => $file->url,
                    'maker_id' => $this->Session->read('Auth.User.m_maker_id'),
                    'type' => $file->type,
                    'fold_id' => $fold_id,
                    'imgz_s' => 1,
                    'imgj_s' => 0,
                    'status' => 1
                );
                $this->Image->save($data);
                $files[$k]->url = Configure::read("images_hosts." . Configure::read("images_default_host")) .  $files[$k]->url;
                $files[$k]->thumbnail_url = Configure::read("images_hosts." . Configure::read("images_default_host")) . $files[$k]->thumbnail_url;
                $files[$k]->id = $this->Image->id;
                $files[$k]->fold_id = $fold_id;
            }
        }

        $this->Speedfold->read(null, $fold_id);
        $this->Speedfold->set("has_child",0);
        $this->Speedfold->save();
        
        $this->set(array(
            'files' => $files,
            '_serialize' => array('files')
        ));
    }

    function beforeFilter() {
        parent::beforeFilter();
       // $this->Auth->allow();
    }

}

?>
