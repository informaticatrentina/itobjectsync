<?php

class DataHandlerserverObjectsync implements OpenPADataHandlerInterface
{
        private $userParameters;
        private $runtimeSettingsINI;
        //ocLogger
        public $ocLoggerUtil;
           
	public function __construct( array $Params )
	{
            $this->ocLoggerUtil = new OCLoggerUtil(null, 'ServerObjectSync');
            
            $this->ocLoggerUtil->addInfoMessage('Construct per ServerObjectSync.');
      
            $this->userParameters = $Params["UserParameters"];
         
            $this->runtimeSettingsINI = eZINI::instance( 'itobjectServersync.ini' );
            
            $this->ocLoggerUtil->writeLogs();
            
	}

	public function getData()
	{ 
        try{
                            
            $returnArray = array();
            $dataAnalisiIni = $this->userParameters["dataAnalisiIni"];
            $dataAnalisiFin = $this->userParameters["dataAnalisiFin"];
                
            //
            // Verifica se le date passate sono corretta altrimenti mette il default
            $dataAnalisiIni = itobjectsutils::dataPerEzfetch($dataAnalisiIni , "INI");
            $dataAnalisiFin = itobjectsutils::dataPerEzfetch($dataAnalisiFin , "FIN");
                // Scommetare per controllare le date che vengono passate alla fetch
                // $dataAnalisiIni = "2015-09-01T11:40:00Z";
                //echo("--->".$dataAnalisiIni."<---");
                //echo("--->".$dataAnalisiFin."<---");
                
                $this->ocLoggerUtil->addInfoMessage('DataAnalisiIni: '.$dataAnalisiIni);
                $this->ocLoggerUtil->addInfoMessage('DataAnalisiFin: '.$dataAnalisiFin);
               
                // recupero la lista delle classi gestite dal server   
                $classiGestite = explode(',', $this->runtimeSettingsINI->variable('serverSyncClasses','serverSyncClassList'));
                $objectedreturned = count($classiGestite);
                //  
                
                $fetch_parameters = array(
                    'query'     => '',
                    'class_id'  => $classiGestite,
                    'limit'     => 1000,
                    'filter'    => array( 'meta_modified_dt:['.$dataAnalisiIni.' TO '.$dataAnalisiFin.']')
                );
                    
                // Fetch su un singolo ID
                //$result = eZFunctionHandler::execute( 'ezfind',
                //        'search', array(
                //        'query'   => '',
                //        'class_id' => array( 'deliberazione' ),
                //        'filter'  => array( 'meta_id_si: ( 43661 )' )
                //        )
                //);
                                              
                $result = eZFunctionHandler::execute('ezfind', 'search', $fetch_parameters );
               
                //
                // Ciclo per recuperare gli array degli oggetti ritornati                
                for ($i = 0; $i < $objectedreturned; $i++) {
                    $returnArray[$i] = array();          
                }
                //
                // Ciclo nell'array degli oggetti ritornati               
                foreach ($result["SearchResult"] as $objectSearchResult) {
                    
                    $identificatore =($objectSearchResult->ContentObject->ClassIdentifier);  
                    $ContentObject = ($objectSearchResult->ContentObject->RemoteID);
                    $PublishedDate = ($objectSearchResult->ContentObject->Published);
                    $ModifiedDate = ($objectSearchResult->ContentObject->Modified);
                    
                    // Il RemoteID viene ritornato se la data Modifica è diversa della data Pubblicazione
                    if (($PublishedDate )  != ($ModifiedDate))
                    {
                        //print_r($objectSearchResult);
                        //                 
                        // Cicla sulle classi e mette gli ID in un array separato per ogni tipo classe 
                        for ($i = 0; $i < $objectedreturned; $i++) {                       
                            if ($classiGestite[$i] == $identificatore ){

                                array_push( $returnArray[$i] ,$ContentObject);                    
                           }                        
                        }                     
                    }
                }
                                
               for ($i = 0; $i < $objectedreturned; $i++) {
                    $response[$classiGestite[$i]] = $returnArray[$i];
                    $this->ocLoggerUtil->addInfoMessage('Numero elementi ritornati: '.count($returnArray[$i]).' per '.$classiGestite[$i]." .");
                }
                
                $this->ocLoggerUtil->addInfoMessage('-- objectController - Server - Exit--');
                $this->ocLoggerUtil->writeLogs();
                
				return $response;
                
        } catch (Exception $ex) { 
                
        	echo 'Got Exception on DataHandlerserverObjectsync - getData: ' . $e->getMessage() . "\n";
            $this->ocLoggerUtil->addErrorMessage('Error in DataHandlerserverObjectsync - getData: '.$e->getMessage());
            $this->ocLoggerUtil->writeLogs();
                
        }
    }
}
