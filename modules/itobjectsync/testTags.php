<?php

	echo('ciao');

	$numberOfChildren = 0;
	$childrenArray = array();
	$ezTagsObject = new eZTagsObject();
	
	
	$numberOfChildren = eZTagsObject::childrenCountByParentID(20);
	$childrenArray = eZTagsObject::fetchByParentID(20);
	echo('----------Il numero dei figli del nodo Aromenti:<br>');
	echo($numberOfChildren.'<br>');
	echo('----------I figli del nodo Aromenti:<br>');
	print_r($childrenArray);
	echo('<br>----------Fine array----------------------');		

?>