<?php

class ItFiltersUtil {
	private $itIniLoaderUtils;
	
	function __construct( ) {
		
		$this->itIniLoaderUtils = new ItIniLoaderUtils();

	}
        public function getSyncClassList(){
            
            return $this->itIniLoaderUtils->getSyncClassList();
            
        }
        
        public function getExcludedAttributes(){
            
            return $this->itIniLoaderUtils->getExcludedAttributes();
            
        }
        
        public function getSyncUrl(){
            
            return $this->itIniLoaderUtils->getSyncUrl();
            
        }
        function getobjectSubfixUrl() {

            return $this->itIniLoaderUtils->getobjectSubfixUrl();

        }
    
        function getApiSubfixUrl() {

            return $this->itIniLoaderUtils->getApiSubfixUrl();
        }
  
        function getTagServerUrl() {
        
        	return $this->itIniLoaderUtils->getTagServerUrl();
        
        }
        
        function getTagSubfixUrl() {
        
        	return $this->itIniLoaderUtils->getTagSubfixUrl();
        
        }
 
        public function getSyncTagList(){
        
        	return $this->itIniLoaderUtils->getSyncTagList();
        
        }
        
	public function iniupdatableclasses($jsonarray) {
		
		$classiniarray = array();
		
		$classiniarray = $this->itIniLoaderUtils->getSyncClassList();
		                
		# ciclo sul json delle classi con oggetti da aggiornare
		$updatableclasses = json_decode($jsonarray, true);
		
		$classestoupdate = array();
				
		foreach ($updatableclasses as $key => $updatableclass) {
					
			# se la classe e' compresa nell'elenco ini tengo la classe
			if (in_array($key, $classiniarray)) {
				
				$classestoupdate[$key]=$updatableclass;
				
			}
			
		}
		
		return json_encode($classestoupdate);

	}
/**
 * 
 * 
 * @param array $jsonarray
 * @return array
 */       
	public function iniupdatabletags($jsonarray) {
	
		$taginiarray = array();
	
		$taginiarray = $this->itIniLoaderUtils->getSyncTagList();
	
		# ciclo sul json i tag con oggetti da aggiornare
		$updatabletags = json_decode($jsonarray, true);
	
		$tagstoupdate = array();
	
		$arrayKeys = array_keys($updatabletags);
		
		for ($i = 0; $i < count($arrayKeys); $i++) {
			
			$singleElement = $updatabletags[$arrayKeys[$i]]['Keyword'];
			
			if (in_array($singleElement, $taginiarray))
				$tagstoupdate[$arrayKeys[$i]]=$updatabletags[$arrayKeys[$i]];
		}
		
		return json_encode($tagstoupdate);
	
	}
	
	public function updatablestatusobjects($objarray) {

        //carico lo stato che permette l'allineamento
        $syncState = $this->itIniLoaderUtils->getSyncState();
		$updstatusobjarray=array();
		
		# controllo lo stato di ogni oggetto
		foreach ($objarray as $ezcontentobject) {

            //carico le cippie di stati gruppo/stato
			$stateIdentifierArray = $ezcontentobject->stateIdentifierArray();

            //scorro tutti gli stati
            foreach($stateIdentifierArray as $group_state){

               //se l'oggetto ha lo stato che permette l'allineamento
               if($group_state==$syncState){
                   array_push($updstatusobjarray,$ezcontentobject);
                   break;
               }
            }
			
		}
	
		return $updstatusobjarray;
	}
		 
}


?>