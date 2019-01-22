<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Post extends AppModel{
      public $validate = array(
          'title' => array('rule' => 'notEmpty'),
          'body' => array('rule' => 'notEmpty')
      );
      
      public function isOwnerdBy($post, $user){
          return $this->field('id', array('id'=>$post, 'user_id'=>$user)) === $post;
      }
}

