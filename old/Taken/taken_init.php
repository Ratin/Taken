<?php

$dir = dirname(__FILE__).'/';

if(! defined('MEDIAWIKI')) {
	#die("This is a MediaWiki extension and can not be used standalone.\n");
}


$wgHooks['ArticlePageDataBefore'][]='MsMootools';
$wgHooks['ParserFirstCallInit'][]='MsMootools';


function MsMootools() {
  global $wgOut,$wgScriptPath,$wgFrameworkLoaded;
  
  if (!$wgFrameworkLoaded){
  $wgOut->addScriptFile( $wgScriptPath.'/extensions/Taken/framework/mootools-core-1.3.js' );
  $wgFrameworkLoaded = true;  
  }

  return true;
}