<?php

App::uses('AppModel', 'Model');
App::uses('CakeSession', 'Model/Datasource');

class File extends AppModel {

    public $useTable = 'Files';
    public $order = 'File.modified DESC';

    public function beforeFind($queryData) {
        $queryData['conditions']['File.status'] = isset($queryData['conditions']['File.status']) ? $queryData['conditions']['File.status'] : 1;
        return $queryData;
    }

    public function delete_File($id = null) {
        if (!$id)
            throw new NotFoundException(__('Not Found'));
        if (!$this->read('status', $id)) {
            throw new NotFoundException(__('Invalid Image'));
        }
        $this->set('status', 0);
        $this->save();
        return true;
    }

    public function setSyncJP($id = null) {
        if (!$id)
            throw new NotFoundException(__('Not Found'));
        if (!$this->read('imgj_s', $id)) {
            throw new NotFoundException(__('Invalid Image'));
        }
        $this->set('imgj_s', 1);
        $this->save();
        return true;
    }

    public function afterFind($results, $primary = false) {

        $client_ip = $_SERVER['REMOTE_ADDR'];
        if (!preg_match('/(^127.0.0.1$)|(^192.168)/', $client_ip)) {
            $contry = geoip_country_name_by_name($client_ip);
            $images_conturys = array_keys(Configure::read('images_hosts'));
            if (in_array($contry, $images_conturys)) {
                $images_host = $contry;
            } else {
                $n = mt_rand(0, count($images_conturys) - 1);
                $images_host = $images_conturys[$n];
            }
        } else {
            $images_host = Configure::read('images_default_host');
        }

        $images_uri = $_SERVER['HTTPS'] == "on" ? Configure::read("images_hosts_ssl.{$images_host}") : Configure::read("images_hosts.{$images_host}");
        // $images_uri = Configure::read("images_hosts.{$images_host}");

        foreach ($results as $key => $img) {
            if (isset($img['Image']['uri'])) {
                $results[$key]['Image']['uri'] = $images_uri . $img['Image']['uri'];
            }
        }
        return $results;
    }

}

?>
