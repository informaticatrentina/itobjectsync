<?php

class itObjectsFiltersUtils
{
    public function getObjectByModifyByDate($dataAnalisi){
    
        // recupero la lista delle classi che il server gestisce
        $classiGestite = array();
        $classiGestite = ($this->runtimeSettingsINI->variable('serverSyncClasses','serverSyncClassList'));

        $modified = date("c" , "08/10/2015");
         
        // 
        $fetch_parameters = array(
                    'query'     => $varfetch,
                    'class_id'  => $classiGestite,
                    'modified'  => $modified
                );
    }
}
?>