<?phpif( !defined( 'MEDIAWIKI' ) ) {	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );	die( 1 );}$wgExtensionCredits['parserhook'][] = array(	'name' => 'taken-MsWorkflow',	'url'  => 'http://www.ta-ken.de/extensions',	'description' => '',	'version' => '1.5',	'author' => '[mailto:info@ta-ken.de info@ta-ken.de] | ta-ken');$dir = dirname(__FILE__).'/';require_once($dir.'msworkflow_body.php');#$wgAutoloadClasses['workflow'] = $dir . 'msworkflow_body.php';$wgHooks['ParserFirstCallInit'][] = 'MsWorkflowSetup';$wgHooks['LanguageGetMagic'][]    = 'MsWorkflowMagic';# Hook when saving page:$wgHooks['EditPage::attemptSave'][] = array( 'MsWorkflowSavePage', false ); function MsWorkflowSetup( &$parser ) {     global $wgScriptPath,$wgOut,$wgTitle;   if(isset($wgTitle) AND $wgTitle->getArticleID()!=0){  $wgOut->addScriptFile( $wgScriptPath.'/extensions/MsWorkflow/msworkflow.js' );  $wgOut->addLink( array(			'rel' => 'stylesheet',			'type' => 'text/css',			'href' => $wgScriptPath.'/extensions/MsWorkflow/msworkflow.css'		));			$parser->setHook( 'workflow', 'MsWorkflowRender' );   }		return true;}function MsWorkflowMagic( &$magicWords, $langCode ) {  $magicWords['workflow'] = array( 0, 'workflow' );  return true;} 