<?php
$wgAjaxExportList[] = 'wfMsUploadRender';
function wfMsUploadRender() {
  
  #global $wgOut,$wgUser,$wgUserName,$wgScriptPath;
  global $output,$wgUser;
  global $wgMSU_AutoKat, $wgMSU_AutoIndex;


  if( !$wgUser->isAllowed( 'upload' ) ) {
    	if( !$wgUser->isLoggedIn() ) {
    	   $output .= "<a id='ImageUploadLoginMsg'>einloggen</a>";
    	   return 0;
    	} else {
    		 $output .= "keine Berechtigung"; 
    		 return 1;
    	}
    } else if( wfReadOnly() ) { 
    		 $output .= "Nur lesen";
    		 return 2;
    } else { 
    
      $output .= "<form action='' method='post' id='upload-form'>";
      $output .= "<ul id='upload_list'></ul>";
      $output .= "<hr noshade>";
      $output .= "</form><a href='#' id='upload_all'></a>";
      
    }  
   
  #return $output."|".$wgUser->getName();
  return $output."|".$wgUser->getName()."|".$wgMSU_AutoKat."|".$wgMSU_AutoIndex;
}

$wgAjaxExportList[] = 'wfMsUploadCheck';
function wfMsUploadCheck($extension){
  global $wgFileExtensions,$wgMSU_PictureExt;
  
  if (!in_array($extension, $wgFileExtensions)){
      return implode(',', $wgFileExtensions);  
  }

  if (in_array($extension, $wgMSU_PictureExt)){
  return "pic";
  } 	

  return '1';
}

$wgAjaxExportList[] = 'wfMsUploadDoAjax';
function wfMsUploadDoAjax($file) {
     
    global $wgUser;

    return  substr($wgUser->editToken(),2);
}

$wgAjaxExportList[] = 'wfMsUploadSaveKat';
function wfMsUploadSaveKat($name,$kat) {

        global $wgContLang,$wgUser;
        
        $mediaString = strtolower( $wgContLang->getNsText( NS_FILE ) );
        
        $title = $mediaString.':'.$name;
        $text = "\n[[".$kat."]]";

        $wgEnableWriteAPI = true;    
        $params = new FauxRequest(array (
        	'action' => 'edit',
        	'section'=>  'new',
        	'title' =>  $title,
        	'text' => $text,
        	'token' => $wgUser->editToken(),//$token."%2B%5C",
        ));

        $enableWrite = true; // This is set to false by default, in the ApiMain constructor
        $api = new ApiMain($params,$enableWrite);
        #$api = new ApiMain($params);
        $api->execute();
        $data = & $api->getResultData();
        
  return $mediaString;
}