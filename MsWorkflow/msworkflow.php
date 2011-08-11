<?php

if( !defined( 'MEDIAWIKI' ) ) {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	die( 1 );
}

$wgExtensionCredits['parserhook'][] = array(
	'name' => 'MsWorkflow',
	'url'  => 'http://www.ratin.de/wiki.html',
	'description' => 'Ampel für Freigabeprozess QM',
	'version' => '1.6',
	'author' => '[mailto:info@ratin.de info@ratin.de] | Ratin',
);

$dir = dirname(__FILE__).'/';
require_once($dir.'msworkflow_body.php');

$wgHooks['ParserFirstCallInit'][] = 'MsWorkflowSetup';
$wgHooks['LanguageGetMagic'][]    = 'MsWorkflowMagic';

# Hook when saving page:
$wgHooks['EditPage::attemptSave'][] = array( 'MsWorkflowSavePage', false );
 
function MsWorkflowSetup( $parser ) {    
 global $wgScriptPath,$wgOut,$wgTitle,$wgFrameworkLoaded;
 
  $path =  $wgScriptPath.'/extensions/MsWorkflow';
  
  if (!$wgFrameworkLoaded){
  $wgOut->addScriptFile( $path.'/mootools-core-1.3.js' );
  $wgFrameworkLoaded = true;  
  }
 
  if(isset($wgTitle) AND $wgTitle->getArticleID()!=0){
  $wgOut->addScriptFile( $path.'/msworkflow.js' );
  $wgOut->addLink( array(
			'rel' => 'stylesheet',
			'type' => 'text/css',
			'href' => $path.'/msworkflow.css'
		));
		
	$parser->setHook( 'workflow', 'MsWorkflowRender' ); 
  }	
	return true;
}

function MsWorkflowMagic( &$magicWords, $langCode ) {
  $magicWords['workflow'] = array( 0, 'workflow' );
  return true;
}

 
