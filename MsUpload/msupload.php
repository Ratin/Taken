<?php

$dir = dirname(__FILE__).'/';

if(! defined('MEDIAWIKI')) {
	#die("This is a MediaWiki extension and can not be used standalone.\n");
}

$wgExtensionCredits['parserhook'][] = array(
	'name' => 'MsUpload',
	'url'  => 'http://www.ratin.de/wiki.html',
	'description' => 'Diese Extension macht Uploads/Multiuploads direkt im Editor möglich',
	'version' => '7.9',
	'author' => '[mailto:info@ratin.de info@ratin.de] | Ratin',
);

$wgAvailableRights[] = 'msupload';

$wgHooks['EditPage::showEditForm:initial'][] = 'MSLSetup';
#$wgHooks['EditPage::showEditForm:initial'][] = 'wfMsUploadRender';
require_once($dir.'msupload_body.php');
  

function MSLSetup() {

  global $wgOut, $wgScriptPath,$wgJsMimeType,$wgHooks,$wgFrameworkLoaded,$wgTitle;


	$path =  $wgScriptPath.'/extensions/MsUpload';
  
  if (!$wgFrameworkLoaded){
  $wgOut->addScriptFile( $path.'/mootools-core-1.3.js' );
  $wgFrameworkLoaded = true;  
  }
	
  if(isset($wgTitle) AND $wgTitle->getArticleID()!=0){
    
    $wgOut->addScriptFile( $path.'/source/Fx.ProgressBar.js' );
    $wgOut->addScriptFile( $path.'/source/Swiff.Uploader.js' );
    $wgOut->addScriptFile( $path.'/source/FancyUpload3.Attach.js' );
  
  		$wgOut->addLink( array(
  			'rel' => 'stylesheet',
  			'type' => 'text/css',
  			'href' => $path.'/upload.css'
  		));
  
    $wgOut->addScriptFile( $wgScriptPath.'/extensions/MsInsert/msinsert.js' );
  	$wgOut->addScriptFile( $path.'/msupload.js' );
  	
    $wgOut->addScript( "<script type=\"{$wgJsMimeType}\">hookEvent(\"load\", function(){create_button('Upload2','extensions/MsUpload/images/button_upload.gif',loadMsUpload);});</script>\n" );
  }

  return true;
}