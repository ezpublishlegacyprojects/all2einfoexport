<?php
/**
 * XML Helper Functions
 *
 * @package widgets
 * @author Patrick Kaiser <pk@okapi.de>
 * @copyright 2009 by Patrick Kaiser
 * @version $Revision$
 * @modifiedby $LastChangedBy$
 * @lastmodified $Date$
 */


class OkapiXML extends XMLWriter {

    protected
    	$rootElement,
    	$options = array( 
    		'ident' => true,
    		'ident_string' => '  ',
    		'dom_version' => '1.0',
    		'dom_charset' => 'UTF-8',
			'debug' => true,
			'index_name' => 'item',
    	),
		$debug = array();
    	
	public function __construct( $rootElement, $options = array() )
	{
        $this->options = array_merge( $this->options, $options );
        
		$this->openMemory();
        $this->setIndent( $options['ident'] );
        $this->setIndentString( $options['ident_string'] );
        $this->startDocument( $options['dom_version'], $options['dom_charset'] );
        
        $this->startElement( $rootElement );
    }

    /**
     * Set an element with a text to a current xml document.
     * @access public
     * @param string $elementName An element's name
     * @param string $elementText An element's text
     * @return null
     */
    public function setElement( $elementName, $elementText )
    {
        $this->startElement( $elementName );
        $this->text( $elementText );
        $this->endElement();
    }
	
	/**
     * Set an CDATA-Tag with a text to a current xml document.
     * @access public
     * @param string $elementName An element's name
     * @param string $elementText An element's text
     * @return null
     */
    public function setCData( $elementName, $elementText )
    {
        $this->startElement( $elementName );
        $this->writeCData( $elementText );
        $this->endElement();
    }
	
	/**
     * Set an Attribute.
     * @access public
     * @param string $attributeName An attribute's name
     * @param string $attributeText An attribute's text
     * @return null
     */
    public function setAttribute( $attributeName, $attributeText )
    {
        $this->startAttribute( );
		$this->writeAttribute( $attributeName, $attributeText );
		$this->endAttribute();
    }
	
	/**
     * Set an Comment.
     * @access public
     * @param string $attributeName An attribute's name
     * @param string $attributeText An attribute's text
     * @return null
     */
    public function setComment( $commentText )
    {
        $this->writeComment( $commentText );
    }

    /**
     * Construct elements and texts from an array.
     * The array should contain an attribute's name in index part
     * and a attribute's text in value part.
     * @access public
     * @param array $data Contains attributes and texts
     * @return null
     */
    public function fromArray( $data )
    {
      if( $this->options['debug'] && empty( $this->debug['data']))
	  	$this->debug['data'] = $data;
		
	  if( is_array( $data ))
      {
        foreach( $data as $index => $element )
        {
          if( is_numeric( $index ))
		  	$index = $this->options['index_name'];
			
		  if( $index == '#attributes#' )
		  {
		  	foreach( $element as $attributeKey => $attributeValue )
			{
				$this->setAttribute( $attributeKey, $attributeValue );
			}
		  }
		  else if( is_array( $element ))
          {
            $this->startElement( $index );
            $this->fromArray( $element );
            $this->endElement();
          }
          else
		  {
			$patternCData 		= '#CDATA#';
			if( preg_match( "/^{$patternCData}/", $index ))
            	$this->setCData( str_replace( $patternCData, '', $index ), $element);
			else
            	$this->setElement( $index, $element);
		  }
        }
      }
    } 
   
    
    
  /**
     * Return the content of a current xml document.
     * @access public
     * @param null
     * @return string Xml document
     */
    public function getDocument()
    {
        $this->endElement();
		
		if( $this->options['debug'] )
		{
			ob_start();
			print_r( $this->debug );
			$output = ob_get_clean();
			$this->setComment( $output );
		}
		
        $this->endDocument();
        return $this->outputMemory();
    }

    /**
     * Output the content of a current xml document.
     * @access public
     * @param array headers, the headers that should be sent
     */
    public function output( $headers = array('Content-type: text/xml'), $echo = true )
    {
        if( $echo ) 
        {
            foreach ($headers as $header)
            {
            	header( $header );
            }
            
            echo $this->getDocument();
        }
        else return $this->getDocument();   
    } 
  
}
?>