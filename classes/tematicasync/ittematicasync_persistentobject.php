<?php

/**
 * Persistent Object necessita che la tabella ittematicasync
 * sia presente sul database
 */
class ITTematicaSyncPersistentObject extends eZPersistentObject
{
    
    /**
     * Costruttore
     * 
     * @param type $row
     */
    public function ITTematicaSyncPersistentObject( $row )
    {
        $this->eZPersistentObject( $row );
    }
    
    /**
     * Definizione degli attributi
     * 
     * @return array
     */
    public static function definition()
    {
        return array( "fields" => array( "id" => array( 'name' => 'ID',
                                                        'datatype' => 'integer',
                                                        'default' => 0,
                                                        'required' => true ),                                      
                                         "repository" => array( 'name' => "repository",
                                                                'datatype' => 'text',                                                                                                                          
                                                                'required' => true ),                                      
                                         "destination_node_id" => array( 'name' => "destination_node_id",
                                                                         'datatype' => 'integer',                                                                                                                          
                                                                         'required' => false ),
                                         "tags" => array( 'name' => "tags",
                                                          'datatype' => 'text',                                                             
                                                          'required' => false )        
                                        ),
                      "keys" => array( "id" ),
                      "sort" => array( "id" => "asc" ),
                      "class_name" => "ITTematicaSyncPersistentObject",
                      "name" => "ittematicasync" );
    }
    
    public static function fetchByRepository( $repository ){
        $cond = array( 'repository' => $repository );
        $return = eZPersistentObject::fetchObject( self::definition(), null, $cond );
        return $return;
    }
    
    public static function fetchList( )
    {
        $ittematicasyncObjectList = eZPersistentObject::fetchObjectList(
                self::definition(),
                null,
                array(),
                null,
                null,
                false
            );
         return $ittematicasyncObjectList;
           
    }

    public static function fetchListCount()
    {
        $countRes = eZPersistentObject::fetchObjectList(
                self::definition(),
                null,
                array(),
                null,
                null,
                false
            ); 
        return $countRes[0]['count'];
    }
    
    public static function fetchById( $id )
    {
        $cond = array( 'id' => $id );
        $return = eZPersistentObject::fetchObject( self::definition(), null, $cond );
        return $return;
    }
    
    public static function removeById( $id )
    {
        $cond = array( 'id' => $id );
        eZPersistentObject::removeObject( self::definition(), $cond );
    }
    
}