<?php	

class ObjectController
{	
    //oggetto di tipo SQLIImportHandlerOptions che contiene gli eventuali i parametri passai all'Handler
    private $options;
    public static $instance;
    private $itFiltersUtil;
    public $ocLoggerUtil;

    public static function get() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    function __construct( SQLIImportHandlerOptions $options = null ) {

                $this->ocLoggerUtil = new OCLoggerUtil(null, 'objectController');
                $this->ocLoggerUtil->addInfoMessage('Construct per objectController.');

		//imposto i parametri passati, a livello gblobale. Poi initRuntimeSettings() valuterà se usarle o meno
		$this->options=$options;
		
		$this->itFiltersUtil = new ItFiltersUtil();
		
                
		#$this->itobjectsutils = new itobjectsutils();
		
                $this->ocLoggerUtil->writeLogs();
 
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
 
    public function loadObjectByDatasource(){

            $urlDaChiamare = array();
            $classiniarray = array();
            $urlIniArray = array();
            $returnArray = array();
            
            $this->ocLoggerUtil->addInfoMessage('ObjectController - loadObjectByDatasource - Enter.');
             
            // Recupera getobjectSubfixUrl per il suffisso da mettere ni link
            $objectSubfixUrl = $this->itFiltersUtil->getobjectSubfixUrl();
           
            // Gestione dei parametri in input.            
            $dataAnalisiIni = '';
            $dataAnalisiFin = '';            
            //se vengono passati dei parametri all'handler li usa
            if($this->options!=null){
                // 1° Verifica se i parametri sono passati da Scheduled import(s)
                if($this->options->hasAttribute('Data_Analisi_Ini')){
                    $dataAnalisiIni = $this->options->attribute('Data_Analisi_Ini');
                }
		if($this->options->hasAttribute('Data_Analisi_Fin')){
				$dataAnalisiFin = $this->options->attribute('Data_Analisi_Fin' );
		}
            }
            else
            {
                // Se non sono date impostate prova a leggere l'ultima data di esecuzione dalla classe clientObjectSync
                // deve fare la fetch per leggere la data                
                $eZContentObject = eZContentObject::fetchByRemoteID('clientObjectSync-lastcorrectjob');
                //echo("<pre>");  
                //print_r($eZContentObject);                 
               
                
                if ($eZContentObject != null){
                    // Assegnazione del dataMap
                    $dataMap = $eZContentObject->attribute( 'data_map' );
                    // Assegnazione dell'attributo lastcorrectjob
                    $dataObjectAttribute = ($dataMap['lastcorrectjob']->toString() ); 
                    //print_r($eZContentObject);
                    setlocale(LC_TIME,"it_IT");
                    date_default_timezone_set("Europe/Rome");
                    //$format = "Y-m-d H:i:s";
                    //$local_datetime = date($format, $dataObjectAttribute);
                    //print_r("-->".$local_datetime."<--");                    
                    $format = "d-m-Y";
                    $dataAnalisiIni =  date($format, $dataObjectAttribute);
                    // Lettura e conversione nel formato italiano
                    $dataAnalisiIni = itobjectsutils::convertTimestampToDateIt($dataObjectAttribute , 12 );  
                   
                    
                }
            }
            
            //echo("->");
            //echo("Data_Analisi_Ini: ".$dataAnalisiIni." - ");
            //echo("Data_Analisi_Fin: ".$dataAnalisiFin);
            //echo(" <-");
            
            $this->ocLoggerUtil->addInfoMessage('Data_Analisi_Ini:'.$dataAnalisiIni);
            $this->ocLoggerUtil->addInfoMessage('Data_Analisi_Fin:'.$dataAnalisiFin);
             
            // Per ogni singolo site si deve individuare quali url devono essere richiamati           
            $classiniarray = $this->itFiltersUtil->getSyncClassList();             
            $urlIniArray = $this->itFiltersUtil->getSyncUrl();
            
            foreach ($classiniarray as $elementClass) {
                $link = $urlIniArray[$elementClass][0].$objectSubfixUrl;               
                array_push($urlDaChiamare, $link);
            }
            
            // Applica la funzione per avere un array con avere solo valori univoci
            $urlDaChiamare = array_unique($urlDaChiamare);
            
            foreach ($urlDaChiamare as $url) {
                
                //es. https://ez101-dev.infotn.it/openpa/data/serverObjectsync/(dataAnalisiIni)/$dataAnalisiIni/(dataAnalisiFin)/");
                $url = $url.'(dataAnalisiIni)/'.$dataAnalisiIni.'/(dataAnalisiFin)/'.$dataAnalisiFin;
                $this->ocLoggerUtil->addInfoMessage('loadObjectByDatasource - Url:'.$url);
                                
                $unparsed_json = file_get_contents($url);
                
                //filtro le classi che non voglio aggiornare
                $unparsed_json = $this->itFiltersUtil->iniupdatableclasses($unparsed_json);
                
                //filtro gli oggetti che si trovano in uno stato per il quale non vanno fatti aggiornamenti
                $filteredObjects = $this->loadLocalObjectsByRemoteId($unparsed_json);
                
                
                
                // Se ci sono oggetti da allineare aggiunge l'array proveniente dal singolo server
                if (count($filteredObjects) > 0)
                	$returnArray = array_merge($returnArray, $filteredObjects);
            }
            
            $this->ocLoggerUtil->addInfoMessage('ObjectController - loadObjectByDatasource - Exit.');
            $this->ocLoggerUtil->writeLogs();
            return $returnArray;
    }	

    public function loadLocalObjectsByRemoteId($unparsed_json){

        $json_array = json_decode($unparsed_json, true);
        $returnArray = array();

        foreach($json_array as $key => $arrayRemoteId){

            foreach($arrayRemoteId as $remoteId){
                $eZContentObject = eZContentObject::fetchByRemoteID($remoteId);

                if($eZContentObject instanceof eZContentObject){
                    array_push($returnArray, $eZContentObject);
                }
            }
        }

        if(itobjectsutils::isEmptyArray($returnArray)){
            return array();
        }
        return $this->itFiltersUtil->updatablestatusobjects($returnArray);
    }
 
    
    //----------------------------------------------------------------------------------------------
    //scrive nell'oggettoEz clientobjectsync
    //la data di ultima esecuzione
    //----------------------------------------------------------------------------------------------
    public function updateLastcorrectjob(){
        
        try{
              
            
            setlocale(LC_TIME,"it_IT");
            date_default_timezone_set('Europe/Rome');
            $dateClientobjectsync = date_create();
                        
            // echo("->timestamp->".date_timestamp_get($date)."<-");
       
            $timestamp  = date_timestamp_get($dateClientobjectsync);
            
            $dateLastcorrectjobContentOptions = new SQLIContentOptions(array(
				'class_identifier'      => 'clientobjectsync',
				'remote_id'             => 'clientObjectSync-lastcorrectjob'
			));
			
                                    
            $dateLastcorrectjob = SQLIContent::create($dateLastcorrectjobContentOptions);
            
            $dateLastcorrectjob->fields->nome = 'MyclientObjectSync';

            // Assegnazione dell'attributo lastcorrectjob
            $dateLastcorrectjob->fields->lastcorrectjob = $timestamp;

            //Prova a recuperare l'ID Nodo: 43 Media [Cartella] dal content.ini
            $contentIni = eZINI::instance( 'content.ini' );
            $idNodoMedia = 43;
            if ($contentIni->hasVariable('NodeSettings','MediaRootNode'))
                $idNodoMedia = $contentIni->variable('NodeSettings','MediaRootNode'); 
            
            $dateLastcorrectjob->addLocation(SQLILocation::fromNodeID($idNodoMedia));   

            $dateLastcorrectjobId = $this->store($dateLastcorrectjob);
 
         } catch (Exception $exp) {
            
        	echo 'Got Exception message on updateLastcorrectjob: ' . $exp->getMessage() . "\n";
                
        	$this->ocLoggerUtil->addErrorMessage('Exception on objectController -  updateLastcorrectjob: ' . $exp->getMessage());
                $this->ocLoggerUtil->writeLogs();
                
            return false; 
        }        
    }
    
    //----------------------------------------------------------------------------------------------
    //esegue lo store dell'oggettoEz passato
    //sono i compounder di ogni oggetto che si devono preoccupare di creare correttamente 
    //il content
    //----------------------------------------------------------------------------------------------
    static function store($content){

        $publisher = SQLIContentPublisher::getInstance();

        $publisher->publish( $content );

        $id = $content->id;

        unset( $content );

            return $id ;
    }
}
	
?>