<?php

$Module = $Params["Module"];
$http = eZHTTPTool::instance();

if( $http->hasPostVariable( 'SelectedCollection' ) )
{
	$object = eZContentObject::fetch(  $http->postVariable( 'SelectedCollection' ) );	
	if ( !$object )
	    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
	
	$objectName = $object->attribute( 'name' );

	/*
	 * Create export data
	 */	
	$export = new all2eInfoExport();
	$rangeFrom = false;
	$rangeTo = false;

	$selectedCollection = $http->postVariable( 'SelectedCollection');
	
	if( $http->postVariable( 'Range') == 'true')
	{
		$rangeFrom = $http->postVariable( 'exportFrom');
		$rangeTo = $http->postVariable( 'exportTo');	
	}
	elseif( $http->postVariable( 'lastExport') == 'true')
	{
		$rangeFrom = $export->getPointer( $selectedCollection );
		$rangeTo = time();
		$export->setPointer( $selectedCollection , $rangeTo);	
	}	
	elseif( $http->postVariable( 'fullExport') == 'true') 
	{
		$rangeFrom = 0;
		$rangeTo = time();
	}
					
	$options = array ( 'objectname' => $objectName, 
					   'rangeFrom' => $rangeFrom, 
					   'rangeTo' => $rangeTo );
	
	
	$export->getCollectionsData($selectedCollection, $options);
	
	$objectName = strtolower(eZURLALiasML::convertToAlias( $objectName ));
	
	/*
	 * Generate XML from collections & output file
	 */
	$xml = new OkapiXML( $objectName, array( 'debug' => false ) );
	$xml->fromArray( $export->collectionsData );
			
	$filename = $objectName.'.xml';
	$headers = array ('Content-type: application/octet-stream',
					  'Content-Disposition: attachment; filename='.$filename);
	
	$xml->output($headers);
	
	/*
	 * Write XML
	 */
	//$dir = $export->exportDir;	
  	//$file = new eZFile();
  	//$file->create( $filename, $dir, $data );
  	
	/* Clean Exit */
	eZExecution::cleanExit();	
}

?>
