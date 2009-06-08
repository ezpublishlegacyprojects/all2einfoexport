<?php

include_once("extension/all2einfoexport/classes/all2einfoexportclass.php");
include_once('kernel/common/template.php');

$Module = $Params["Module"];

$export = new all2eInfoExport();

$tpl = templateInit();

$export->fetchCollectionsList();
$tpl->setVariable('availableCollections',$export->availableCollections);
$template = "select_export.tpl";    

//$tpl->setVariable('varnish',$erg);

$Result = array();
$Result['pagelayout'] = true;
$Result['content'] = $tpl->fetch( "design:all2einfoexport/".$template );
$Result['path'] = array( array( 'url' => '/all2einfoexport/select_export',
                                'text' => 'Collected Information Export'
                         )
                  );
$Result['left_menu'] = 'design:all2einfoexport/left_menu.tpl';

?>
