<?php

$Module = array( 'name' => 'all2einfoexport' );

$ViewList = array();
$ViewList['select_export'] = array('script'                   => 'select_export.php',
                                     'functions'                => array( 'select_export' ),
                                     'default_navigation_part'  => 'all2enavigationpart',
                                    );

$ViewList['export'] = array('script'                   => 'export.php',
							 'functions'                => array( 'export' ),
							 'default_navigation_part'  => 'all2enavigationpart',
);
                                    
$FunctionList = array();
$FunctionList['select_export'] = array();
$FunctionList['export'] = array();

?>
