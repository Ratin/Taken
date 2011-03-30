<?php

# Usage:
#{{#l:Beispieldatei.zip|Beschreibung|left}}
# LocalSettings.php:
#require_once("$IP/extensions/MsLinks/mslinks.php");

$dir = dirname(__FILE__).'/';


if(! defined('MEDIAWIKI')) {
	die("This is a MediaWiki extension and can not be used standalone.\n");
}

$wgExtensionCredits['parserhook'][] = array(
	'name' => 'taken-MsLinks',
	'url'  => 'http://www.ta-ken.de/extensions',
	'description' => 'Erzeugt einen Link mit passendem Icon sowie einen Direkt- und Versionslink.',
	'version' => '2.4',
	'author' => '[mailto:info@ta-ken.de info@ta-ken.de] | ta-ken'
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
function htAddHTMLHeader(&$wgOut)
{
global $wgScriptPath;

$wgOut->addScriptFile( $wgScriptPath.'/extensions/MsLinks/mslinks.js' );
	
return true;

}