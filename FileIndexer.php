<?php
/**
 * TITEL:				Extension: FileIndexer
 * ERSTELLDATUM:		15.05.2008
 * AUTHOR:				Ramon Dohle aka 'raZe'
 * ORGANISATION:		GRASS-MERKUR GmbH & Co. KG & raZe.it
 * VERSION:				0.4.5.01	30.09.2010
 * REVISION:
 * 		15.05.2008	0.1.0.00	raZe		*	Initiale Version
 * 		26.06.2008	0.2.0.00	raZe		*	Komplettueberarbeitung
 * 		29.06.2009	0.2.1.00	raZe		*	Weitere Offene Punkte abgearbeitet:
 * 												*	$wgFiArticleNamespace auch bei Uploads nutzen.
 * 												*	$wgFiAutoIndexMark automatisch beim Upload mit Indexerstellungs-Aufforderung im Artikel einsetzbar machen.
 * 													Neue Option: $wgFiSetAutoIndexMark
 * 												*	$wgFiCheckSystem nutzen um Systemvoraussetzung bei jedem Aufruf zu pruefen
 * 													Neue Option: $wgFiCheckSystem
 * 												*	Temporaere Datei muss eindeutig einer Session zugeordnet werden koennen...
 * 		01.07.2009	0.2.2.00	raZe		*	Bug beseitigt, dass $wgFiAutoIndexMark beim Dateiupload nicht beruecksichtigt wurde
 * 		26.08.2010	0.3.0.00	raZe		*	BUGFIX: Variablenfehler in wfFiCheckIndexcreation() behoben
 * 											*	Neue Konfigurationsparameter eingebaut (siehe Beschreibung unter KONFIGURATION)
 * 											*	Neue Dateitypen (Office 2007) eingearbeitet
 * 											*	UTF-8 bei antiword Aufrufen eingetragen
 * 		27.08.2010	0.3.1.00	raZe		*	Indexerstellung per Spezialseite nun auch fuer mehrere Seiten eingebaut
 * 		28.08.2010	0.3.1.01	raZe		*	Revisionskopf (Beschreibungen...) aktualisiert
 * 					0.3.2.00	raZe		*	Neue Funktion wfFiGetIndexFragments() zur Aufteilung eines Artikeltextes in ein Array (pre, index, post).
 * 											*	Steuerung ueber Indexupdateregelung in Spezialseitenklasse ausgelagert
 * 		29.08.2010	0.3.3.00	raZe		*	MediaWiki 1.16 hat das Upload-Objekt umgebaut und viele Bestandteile als protected deklariert, sodass
 * 												ein Ausweichen auf das $wgRequest-Objekt notwendig wurde um in Hookfunktion wfFiBeforeProcessing() auf
 * 												den eingegebenen Summary bzw. Beschreibung/Quelle zugreifen zu koennen.
 * 											*	Gleiches galt fuer das Image-Objekt in Hookfunktion wfFiUploadComplete().
 * 											*	Kommentarmanipulation nicht mehr im Hook UploadForm:BeforeProcessing moeglich, daher Verlagerung nach ArticleSave
 * 											*	Voruebergehend Kompatibilitaet zu 1.15 aufrecht erhalten
 * 		30.08.2010	0.4.0.00	raZe		*	Mehrere Umstellungen auf Formularanpassungen im Page edit und Upload Bereich:
 * 												*	Konfigurationsparameter und Funktionen abgespeckt / veraendert
 * 												*	Formulare werden nun fuer die Indexerstellung ueber eine Checkbox gesteuert
 * 												*	Automatische Indexerstellung wird nun nicht mehr durch ein extra Merkmal festgemacht, sondern
 * 													als Vorschlag je Veraenderung im Formular gesteuert.
 * 		05.09.2010	0.4.0.01	raZe		*	Versionsangabe korrigiert
 * 		23.09.2010	0.4.1.00	raZe		*	BUGFIX: Falsche Arraypruefung bei der Kommandoermittlung zur Indexbildung korrigiert
 * 		25.09.2010	0.4.2.00	raZe		*	Defaultwert fuer das Wildcard Zeichen auf * geaendert
 * 											*	Defaultwert fuer den Namensraum auf NS_IMAGE geaendert
 * 											*	Neue Konfigurationsparameter $wgFiSpWildcardSignChangeable, $wgFiSpNamespaceChangeable, $wgFiLowercaseIndex,
 * 												$wgFiCreateOnUploadByDefault, $wgFiUpdateOnEditArticleByDefault (Beschreibungen s.u.)
 * 											*	Folgende Konfigurationsparameter wurden entfernt: $wgFiCreateIndexByDefault
 * 											*	BUGFIX: Fehler behoben, der bei bspw. Officedokumenten das entfernen der Tags verhinderte
 * 											*	Konfiguration in neue Datei FileIndexer_cfg.php ausgelagert.
 * 											*	Ungenutzte Funktion wfFiCheckNamespace() entfernt
 *		26.09.2010	0.4.3.00	raZe		*	Variable $wgFiFilenamePlaceholder durch Konstante WC_FI_FILEPATH ersetzt
 *											*	Variable $wgFiTempFilePrefix durch Konstante WC_FI_TMPFILE ersetzt
 *											*	SCHNITTSTELLE: Funktion wfFiGetIndex() liefert nun im Fehlerfall einen Fehlercode (INT) zurueck
 *											*	Fehler bei der Indexerstellung werden nun auch in der Summary beim Artikelspeichern angegeben
 *		27.09.2010	0.4.4.00	raZe		*	Neue Konstante: WC_FI_COMMAND fuer Konfigurationsparameter $wgFiCommandCalls
 *					0.4.4.01	raZe		*	Version korrigiert
 *		28.09.2010	0.4.5.00	raZe		*	BUGFIX: wfFiAddCheckboxToEditForm() Verwendung der globalen $wgFiCreateOnUploadByDefault und
 *												$wgFiUpdateOnEditArticleByDefault korrigiert
 *		30.09.2010	0.4.5.01	raZe		*	Defaultkonfiguration veraendert
 *											*	Revisionskopf aufgeraeumt
 *
 * BESCHREIBUNG:
 * 		Diese Erweiterung basiert auf der Wiki-Erweiterung 'FileIndexer' vom Stand 15.05.2008.
 * 		Wie sein Original soll sie Dateien Indexieren um auch den Inhalt dieser Dateien durch Suchmaschienen zu erfassen.
 *
 * OFFENE PUNKTE:
 * 	5	TODO:	[ ]	Bessere Filter-Operationen einbauen.
 * 	9	TODO:	[ ]	Temporaere Dateien mit Datum versehen, sodass sie ggf. per Verfallsdatum geloescht werden koennen.
 *
 * LEGELDE:
 * 		[ ]: Offen
 * 		[B]: Beschlossen zum kommenden Release
 * 		[I]: Inaktiv aber reserviert / zeitlich geplant
 * 		[A]: Aktiv in Bearbeitung
 * 		[T]: Realisiert, aber ungetestet
 * 		[P]: Neu zu pruefen und zu bewerten, ggf. abzusagen
 * 		[D]: Realisiert und getestet, aber noch nicht dokumentiert
 * 		[X]: Temporaere Markierung fuer die Fertigstellung fuer Patchnotes
 */

