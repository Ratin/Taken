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
    PRIMARY KEY (ID) );";
    $m_res = $m_dbObj->query( $m_sql, __METHOD__ );
    
    # Free result:
    $m_dbObj->freeResult( $m_res );
  $output .= "Datenbank erstellt!";
  */
  return $output;
}

$wgAjaxExportList[] = 'getRevision';
function getRevision($artID,$title){
    $m_dbObj =& wfGetDB( DB_SLAVE );
    $m_tblCatLink = $m_dbObj->tableName( 'revision' );
    $m_sql = "SELECT COUNT(*) as revision FROM  $m_tblCatLink WHERE rev_page = '".$artID."'";
    $m_res = $m_dbObj->query( $m_sql, __METHOD__ ); 
    $m_row = $m_dbObj->fetchRow( $m_res );
    $revision = $m_row['revision'];
    $m_dbObj->freeResult( $m_res );
    return $revision;
}

$wgAjaxExportList[] = 'databaseRead';
function databaseRead($artID,$title){

    global $wgUserFunktionen;
    $output2 = "";
    $anz2 = 0;
    
    $m_dbObj =& wfGetDB( DB_SLAVE );
    $m_tblCatLink = $m_dbObj->tableName( 'msworkflow' );
    $m_sql = "SELECT status, user, timestamp FROM  $m_tblCatLink WHERE articleID = '".$artID."' ORDER BY status";
    $m_res = $m_dbObj->query( $m_sql, __METHOD__ ); 
    while ( $m_row = $m_dbObj->fetchRow( $m_res ) ) {
        
        if($wgUserFunktionen[$m_row['user']]){
        $funktion = $wgUserFunktionen[$m_row['user']];
        } else {$funktion = "";}
        
        $datum = date("d. M. Y",$m_row['timestamp']); //Formatiert den Timestamp um in Tag.Monat.Jahr
        $uhrzeit = date("H:i",$m_row['timestamp']); // H:i ist das K�rzel f�r Stunde : Minute
        #$funktion = "QM";
        $output2 .= "||".$m_row['user']."|".$funktion."|".$uhrzeit.", ".$datum;
        $anz2 ++;   
        
    }//while 
    $m_dbObj->freeResult( $m_res );    
    $output2 = $anz2.$output2;
    

    if ($anz2==1){
    
        databaseDeleteKat($artID,"Freigegeben");
        databaseDeleteKat($artID,"Nicht freigegeben");
        databaseDeleteKat($artID,"Geprüft");
        databaseSendKat($artID,"Nicht freigegeben",$title);
    }
    
    return $output2;
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
    $m_dbObj->freeResult( $m_res );
    return $user;
}

function MsWorkflowSavePage($m_isUpload, $m_pageObj){
    
    global $wgUser,$wgTitle,$wgContLang;
       databaseDelete($wgTitle->getArticleID());
       databaseSend($wgTitle->getArticleID(),1);
    return true;
}


function databaseDelete($artID){
   $m_dbObj =& wfGetDB( DB_SLAVE );
   $m_tblCatLink = $m_dbObj->tableName( 'msworkflow' );
   $m_sql = "DELETE FROM $m_tblCatLink WHERE articleID = '".$artID."'";
   $m_res = $m_dbObj->query( $m_sql, __METHOD__ ); 
   return true;
}

function databaseSend($artID,$status){

   global $wgUser;
   $m_dbObj =& wfGetDB( DB_SLAVE );
   $m_tblCatLink = $m_dbObj->tableName( 'msworkflow' );
   $m_sql = "INSERT INTO $m_tblCatLink (ID, articleID, status, user, timestamp) VALUES ('','".$artID."','".$status."','".$wgUser->getName()."','".wfTimestamp()."')";
   $m_res = $m_dbObj->query( $m_sql, __METHOD__ ); 
   return true;
}

$wgAjaxExportList[] = 'databaseDeleteKat';
function databaseDeleteKat($artID,$kat){
   $m_dbObj =& wfGetDB( DB_SLAVE );
   $m_tblCatLink = $m_dbObj->tableName( 'categorylinks' );
   $m_sql = "DELETE FROM $m_tblCatLink WHERE cl_from = '".$artID."' AND cl_to='".$kat."' ";
   $m_res = $m_dbObj->query( $m_sql, __METHOD__ ); 
   return true;
}

function databaseSendKat($artID,$status,$artName){

   $datum = date("Y-m-d",wfTimestamp()); //Formatiert den Timestamp um in Tag.Monat.Jahr
   $uhrzeit = date("H:i:s",wfTimestamp()); // H:i ist das K�rzel f�r Stunde : Minute
   $m_dbObj =& wfGetDB( DB_SLAVE );
   $m_tblCatLink = $m_dbObj->tableName( 'categorylinks' );
   $m_sql = "INSERT INTO $m_tblCatLink (cl_from, cl_to, cl_sortkey, cl_timestamp) VALUES ('".$artID."','".$status."','".$artName."','".$datum." ".$uhrzeit."')";
   $m_res = $m_dbObj->query( $m_sql, __METHOD__ ); 
   return true;
}


$wgAjaxExportList[] = 'apiSend';
function apiSend($title,$status,$artID) {

global $output,$wgScriptPath;
global $wgUser,$wgTitle,$wgUserFunktionen;
        
       if( !$wgUser->isLoggedIn() ) {
    	   return '1';
    	 } 
        
        $user = databaseGetUser($artID);
        
        $statusInt = 0;
        if($status == "erstellt"){
          $kat = "Nicht freigegeben";
          $statusInt = 1;
          
        }elseif($status == "geprueft"){
          $statusInt = 2;
          if ($wgUser->getName() == $user[0]){$statusInt = 0;} else {
          databaseDeleteKat($artID,"Nicht freigegeben");
          $kat = "Geprüft";
          }
          
        }elseif($status == "freigegeben"){
          $statusInt = 3;

          if ($wgUser->getName() == $user[0] OR $wgUser->getName() == $user[1]){$statusInt = 0;} else {
          databaseDeleteKat($artID,"Nicht freigegeben");
          databaseDeleteKat($artID,"Geprüft");
          $kat = "Freigegeben";
          }
        }
        
        if ($statusInt != 0){
        
        if($wgUserFunktionen[$wgUser->getName()]){
        $funktion = $wgUserFunktionen[$wgUser->getName()];
        } else {$funktion = "";}
        
        $datum = date("d. M. Y",wfTimestamp()); //Formatiert den Timestamp um in Tag.Monat.Jahr
        $uhrzeit = date("H:i",wfTimestamp()); // H:i ist das K�rzel f�r Stunde : Minute
        $bla = $wgUser->getName()."|".$funktion."|".$uhrzeit.", ".$datum;
        
        databaseSend($artID,$statusInt);
        databaseSendKat($artID,$kat,$title);
        
        return $bla;
        }
   
    return '0';

}

