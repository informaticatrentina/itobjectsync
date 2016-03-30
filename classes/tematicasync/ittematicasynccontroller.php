<?php

/**
 * Ricava gli oggetti da caricare dal siteaccess remoto.
 * 
 */
class ITTematicaSyncController {
    private $options;
    
    /**
     * Costruttore
     */
    public function __construct( SQLIImportHandlerOptions $options = null ){
        $this->options = $options;
        
    }
    
    /**
     * Ritorna l'elenco degli oggetti da caricare
     * @return array
     */
    public function loadDataSource(){
        
        echo 'ITTematicaSyncController - loadDataSource - Enter' . PHP_EOL;
        
        $_data = array();
        
        // Lettura di ocrepository.ini
        $ini = eZINI::instance('ocrepository.ini');
        
        // Elenco dei repository con sync abilitato
        $availableRepositories = $ini->variable('Client', 'AvailableRepositories');
        
        // Numero di giorni da caricare (Default: 1)
        $days = '1';
        if($this->options->hasAttribute('Days')){
            $days = $this->options->attribute('Days');
        }
        
        // Verifica presenza nodo destinazione e tematiche selezionate
        foreach( $availableRepositories as $repository ){
            $client = 'Client_' . $repository;
            
            $url = $ini->variable($client, 'Url');
            $tematicaSync = $ini->variable($client, 'TematicaSync');
            
            if( $tematicaSync == 'true' ){
                $itTematicaSync = new ITTematicaSync( $repository );
                
                $nodeID = $itTematicaSync->getDefaultDestinationNodeID();
                
                if(isset($nodeID)){
                    $_tags = $itTematicaSync->getTematiche();
                    $tags = implode($_tags, '|');
                    
                    // Chiamata remota con elenco remoteid ( /itobjectsync/tematichequery/$repository/$tags/$days )
                    $remote_call = $url . '/itobjectsync/tematichequery/' . $repository . '/' . urlencode($tags) . '/' . $days;
                    
                    echo 'Remote call: ' . $remote_call . PHP_EOL;
                    
                    $remote_obj_json = file_get_contents( $remote_call );
                    $remote_obj = json_decode($remote_obj_json, true);
                    
                    $remote_data = array();
                    foreach($remote_obj as $obj){
                        $remote_data[] = array(
                            'base_url' => $url,
                            'parent_node_id' => $nodeID,
                            'remote_node_id' => $obj
                        );
                    }
                    
                    $_data = array_merge($_data, $remote_data);
                }
                
            }
            
        }
        
        return $_data;
    }
}

