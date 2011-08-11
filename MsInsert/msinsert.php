<?php

if(! defined('MEDIAWIKI')) {
	die("This is a MediaWiki extension and can not be used standalone.\n");
}

$wgExtensionCredits['parserhook'][] = array(
	'name' => 'MsInsert',
  'url'  => 'http://www.ratin.de/wiki.html',
	'description' => 'Per Dropdown koennen bestimmte Seiten als Vorlage in den Editor geladen werden.',
	'version' => '2.0',
	'author' => '[mailto:info@ratin.de info@ratin.de] | Ratin',
);

$wgHooks['EditPage::showEditForm:initial'][] = 'MSISetup';

function MSISetup() {

  global $wgOut,$wgScriptPath,$wgJsMimeType,$wgVorlagen;

  $path =  $wgScriptPath.'/extensions/MsInsert';
  
  $vorlagen = array();
  foreach($wgVorlagen as $key => $vorlage) {

        $title = Title::newFromText(htmlentities($vorlage));
        $title2 = Title::newFromText($vorlage);
    
        if( $title && $title->exists() ) {
           $vorlagen[] = htmlentities($vorlage);
        } elseif ($title2 && $title2->exists()){
           $vorlagen[] = $vorlage;
        }
  
  }
  

  $vorlagen = 'var vorlagen = new Array("' . implode ( '", "', $vorlagen ) . '");';
  $wgOut->addScript( "<script type=\"{$wgJsMimeType}\">$vorlagen</script>\n" );
  $wgOut->addScript( "<script type=\"{$wgJsMimeType}\" src=\"$path/msinsert.js\"></script>\n");
	$wgOut->addScript( "<script type=\"{$wgJsMimeType}\">hookEvent(\"load\",create_btn_insert);</script>\n" );
  
    return true;
}

$wgAjaxExportList[] = 'wfAjaxVorlage';
function wfAjaxVorlage($title)
{
 $test = $title;
 $title = Title::newFromText($title);
        if( $title && $title->exists() ) {
            $revision = Revision::newFromTitle($title);
            return $revision->getText();
        } else {
            return $test;
        }   
}