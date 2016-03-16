<?php

class ItIniLoaderUtils {
	
	private $syncClassList= array();
	public $syncTagIDList;
        public $excludeAttributes;
        public $syncState;
        public $objectSubfixUrl;
        public $apiSubfixUrl;
        
        private $syncServerClass= array();
        public $urlCorrente= array();
        
        public $tagServerUrl;
        public $tagSubfixUrl;
    
	function __construct() {
		
            
                // Lettura del file ini dell'estensione per le classi           
		$itObject = eZINI::instance( 'itobjects.ini' );        
                $this->objectSubfixUrl = $itObject->variable('syncServerClass', 'objectSubfixUrl' );
                $this->apiSubfixUrl = $itObject->variable('syncServerClass', 'apiSubfixUrl' );
                $this->syncServerClass = explode(',', $itObject->variable('syncServerClass', 'syncUrlClassList' ));
                foreach ($this->syncServerClass as $elementUrl) {
			 $this->urlCorrente[$elementUrl] = explode(",",$itObject->variable( 'syncServerUrl', $elementUrl ));
                }
              
                // Fine lettura e assegnazione del file ini dell'estensione  
 
                // Lettura del file ini dell'estensione per i tag
		$itObject = eZINI::instance( 'ittags.ini' );
                $this->tagServerUrl = $itObject->variable('syncTagServer', 'tagServerUrl' );
                $this->tagSubfixUrl = $itObject->variable('syncTagServer', 'tagSubfixUrl' );
                // Fine lettura e assegnazione del file ini dell'estensione per i tag
                
                //   
                // Lettura del file ini delle classi di ogni singolo client    
		$itobjectsync = eZINI::instance( 'itobjectsync.ini' );

				$this->syncClassList = explode(',', $itobjectsync->variable( 'syncClasses', 'syncClassList' ));
		
				foreach ($this->syncClassList as $element) {
					$this->excludedAttributes[$element] = explode(",",$itobjectsync->variable( 'excludedAttributes', $element ));
				}
				
				$this->syncTagIDList = $itobjectsync->variable( 'syncTagID', 'syncTagIDList' );

                $this->syncState = $itobjectsync->variable( 'syncStates', 'syncState' );
                // fine lettura del file ini delle classi di ogni singolo client

                // Lettura del file ini delle classi di ogni singolo client
		$itobjectsync = eZINI::instance( 'ittagsync.ini' );
                
                $this->syncTagList = explode(',', $itobjectsync->variable( 'syncTag', 'syncTagList' ));
                
                // fine lettura del file ini delle classi di ogni singolo client
                
	}
	
	function getSyncClassList() {
		
		return $this->syncClassList;
		
	}
        
        function getSyncUrl() {

            return $this->urlCorrente;
		
	}
	
	function getSyncTagIDList() {
		
		return $this->syncTagIDList;
		
	}

	function getExcludedAttributes() {
		
		return $this->excludedAttributes;
		
	}


    function getSyncState() {

        return $this->syncState;

    }

     function getobjectSubfixUrl() {

        return $this->objectSubfixUrl;

    }
    
     function getApiSubfixUrl() {

        return $this->apiSubfixUrl;

    }

    function getTagServerUrl() {
    
    	return $this->tagServerUrl;
    
    }
    
    function getTagSubfixUrl() {
    
    	return $this->tagSubfixUrl;
    
    }

    function getSyncTagList() {
    
    	return $this->syncTagList;
    
    }
    
}
?>