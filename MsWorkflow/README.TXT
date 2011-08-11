//Start----------------MsWorkflow
$wgUserFunktionen = array('Name'=>'Funktion',
                          'Name2'=>'Funktion'
                          );
require_once("$IP/extensions/MsWorkflow/msworkflow.php");
//End------------------MsWorkflow


## für neue Namespaces 
  
define("NS_AA", 102);
define("NS_AA_TALK", 103);
$wgExtraNamespaces[102] = "AA";
$wgExtraNamespaces[103] = "AA_TALK";
$wgNamespacesWithSubpages[102] = true;
$wgNamespacesWithSubpages[103] = true;

#Set default searching
$wgNamespacesToBeSearchedDefault = array(
	NS_MAIN =>           true,
	NS_TALK =>           true,
	NS_USER =>           true,
	NS_USER_TALK =>      true,
	NS_PROJECT =>        true,
	NS_PROJECT_TALK =>   true,
	NS_IMAGE =>          true,
	NS_IMAGE_TALK =>     true,
	NS_MEDIAWIKI =>      true,
	NS_MEDIAWIKI_TALK => true,
	NS_TEMPLATE =>       true,
	NS_TEMPLATE_TALK =>  true,
	NS_HELP =>           true,
	NS_HELP_TALK =>      true,
	NS_CATEGORY =>       true,
	NS_CATEGORY_TALK =>  true,
	NS_AA =>             true,
	NS_AA_TALK=>         true,
);
