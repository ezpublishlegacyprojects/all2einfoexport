<?php

class all2eInfoExportOperators
{

    /**
     Return an array with the template operator name.
    */
    public function operatorList()
    {
        return array( 'lastexport','countcollections');
    }

    /**
     @return true to tell the template engine that the parameter list exists per operator type,
             this is needed for operator classes that have multiple operators.
    */
    public function namedParameterPerOperator()
    {
        return true;
    }

    /**
     @See eZTemplateOperator::namedParameterList
    */
    public function namedParameterList()
    {

        return array(
                      'lastexport' => array(),
        			  'countcollections' => array(),
                    );
    }

    /**
    */
    public function modify( &$tpl, &$operatorName, &$operatorParameters, &$rootNamespace, &$currentNamespace, &$operatorValue, &$namedParameters )
    {

        switch ( $operatorName )
        {
            case 'lastexport':
            {
            	$export = new all2eInfoExport();
            	$operatorValue = $export->getPointer($operatorValue);
            	break;
            }
            case 'countcollections':
            {
            	$export = new all2eInfoExport();
            	$operatorValue = $export->getCollectionsCount($operatorValue);
            	break;
            }            
        }
    }

}

?>