/*
 * **********************************************************************************************
 * *********************************** Vorbereitung/Skriptteil **********************************
 * **********************************************************************************************
 */

// *** HOOKS
$wgHooks['EditPage::showEditForm:initial'][] = 'wfFiAddCheckboxToEditForm';
$wgHooks['UploadForm:initial'][] = 'wfFiAddCheckboxToUploadForm';
$wgHooks['UploadForm:BeforeProcessing'][] = 'wfFiBeforeProcessing';
$wgHooks['ArticleSave'][] = 'wfFiArticleSave';
$wgHooks['UploadComplete'][] = 'wfFiUploadComplete';

// *** KONSTANTEN
define("WC_FI_TMPFILE", "/fi.requested.article.");
define("WC_FI_FILEPATH", "[=-FILE_NAME-=]");
define("WC_FI_COMMAND", "[=-COMMAND_PATH-=]");
// Fehlercodes
define("WC_FI_ERR_MISSING_SYSTEMCOMMAND", -1);
define("WC_FI_ERR_UNKNOWN_FILETYPE", -2);

// *** INTERN GENUTZTE VARIABLEN
$wgFiCreateIndexThisTime = false;							// Temporaerer Schalter fuer die Erstellung eines Indexes beim speichern eines Artikels

// *** KONFIGURATIONSPARAMETER
include_once("FileIndexer_cfg.php");

