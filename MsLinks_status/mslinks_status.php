<?php
# Usage:
#{{#mslink:dlink|Beispieldatei.zip|Beschreibung|left}}
#{{#l:status|Dateiname.ext}}
# LocalSettings.php:
#require_once("$IP/extensions/MsLinks_status/mslinks_status.php");
$dir = dirname(__FILE__).'/';


if(! defined('MEDIAWIKI')) {
	die("This is a MediaWiki extension and can not be used standalone.\n");
}


$wgExtensionCredits['parserhook'][] = array(
	'name' => 'MsLinks_status',
	'url'  => 'http://www.ratin.de/wiki.html',
	'description' => 'Erzeugt einem Link mit dem passenden Icon sowie eine eine Statusampel zum klicken.',
	'version' => '1.0',
	'author' => '[mailto:info@ratin.de info@ratin.de] | Ratin'
);
 

require_once('mslinks_body.php');

$wgExtensionFunctions[] = "wfMsLinksSetup";
	$wgHooks['BeforePageDisplay'][]='htAddHTMLHeader';
	$wgHooks['LanguageGetMagic'][] = 'wfMsLinksMagic';


function wfMsLinksSetup() {
	global $wgParser;
	
	$wgParser->setFunctionHook('mslink', 'wfMsLinksRender');
}
 
 
function wfMsLinksMagic( &$magicWords, $langCode ) {

	$magicWords['mslink'] = array(0, 'mslink','l');
	return true;
}

#für javascript
function htAddHTMLHeader(&$wgOut){

  global $wgScriptPath,$wgFrameworkLoaded;
  
  $path =  $wgScriptPath.'/extensions/MsLinks_status';
  
  if (!$wgFrameworkLoaded){
  $wgOut->addScriptFile( $path.'/mootools-core-1.3.js' );
  $wgFrameworkLoaded = true;  
  }
  
  $wgOut->addScriptFile( $path.'/mslinks_status.js' );

  return true;
}