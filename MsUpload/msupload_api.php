<?
error_reporting(E_ALL);
ini_set("display_errors", 1);

#echo getcwd()."\n";
chdir("../../");

require_once ( 'includes/WebStart.php');

if(isset($_GET['user'])){
$wgUser = User::newFromName($_GET['user']); 
}
    
#echo $wgUser->getToken();
#echo $wgUser->getName();

#apiupload.php
#functions in UploadBase.php
$mUpload = new UploadFromFile();

global $sCreateIndexFilepath;

$sCreateIndexFilepath = false;
$ignorewarnings = true;   
$text = "";
$comment = "MsUpload";       
$watch = false;
$error = false;


if (isset($_FILES['Filedata']['name'])) {     

  $filename = $_FILES['Filedata']['name'];
  
  $anz = 1;
  #  if (array_key_exists('name_hidden', $_POST)) {
          foreach( $_POST['name_hidden'] as $nam ) {
            $change = explode("|", $nam); 
            if ($change[0] == $filename){ #namen stimmen überein 
            $ids=$anz;
              if(isset($change[1])){ #gibt es eine änderung?
                $filename = $change[1]; #ändern
              } //if
            } //if
            $anz++;
          } //foreach
  #  } //if 
    
  //Dateinamen überprüfen ob indiziert werden soll
  #wfFileIndexer($filename,false);
  if(class_exists('FileIndexer')){
  $fi = new FileIndexer;
  $fi = $fi->checkFileType($filename);
  
    if($fi==true){
      if(isset($_POST['fi'][$ids])){
        $sCreateIndexFilepath = true;  
      }  
    } 
   }
   
  if(isset($_POST['kat'][$ids])){ #kategorie gesetzt
    $comment = "[[".$_POST['kat_hidden']."]]";  
  }  
    
  $mUpload->initialize($filename, $_FILES['Filedata']['tmp_name'], $_FILES['Filedata']['size']);

}//if isset


		
		$permErrors = $mUpload->verifyPermissions( $wgUser );
		
		if ( $permErrors !== true ) {
			$error = "missing permissions";
			print_r($permErrors);
		}
		
		 
		 

		$verification = $mUpload->verifyUpload();
		
		#print_r($verification['status']);
		/*
		if ( $verification['status'] !== UploadBase::OK ) {
			
      $result['result'] = 'Failure';
			switch( $verification['status'] ) {
				case UploadBase::EMPTY_FILE:
					echo"bad1";
          #$this->dieUsage( 'The file you submitted was empty', 'empty-file' );
					break;
				case UploadBase::FILETYPE_MISSING:
				  echo"bad1";
					#$this->dieUsage( 'The file is missing an extension', 'filetype-missing' );
					break;
				case UploadBase::FILETYPE_BADTYPE:
					#global $wgFileExtensions;
					echo"bad1",
					#$this->dieUsage( 'This type of file is banned', 'filetype-banned',
					#		0, array(
					#			'filetype' => $verification['finalExt'],
					#			'allowed' => $wgFileExtensions
					#		) );
					break;
				case UploadBase::MIN_LENGTH_PARTNAME:
					echo"bad1";
          #$this->dieUsage( 'The filename is too short', 'filename-tooshort' );
					break;
				case UploadBase::ILLEGAL_FILENAME:
					echo"bad1",
          #$this->dieUsage( 'The filename is not allowed', 'illegal-filename',
					#		0, array( 'filename' => $verification['filtered'] ) );
					break;
				case UploadBase::OVERWRITE_EXISTING_FILE:
					echo"bad1";
          #$this->dieUsage( 'Overwriting an existing file is not allowed', 'overwrite' );
					break;
				case UploadBase::VERIFICATION_ERROR:
					#$this->getResult()->setIndexedTagName( $verification['details'], 'detail' );
					 echo"bad1",
          #$this->dieUsage( 'This file did not pass file verification', 'verification-error',
					#		0, array( 'details' => $verification['details'] ) );
					break;
				case UploadBase::HOOK_ABORTED:
					echo"bad1",
          #$this->dieUsage( "The modification you tried to make was aborted by an extension hook",
					#		'hookaborted', 0, array( 'error' => $verification['error'] ) );
					break;
				default:
					echo"bad1",
          #$this->dieUsage( 'An unknown error occurred', 'unknown-error',
					#		0, array( 'code' =>  $verification['status'] ) );
					break;
			}
			#return $result;
		}
		*/
		
		if ( !$ignorewarnings ) {
		
			$warnings = $mUpload->checkWarnings();
      $sessionKey = $mUpload->stashSession();
      /*
			if ( $warnings ) {
				// Add indices
				$this->getResult()->setIndexedTagName( $warnings, 'warning' );

				if ( isset( $warnings['duplicate'] ) ) {
					$dupes = array();
					foreach ( $warnings['duplicate'] as $key => $dupe )
						$dupes[] = $dupe->getName();
					$this->getResult()->setIndexedTagName( $dupes, 'duplicate' );
					$warnings['duplicate'] = $dupes;
				}


				if ( isset( $warnings['exists'] ) ) {
					$warning = $warnings['exists'];
					unset( $warnings['exists'] );
					$warnings[$warning['warning']] = $warning['file']->getName();
				}

				$result['result'] = 'Warning';
				$result['warnings'] = $warnings;

				$sessionKey = $this->mUpload->stashSession();
				
				
				
				if ( !$sessionKey )
					$this->dieUsage( 'Stashing temporary file failed', 'stashfailed' );

				$result['sessionkey'] = $sessionKey;

				return $result;
			}
			*/
			
		}

    // Check whether the user has the appropriate permissions to upload anyway
		$permission = $mUpload->isAllowed( $wgUser );

		if ( $permission !== true ) {
			if ( !$wgUser->isLoggedIn() )
			  $error = "mustbeloggedin";
			else
			  $error = "badaccess-groups";
		}
		

		// Use comment as initial page text by default
		if ( is_null( $text ) )
			$text = $comment;

#wfFiUploadComplete($mUpload);

		// No errors, no warnings: do the upload
		$status = $mUpload->performUpload( $comment,$text, $watch, $wgUser );

		if ( !$status->isGood() ) {
			$error = $status->getErrorsArray();
			$error = "An internal error occurred";
		}

		$file = $mUpload->getLocalFile();


#$result['imageinfo'] = $mUpload->getImageInfo( $this->getResult() );

		



#$path_parts = pathinfo($file->getName());
#$extension = $path_parts['extension'];

#if (in_array($extension, $wgPictureExt)){
#$extension = "pic";
#} 	
  
  
          
$mUpload->cleanupTempFile();



if ($error) {

	$return = array(
		'status' => '0',
		'error' => $error
	);

} else {

	$return = array(
		'status' => '1',
		//'extension'=> "|".$extension."|",
		'name' => $file->getName(),
		'fi' => $sCreateIndexFilepath
	);
	
}

if (isset($_REQUEST['response']) && $_REQUEST['response'] == 'xml') {
	// header('Content-type: text/xml');

	// Really dirty, use DOM and CDATA section!
	echo '<response>';
	foreach ($return as $key => $value) {
		echo "<$key><![CDATA[$value]]></$key>";
	}
	echo '</response>';
} else {
	// header('Content-type: application/json');

	echo json_encode($return);
}
?>