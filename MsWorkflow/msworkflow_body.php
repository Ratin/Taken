<?php

if( !defined( 'MEDIAWIKI' ) ) {
  echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
  die();
}

function MsWorkflowRender( $input, $args, $parser ) {

  global $wgTitle;
  $output = "";	
  #$ns = $wgTitle->getNamespace();
  if($input=='ampel'){
    $output= "<div id='show_ampel'><span id='ampel_rot' class='ampel_grau'></span><span id='ampel_gelb' class='ampel_grau'></span><span id='ampel_gruen' class='ampel_grau'></span></div>";
  }
    /*  
    # Get a database object:
    $m_dbObj =& wfGetDB( DB_SLAVE );
     
    $m_sql = "CREATE TABLE msworkflow (
    ID int(11) NOT NULL auto_increment,
    articleID int(11) NOT NULL DEFAULT '0',
    status int(4) NOT NULL DEFAULT '0',
    user varchar(255) NOT NULL,
    timestamp varchar(14) NOT NULL,
    in_use int(11) NOT NULL DEFAULT '0',
    revision int(11) NOT NULL DEFAULT '0',
    PRIMARY KEY (ID) );";
    $m_res = $m_dbObj->query( $m_sql, __METHOD__ );
    
    # Free result:
    $m_dbObj->freeResult( $m_res );
  $output .= "Datenbank erstellt!";
  */
  return $output;
}

$wgAjaxExportList[] = 'getRevision';
function getRevision($artID){

    $revision = "1";
    $m_dbObj =& wfGetDB( DB_SLAVE );
    $m_tblCatLink = $m_dbObj->tableName( 'msworkflow' );
    $m_sql = "SELECT revision FROM  $m_tblCatLink WHERE articleID = '".$artID."' ORDER BY status";
    $m_res = $m_dbObj->query( $m_sql, __METHOD__ ); 
    $m_row = $m_dbObj->fetchRow( $m_res );
    if($m_row){
    $revision = $m_row['revision'];
    }
    //$m_dbObj->freeResult( $m_res );
    
    if($revision == "0" ){$revision = "1";}
    return $revision;
}

$wgAjaxExportList[] = 'databaseRead';
function databaseRead($artID,$title,$revID,$namespace){

    global $wgUserFunktionen;
    $output2 = "";
    $anz2 = 0;
    
    $m_dbObj =& wfGetDB( DB_SLAVE );
    $m_tblCatLink = $m_dbObj->tableName( 'msworkflow' );
    $m_sql = "SELECT status, user, timestamp FROM  $m_tblCatLink WHERE articleID = '".$artID."' ORDER BY status";
    $m_res = $m_dbObj->query( $m_sql, __METHOD__ ); 
    while ( $m_row = $m_dbObj->fetchRow( $m_res ) ) {
        
        
        $funktion = "";
        
        if (isset($wgUserFunktionen[$m_row['user']])){
        $funktion = $wgUserFunktionen[$m_row['user']];
        } else {$funktion = "";}
        
        $datum = date("d. M. Y",$m_row['timestamp']); //Formatiert den Timestamp um in Tag.Monat.Jahr
        $uhrzeit = date("H:i",$m_row['timestamp']); // H:i ist das K�rzel f�r Stunde : Minute
        #$funktion = "QM";
        $output2 .= "!".$m_row['user']."|".$uhrzeit.", ".$datum."|".$funktion;
        $anz2 ++;   
        
    }//while 
    #$m_dbObj->freeResult( $m_res );    
    $output2 = $anz2.$output2;
    
    if ($anz2==1){
    
        #databaseDeleteKat($artID,"Freigegeben");
        #databaseDeleteKat($artID,"Nicht freigegeben");
        #databaseDeleteKat($artID,"Geprüft");
        
        #return databaseSendKat($artID,"Nicht_freigegeben",$title);    
        
        #return $artID."Nicht_freigegeben".$title;
        
        $kat = "";
        $kat = "Nicht freigegeben";
        #$kat_del = "Nicht freigegeben";
        $kat_del = "Freigegeben";
        
        $output2.= "";
        if($namespace != ""){
        $title = $namespace.":".$title;
        }
        #databaseSaveKat($artID,$title,$kat,$kat_del,$revID);
        
        
      
        
    } elseif ($anz2==2){ 
        #$output2 .= " - ".$artID." - ".
        #databaseDeleteKat($artID,"Nicht_freigegeben");
        #databaseDeleteKat($artID,"Nicht freigegeben");
        
        #databaseSendKat($artID,"Geprüft",$title);
        $kat = "Geprüft";
        #$kat = "";
        $kat_del = "Nicht freigegeben";
        #$kat_del = "Freigegeben";
        
        $output2.= "";
        if($namespace != ""){
        $title = $namespace.":".$title;
        }
        #$output2 .= 
        #databaseSaveKat($artID,$title,$kat,$kat_del,$revID);
        
        
    } elseif ($anz2==3){ 
        
        
        #databaseDeleteKat($artID,"Nicht freigegeben");
        #databaseDeleteKat($artID,"Nicht_freigegeben");
        
        #$kat = "Freigegeben";
        $kat = "";
        $kat_del = "Nicht freigegeben";
        
        #$output2.= ".".$artID.".".$title.".".$kat.".".$kat_del.".".$revID;
        $output2.= "";
        if($namespace != ""){
        $title = $namespace.":".$title;
        }
        #databaseSaveKat($artID,$title,$kat,$kat_del,$revID);
        $kat = "";
        #$kat_del = "Nicht_freigegeben";
        $kat_del = "Geprüft";
        $output2 .= "";
        #databaseSaveKat($artID,$title,$kat,$kat_del,$revID);

    }
    
    return $output2;
}

