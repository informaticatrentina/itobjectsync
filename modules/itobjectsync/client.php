<?php

$module = $Params['Module'];
$http = eZHTTPTool::instance();
$tpl = eZTemplate::factory();

$repository = $Params['repository'];

try{
    $itTematicaSync = new ITTematicaSync( $repository );
    
    $remoteTags = $itTematicaSync->fetchRemoteTags(); // Tematiche dal sito remoto
    
    if($http->hasPostVariable( 'SelezionaDestinazione' )){
        eZContentBrowse::browse(
            array(
                'action_name' => 'SelectDestinationNodeID',
                'selection' => 'single',
                'return_type' => 'NodeID',
                'start_node' => 2,
                'from_page' => '/itobjectsync/client/' . $repository,
                'cancel_page' => '/itobjectsync/client/' . $repository,
            ),
            $module
        );
        return;
    }
    
    $itTematicaSync->modifySelection( $http ); // Eventuali modifiche alla selezione
    
    // Variabili passate al template
    $tpl->setVariable('remote_tags', $remoteTags );
    
    $tpl->setVariable('repository_url', $itTematicaSync->getRepositoryUrl());
    $tpl->setVariable('default_dest_node_id', $itTematicaSync->getDefaultDestinationNodeID());
    $tpl->setVariable('tematiche', $itTematicaSync->getTematiche());
    
} catch (Exception $ex) {
    $tpl->setVariable('exception', $ex->getMessage() );
}

$Result['content'] = $tpl->fetch( "design:itobjectsync/client.tpl" );
$Result['path'] = array( array( 'url' => 'content/dashboard',
                                'text' => 'Pannello strumenti' ) ,
                         array( 'url' => false,
                                'text' => 'Import automatico Tematica' ) );
