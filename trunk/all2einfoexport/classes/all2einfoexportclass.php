<?php

/**
 * all2einfoexport extension for eZ Publish
 * Written by Norman  Leutner <n.leutner@all2e.com>
 * Copyright (C) 2009. all2e GmbH.  All rights reserved.
 * http://www.all2e.com
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; version 2 of the License.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 */

class all2eInfoExport
{   
    
    public $availableCollections;
    public $selectedCollection;
    public $count;
    public $collections;
    public $collectionsData;
    public $exportDir;
    private $rangeFrom;
    private $rangeTo;
    
    /*
        Constructor:
        Automaticly read out the settings from all2einfoexport.ini
    */
    function __construct()
    {
        $this->getSettings();
    }
    
    /*
        function getSettings():
        Read out the settings and set them in object
    */
    function getSettings()
    {
        $ini = eZINI::instance( "all2einfoexport.ini" );
        list( $this->exportDir ) = $ini->variableMulti( "GeneralSettings", array( "ExportDir" ) );
    }

    /*
        function fetchCollectionsList():
        Fetches the available collections
    */
    function fetchCollectionsList()
    {
		$db = eZDB::instance();
		$objects = $db->arrayQuery( 'SELECT DISTINCT ezinfocollection.contentobject_id,
                                    ezcontentobject.name,
                                    ezcontentobject_tree.main_node_id,
                                    ezcontentclass.serialized_name_list,
                                    ezcontentclass.identifier AS class_identifier
                             FROM   ezinfocollection,
                                    ezcontentobject,
                                    ezcontentobject_tree,
                                    ezcontentclass
                             WHERE  ezinfocollection.contentobject_id=ezcontentobject.id
                                    AND ezcontentobject.contentclass_id=ezcontentclass.id
                                    AND ezcontentclass.version = ' . eZContentClass::VERSION_STATUS_DEFINED . '
                                    AND ezinfocollection.contentobject_id=ezcontentobject_tree.contentobject_id');
		
		$this->availableCollections = $objects;
    }
    /*
        function getCollections():
        Fetches the collected information
    */
    function getCollections()
    {
	  	$this->collections = eZInformationCollection::fetchCollectionsList(
		                 $this->selectedCollection,
		                 false,
		                 false,
		                 array() );  
    }    
   
    /*
        function getCollectionsData():
        Fetches the collected information data attributes
    */    
    function getCollectionsData($selectedCollection, $options)
    {
		$this->selectedCollection = $selectedCollection;
    	$this->getCollections();
    	
    	foreach ($this->collections as $collection)
		{

			if (  $collection->attribute('created') >= $options['rangeFrom']
				&& $collection->attribute('created') <= $options['rangeTo'] )
			{
				$content[$collection->attribute('id')]['ID'] = $collection->attribute('id');
				$content[$collection->attribute('id')]['created'] = date('d.m.Y H:i:s',$collection->attribute('created'));
				
				$attribues = $collection->dataMap();

				foreach($attribues as $key => $attribute)
				{				
					$contentobjectattribute = $attribute->contentObjectAttribute();
					
					switch ($contentobjectattribute->attribute('data_type_string')) {
						case 'ezselection':
							$selected = $attribute->content();
	        				$classContent = $attribute->classContent();
							$returnData = array();
							
					        if ( count( $selected ) )
					        {
					            $optionArray = $classContent['options'];
					            foreach ( $selected as $id )
					            {
					                foreach ( $optionArray as $option )
					                {
					                    $optionID = $option['id'];
					                    if ( $optionID == $id )
					                        $returnData[] = $option['name'];
					                }
					            }
					            $content[$collection->attribute('id')][$key] = eZStringUtils::implodeStr( $returnData, '|' );
					        }							
						break;
						
						default:
							$content[$collection->attribute('id')][$key] = $attribute->content();
					}
				}				
			}
		}
		
		$this->collectionsData = $content;
		return $this->collectionsData;
    }   
    /*
        function getCollectionsCount():
        Fetches the number of collected informations
    */    
    static function getCollectionsCount($id)
    {
  		$count = eZInformationCollection::fetchCollectionCountForObject( $id );
  		return $count;  
    }   

    /*
        function setPointer():
        Writes current timestamp to pointerfile
    */
    public function setPointer( $id , $timestamp)
    {
  		$file = new eZFile();
  		$filename = $id.'_last_export';
		$file->create( $filename, $this->exportDir, $timestamp );
    }
    /*
        function getPointer():
        Reads the timestamp from pointerfile
    */
    public function getPointer( $id )
    {
  		$filename = $id.'_last_export';
  		if (file_exists($this->exportDir.'/'.$filename))
  			return file_get_contents($this->exportDir.'/'.$filename); 
    }      
}

?>
