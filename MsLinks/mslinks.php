<?php
# Usage:
#{{#mslink:dlink|Beispieldatei.zip|Beschreibung|left}}
#
# LocalSettings.php:
#require_once("$IP/extensions/MsLinks/mslinks.php");
$dir = dirname(__FILE__).'/';


if(! defined('MEDIAWIKI')) {
	die("This is a MediaWiki extension and can not be used standalone.\n");
}

$wgExtensionCredits['parserhook'][] = array(
	'name' => 'MsLinks',
	'url'  => 'http://www.ratin.de/wiki.html',
	'description' => 'Erzeugt einem Link mit dem passenden Icon sowie einen Direkt- und Versionslink',
	'version' => '2.4',
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
function htAddHTMLHeader(&$wgOut)
{
global $wgScriptPath;

$wgOut->addScriptFile( $wgScriptPath.'/extensions/MsLinks/mslinks.js' );

return true;

}

