<?php

/**
 * Functions file for extension MsComment.
 *  
 * @author Martin Schwindl  <martin.schwindl@ratin.de> 
 * @copyright © 2011 by Martin Schwindl
 *
 * @licence GNU General Public Licence 2.0 or later
 */

if( !defined( 'MEDIAWIKI' ) ) {
  echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
  die();
}
  
 
$wgAjaxExportList[] = 'GetComment';
function GetComment($dateiname) {

    $m_dbObj =& wfGetDB( DB_SLAVE );

    $m_tblCatLink = $m_dbObj->tableName( 'image' );
    
    $dateiname=strtr($dateiname, ' ', '_'); //database (leerzeichen durch unterstriche ersetzen)
    
    $m_sql = "SELECT img_description FROM $m_tblCatLink  WHERE img_name = '".$dateiname."'"; 
    $m_res = $m_dbObj->query( $m_sql, __METHOD__ );
    $m_row = $m_dbObj->fetchRow( $m_res );
    $comment = $m_row['img_description'];
    $m_dbObj->freeResult( $m_res );

    return $comment;

}

$wgAjaxExportList[] = 'SaveComment';
function SaveComment($dateiname,$comment) {

    $dateiname=strtr($dateiname, ' ', '_'); //database (leerzeichen durch unterstriche ersetzen)
    $m_dbObj =& wfGetDB( DB_SLAVE );
    $m_tblCatLink = $m_dbObj->tableName( 'image' );
    $m_sql = "UPDATE $m_tblCatLink SET img_description = '".$comment."' WHERE img_name = '".$dateiname."'"; 
    $m_res = $m_dbObj->query( $m_sql, __METHOD__ );
    #$m_dbObj->freeResult( $m_res );

    return $comment;

}