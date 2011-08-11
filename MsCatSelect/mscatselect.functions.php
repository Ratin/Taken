<?php

/**
 * Functions file for extension MsCatSelect.
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
 
 
## Entry point for the hook and main worker function for editing the page:
function fnSelectCategoryShowHook( $m_isUpload = false, $m_pageObj ) {
	  
    # Register CSS file for our select box:
    global $wgOut, $wgScriptPath,$wgWarnNoCat;
    global $wgTitle, $wgRequest;
    
    $wgOut->addHtml('<style>
    .mscs{
    	width: 98%;
    	padding: .5em;
      background: #ddd;
      margin: 5px 0px;
    }
    #WarnNoCat{
      padding: .5em 0em;
      color: red;
      font-size:1.35em;
    }
    
    </style>'); 
  
	
    # Get all categories from wiki:
    $m_allCats = fnSelectCategoryGetAllCategories(true);
   
    global $wgMainCategories,$m_allCats_os;
    if($wgMainCategories){
    
      foreach ($wgMainCategories as $key => $value) {
      $m_allCats_os[$value]=0;
      }
 
    } else {
      
      # Get all categories from wiki:
      $m_allCats_os = fnSelectCategoryGetAllCategories(false);
    }

    # Load system messages:
    wfLoadExtensionMessages( 'mscatselect' );
    # Get the right member variables, depending on if we're on an upload form or not:
    
    if( !$m_isUpload ) {
      # Extract all categorylinks from editfield:
      #$m_pageCats = fnSelectCategoryGetPageCategories( $m_pageObj );
      #fnCleanTextbox($m_pageObj);
      
      if(	$wgRequest->getVal("preload") != "") {
        # neue seiten die mit preload angelegt werden
        $m_pageCats = fnGetPageCategories($wgRequest->getVal("preload"));
      } else {
        $m_pageCats = fnGetPageCategories();
      }
      
      # Never ever use editFormTextTop here as it resides outside the <form> so we will never get contents
      $m_place = 'editFormTextAfterWarn';
      # Print the localised title for the select box:
      $m_textBefore = '<b>'. wfMsg( 'selectcategory-title' ) . ': </b>';
    
    } else {  # upload
      # No need to get categories:
      $m_pageCats = array();
      # Place output at the right place:
      $m_place = 'uploadFormTextAfterSummary';
      # Print the part of the table including the localised title for the select box:
      $m_textBefore = "\n</td></tr><tr><td align='right'><label for='wpSelectCategory'>" . wfMsg( 'selectcategory-title' ) .":</label></td><td align='left'>";
    }

    $m_pageObj->$m_place .= "<div class='mscs'><!-- mscs begin -->\n";
    # Print the select box:
    $m_pageObj->$m_place .= "\n$m_textBefore\n";

         $m_ober_cat="";
         $unter_cat = array();
    while (list($key,$value) = each($m_allCats_os)) {

         $m_ober_cat .= $key." ";
         $m_unter_cat = fnSelectCategoryGetChildren($key);

                 $m_string="";
                 while (list($key_sub,$value_sub) = each($m_unter_cat)){
                                 $m_string .= $key_sub." ";
                 }

            array_push($unter_cat,$m_string);
         }

    
    global $wgFrameworkLoaded;
    
    $path =  $wgScriptPath.'/extensions/MsCatSelect';
    
    if (!$wgFrameworkLoaded){
    $wgOut->addScriptFile( $path.'/mootools-core-1.3.2.js' );
    $wgFrameworkLoaded = true;  
    }

    $wgOut->addScriptFile( $path.'/mscatselect.js' );
    $m_pageObj->$m_place .= "<select id='dd_1' name='auswahl' onchange=\"getUnterkat(this.value,1)\"><option value=''>----</option>";

     $i=0;
     
    
    foreach( $m_allCats_os as $m_cat => $m_depth){
    
    $category =  htmlspecialchars( $m_cat );
    $m_pageObj->$m_place .= "<option name='$category' value='$category'>$category</option>";
    $i++;
    }

    $m_pageObj->$m_place .= "</select><span id='sdd'></span>";
    $m_pageObj->$m_place .= "&nbsp;<input type='button' value='". wfMsg( 'selectcategory-add' ) ."' onclick=\"addKat(1)\">";
    $m_pageObj->$m_place .= "<br><b>". wfMsg( 'selectcategory-untercat' ) .": </b><input type='text' value='' size='10' id='new_name'>";
    $m_pageObj->$m_place .= "&nbsp;<input type='button' value='". wfMsg( 'selectcategory-go' ) ."' onclick=\"neu()\">";
    $m_pageObj->$m_place .= " (". wfMsg( 'selectcategory-untercat-hinw' ) .")<br>";
    
    
    #all added categories
    
    fnCleanTextbox($m_pageObj);
    
    if(count($m_pageCats) == 0 AND $wgWarnNoCat) { # warn no Category
    
      $m_pageObj->$m_place .= "<div id='WarnNoCat'>". wfMsg( 'selectcategory-warnnocat' ) ."</div><div id='msc_added'>";
    
    } else { #more than zero
    
      $m_pageObj->$m_place .= "<b>". wfMsg( 'selectcategory-cats' ) .":</b><br><div id='msc_added'>";
    
      foreach($m_pageCats as $m_cat =>$m_depth ){
    
         $category =  htmlspecialchars( $m_cat );
         
         if ($m_depth != 1){
         $category = $category."|".$m_depth; 
         }
         $m_pageObj->$m_place .= "<input class='msc_checkbox' type='checkbox' name='SelectCategoryList[]' value='".$category."' class='checkbox' checked='checked' /><br>"; 
      }//foreach
    
    }
    
    $m_pageObj->$m_place .= "</div>";
    $m_pageObj->$m_place .= "</div><!-- mscs end -->\n";

  # Return true to let the rest work:
  return true;
}
    ## Entry point for the hook and main worker function for saving the page:
function fnSelectCategorySaveHook( $m_isUpload, $m_pageObj ) {
  global $wgContLang;
  global $wgTitle;

    # Get localised namespace string:
    $m_catString = $wgContLang->getNsText( NS_CATEGORY );

    # default sort key is page name with stripped namespace name,
    # otherwise sorting is ugly.
    if ($wgTitle->getNamespace() == NS_MAIN) {
      $default_sortkey = "";
    } else {
      #$default_sortkey = "|{{PAGENAME}}"; macht bei dateien probleme (anderer NS)
    }
    $m_text = "\n";

    # Iterate through all selected category entries:
    if (array_key_exists('SelectCategoryList', $_POST)) {
      foreach( $_POST['SelectCategoryList'] as $m_cat ) {
        $m_text .= "\n[[$m_catString:$m_cat$default_sortkey]]";
      }
    }
    # If it is an upload we have to call a different method:
    if ( $m_isUpload ) {
      $m_pageObj->mUploadDescription .= $m_text;
    } else{
      $m_pageObj->textbox1 .= $m_text;
    }

  # Return to the let MediaWiki do the rest of the work:
  return true;
}

## Get all categories from the wiki - starting with a given root or otherwise detect root automagically (expensive)
## Returns an array like this:
## array (
##   'Name' => (int) Depth,
##   ...
## )

$wgAjaxExportList[] = 'fnSelectCategoryGetAllCategories';
function fnSelectCategoryGetAllCategories($m_sub_cats) {
  global $wgTitle;
  global $wgSelectCategoryRoot;

  # Get current namespace (save duplicate call of method):
  $m_namespace = $wgTitle->getNamespace();
  if( $m_namespace >= 0 && $wgSelectCategoryRoot[$m_namespace] ) {
    # Include root and step into the recursion:
    $m_allCats = array_merge( array( $wgSelectCategoryRoot[$m_namespace] => 0 ), fnSelectCategoryGetChildren( $wgSelectCategoryRoot[$m_namespace]) );
  } else {
    # Initialize return value:
    $m_allCats = array();
    # Get a database object:
    $m_dbObj =& wfGetDB( DB_SLAVE );
    # Get table names to access them in SQL query:
    $m_tblCatLink = $m_dbObj->tableName( 'categorylinks' );
    $m_tblPage = $m_dbObj->tableName( 'page' );

    # Automagically detect root categories:
    $m_sql = "  SELECT tmpSelectCat1.cl_to AS title
        FROM $m_tblCatLink AS tmpSelectCat1
        LEFT JOIN $m_tblPage AS tmpSelectCatPage ON (tmpSelectCat1.cl_to = tmpSelectCatPage.page_title AND tmpSelectCatPage.page_namespace = 14)
        LEFT JOIN $m_tblCatLink AS tmpSelectCat2 ON tmpSelectCatPage.page_id = tmpSelectCat2.cl_from
        WHERE tmpSelectCat2.cl_from IS NULL GROUP BY tmpSelectCat1.cl_to";
    # Run the query:
    $m_res = $m_dbObj->query( $m_sql, __METHOD__ );
    # Process the resulting rows:
    while ( $m_row = $m_dbObj->fetchRow( $m_res ) ) {
    
      $cat=strtr($m_row['title'], '_', ' '); //database
      
      $m_allCats += array( $cat => 0 );
      if($m_sub_cats == true){
       $m_allCats += fnSelectCategoryGetChildren($cat);
    }
    }
    # Free result:
    $m_dbObj->freeResult( $m_res );
  }
  # Afterwards return the array to the caller:
  return $m_allCats;
}

$wgAjaxExportList[] = 'fnCategoryGetChildren';
function fnCategoryGetChildren($kat){

  $arr_childkats = fnSelectCategoryGetChildren($kat);
  
  ksort($arr_childkats);

  foreach($arr_childkats as $key => $kat) {
  $unterkat[] = strtr($key, '_', ' '); //database
  }
  if (is_array($unterkat)) {
  $unterkat = implode ( '|',$unterkat);
  } else {
  $unterkat = "0";
  }
  
  
  return $unterkat;

}

function fnSelectCategoryGetChildren( $m_root, $m_depth = 1 ) {
  # Initialize return value:
  $m_allCats = array();

  # Get a database object:
  $m_dbObj =& wfGetDB( DB_SLAVE );
  # Get table names to access them in SQL query:
  $m_tblCatLink = $m_dbObj->tableName( 'categorylinks' );
  $m_tblPage = $m_dbObj->tableName( 'page' );

  $m_root=strtr($m_root, ' ', '_'); //database

  # The normal query to get all children of a given root category:
  $m_sql = "  SELECT tmpSelectCatPage.page_title AS title
      FROM $m_tblCatLink AS tmpSelectCat
      LEFT JOIN $m_tblPage AS tmpSelectCatPage ON tmpSelectCat.cl_from = tmpSelectCatPage.page_id
      WHERE tmpSelectCat.cl_to LIKE " . $m_dbObj->addQuotes( $m_root ) . " AND tmpSelectCatPage.page_namespace = 14";
  # Run the query:
  $m_res = $m_dbObj->query( $m_sql, __METHOD__ );
  # Process the resulting rows:
  while ( $m_row = $m_dbObj->fetchRow( $m_res ) ) {
    # Survive category link loops:

    if( $m_root == $m_row['title'] ) {
      continue;
    }
    # Add current entry to array:
    $m_allCats += array($m_row['title'] => $m_depth);
  }
  # Free result:
  $m_dbObj->freeResult( $m_res );

  # Afterwards return the array to the upper recursion level:
  return $m_allCats;

}

## Returns an array with the categories the articles is in.
## Also removes them from the text the user views in the editbox.
function fnCleanTextbox( $m_pageObj ) {

  global $wgContLang;
  # Get page contents:
  $m_pageText = $m_pageObj->textbox1;
  # Get localised namespace string:
  $m_catString = strtolower( $wgContLang->getNsText( NS_CATEGORY ) );
  # The regular expression to find the category links:
  $m_pattern = "\[\[({$m_catString}|category|Category):([^\|\]]*)(\|[^\|\]]*)?\]\]";
  $m_replace = "$2";
  # The container to store the processed text:
  $m_cleanText = '';

  # Check linewise for category links:
  foreach( explode( "\n", $m_pageText ) as $m_textLine ) {
    # Filter line through pattern and store the result:
    $m_cleanText .= preg_replace( "/{$m_pattern}/i", "", $m_textLine ) . "\n";
  }
  # Place the cleaned text into the text box:
  $m_pageObj->textbox1 = trim( $m_cleanText );

  return true;
}

function fnGetPageCategories($title = "") {

  global $wgTitle,$wgArticleId;

    $m_catLinks = array();
    # Get a database object:
    $m_dbObj =& wfGetDB( DB_SLAVE );
    # Get table names to access them in SQL query:
    $m_tblCatLink = $m_dbObj->tableName( 'categorylinks' );
    
    if($title != ""){
    
      $m_tblPage = $m_dbObj->tableName( 'page' );
      $m_sql = "SELECT cl_to AS title, cl_sortkey FROM $m_tblCatLink l, $m_tblPage p WHERE p.page_title = '".$title."' AND p.page_id = l.cl_from"; 
    
    } else {
    
      $m_sql = "SELECT cl_to AS title, cl_sortkey FROM $m_tblCatLink  WHERE cl_from = '".$wgTitle->getArticleID()."'"; 
      
    }

    
    $m_res = $m_dbObj->query( $m_sql, __METHOD__ );
    # Process the resulting rows:
    while ( $m_row = $m_dbObj->fetchRow( $m_res ) ) {

      $cat=strtr($m_row['title'], '_', ' '); //database
      $m_catLinks[$cat] = $m_row['cl_sortkey'];
    }
    # Free result:
    $m_dbObj->freeResult( $m_res );

  # Return the list of categories as an array:
  return $m_catLinks;
}


$wgAjaxExportList[] = 'fnNewCategory';
function fnNewCategory($title,$category) {

  $title = Title::newFromText( $title );

  if ($title->getArticleID()){ //page already exists
  	
  return "no".$wgPageName.$title->getArticleID();
  
  }
  #if ($category !=0){
  #$category = "[[Kategorie:".$category."]]";
  #}  	
	$text = $category;
	$summary = "MsCatSelect";
 
	#now create the page	
	$talkPage = new Article($title,0);
	$talkPage->doEdit( $text, $summary, EDIT_NEW );
	return 1;

}