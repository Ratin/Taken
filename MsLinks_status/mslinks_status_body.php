<?php

function wfMsLinksRender(&$parser, $typ = '', $url = '', $beschreibung = '', $align = '') {
	
  global $wgOut,$wgScriptPath,$wgFileTypes;
	
	 
	
	  if (empty($typ)) {
		return 'kein typ angegeben';
	  } 
	
  $base = "Media";
  $version = "";
  $status = "";
  
  if($typ == "status") {
  
     $status="<span class='status_file'>".md5($url)."</span>";
  
  } elseif($typ != "dlink") {
  
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
     
    #$file_info = pathinfo($url);
    #$file_info['extension']
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

  
  return $status.$html;
 

}

$wgAjaxExportList[] = 'wfMsLinksDB';
function wfMsLinksDB() {

    # Get a database object:
    $m_dbObj =& wfGetDB( DB_SLAVE );
    $m_tblCatLink = $m_dbObj->tableName( 'msstatus' );
     
    $m_sql = "CREATE TABLE $m_tblCatLink (
    ID int(11) NOT NULL auto_increment,
    datei varchar(60) NOT NULL DEFAULT '0',
    status int(4) NOT NULL DEFAULT '0',
    user varchar(255) NOT NULL,
    timestamp varchar(14) NOT NULL,
    PRIMARY KEY (ID) );";
    $m_res = $m_dbObj->query( $m_sql, __METHOD__ );
    
    # Free result:
    $m_dbObj->freeResult( $m_res );
  $output .= "Datenbank erstellt!";

  return $output;
}

$wgAjaxExportList[] = 'databaseSave';
function databaseSave($status,$datei){

   global $wgUser,$wgStatusList;
   
   $m_dbObj =& wfGetDB( DB_SLAVE );
   $m_tblCatLink = $m_dbObj->tableName( 'msstatus' );
   
      if(databaseRead($datei)==$datei){ #nicht vorhanden

       $m_sql = "INSERT INTO $m_tblCatLink (ID, datei, status, user, timestamp) VALUES ('','".$datei."','".$status."','".$wgUser->getName()."','".wfTimestamp()."')";
       $m_res = $m_dbObj->query( $m_sql, __METHOD__ ); 

      } else { #schon vorhanden
      
       $m_sql = "UPDATE $m_tblCatLink SET status = '".$status."', user = '".$wgUser->getName()."', timestamp ='".wfTimestamp()."' WHERE datei = '".$datei."'";
       $m_res = $m_dbObj->query( $m_sql, __METHOD__ ); 


      }
      
    return $wgStatusList[$status-1];
}


$wgAjaxExportList[] = 'databaseRead';
function databaseRead($datei){

    
    $m_dbObj =& wfGetDB( DB_SLAVE );
    $m_tblCatLink = $m_dbObj->tableName( 'msstatus' );
    $m_sql = "SELECT datei, status, user, timestamp FROM $m_tblCatLink WHERE datei = '".$datei."'";
    $m_res = $m_dbObj->query( $m_sql, __METHOD__ ); 
    $m_row = $m_dbObj->fetchRow( $m_res ); 
        
    if ($m_row) {
        
        
        $datum = date("d. M. Y",$m_row['timestamp']); //Formatiert den Timestamp um in Tag.Monat.Jahr
        $uhrzeit = date("H:i",$m_row['timestamp']); // H:i ist das K?rzel f?r Stunde : Minute
        
        $output = $datei."|".$m_row['status']."|".$m_row['user']."|".$uhrzeit.", ".$datum;
    
    
    } else { return $datei; }//if 

    $m_dbObj->freeResult( $m_res );   
     
    return $output;
}