// *** EXTENSION INFORMATIONEN
// Informationen fuer Special:Version
$wgExtensionCredits['other'][] = array(
	'name' => 'FileIndexer',
	'version' => '0.4.5.01',
	'author' => 'Ramon Dohle (raZe) | Original: MHart and Flominator',
	'description' => 'Index-Erzeugung aus hochgeladenen Dateien zur Erfassung durch Suchfunktionen',
	'url' => 'http://www.mediawiki.org/wiki/Extension:FileIndexer'
);

// Auch in Bezug auf die neue Spezialseite "Special:FileIndexer"
$wgExtensionCredits['specialpage'][] = array(
	'name' => 'FileIndexer',
	'version' => '0.4.5.01',
	'author' => 'Ramon Dohle (raZe) | Original: MHart and Flominator',
	'description' => 'Index-Erzeugung aus hochgeladenen Dateien zur Erfassung durch Suchfunktionen',
	'url' => 'http://www.mediawiki.org/wiki/Extension:FileIndexer'
);

$wgExtensionFunctions[] = 'wfFiSetupExtension';

$dir = dirname(__FILE__) . '/';
$wgAutoloadClasses['FileIndexer'] = $dir . 'FileIndexer_body.php';
$wgSpecialPages['FileIndexer'] = 'FileIndexer';

/**
 * Bereitet das Wiki auf die Spezialseite vor.
 *
 * @return BOOL TRUE
 */
function wfFiSetupExtension() {
	global $wgMessageCache;

	if(!function_exists('efFiMessages')){
		require_once('FileIndexer.i18n.php');

		foreach(efFiMessages() as $sLanguage => $sMessages){
			$wgMessageCache->addMessages($sMessages, $sLanguage);
		}
	}

	return true;
}

/*
 * **********************************************************************************************
 * *********************************** Hookfunktionen *******************************************
 * **********************************************************************************************
 */

function wfFiAddCheckboxToEditForm(&$oEditPage){
	global $wgRequest, $wgFiUpdateOnEditArticleByDefault, $wgFiCreateOnUploadByDefault, $wgFiArticleNamespace;

  /*
	 * Formular abgeschickt?
	 * Ja => Harken setzen, wie vor dem Abschicken
	 * Nein => Datei mit Artikeltitel im Namensraum NS_IMAGE existiert?
	 * 		Ja => Artikel existiert?
	 * 			Ja => Harken setzen, wenn darin Index gefunden wird und $wgFiUpdateOnEditArticleByDefault == true
	 * 			Nein => Harken nach globalen $wgFiCreateOnUploadByDefault setzen
	 * 		Nein => return
	 */
	$bCheckboxChecked = true;
	if(!is_null($wgRequest->getVal('wpSave')) || !is_null($wgRequest->getVal('wpPreview')) || !is_null($wgRequest->getVal('wpDiff'))){
		$bCheckboxChecked = (!is_null($wgRequest->getVal('wpProcessIndex')) && $wgRequest->getVal('wpProcessIndex') == "true");
  }
	else{
		$iIndexArticleNamespace = ($wgFiArticleNamespace > -1) ? $wgFiArticleNamespace : NS_IMAGE;
		$oFileTitle = Title::makeTitleSafe(NS_IMAGE, $oEditPage->mTitle->getDBkey());
		$oFileArticle = new Article($oFileTitle);
		if($oFileArticle->exists()){
			$oArticle = new Article($oEditPage->mTitle);
			if($oArticle->exists()){
				$oArticle->loadContent();
				$xFragments = wfFiGetIndexFragments($oArticle->mContent);
				$bCheckboxChecked = $xFragments !== false && $wgFiUpdateOnEditArticleByDefault || $xFragments === false && $wgFiCreateOnUploadByDefault;
			}
			else{
				$bCheckboxChecked = $wgFiCreateOnUploadByDefault;
			}
		}
		else{
			return true;
		}
	}

	$oEditPage->editFormTextAfterWarn .= "<b>FileIndexer:</b> <input type='checkbox' name='wpProcessIndex' value='true' " . ($bCheckboxChecked ? "checked" : "") . "> " . wfMsg('fileindexer_form_label_create_index') . "\n";

	return true;
}

