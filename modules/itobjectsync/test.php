<?php

echo('ciao');


$objectToEz = new ObjectToEz();

$varArraywsCall = $objectToEz->loadObjectByDatasource();

echo '<pre>';
print_r($varArraywsCall);
die();

?>