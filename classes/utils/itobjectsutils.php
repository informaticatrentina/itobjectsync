<?php	

class itobjectsutils
{
	private $DEFAULT_DATE_PATTERN = 'Y-m-d\TH:i:s';
	
	//----------------------------------------------------------------------------------------------
	//
	//----------------------------------------------------------------------------------------------	
	public static function isEmptyString($stringToCheck){
		
		return (!isset($stringToCheck) || trim($stringToCheck)==='');
	}

        
        public static function isEmptyArray($arrayToCheck){
 
            $errors = array_filter($arrayToCheck);
            return empty($errors);
            
        }
         
	//----------------------------------------------------------------------------------------------
	//
	//----------------------------------------------------------------------------------------------	
	public static function dataPerEzfetch($inputDate, $format){
           
            try{
                sscanf($inputDate, '%2d-%2d-%4d', $gg , $mm, $aaaa);
                
                //echo($mm.$gg.$aaaa."!");
                //echo(checkdate ( $mm , $gg ,  $aaaa ));
                
                if(checkdate ( $mm , $gg ,  $aaaa )){
                    
                    // La data è valida e viene ora ritornata in formato valido per EZ fetch_parameters                    
                    if($format== "INI")
                        return ($aaaa."-".$mm."-".$gg.'T00:00:00Z');
                    else
                        return ($aaaa."-".$mm."-".$gg.'T23:59:59Z');
                    
                }else{
                    // errore nella conversione della data, viene restituito il dafault                    
                    return(itobjectsutils::dataDefaultEzfetch($format));
                }
            }
            // errore nella conversione della data, viene restituito il dafault
            catch (Exception $e) {
                return(itobjectsutils::dataDefaultEzfetch($format));
            }
        }
        
        public static function dataDefaultEzfetch($format){
            
            switch ($format) {
                case "INI":
                    // Se la data errata è la data inizio deve esse restituita la data corrente - 1 Giorno                   
                    return date("Y-m-d", strtotime("- 1 day")).'T00:00:00Z';
                case "FIN":
                    return " * ";
                    break;
                default:
                    return " * ";
            }
        }    
	//----------------------------------------------------------------------------------------------
	//
	//----------------------------------------------------------------------------------------------	
	public static function convertDate($inputDate, $format=null){
		
		if($inputDate==null){
			return null;
		}
		
		if(self::isEmptyString($format)){
		
			//FIXME
			//$format = $this->DEFAULT_DATE_PATTERN;
			$format = 'Y-m-d\TH:i:s';

		}
			
		$dateTime = DateTime::createFromFormat($format, $inputDate);
		
		if ( $dateTime instanceOf DateTime ){

			$timestamp = $dateTime->format('U');

		}else{
		
			throw new Exception( 'Errore nella conversione della data: '.$inputDate );
		}
		
		return $timestamp;
	}
	
			
	//----------------------------------------------------------------------------------------------
	//
	//----------------------------------------------------------------------------------------------	
	public function loginAs($user){
	
		echo 'loginAs ' .$user. "\xA";
	
		$user = eZUser::fetchByName($user);
		eZUser::setCurrentlyLoggedInUser( $user , $user->attribute( 'contentobject_id' ) );	
	}
	
