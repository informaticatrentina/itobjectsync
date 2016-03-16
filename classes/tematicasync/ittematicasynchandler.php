<?php

/**
 * Questa classe implementa un Handler per SQLI per fare in modo 
 * di importare automaticamente nuovi contenuti in base al Tag (ezTags) Tematica. 
 * 
 * La configurazione delle tematiche da importare e le classi
 * in cui vanno importate sta nel ocrepository.ini. 
 * 
 * La selezione delle tematiche può essere fatta tramite la dashboard.
 */
class ITTematicaSyncHandler extends SQLIImportAbstractHandler implements ISQLIImportHandler{
    
    protected $rowIndex = 0;
    protected $rowCount;
    
    protected $curLog;
    protected $controller;
    
    protected $currentGUID;
    
    /**
     * Costruttore
     */
    public function __construct( SQLIImportHandlerOptions $options = null ){
        $this->curLog = ObjectController::get();
        
        try{
            parent::__construct( $options );

            $this->curLog->ocLoggerUtil->addInfoMessage('---------------------------');
            $this->curLog->ocLoggerUtil->addInfoMessage('ITTematicaSyncHandler - Enter');

            $this->controller = new ITTematicaSyncController( $options );

            $this->curLog->ocLoggerUtil->writeLogs();
        } 
        catch (Exception $ex) {
            $this->curLog->ocLoggerUtil->addErrorMessage('Error in ITTematicaSyncHandler - construct: ' . $ex->getMessage());
            $this->curLog->ocLoggerUtil->writeLogs();
            echo 'Got Exception on ITTematicaSyncHandler - initialize: ' . $ex->getMessage() . "\n";
        }
    }
    
    /**
     * Caricamento datasource
     */
    public function initialize() {
        $this->curLog->ocLoggerUtil->addInfoMessage('ITTematicaSyncHandler - initialize - Enter');
        
        try{
          $this->dataSource = $this->controller->loadDataSource();
        } 
        catch (Exception $ex) {
          $this->curLog->ocLoggerUtil->addErrorMessage('Error in ITTematicaSyncHandler - initialize: ' . $ex->getMessage());
          $this->curLog->ocLoggerUtil->writeLogs();
          echo 'Got Exception on ITTematicaSyncHandler - initialize: ' . $ex->getMessage() . "\n";
        }    
    }
    
    /**
     * Calcolo della lunghezza del processo
     * @return integer
     */
    public function getProcessLength() {
        $this->curLog->ocLoggerUtil->addInfoMessage('ITTematicaSyncHandler - getProcessLength - Enter');
        
        if( !isset( $this->rowCount ) ){   
            $this->rowCount = count( $this->dataSource );
            if ($this->rowCount == 0){
                $this->rowCount = 1;
            }
        }  
        else {
            $this->rowCount = 1;
        }
        
        $this->curLog->ocLoggerUtil->addInfoMessage('ITTematicaSyncHandler - getProcessLength - rowCount: '.$this->rowCount);
        $this->curLog->ocLoggerUtil->writeLogs();
        return $this->rowCount;
    }
    
    /**
     * Ritorna il prossimo elemento da processare
     * @return object
     */
    public function getNextRow() {
        try{
            if( $this->rowIndex < $this->rowCount ){
                $row = $this->dataSource[$this->rowIndex];
                $this->rowIndex++;
            }
            else{
                $row = false; // We must return false if we already processed all rows
            }
        }catch (Exception $e){
            $row = false; // We must return false if we already processed all rows
        }
        return $row;
    }
    
    public function cleanup() {
        $this->curLog->ocLoggerUtil->addInfoMessage('# End #');
        
        $this->curLog->ocLoggerUtil->writeLogs();

        // Nothing to clean up
        return;
    }

    
    /**
     * Processa l'importazione di un oggetto
     * @param object $row
     */
    public function process( $row ) {
        try{
            $apiNodeUrl = rtrim( $row['base_url'], '/' );
            $apiNodeUrl .= '/api/opendata/v1/content/node/' . $row['remote_node_id'];
            
            $remoteApiNode = OCOpenDataApiNode::fromLink( $apiNodeUrl );
            if ( !$remoteApiNode instanceof OCOpenDataApiNode ){
                throw new Exception( "Url remoto \"{$apiNodeUrl}\" non raggiungibile" );
            }
            
            $newObject = $remoteApiNode->createContentObject( $row['parent_node_id'] );
            if ( !$newObject instanceof eZContentObject ){
                throw new Exception( "Fallita la creazione dell'oggetto da nodo remoto" );
            }
            
        }catch (Exception $e){
            $this->curLog->ocLoggerUtil->addErrorMessage('Error in ITTematicaSyncHandler - process: '.$e->getMessage());
            $this->curLog->ocLoggerUtil->writeLogs();
            echo 'Got Exception on ITTematicaSyncHandler - process: ' . $e->getMessage() . "\n";
        }
    }
    
    /**
     * Identificativo dell'handler
     * @return string
     */
    public function getHandlerIdentifier() {
        return 'tematicaSyncHandler';
    }

    /**
     * Nome dell'handler
     * @return string
     */
    public function getHandlerName() {
        return 'tematicaSync Handler';
    }

    /**
     * 
     * @return type
     */
    public function getProgressionNotes() {
        return 'Currently importing : ' . $this->currentGUID;
    }
}
