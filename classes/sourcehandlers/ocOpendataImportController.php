<?php	

class OCOpendataImportController
{	
    //oggetto di tipo SQLIImportHandlerOptions che contiene gli eventuali i parametri passai all'Handler
    private $options;
    public static $instance;
    private $itFiltersUtil;
    private $remoteApiNode;

    function __construct( SQLIImportHandlerOptions $options = null ) {

        $this->ocLoggerUtil = new OCLoggerUtil(null, 'ocOpendataImportController');
        $this->ocLoggerUtil->addInfoMessage('Construct per ocOpendataImportController.');

		//imposto i parametri passati, a livello gblobale. Poi initRuntimeSettings() valuterà se usarle o meno
		$this->options=$options;
		
		$this->itFiltersUtil = new ItFiltersUtil();
		
                
		#$this->itobjectsutils = new itobjectsutils();
		
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
 
    public function loadObjectbyRemoteID($eZContentObject){
    	
        $urlIniArray = $this->itFiltersUtil->getSyncUrl();
                       
        # parte fissa link recupero oggetto da server
        $objectRetrieveUrl = 'api/opendata/v1/content/node/0/remoteid/';

        # recupero url dal file ini per la classe interessata
        $classUrl = $urlIniArray[$eZContentObject->ClassIdentifier];

        # recupero remoteID per costruzione link recupero oggetto da server
        $elementRemoteID = $eZContentObject->RemoteID;

        $apiNodeUrl = $classUrl[0] . $objectRetrieveUrl . $elementRemoteID;

        $this->remoteApiNode = OCOpenDataApiNode::fromLink( $apiNodeUrl );

        if ( !$this->remoteApiNode instanceof OCOpenDataApiNode )
        {
                throw new Exception( "Fallita la creazione di OCOpenDataApiNode " );
        }

        $result_value = $this->updateContentObject( $eZContentObject );

        if (!$result_value)
        {
                throw new Exception( "Aggiornamento dell'oggetto fallito" );
        }

        return true;
    }


    private function updateContentObject(eZContentObject $eZContentObject)
    {


        $mainNodeID = $eZContentObject->mainNodeID();
        $parentNodeID = eZContentObjectTreeNode::getParentNodeId($mainNodeID);
        $classIdentifier = $eZContentObject->ClassIdentifier;
        $remoteID = $eZContentObject->RemoteID;

        if ( !eZContentClass::fetchByIdentifier($classIdentifier))
        {
            throw new Exception( "La classe" . $classIdentifier . "non esiste in questa installazione" );
        }

        $params                     = array();

        $params['class_identifier'] = $classIdentifier;
        $params['remote_id']        = $remoteID;
        $params['parent_node_id']   = $parentNodeID;

        $params['attributes']       = $this->getAttributesStringArray($eZContentObject);


        $result = eZContentFunctions::updateAndPublishObject( $eZContentObject, $params);

        return $result;

    }

    private function getAttributesStringArray(eZContentObject $eZContentObject)
    {
        $attributeList = array();
        
        $excludedAttributes = array();
        $excludedAttributesOfClass = array();



        # filtro i campi da scartare per la specifica classe
        $classidentifier = $this->remoteApiNode->metadata['classIdentifier'];
        
        #print_r('$classidentifier=');
        #print_r($classidentifier);
        
        $excludedAttributes = $this->itFiltersUtil->getExcludedAttributes();
        
        //echo '<pre>';
        //print_r('ARRAY ATTRIBUTI DA SCARTARE $excludedAttributes:');
        //print_r($excludedAttributes);
        
        $excludedAttributesOfClass = $excludedAttributes[$classidentifier];
        
        //print_r('  CLASSE=');
        //print_r($classidentifier);
        //print_r('  ARRAY ATTRIBUTI DA SCARTARE PER LA MIA CLASSE $excludedAttributes:');
        //print_r($excludedAttributesOfClass);

        foreach( (array) $this->remoteApiNode->fields as $identifier => $fieldArray )
        {
                      
            if (!in_array($fieldArray['identifier'],(array)$excludedAttributesOfClass)){
                
              	switch( $fieldArray['type'] )
            	{
            		case 'ezxmltext':
            			$attributeList[$identifier] = SQLIContentUtils::getRichContent( $fieldArray['value'] );
            			break;
            		case 'ezbinaryfile':
            		case 'ezimage':
            			if ( !empty( $fieldArray['value'] ) )
            			{
            				$attributeList[$identifier] = SQLIContentUtils::getRemoteFile( $fieldArray['value'] );
            			}
            			break;
            			/*
            			 case 'ezobjectrelationlist':
            			 $parentNodeID = $this->findRelationObjectLocation( $identifier, $parentNodeID );
            			 $attributeList[$identifier] = $this->createRelationObjects( $fieldArray, $parentNodeID, $isUpdate );
            			 break;
            			 */
                                
                        case 'eztags':
                            $attributeList[$identifier] = $this->createLocalTags( $identifier, $fieldArray['string_value'] );
                            break;
                        
            		default:
            			$attributeList[$identifier] = $fieldArray['string_value'];
            			break;
            	}
            	     	
            }
        	    
        }
        return $attributeList;
    }
    
    /**
     * Cerca sul siteaccess locale tramite le KeyWord se esistono i tag uguali e
     * li imposta. Nel caso di Keyword uguali con nodo padre diverso questo metodo non funziona.
     * 
     * @param string $identifier
     * @param string $remoteTags
     */
    protected function createLocalTags( $identifier, $remoteTags ){
        $tag = "";
        
        $ezTagsArray = explode("|#", $remoteTags);

        // Verifico ci siano tags impostati
        if( !empty($ezTagsArray) ){
            // Verifico che la lunghezza dell'array sia un multiplo di 3
            if( count($ezTagsArray) % 3 == 0 ){
                $tagsNum = count($ezTagsArray) / 3;
                
                $tagIDs = "";
                $tagKeywords = "";
                $tagParents = "";
                
                for($i = $tagsNum; $i<$tagsNum*2; $i++){
                    $keyword = $ezTagsArray[$i];
                    
                    $ezTag = eZTagsObject::fetchByKeyword( $keyword );
                    
                    if(!empty($ezTag)){
                        $tagIDs .= $ezTag[0]->ID . '|#';
                        $tagKeywords .= $ezTag[0]->Keyword . '|#';
                        $tagParents .= $ezTag[0]->ParentID . '|#';
                    }
                }
                
                $tag = $tagIDs . $tagKeywords . $tagParents;
                $tag = substr($tag, 0, strlen($tag)-2);
            }
        }
        
        return $tag;
    }
}
	
?>