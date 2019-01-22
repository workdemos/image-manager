<?php
 $data['count']=$this->Paginator->counter(array('format' => '{:count}'));
 
 echo json_encode($data);