<?php

/**
 * Contiene le funzionalità per l'attivazione della sincronizzazione
 * automatica di un contenuto in base alla Tematica
 */
class ITTematicaSync{
    private $repository_id;
    private $repository_url;
    private $default_dest_node_id;
    private $tematiche;
    
    ////////////////////// CONSTRUCTOR //////////////////////
    
    public function __construct( $repository ) {
        $this->repository_id = $repository;
        
        // Ricavo sorgente del repository
        $repositoryINI = eZINI::instance('ocrepository.ini', 'settings', null, FALSE);
        $this->repository_url = $repositoryINI->variable( 'Client_' . $this->repository_id, 'Url' );
        $this->default_dest_node_id = $repositoryINI->variable( 'Client_' . $this->repository_id, 'DefaultDestinationNodeID' );
        $this->tematiche = $repositoryINI->variable( 'Client_' . $this->repository_id, 'Tematiche' );
        
    }
    
    ////////////////////// GETTER AND SETTER //////////////////////
    
    public function getRepositoryUrl(){ return $this->repository_url; }
    public function getDefaultDestinationNodeID(){ return $this->default_dest_node_id; }
    public function getTematiche(){ return $this->tematiche; }
    
    ////////////////////// PRIVATE UTILS //////////////////////
    
    private function currentSiteAccess(){
        $ezCurrentAccess = $GLOBALS['eZCurrentAccess'];
        
        $siteaccess = $ezCurrentAccess['name'];
        $siteaccess = str_replace('debug', '', $siteaccess);
        
        return $siteaccess;
    }
    
    
    ////////////////////// UTILS //////////////////////
    
    /**
     * Ricava le tematiche dal sito remoto
     * @return array
     */
    public function fetchRemoteTags(){
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
    public function modifySelection( $http ){
        if($http->hasPostVariable( 'BrowseActionName' ) && $http->postVariable('BrowseActionName') == 'SelectDestinationNodeID'){
            // Scelta del nodo di destinazione
            
            $nodeIDArray = $http->postVariable('SelectedNodeIDArray');
            $this->default_dest_node_id = $nodeIDArray[0];
            
            $repositoryINI = eZINI::instance('ocrepository.ini.append.php', 'settings/siteaccess/' . $this->currentSiteAccess());
            $repositoryINI->setVariable('Client_' . $this->repository_id, 'DefaultDestinationNodeID', $this->default_dest_node_id);
            
            $repositoryINI->save();
            $repositoryINI->resetCache();
        }
        else{
            // Abilitazione e disabilitazione delle tematiche
            $tematicheChanged = false;
            
            foreach($http->attribute('post') as $key => $value){
                $action = explode('_', $key);
                
                if($action[0] == 'DisableTag'){
                    if(($_key = array_search($value, $this->tematiche)) !== false) {
                        unset($this->tematiche[$_key]);
                    }
                    
                    $tematicheChanged = true;
                }
                else if($action[0] == 'EnableTag'){
                    $this->tematiche[] = $value;
                    
                    $tematicheChanged = true;
                }
            }
            
            if($tematicheChanged){
                $repositoryINI = eZINI::instance('ocrepository.ini.append.php', 'settings/siteaccess/' . $this->currentSiteAccess());
                $repositoryINI->setVariable('Client_' . $this->repository_id, 'Tematiche', $this->tematiche);
                $repositoryINI->save(false,false,false,false,true,true,true);
                $repositoryINI->resetCache();
            }
        }
    }
}
