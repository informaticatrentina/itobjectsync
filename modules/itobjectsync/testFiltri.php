<?php

echo('ciao');


$itFiltersUtil = new ItFiltersUtil();

$json = array();
$json["deliberazione"]=array(01,02,03,04);
$json["determinazione"]=array(05,06,07);


#$json='{"deliberazione":[01,02,03,04],"determine":[05,06,07]}';


$itFiltersUtil->iniupdatableclasses(json_encode($json,true));

die();

?>