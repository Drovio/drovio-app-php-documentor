<?php
//#section#[header]
// Use Important Headers
use \API\Platform\importer;
use \API\Platform\engine;
use \Exception;

// Check Platform Existance
if (!defined('_RB_PLATFORM_')) throw new Exception("Platform is not defined!");

// Import DOM, HTML
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");

use \UI\Html\DOM;
use \UI\Html\HTML;

// Import application for initialization
importer::import("AEL", "Platform", "application");
use \AEL\Platform\application;

// Increase application's view loading depth
application::incLoadingDepth();

// Set Application ID
$appID = 71;

// Init Application and Application literal
application::init(71);
// Secure Importer
importer::secure(TRUE);

// Import SDK Packages
importer::import("AEL", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");

// Import APP Packages
application::import("Main");
//#section_end#
//#section#[view]
use \AEL\Literals\appLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;
use \APP\Main\manualObject;
use \APP\Main\manualManager;

$objectType = engine::getVar("type");
$objectID = engine::getVar("oid");
$mManager = new manualManager($objectType, $objectID);
if (engine::isPost())
{
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Delete object from library
	$status = $mManager->remove();
	// Delete object files
	$mObject = new manualObject($objectID, $objectType);
	$status = $mObject->remove();

	// If there is an error in creating the folder, show it
	if ($status !== TRUE)
	{
		$err = $errFormNtf->addHeader("Deleting object");
		$errFormNtf->addDescription($err, DOM::create("span", $status));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE, $disposable = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	
	
	// Refresh object library
	$succFormNtf->addReportAction("objectExplorer.refresh");
	
	// Clear editor container
	$succFormNtf->addReportAction("editorContainer.clear");
	
	return $succFormNtf->getReport();
}


// Build the frame
$frame = new dialogFrame();
$title = appLiteral::get("editor", "hd_deleteObjectDialogTitle");
$frame->build($title, "", FALSE)->engageApp("deleteObjectDialog");
$form = $frame->getFormFactory();

// Object ID
$input = $form->getInput($type = "hidden", $name = "oid", $value = $objectID, $class = "", $autofocus = FALSE);
$form->append($input);

// Object Type
$input = $form->getInput($type = "hidden", $name = "type", $value = $objectType, $class = "", $autofocus = FALSE);
$form->append($input);

// Header
$attr = array();
$attr['oname'] = $mManager->getName();
$title = appLiteral::get("editor", "hd_deleteObject", $attr);
$hd = DOM::create("h3", $title);
$frame->append($hd);

// Return the report
return $frame->getFrame();
//#section_end#
?>