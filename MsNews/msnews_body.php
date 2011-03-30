<?php

if( !defined( 'MEDIAWIKI' ) ) {
  echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
  die();
}

function MsNewsRender( $input, $args, $parser ) {

  $output= "<div id='show_msnews'>".$args."</div>";
  return $output;
}

$wgAjaxExportList[] = 'getLatestNews';
function getLatestNews($name_kat){

    global  $wgOut,$parser;
    
    $m_dbObj =& wfGetDB( DB_SLAVE );
    
    $m_tblCatLink = $m_dbObj->tableName( 'categorylinks' );
    $m_tblPage = $m_dbObj->tableName( 'page' );
    
    #$m_sql = "SELECT * FROM  $m_tblCatLink l  WHERE cl_to = '".$name_kat."' ORDER BY cl_timestamp DESC LIMIT 1";
    
    $m_sql = "SELECT cl_to,cl_from,page_id,cl_timestamp,page_title FROM  $m_tblCatLink l, $m_tblPage p WHERE cl_to = '".$name_kat."' AND l.cl_from = p.page_id GROUP BY cl_timestamp DESC LIMIT 1";
    $m_res = $m_dbObj->query( $m_sql, __METHOD__ ); 
    $m_row = $m_dbObj->fetchRow( $m_res );
    
    $latestNewsPic = getPicture($m_row['cl_from']);
    
    $latestNews =  "[[Image:".$latestNewsPic."|link=".$m_row['page_title']."|250px]]";
    
    $m_dbObj->freeResult( $m_res );
    
    $latestNews = $wgOut->parse( $latestNews);

    return $latestNews;
    
}

function getPicture($page_id){

    $m_dbObj =& wfGetDB( DB_SLAVE );
    
    $m_tblCatLink = $m_dbObj->tableName( 'imagelinks' );
    
    $m_sql = "SELECT il_from,il_to FROM  $m_tblCatLink WHERE il_from = '".$page_id."' LIMIT 1";
    $m_res = $m_dbObj->query( $m_sql, __METHOD__ ); 
    $m_row = $m_dbObj->fetchRow( $m_res );
    $pic = $m_row['il_to'];
    $m_dbObj->freeResult( $m_res );
    
    return $pic;              
}