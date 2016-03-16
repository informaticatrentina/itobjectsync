<?php

/**
 * Estrae in formato JSON l'elenco delle tematiche
 */

$module = $Params['Module'];

$tematiche = array();

$subtree = eZTagsObject::fetchByKeyword( 'Tematiche' );

if(count($subtree) > 0){
    $tematiche = eZTagsObject::subTreeByTagID( array( 'SortBy' => array( 'keyword', 'asc')
                                                    , 'Depth' => 1 ), $subtree[0]->ID);
}

header('Content-Type: application/json');
echo json_encode( $tematiche );    
eZExecution::cleanExit();