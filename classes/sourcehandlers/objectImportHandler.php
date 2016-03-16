<?php
 
class ObjectImportHandler extends SQLIImportAbstractHandler implements ISQLIImportHandler
{
    protected $rowIndex = 0;
    protected $rowCount;
    protected $currentGUID;
    protected $objectController;
    protected $ocOpendataImportController;
    protected $curLog;

    /**
     * Constructor
     */
    public function __construct( SQLIImportHandlerOptions $options = null )
    {   
        $this->curLog = ObjectController::get();
        
        try{
            
            parent::__construct( $options );

            $this->curLog->ocLoggerUtil->addInfoMessage('---------------------------');
            $this->curLog->ocLoggerUtil->addInfoMessage('ObjectImportHandler - Enter');

            $this->objectController = new ObjectController($options);

            $this->ocOpendataImportController = new OCOpendataImportController();

            $this->curLog->ocLoggerUtil->writeLogs();
        } 
        catch (Exception $e) {
            $this->curLog->ocLoggerUtil->addErrorMessage('Error in ObjectImportHandler - construct: '.$e->getMessage());
            $this->curLog->ocLoggerUtil->writeLogs();
            echo 'Got Exception on ObjectImportHandler - initialize: ' . $e->getMessage() . "\n";
        }
    }
    
    public function initialize() {

        try{
          $this->dataSource = $this->objectController->loadObjectByDatasource();
        } 
        catch (Exception $ex) {
          $this->curLog->ocLoggerUtil->addErrorMessage('Error in ObjectImportHandler - initialize: '.$e->getMessage());
          $this->curLog->ocLoggerUtil->writeLogs();
          echo 'Got Exception on ObjectImportHandler - initialize: ' . $e->getMessage() . "\n";
        }       
    }

    public function getProcessLength() {
        
       
        $this->curLog->ocLoggerUtil->addInfoMessage('ObjectImportHandler - getProcessLength - Enter');
        
        if( !isset( $this->rowCount ) )
        {   
            $this->rowCount = count($this->dataSource);
            if ($this->rowCount == 0)
                $this->rowCount = 1;
        }  
        else {
            $this->rowCount = 1;
        }
        
        
        $this->curLog->ocLoggerUtil->addInfoMessage('ObjectController - getProcessLength - rowCount: '.$this->rowCount);

        $this->curLog->ocLoggerUtil->writeLogs();
        
        return $this->rowCount;
    }


    public function getNextRow() {
        
       
        try{
            if( $this->rowIndex < $this->rowCount )
            {
                $row = $this->dataSource[$this->rowIndex];
                $this->rowIndex++;
            }
            else
            {
                $row = false; // We must return false if we already processed all rows
            }
        }catch (Exception $e){
            $row = false; // We must return false if we already processed all rows
        }
        return $row;
    }

     public function cleanup()
    {
        // E' ora possibile eseguire l'update dalla data         
        $this->objectController->updateLastcorrectjob();
         
        $this->curLog->ocLoggerUtil->addInfoMessage('# End #');
        
        $this->curLog->ocLoggerUtil->writeLogs();

        // Nothing to clean up
        return;
    }

    public function process($row) {

        try{

            $this->ocOpendataImportController->loadObjectbyRemoteID($row);

        }catch (Exception $e){
            $this->curLog->ocLoggerUtil->addErrorMessage('Error in ObjectImportHandler - process: '.$e->getMessage());
            $this->curLog->ocLoggerUtil->writeLogs();
            echo 'Got Exception on ObjectImportHandler - initialize: ' . $e->getMessage() . "\n";
        }
    }
    
     public function getHandlerIdentifier() {
        return 'objectImportHandler';

    }

    public function getHandlerName() {
          return 'objectImport Handler';
    }
    
    public function getProgressionNotes()
    {
        return 'Currently importing : '.$this->currentGUID;
    }
    
}
?>