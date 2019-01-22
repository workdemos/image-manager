<?php 
$json = json::fromArray(array('response' => $files));
echo $json->asJSON;