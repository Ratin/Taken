<?php

# Setup and Hooks for the MsComment extension

if( !defined( 'MEDIAWIKI' ) ) {
        echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
        die();
}

## Register extension setup hook and credits:
$wgExtensionCredits['parserhook'][] = array(
        'name'           => 'MsComment',
        'url'  => 'http://www.ratin.de/wiki.html',
        'version'        => '1.2',
        'author' => '[mailto:info@ratin.de info@ratin.de] | Ratin',
        'description' => 'Mit dieser Extension kann einer Datei manuell ein Kommentar hinzugef&uuml;gt werden.',
        'descriptionmsg' => 'selectcategory-desc',
);

## Load the file containing the hook functions:
$dir = dirname(__FILE__).'/';
require_once($dir.'mscomment_body.php');

$wgHooks['ParserFirstCallInit'][] = 'MsCommentSetup';
 
function MsCommentSetup( &$parser ) {    
 global $wgScriptPath,$wgOut,$wgTitle,$wgFrameworkLoaded;
 
  $path =  $wgScriptPath.'/extensions/MsComment';
  
  if (!$wgFrameworkLoaded){
  $wgOut->addScriptFile( $path.'/mootools-core-1.3.js' );
  $wgFrameworkLoaded = true;  
  }
 
  if(isset($wgTitle) AND $wgTitle->getArticleID()!=0){
  $wgOut->addScriptFile( $path.'/mscomment.js' );
  $wgOut->addLink( array(
			'rel' => 'stylesheet',
			'type' => 'text/css',
			'href' => $path.'/mscomment.css'
	 ));
		
  }	
	return true;
}