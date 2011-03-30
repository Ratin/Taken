<?php

if(! defined('MEDIAWIKI')) {
	die("This is a MediaWiki extension and can not be used standalone.\n");
}

$wgExtensionCredits['parserhook'][] = array(
	'name' => 'taken-MsInsert',
	'url'  => 'http://www.ta-ken.de/extensions',
	'description' => 'Per Dropdown koennen bestimmte Seiten als Vorlage in den Editor geladen werden.',
	'version' => '1.8',
	'author' => '[mailto:info@ta-ken.de info@ta-ken.de] | ta-ken'
);

  #$dir = dirname(__FILE__).'/';
  #require_once($dir.'msinsert_body.php');

#$wgExtensionFunctions[] = "wfMsInsertRender";
#$wgHooks['EditPage::showEditForm:initial'][] = 'wfMsInsertRender';
#$wgHooks['BeforePageDisplay'][]='MSISetup';
$wgHooks['EditPage::showEditForm:initial'][] = 'MSISetup';

#$wgHooks['AlternateEdit'][] = 'MSISetup';

#$wgHooks['EditPageBeforeEditChecks'][] = 'MSISetup';

function MSISetup() {

  global $wgOut,$wgScriptPath,$wgJsMimeType,$wgVorlagen;


  #$wgOut->addScript( '<script type="text/javascript" src="/extensions/MsInsert/msinsert.js"></script>');
  
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
  #$wgOut->addScriptFile( $wgScriptPath.'/extensions/MsInsert/msinsert.js' );
  $wgOut->addScript( "<script type=\"{$wgJsMimeType}\" src=\"$wgScriptPath/extensions/MsInsert/msinsert.js\">".$bla."</script>\n");
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