function databaseDeleteKat($artID,$kat){
   $m_dbObj =& wfGetDB( DB_SLAVE );
   $m_tblCatLink = $m_dbObj->tableName( 'categorylinks' );
   $m_sql = "DELETE FROM $m_tblCatLink WHERE cl_from = '".$artID."' AND cl_to='".$kat."' ";
   $m_res = $m_dbObj->query( $m_sql, __METHOD__ ); 
   return true;
}
    
function databaseGetUser($artID){
    $user = array();
    $m_dbObj =& wfGetDB( DB_SLAVE );
    $m_tblCatLink = $m_dbObj->tableName( 'msworkflow' );
    $m_sql = "SELECT user FROM  $m_tblCatLink WHERE articleID = '".$artID."' ORDER BY status";
    $m_res = $m_dbObj->query( $m_sql, __METHOD__ ); 
    while ( $m_row = $m_dbObj->fetchRow( $m_res ) ) {
    $user[] = $m_row['user'];
    }
    //$m_dbObj->freeResult( $m_res );
    return $user;
}

function MsWorkflowSavePage($m_isUpload, $m_pageObj){
    
   global $wgTitle;
   
   $artID = $wgTitle->getArticleID();

   $m_dbObj =& wfGetDB( DB_SLAVE );
   $m_tblCat = $m_dbObj->tableName( 'msworkflow' );
   
    $m_sql = "SELECT * FROM  $m_tblCat WHERE articleID = '".$artID."' ORDER BY 'timestamp' DESC LIMIT 1";
    $m_res = $m_dbObj->query( $m_sql, __METHOD__ );
    $m_row = $m_dbObj->fetchRow( $m_res );
    if($m_row){
    
      if($m_row['in_use']==0){
        #databaseUpdate($artID); #eintrag 
        databaseDelete($artID); #alle löschen
        databaseSend($artID,1,$m_row['revision']); #neuen eintrag speichern
        
      } else { #kommt vom kategorie speichern
        databaseSetUse($artID,0);
      }
    } else { #es gibt noch garkeinen Eintrag
      
      databaseSend($artID,1); #neuen eintrag speichern
    } 
   
   return true;

}

function databaseDelete($artID){
   $m_dbObj =& wfGetDB( DB_SLAVE );
   $m_tblCatLink = $m_dbObj->tableName( 'msworkflow' );
   $m_sql = "DELETE FROM $m_tblCatLink WHERE articleID = '".$artID."'";
   $m_res = $m_dbObj->query( $m_sql, __METHOD__ ); 
   return true;
}

function databaseSend($artID,$status,$rev = '0'){

   global $wgUser;
   
   $m_dbObj =& wfGetDB( DB_SLAVE );
   $m_tblCatLink = $m_dbObj->tableName( 'msworkflow' );
   $m_sql = "INSERT INTO $m_tblCatLink (ID, articleID, status, user, timestamp, in_use, revision ) VALUES ('','".$artID."','".$status."','".$wgUser->getName()."','".wfTimestamp()."','','".$rev."')";
   $m_res = $m_dbObj->query( $m_sql, __METHOD__ ); 
   return true;
}

function databaseSetUse($artID,$use){

   $m_dbObj =& wfGetDB( DB_SLAVE );
   $m_tblCat = $m_dbObj->tableName( 'msworkflow' );
   $m_sql = "UPDATE ".$m_tblCat." SET in_use = '".$use."' WHERE articleID = '".$artID."'";
   $m_res = $m_dbObj->query( $m_sql, __METHOD__ ); 
   return true;
}

function databaseSetRevision($artID){

   $m_dbObj =& wfGetDB( DB_SLAVE );
   $m_tblCat = $m_dbObj->tableName( 'msworkflow' );
   $m_sql = "UPDATE ".$m_tblCat." SET revision = revision +1 WHERE articleID = '".$artID."' ORDER BY 'timestamp' DESC LIMIT 1";
   $m_res = $m_dbObj->query( $m_sql, __METHOD__ ); 
   return true;
}


