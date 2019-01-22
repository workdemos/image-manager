<?php 
$json = json::fromArray(array('response' => $recipes));
echo $json->asJSON;