<?php

function wfMsLinksRender(&$parser, $typ = '', $url = '', $beschreibung = '', $align = '') {
	
  global $wgOut,$wgScriptPath,$wgFileTypes;
	
	  if (empty($typ)) {
		return 'kein typ angegeben';
	  } 
	
  $base = "Media";
  $version = "";
  
  if($typ != "dlink") {
  
    if($typ != "vlink") {  #wenn weder d noch v link, dann eins weiterschieben
    $align = $beschreibung;
    $beschreibung = $url;
    $url = $typ;
    }
    
    $img = Image::newFromName($url);
		  if ($img && $img->exists()) { #datei existiert 
        $base = ":Image";
      }
    
  
  } //if

    $img = Image::newFromName($url);
		  
    if ($img && $img->exists()) { #datei existiert 
        $base = ":Image";
    }
     
    $extension = strtolower(substr(strrchr ($url, "."), 1));
  
    if($beschreibung == "") {
    #$beschreibung = $file_info['filename'];
    $beschreibung = substr($url,0,(strlen($url)-(strlen($extension)+1))); // damit umlaute auch angezeigt werden
    }

		$html = "[[$base:$url|$beschreibung]]";
    
    
    $bild = "<img src='$wgScriptPath/extensions/MsLinks/images/".$wgFileTypes['no']."'>";
    
    
    if (isset($wgFileTypes)){
    foreach($wgFileTypes as $key => $value) 
    { 
      if($key==$extension){
        $bild = "<img title='$extension' src='$wgScriptPath/extensions/MsLinks/images/$value'>"; 
      }
   
    }
    } //if
    
    $bild = $parser->insertStripItem($bild, $parser->mStripState);
    
    
    if($typ != "vlink" && $typ != "dlink") {
      $base = "Media";
     }
     
    $bild = "[[$base:$url|".$bild."]]";


	
	if ($align == "right") { 
    $html = $html." ".$bild." ".$version;
  } else { #standardausrichtung
    $html = $bild." ".$html." ".$version;
  }

  
  return $html;
 

}