$wgAjaxExportList[] = 'apiSend2';
function apiSend2($status,$revision,$vars) {

$vars = split(",",$vars);
   
$title = $vars[0];
$artID = $vars[1];
$revID = $vars[2];
$namespace = $vars[3];

if($namespace != ""){
$title = $namespace.":".$title;
}

#sajax_do_call( 'apiSend2', ['geprueft',revision,vars], 
#vars = new Array(wgTitle,wgArticleId,wgCurRevisionId,wgCanonicalNamespace);

    global $wgUser,$wgTitle,$wgUserFunktionen;
        
       if( !$wgUser->isLoggedIn() ) {
    	   return '1';
    	 } 
        
        $user = databaseGetUser($artID);
        
        $statusInt = 0;
        if($status == "erstellt"){
          $statusInt = 1;
          
          $kat = "Nicht_freigegeben";
          $kat_del = "Freigegeben";
          
        }elseif($status == "geprueft"){
          $statusInt = 2;
          if ($wgUser->getName() == $user[0]){$statusInt = 0;} else {
          #databaseDeleteKat($artID,"Nicht_freigegeben");
          $kat = "Geprüft";
          $kat_del = "Nicht_freigegeben";
          }
          
        }elseif($status == "freigegeben"){
          $statusInt = 3;

          if ($wgUser->getName() == $user[0] OR $wgUser->getName() == $user[1]){$statusInt = 0;} else {
          #databaseDeleteKat($artID,"Nicht_freigegeben");
          #databaseDeleteKat($artID,"Geprüft");
         
          $kat = "Freigegeben";
          $kat_del = "Geprüft";
          }
        }
        
        if ($statusInt != 0){
        
        if($wgUserFunktionen[$wgUser->getName()]){
        $funktion = $wgUserFunktionen[$wgUser->getName()];
        } else {$funktion = "";}
        
        $datum = date("d. M. Y",wfTimestamp()); //Formatiert den Timestamp um in Tag.Monat.Jahr
        $uhrzeit = date("H:i",wfTimestamp()); // H:i ist das K�rzel f�r Stunde : Minute
        $bla = $wgUser->getName()."|".$uhrzeit.", ".$datum."|".$funktion;
        
       
        databaseSend($artID,$statusInt,$revision);  #neuen eintrag erstellen

        databaseSaveKat($artID,$title,$kat,$kat_del,$revID);

        if ($statusInt == 3){
        databaseSetRevision($artID);  #revision nur bei freigabe erhöhen
        }
        
        #databaseSendKat($artID,$kat,$title);
        
        return $bla;
        }
   
    return '0';

}

function databaseSaveKat($id,$title,$kat,$kat_del,$rev) {

  databaseSetUse($id,1);
  
  $text = get_text($rev);
          
        $suchmuster = "#\[\[(Kategorie):(".$kat_del.")?(\|(.*?))?\]\]#si"; 
        
        $text = preg_replace($suchmuster, "", $text);
        

        global $wgUser;

        if ($kat!=""){
        $text = $text."\n[[Kategorie:".$kat."]]";
        }
        
       
        $wgEnableWriteAPI = true;    
        $params = new FauxRequest(array (
        	'action' => 'edit',
        	'title' =>  $title,
        	'text' => $text,
        	'token' => $wgUser->editToken(),//$token."%2B%5C",
        ));

        $enableWrite = true; // This is set to false by default, in the ApiMain constructor
        $api = new ApiMain($params,$enableWrite);
        #$api = new ApiMain($params);
        $api->execute();
        $data = & $api->getResultData();
        
  return $text;

}

function get_text($rev){
        
  $m_dbObj =& wfGetDB( DB_SLAVE );
  $m_tblCatLink = $m_dbObj->tableName( 'revision' );
    
  $m_sql = "SELECT * FROM $m_tblCatLink  WHERE rev_id = '".$rev."'"; 
  $m_res = $m_dbObj->query( $m_sql, __METHOD__ );
  $m_row = $m_dbObj->fetchRow( $m_res );
  
  $rev_id = $m_row['rev_text_id'];
  
  $m_tblCatLink = $m_dbObj->tableName( 'text' );
  $m_sql = "SELECT * FROM $m_tblCatLink  WHERE old_id = '".$rev_id."'"; 
  $m_res = $m_dbObj->query( $m_sql, __METHOD__ );
  $m_row = $m_dbObj->fetchRow( $m_res );
   
  $text = $m_row['old_text'];
  #$m_dbObj->freeResult( $m_res );
  
 return $text;       
}


