<?php

/**
 * Contiene le funzionalità per l'attivazione della sincronizzazione
 * automatica di un contenuto in base alla Tematica
 */
class ITTematicaSync
{
    private $repository_id;
    private $repository_url;
    
    private $object; // Persistent object abbinato
    
    ////////////////////// CONSTRUCTOR //////////////////////
    
    public function __construct( $repository ) 
    {
        $this->repository_id = $repository;
        
        // Ricavo sorgente del repository
        $repositoryINI = eZINI::instance('ocrepository.ini', 'settings', null, FALSE);
        $this->repository_url = $repositoryINI->variable( 'Client_' . $this->repository_id, 'Url' );
        
        // Ricavo le tematiche abilitate ed il nodo destinazione dal db
        $this->object = ITTematicaSyncPersistentObject::fetchByRepository($repository);
        
        // Creo il record se non esiste
        if($this->object === null){
            $this->object = new ITTematicaSyncPersistentObject(array('repository' => $repository));
            $this->object->store();
        }
    }
    
    ////////////////////// GETTER AND SETTER //////////////////////
    
    public function getRepositoryUrl()
    { 
        return $this->repository_url; 
    }
    public function getDefaultDestinationNodeID()
    { 
        return $this->object->attribute('destination_node_id');
    }
    public function getTematiche()
    {
        return explode(';', $this->object->attribute('tags'));
    }
    
    ////////////////////// PRIVATE METHODS //////////////////////
    
    private function currentSiteAccess()
    {
        $ezCurrentAccess = $GLOBALS['eZCurrentAccess'];
        
        $siteaccess = $ezCurrentAccess['name'];
        $siteaccess = str_replace('debug', '', $siteaccess);
        
        return $siteaccess;
    }
    
    ////////////////////// PUBLIC METHODS //////////////////////
    
    /**
     * Ricava le tematiche dal sito remoto
     * @return array
     */
    public function fetchRemoteTags()
    {
        $remote_tags = array();
        
        if( $this->repository_url != FALSE ){
            $remote_tags_json = file_get_contents($this->repository_url . '/itobjectsync/tematiche');
            $remote_tags = json_decode($remote_tags_json, true);
        }
        
        return $remote_tags;
    }
    
    /**
     * Aggiorna la selezione delle tematiche da sincronizzare
     * @param eZHTTPTool $http
     */
    public function modifySelection( $http )
    {
        if($http->hasPostVariable( 'BrowseActionName' ) && $http->postVariable('BrowseActionName') == 'SelectDestinationNodeID'){
            // Scelta del nodo di destinazione
            
            $nodeIDArray = $http->postVariable('SelectedNodeIDArray');
            $this->object->setAttribute('destination_node_id', $nodeIDArray[0]);
            $this->object->store();
        }
        else{
            // Abilitazione e disabilitazione delle tematiche
            $tematicheChanged = false;
            
            foreach($http->attribute('post') as $key => $value){
                $action = explode('_', $key);
                
                if($action[0] == 'DisableTag'){
                    if(($_key = array_search($value, $this->getTematiche())) !== false) {
                        $_tematiche = $this->getTematiche();
                        
                        unset($_tematiche[$_key]);
                        $this->object->setAttribute('tags', implode(';', $_tematiche));
                    }
                    
                    $tematicheChanged = true;
                }
                else if($action[0] == 'EnableTag'){
                    $_tematiche = $this->getTematiche();
                    $_tematiche[] = $value;
                    $this->object->setAttribute('tags', implode(';', $_tematiche));
                    
                    $tematicheChanged = true;
                }
            }
            
            if($tematicheChanged){
                $this->object->store();
            }
        }
    }
}