	//----------------------------------------------------------------------------------------------
	//get file remoto
	//----------------------------------------------------------------------------------------------	
	public static function getRemoteFile( $url, $fileName, array $httpAuth = null, $debug = false, $allowProxyUse = true )
    {
        $url = trim( $url );
        $ini = eZINI::instance();
        $importINI = eZINI::instance( 'sqliimport.ini' );
        
        $localPath = $ini->variable( 'FileSettings', 'TemporaryDir' ).'/'.basename( $fileName );
        $timeout = $importINI->variable( 'ImportSettings', 'StreamTimeout' );

        $ch = curl_init( $url );
        $fp = fopen( $localPath, 'w+' );
        curl_setopt( $ch, CURLOPT_HEADER, false );
        curl_setopt( $ch, CURLOPT_FILE, $fp );
        curl_setopt( $ch, CURLOPT_TIMEOUT, (int)$timeout );
        curl_setopt( $ch, CURLOPT_FAILONERROR, true );
		//curl_setopt( $ch, CURLOPT_REFERER, 'http://pat-dev.opencontent.it/' );
		
        if ( $debug )
        {
            curl_setopt( $ch, CURLOPT_VERBOSE, true );
            curl_setopt( $ch, CURLOPT_NOPROGRESS, false );
        }

        // Should we use proxy ?
        $proxy = $ini->variable( 'ProxySettings', 'ProxyServer' );
        if ( $proxy && $allowProxyUse )
        {
            curl_setopt( $ch, CURLOPT_PROXY, $proxy );
            $userName = $ini->variable( 'ProxySettings', 'User' );
            $password = $ini->variable( 'ProxySettings', 'Password' );
            if ( $userName )
            {
                curl_setopt( $ch, CURLOPT_PROXYUSERPWD, "$userName:$password" );
            }
        }
        
        // Should we use HTTP Authentication ?
        if( is_array( $httpAuth ) )
        {
            if( count( $httpAuth ) != 2 )
                throw new SQLIContentException( __METHOD__.' => HTTP Auth : Wrong parameter count in $httpAuth array' );
            
            list( $httpUser, $httpPassword ) = $httpAuth;
            curl_setopt( $ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY );
            curl_setopt( $ch, CURLOPT_USERPWD, $httpUser.':'.$httpPassword );
        }
        
        $result = curl_exec( $ch );
        if ( $result === false )
        {
            $error = curl_error( $ch );
            $errorNum = curl_errno( $ch );
            curl_close( $ch );
            throw new SQLIContentException( "Failed downloading remote file '$url'. $error", $errorNum);
        }
        
        curl_close( $ch );
        fclose( $fp );

            
        return trim($localPath);
    }

    //----------------------------------------------------------------------------------------------
    //
    //----------------------------------------------------------------------------------------------
    public static function print_r_to_string($anyObject){
        $myString = print_r($anyObject, TRUE);
        return $myString;
    }

    public static function getObjectAttrNameAsArray($anyObject){

        $finalArray = array();
        $properties = get_object_vars($anyObject);

        foreach($properties as $key => $value) {
            $finalArray[$key] =$key;
        }

        return $finalArray;
    }


    public static function getObjectAsArray($anyObject){
    
    	$finalArray = array();
    	$properties = get_object_vars($anyObject);
    
    	foreach($properties as $key => $value) {
    		$finalArray[$key] =$value;
    	}
    
    	return $finalArray;
    }
    
    public static function sudo( Closure $callback )
    {
    
    	$loggedUser = eZUser::currentUser();
    	$admin = eZUser::fetchByName( 'admin' );
    
    	if ( $admin instanceof eZUser )
    	{
    		eZUser::setCurrentlyLoggedInUser( $admin, $admin->attribute( 'contentobject_id' ), 1 );
    	}
    	try
    	{
    		$returnValue = $callback();
    	}
    	catch ( Exception $e  )
    	{
    		eZUser::setCurrentlyLoggedInUser( $loggedUser, $loggedUser->attribute( 'contentobject_id' ), 1 );
    		throw $e;
    	}
    
    	eZUser::setCurrentlyLoggedInUser( $loggedUser, $loggedUser->attribute( 'contentobject_id' ), 1 );
    	return $returnValue;
    }

    public static function convertTimestampToDate( $timestamp )
    {
 
        //echo gmdate("Y-m-d\TH:i:s\Z", $timestamp);
        $timestampToDate = gmdate("Y-m-d\TH:i:s\Z", $timestamp);
        return $timestampToDate;
       
    }
    
    public static function convertTimestampToDateIt( $timestamp , $hour )
    {
       
       $timestampToDate = gmdate("d-m-Y", $timestamp); 
       return $timestampToDate;
       
    }
    
     public static function convertTimestampToDateTimeIt( $timestamp , $hour )
    {
       setlocale(LC_TIME,"it_IT");
       date_default_timezone_set("Europe/Rome");
                            
       $format = "Y-m-d";
       $timestampToDate = gmdate($format, $timestamp); 
       $format = "H:i:s";
       $timestampToHour = gmdate($format, $timestamp); 

       return $timestampToDate.'T'.$timestampToHour;
       
    }
}
	
?>