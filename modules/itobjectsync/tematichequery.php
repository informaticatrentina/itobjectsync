<?php

/**
 * Estrae in formato JSON l'elenco dei nodeid che contengono
 * almeno una delle tematiche impostate
 */

$module = $Params['Module'];

$class_id = $Params['class_id'];
$tematiche = $Params['tematiche'];
$days = $Params['days'];

if(isset($tematiche)){
    $tematiche = explode("|", $tematiche);
}

// Ultimo giorno (Default)
if(!isset($days)){
    $days = 1;
}

// Tutte le tematiche in OR
$tag_filters = array('or');
foreach($tematiche as $tag){
    $tag_filters[] = 'attr_tematica_lk: "' . $tag .'"';
}

// Data inizio è di $days giorni indietro
$_startDate = new DateTime();
$_endDate   = new DateTime();
$_startDate->modify("-" . $days . " day");

$startDate  = ezfSolrDocumentFieldBase::preProcessValue( $_startDate->format('U'), 'date' );        
$endDate    = ezfSolrDocumentFieldBase::preProcessValue( $_endDate->format('U') , 'date' );  

// Composizione del filtro
$filters = array( 'and'
                , 'meta_published_dt:[' . $startDate . ' TO ' . $endDate . ']'
                , $tag_filters);

$params = array(
            'SortBy' => array( 'meta_published_dt' => 'desc' ),
            'Filter' => $filters,
            'SearchContentClassID' => array( $class_id ),
            'SearchSubTreeArray' => array( eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'RootNode' ) )
        );

// Esecuzione query Solr
$solrSearch = new eZSolr();
$result = $solrSearch->search( '', $params );

// Estrazione del NodeID
$node_ids = array();
foreach($result['SearchResult'] as $object ){
    $node_ids[] = $object->ContentObject->mainNodeID();
}

// Output in JSON
header('Content-Type: application/json');
echo json_encode( $node_ids );
// echo json_encode( $result['SearchResult'] ); // DEBUG
eZExecution::cleanExit();