function wfFiAddCheckboxToUploadForm(&$oUploadForm){
	global $wgRequest, $wgFiCreateOnUploadByDefault, $wgFiArticleNamespace;

	/*
	 * Formular abeschickt?
	 * Ja => Harken setzen, wie vor dem Abschicken
	 * Nein => Artikel zur Datei aus dem Namensraum $wgFiArticleNamespace existiert?
	 * 		Ja => Harken setzen, wenn darin Index gefunden wird
	 * 		Nein => Harken nach globalen $wgFiCreateOnUploadByDefault setzen
	 */
	$bCheckboxChecked = true;
	if(!is_null($wgRequest->getVal('wpUpload'))){
		$bCheckboxChecked = (!is_null($wgRequest->getVal('wpProcessIndex')) && $wgRequest->getVal('wpProcessIndex') == "true");
	}
	else{
		$iIndexArticleNamespace = ($wgFiArticleNamespace > -1) ? $wgFiArticleNamespace : NS_IMAGE;
		if($oUploadForm->mDesiredDestName != ""){
			$oTitle = Title::makeTitleSafe($iIndexArticleNamespace, $oUploadForm->mDesiredDestName);
			$oArticle = new Article($oTitle);
			if($oArticle->exists()){
				$oArticle->loadContent();
				$bCheckboxChecked = !(wfFiGetIndexFragments($oArticle->mContent) === false);
			}
			else{
				$bCheckboxChecked = $wgFiCreateOnUploadByDefault;
			}
		}
		else{
			$bCheckboxChecked = $wgFiCreateOnUploadByDefault;
		}
	}
	$oUploadForm->uploadFormTextAfterSummary .= "</td></tr><tr><td align=right>FileIndexer:</td><td><input type='checkbox' name='wpProcessIndex' value='true' " . ($bCheckboxChecked ? "checked" : "") . "> " . wfMsg('fileindexer_form_label_create_index') . "\n";

	return true;
}

/**
 * Sucht im Kommentar nach dem Zeichen zur Indexerzeugung und erstellt unter Einsatz externer Programme den Index. Dieser wird zunaechst
 * in einer globalen Variable abgelegt um spaeter von anderen Funktionen verarbeitet zu werden.
 *
 * @param $oUploadForm OBJECT Alle Informationen aus dem Uploadformular
 * @return BOOL TRUE
 */
function wfFiBeforeProcessing(&$oUploadForm){
	global $wgFiRequestIndexCreationFile, $wgUploadDirectory, $wgHashedUploadDirectory, $wgRequest;

	// Im Kommentar zum Upload wird geschaut, ob der Index erzeugt werden soll. Wenn ja, dann entferne das Zeichen aus dem Kommentar. Ansonsten gehe ohne getane Arbeit raus.
	if(is_null($wgRequest->getVal('wpProcessIndex')) || $wgRequest->getVal('wpProcessIndex') != "true"){
		return true;
	}

  // Im Falle einer gueltigen Dateiendung => Endgueltigen Pfad in temporaere Datei schreiben
	if(isset($wgFiCommandCalls[strtolower(substr(strrchr($oUploadForm->mDesiredDestName, '.'), 1))])){
		exec("echo \"" . $wgUploadDirectory . "/" . FileRepo::getHashPathForLevel($oUploadForm->mDesiredDestName, $wgHashedUploadDirectory ? 2 : 0) . $oUploadForm->mDesiredDestName . "\" > " . $wgFiRequestIndexCreationFile . WC_FI_TMPFILE . session_id());
	}

	return true;
}

