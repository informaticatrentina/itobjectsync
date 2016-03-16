<?php	

class TagController
{	
    //oggetto di tipo SQLIImportHandlerOptions che contiene gli eventuali i parametri passati all'Handler
    private $options;
    public static $instance;
    private $itFiltersUtil;  
  
    function __construct( SQLIImportHandlerOptions $options = null ) {
                
		//imposto i parametri passati, a livello gblobale. Poi initRuntimeSettings() valuterà se usarle o meno
		$this->options=$options;
		
		$this->itFiltersUtil = new ItFiltersUtil();
    }
	
	//----------------------------------------------------------------------------------------------
	//inizializza gli attributi usati a runtime da mandare al ws
	//----------------------------------------------------------------------------------------------
    private function initRuntimeSettings(){
      
        $arrayForLog = array();

        //se ho passato dei parametri all'handler li uso
        if($this->options!=null){
            
                
         }else{
                
        }
    }
	
	//----------------------------------------------------------------------------------------------
	//invoca il WS
	//----------------------------------------------------------------------------------------------
    public function wsCall(){

        $this->initRuntimeSettings();
        $returnArray = array();
        // Eseguire la call!!!
        
    }
 
    public function loadTagByDatasource(){
    	    
    	$tagServerUrl = $this->itFiltersUtil->gettagServerUrl();
    	$tagSubfixUrl = $this->itFiltersUtil->gettagSubfixUrl();
    	
    	$unparsed_json = file_get_contents($tagServerUrl.$tagSubfixUrl);
    	 
        $unparsed_json = $this->itFiltersUtil->iniupdatabletags($unparsed_json);

        $dataAssociativeArray = json_decode($unparsed_json, true);
        
        $ezTagsObjectServer = new eZTagsObject(array());
        $ezTagsObjectClient = new eZTagsObject(array());
        
                        
        # modifica attributi tag
        # prendo un remoteid come esempio
        # a regime dovremo impostare un ciclo su tutti i remoteid di tutti i tag_figli
        
        foreach ($dataAssociativeArray as $key => $value) {
        		$childrenarray = array();
        		
        		$this->publishTagByRemoteID($key, 0, $value['Keyword']);
        		
        		
        		#recupero id del tag padre
        		$parentIdClientRoot = eZTagsObject::fetchByRemoteID($key)->ID;
        		$childrenarray = $value['subTags'];
				for ($i = 0; $i < count($childrenarray); $i++) {
					
					# controllo esistenza tag figlio. Se non esiste lo genero fissando remoteID e keyword e tag padre
								
					$this->publishTagByRemoteID($childrenarray[$i]['RemoteID'], $parentIdClientRoot, $childrenarray[$i]['Keyword']);
					
					$ezTagsObjectServer = (object)$childrenarray[$i];
				} 
        }
        
           
    }
    
    public function loadLocalTagsByRemoteId($unparsed_json){
    
    	$json_array = json_decode($unparsed_json, true);
    	$returnArray = array();
    
    	foreach($json_array as $key => $arrayRemoteId){
    
    		foreach($arrayRemoteId as $remoteId){
    			$eZTagsObject = eZTagsObject::fetchByRemoteID ($remoteId);
    
    			if($eZTagsObject instanceof eZTagsObject){
    				array_push($returnArray, $eZTagsObject);
    			}
    
    		}
    
    
    	}
   		return $returnArray;
    }

    
    public function publishTagByRemoteID($tagRemoteID, $tagParentID, $tagKeyword) {
    	
    	$ezTagsObject = eZTagsObject::fetchByRemoteID($tagRemoteID);
    	
    	if (!($ezTagsObject instanceof eZTagsObject)) {
    		$ezTagsObject = new eZTagsObject(array('remote_id'=>$tagRemoteID));
    	}
    		
    	$ezTagsObject->setAttribute('parent_id', $tagParentID); // ParentID
    	$ezTagsObject->setAttribute('keyword', $tagKeyword);    // Keyword
    	
    	$ezTagsObject->store();
    	
    	    	
    	return true; 
    }
    
}
	
?>