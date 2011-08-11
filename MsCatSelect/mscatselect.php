<?php

# Setup and Hooks for the MsCatSelect extension


if( !defined( 'MEDIAWIKI' ) ) {
        echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
        die();
}

## Register extension setup hook and credits:
$wgExtensionCredits['parserhook'][] = array(
        'name'           => 'MsCatSelect',
        'url'  => 'http://www.mediawiki.org/wiki/Extension:MsCatSelect',
        'version'        => '4.2',
        'author' => '[mailto:info@ratin.de info@ratin.de] | Ratin',
        'description' => 'Mit dieser Extension kann eine Seite einer bestehenden oder neuen Kategorie per DropDown zugewiesen werden oder auch neue Unterkategorien erstellt werden.',
        'descriptionmsg' => 'selectcategory-desc',
);

## Load the file containing the hook functions:
require_once( 'mscatselect.functions.php' );

$dir = dirname(__FILE__) . '/';
$wgExtensionMessagesFiles['mscatselect'] = $dir . 'mscatselect.i18n.php';

## Set Hook:
global $wgHooks, $wgScriptPath;

## Showing the boxes
# Hook when starting editing:
$wgHooks['EditPage::showEditForm:initial'][] = array( 'fnSelectCategoryShowHook', false );

## Saving the data
# Hook when saving page:
$wgHooks['EditPage::attemptSave'][] = array( 'fnSelectCategorySaveHook', false );