function wfFileIndexer(&$mDesiredDestName,$doit){
	global $sCreateIndexFilepath,$wgFiCommandCalls, $wgFiRequestIndexCreationFile, $wgUploadDirectory, $wgHashedUploadDirectory;

  $sCreateIndexFilepath = $doit;
  // Im Falle einer gueltigen Dateiendung => Endgueltigen Pfad in temporaere Datei schreiben
	if(isset($wgFiCommandCalls[strtolower(substr(strrchr($mDesiredDestName, '.'), 1))])){
	#echo " ja ".$wgFiRequestIndexCreationFile." > ".$wgHashedUploadDirectory." > ".$wgUploadDirectory;
		exec("echo \"" . $wgUploadDirectory . "/" . FileRepo::getHashPathForLevel($mDesiredDestName, $wgHashedUploadDirectory ? 2 : 0) . $mDesiredDestName . "\" > " . $wgFiRequestIndexCreationFile . WC_FI_TMPFILE . session_id());
  }

	return true;
}
/**
 * Diese Hook-Funktion wird nach dem erfolgreichen Upload einer Datei aufgerufen und stoesst das Update des zur Datei
 * gehoerigen Artikels an, sollte die Indexerstellung gefordert sein.
 *
 * @param $oImage OBJECT Die Uploadinformationen
 * @return BOOL TRUE
 */
function wfFiUploadComplete(&$oImage){
	global $wgFiCreateIndexThisTime, $wgFiRequestIndexCreationFile, $wgFiArticleNamespace, $wgVersion;

	$aVersion = explode(".", $wgVersion);
	$iVersionRange = ($aVersion[0] == 1 && $aVersion[1] < 16) ? 1 : 2;

	// Pruefen, ob diesmalig zur Indexierung aufgefordert....
	$sUploadedFilepath = false;
	if($iVersionRange == 1){
		$sUploadedFilepath = $oImage->mLocalFile->repo->directory . "/" . $oImage->mLocalFile->hashPath . $oImage->mLocalFile->name;
	}
	else{
		$sUploadedFilepath = $oImage->getLocalFile()->repo->directory . "/" . $oImage->getLocalFile()->hashPath . $oImage->getLocalFile()->name;
	}
	
	
	global $sCreateIndexFilepath;
  #$sCreateIndexFilepath = wfFiReadFilepath(); 
  #echo  "filexx >".$sCreateIndexFilepath."<".$iVersionRange.">";


	if($sUploadedFilepath !== false && $sCreateIndexFilepath == true){
	
	  #echo " filepath: ".$sUploadedFilepath.">".$wgFiArticleNamespace;
		// Zunaechst wird der Artikel gesucht und ggf. geladen, in den der Index abgelegt wuerde...
		$oArticle = false; // Bekannt machen...
		$iIndexArticleNamespace = ($wgFiArticleNamespace > -1) ? $wgFiArticleNamespace : NS_IMAGE;
		$oTitle = Title::makeTitleSafe($iIndexArticleNamespace, ($iVersionRange == 1) ? $oImage->mLocalFile->getTitle()->getDBkey() : $oImage->getTitle()->getDBkey());
		if($oTitle !== NULL){
			$oArticle = new Article($oTitle);

			$wgFiCreateIndexThisTime = true;
			$oArticle->doEdit($oArticle->mContent, wfMsg('fileindexer_upl_index_creation_comment'), 0);
			$wgFiCreateIndexThisTime = false;
		}
	}

	// Alles in Ausgangslage zuruecksetzen (temporaere Datei loeschen und Flag zuruecknehmen)...
	if(file_exists($wgFiRequestIndexCreationFile . WC_FI_TMPFILE . session_id())){
		unlink($wgFiRequestIndexCreationFile . WC_FI_TMPFILE . session_id());
	}

	return true;
}

/**
 * Diese Hook-Funktion aktualisiert die Index-Sektion, sollte es sich um einen FileUpload handeln
 * und ein neuer Inhalt fuer diese Sektion vorbereitet worden sein.
 * In jedem Fall wird die global abgelegte Index-Sektions-Inhalts-Variable wieder geleert.
 *
 * @param $oArticle OBJECT Der Artikel
 * @param $oUser OBJECT Der Benutzer
 * @param $sContent STRING Inhalt des Artikels
 * @param $sSummary STRING Zusammenfassung fuer das Update
 * @param $minor SIEHE WIKIDOKU
 * @param $watch SIEHE WIKIDOKU
 * @param $sectionanchor SIEHE WIKIDOKU
 * @param $flags SIEHE WIKIDOKU
 * @return BOOL TRUE
 */
