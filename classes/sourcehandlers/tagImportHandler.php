<?php
 
class TagImportHandler extends SQLIImportAbstractHandler implements ISQLIImportHandler
{
    protected $rowIndex = 0;
    protected $rowCount;
    protected $currentGUID;
    protected $tagController;
    
    /**
     * Constructor
     */
    public function __construct( SQLIImportHandlerOptions $options = null )
    {   
        parent::__construct( $options );
        
        $this->tagController = new TagController($options);
    }
    
    public function initialize() {
         
        $this->dataSource = $this->tagController->loadTagByDatasource();
      
        
    }
    
    public function getProcessLength() {
        
         if( !isset( $this->rowCount ) )
        {   
            $this->rowCount = count($this->dataSource);
            if ($this->rowCount == 0)
                return 1;
        }  
        else {
            $this->rowCount = 1;
        }
		
        return $this->rowCount;
    }
    
    function ObjectImportHandler()
    {
      
    }

    public function cleanup() {
        
    
        #$this->objectToEz->ocLoggerUtil->writeLogs();
         // Nothing to clean up
        return;
    }

    public function getHandlerIdentifier() {
        
    }

    public function getHandlerName() {
        
    }

    public function getNextRow() {
    
    	if( $this->rowIndex < $this->rowCount )
    	{
    		$row = $this->dataSource[$this->rowIndex];
    		$this->rowIndex++;
    	}
    	else
    	{
    		$row = false; // We must return false if we already processed all rows
    	}
    
    	return $row;
    }
    
  

    public function getProgressionNotes() {
        
    }

   

    public function process($row) {
        
    	echo '<pre>';
    	print_r($row);
    	die();
    	
    }

    
}

?>