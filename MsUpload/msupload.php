<?php

$dir = dirname(__FILE__).'/';

if(! defined('MEDIAWIKI')) {
	#die("This is a MediaWiki extension and can not be used standalone.\n");
}

$wgExtensionCredits['parserhook'][] = array(
	'name' => 'taken-MsUpload',
	'url'  => 'http://www.ta-ken.de/extensions',
	'description' => 'Diese Extension macht Uploads/Multiuploads direkt im Editor möglich',
	'version' => '7.6',
	'author' => '[mailto:info@ta-ken.de info@ta-ken.de] | ta-ken'
);

$wgAvailableRights[] = 'msupload';

$wgHooks['EditPage::showEditForm:initial'][] = 'MSLSetup';
#$wgHooks['EditPage::showEditForm:initial'][] = 'wfMsUploadRender';
require_once($dir.'msupload_body.php');
  

function MSLSetup() {

  global $wgOut, $wgScriptPath,$wgJsMimeType,$wgHooks;

	$dir = dirname(__FILE__).'/';
  if(isset($wgTitle) AND $wgTitle->getArticleID()==0){
  $wgOut->addScriptFile( $wgScriptPath.'/extensions/MsUpload/mootools/mootools-core-1.3.js' );
  }
  $wgOut->addScriptFile( $wgScriptPath.'/extensions/MsUpload/source/Fx.ProgressBar.js' );
  $wgOut->addScriptFile( $wgScriptPath.'/extensions/MsUpload/source/Swiff.Uploader.js' );
  $wgOut->addScriptFile( $wgScriptPath.'/extensions/MsUpload/source/FancyUpload3.Attach.js' );

		$wgOut->addLink( array(
			'rel' => 'stylesheet',
			'type' => 'text/css',
			'href' => $wgScriptPath.'/extensions/MsUpload/upload.css'
		));

  $wgOut->addScriptFile( $wgScriptPath.'/extensions/MsInsert/msinsert.js' );
	$wgOut->addScriptFile( $wgScriptPath.'/extensions/MsUpload/msupload.js' );
	
  $wgOut->addScript( "<script type=\"{$wgJsMimeType}\">hookEvent(\"load\", function(){create_button('Upload2','extensions/MsUpload/images/button_upload.gif',loadMsUpload);});</script>\n" );


    return true;
}