<?php

class DataHandlerserverTagsync implements OpenPADataHandlerInterface
{
        private $userParameters;
        private $runtimeSettingsINI;
        private $tagGestiti;
    
    
	public function __construct( array $Params )
	{
					
		$userParameters = $Params["UserParameters"];
		
		$this->runtimeSettingsINI = eZINI::instance( 'ittagServersync.ini' );
	
	}

	public function getData()
	{
		
		$ezTagsObject = new eZTagsObject();
		
		$returnArray = array();
		$childrenArray = array();
		
		// recupero la lista dei remoteID dei tags che il server gestisce
		$tagsGestiti = explode(',', $this->runtimeSettingsINI->variable('serverSyncTags','serverSyncTagsList'));
		$numtagreturned = count($tagsGestiti);
		
		
		
		
		// Ciclo per recuperare gli array degli oggetti ritornati
		for ($i = 0; $i < $numtagreturned; $i++) {
			
			$parentFields = array();
			
			#$eZTagParentObj = eZTagsObject::fetchByRemoteID('e4fd6bcf76ab7ce0e04684bdb867a76d');
			#$eZTagParentObj = eZTagsObject::fetchByRemoteID(trim($tagsGestiti[$i]));
			
			$eZTagParentObj_array = eZTagsObject::fetchByKeyword(trim($tagsGestiti[$i]),true);
			$eZTagParentObj = $eZTagParentObj_array[0];
			
			/*
			$eZTagParentObj->RemoteID;
			$eZTagParentObj->getAttribute('RemoteID');
			$eZTagParentObj->attributes('remote_id');
			*/
		
			# recupero e trasformo in array l'oggetto parentID
			$parentFields = itobjectsutils::getObjectAsArray($eZTagParentObj);

			# recupero tutti i children tags
			$childrenArray = eZTagsObject::fetchByParentID($eZTagParentObj->ID);

			# aggiungo al mio array generale il blocco dei children tags 
			$parentFields['subTags'] = $childrenArray;
			
			$returnArray[$eZTagParentObj->RemoteID] = $parentFields;
			
			
		}
		return $returnArray;
	}
        
}