function wfFiArticleSave(&$oArticle, &$oUser, &$sContent, &$sSummary, $minor, $watch, $sectionanchor, &$flags){
	global $wgFiPrefix, $wgFiPostfix, $wgUploadDirectory, $wgHashedUploadDirectory, $wgRequest, $wgFiCreateIndexThisTime;

	// Spezialseite und UploadFormular setzen $wgFiCreateIndexThisTime auf true zur Indexerstellung
	if($wgFiCreateIndexThisTime === true || !is_null($wgRequest->getVal('wpProcessIndex')) && $wgRequest->getVal('wpProcessIndex') == "true"){
		// Datei holen und Index erstellen
		$sFilepath = $wgUploadDirectory . "/" . FileRepo::getHashPathForLevel($oArticle->mTitle->mDbkeyform , $wgHashedUploadDirectory ? 2 : 0) . $oArticle->mTitle->mDbkeyform;
		
    $sIndex = wfFiGetIndex($sFilepath);
		if(is_numeric($sIndex)){
			// kein Index aus Datei erzeugt
			switch ($sIndex){
				case WC_FI_ERR_MISSING_SYSTEMCOMMAND:
					$sReason = wfMsg('fileindexer_index_creation_failed_comment_missing_systemcommand');
					break;
				case WC_FI_ERR_UNKNOWN_FILETYPE:
					$sReason = wfMsg('fileindexer_index_creation_failed_comment_unknown_filetype');
					break;
				default:
					$sReason = wfMsg('fileindexer_index_creation_failed_comment_unknown_reason');
			}

			$sSummary .= ((substr($sSummary, strlen($sSummary) - 1, 1) == "\n") ? "" : "\n") . wfMsg('fileindexer_index_creation_failed_comment') . $sReason;

			return true;
		}

		// Index suchen und Text in Fragmente splitten
		$aFragments = wfFiGetIndexFragments($sContent);
		if($aFragments === false){
			// kein Index gefunden
			if(substr($sContent, strlen($sContent) - 1, 1) != "\n"){
				$sContent .= "\n";
			}
			$sContent .= $sIndex;
			$sSummary .= ((substr($sSummary, strlen($sSummary) - 1, 1) == "\n") ? "" : "\n") . wfMsg('fileindexer_index_creation_complete_comment');

			return true;
		}
		else{
			// Index gefunden
			$sContent = $aFragments['pre'] . $sIndex . $aFragments['post'];
			$sSummary .= ((substr($sSummary, strlen($sSummary) - 1, 1) == "\n") ? "" : "\n") . wfMsg('fileindexer_index_update_complete_comment');

			return true;
		}
	}

	return true;
}

/*
 * **********************************************************************************************
 * *********************************** Hilfsfunktionen ******************************************
 * **********************************************************************************************
 */

/**
 * Liefert ein Array, dass eine Trennung von den Teilen vor dem Index ('pre'), dem Index selbst ('index') und dem Teil nach dem Index ('post')
 * oder FALSE, wenn kein Index lokalisiert werden konnte.
 *
 * @param $sText STRING zu durchsuchender Text
 * @return ARRAY | FALSE Fragmente oder FALSE
 */
function wfFiGetIndexFragments($sText){
	global $wgFiPrefix, $wgFiPostfix;

	$aFragments = false;

	$iPostFileIndexPos = false;
	$iFileIndexPos = strpos($sText, $wgFiPrefix);
	if($iFileIndexPos === false){
		return false;
	}
	else{
		$aFragments['pre'] = substr($sText, 0, $iFileIndexPos);

		$iPostFileIndexPos = strpos($sText, $wgFiPostfix, $iFileIndexPos);
		if($iPostFileIndexPos !== false){
			$aFragments['index'] = substr($sText, $iFileIndexPos, $iPostFileIndexPos - $iFileIndexPos);
			$aFragments['post'] = substr($sText, $iPostFileIndexPos + strlen($wgFiPostfix));
		}
		else{
			return false;
		}
	}

	return $aFragments;
}

/**
 * Prueft, ob der Artikel zur Erstellung eines Indexes gemaess der Namensraumkonfigurationen valide ist.
 *
 * @param $oTitle OBJECT Der Artikel
 * @return BOOL Erfolg
 */
function wfFiCheckNamespace($oTitle){
	global $wgFiArticleNamespace;

	return !($wgFiArticleNamespace > -1 && $wgFiArticleNamespace != $oTitle->mNamespace);
}

/**
 * Versucht die temporaere Datei auszulesen und prueft, ob der Inhalt eine existierende Datei darstellt.
 *
 * @return STRING | FALSE Pfad oder Fehler
 */
function wfFiReadFilepath(){
	global $wgFiRequestIndexCreationFile;


	if(file_exists($wgFiRequestIndexCreationFile . WC_FI_TMPFILE . session_id())){
		exec ("cat \"" . $wgFiRequestIndexCreationFile . WC_FI_TMPFILE . session_id() . "\"", $aReturn);
		$sFileHashPath = $aReturn[0];
		if($sFileHashPath != ""){
			if(!file_exists($sFileHashPath)){
				// Datei konnte nicht gefunden werden...
				return false;
			}
			else{
				return $sFileHashPath;
			}
		}
		else{
			// Es ist kein Dateipfad zur Indexerstellung hinterlegt worden...
			return false;
		}
	}
	else{
		// Es ist keine Datei zur Indexerstellung hinterlegt worden.
		return false;
	}

}

/**
 * Sucht im Kommentar nach dem Zeichen zur Indexerzeugung und erstellt unter Einsatz externer Programme den Index. Dieser wird zunaechst
 * in einer globalen Variable abgelegt um spaeter von anderen Funktionen verarbeitet zu werden.
 *
 * @param $sFileHashPath STRING Dateipfad
 * @return STRING Index
 */
function wfFiGetIndex($sFileHashPath){
	global $wgFiPrefix, $wgFiPostfix, $wgFiMinWordLen, $wgFiCommandCalls, $wgFiCommandPaths, $wgFiTypesToRemoveTags, $wgFiLowercaseIndex;

	$sReturn = "";

	// Systemvoraussetzungen checken...
	FileIndexer::checkNecessaryCommands();
	if(in_array(false, $wgFiCommandPaths)){
		return WC_FI_ERR_MISSING_SYSTEMCOMMAND;
	}

	$sFileExtension = strtolower(substr(strrchr($sFileHashPath, '.'),1));
	if(!isset($wgFiCommandCalls[$sFileExtension])){
		// Unbekannter Dateityp => Abbruch
		return WC_FI_ERR_UNKNOWN_FILETYPE;
	}

	// ExecutionCommand ermitteln
	$sExecutionCommand = isset($wgFiCommandCalls[$sFileExtension]) ? FileIndexer::getCommandLine($sFileHashPath) : "";

	if ($sExecutionCommand != ""){
		exec($sExecutionCommand, $sDocText);

		$sReturn = $wgFiPrefix;
		$aIndex = array();

		// Feststellung der Mindest-Wortlaenge fuer ein Indexwort
		$wgFiMinWordLen = ($wgFiMinWordLen > 0) ? $wgFiMinWordLen : 3;

		foreach ($sDocText as $sDocLine){
			if(in_array($sFileExtension, $wgFiTypesToRemoveTags)){
				// Tags entfernen... Vorher vor jedem "<" Leerzeichen einfuegen, damit keine Worte zusammenfallen!
				$sDocLine = strip_tags(str_replace("<", " <", $sDocLine));
			}

			// Sonderzeichen entfernen...
			// ATTENTION: German only! Umlaute werden durch strtolower nicht in Kleinbuchstaben gewandelt...
			if($wgFiLowercaseIndex){
				$sDocLine = strtolower(ereg_replace("[[:punct:]][[:space:]]|[[:space:]][[:punct:]]|[[:punct:]][[:punct:]]", " ", ereg_replace("Ä", "ä", ereg_replace("Ö", "ö", ereg_replace("Ü", "ü", $sDocLine)))));
			}
			else{
				$sDocLine = preg_replace("^/[[:punct:]][[:space:]]|[[:space:]][[:punct:]]|[[:punct:]][[:punct:]]/i^", " ", $sDocLine);
			}

			// Worte filtern und in Index packen...
			$aSplit = explode(" ", $sDocLine);
			foreach($aSplit as $sWord){
				if($sWord != "" && !is_numeric($sWord) && strlen($sWord) >= $wgFiMinWordLen){
					$aIndex[$sWord] = true;
				}
			}
		}

		// Index global setzen...
		foreach(array_keys($aIndex) as $skeyword){
			$sReturn .= $skeyword . " ";
		}

		$sReturn .= $wgFiPostfix;
	}

	return $sReturn;
}
