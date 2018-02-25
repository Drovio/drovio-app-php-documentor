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
importer::import("DEV", "Documentation");
importer::import("UI", "Apps");
importer::import("UI", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Navigation");

// Import APP Packages
application::import("Main");
//#section_end#
//#section#[view]
use \UI\Apps\APPContent;
use \UI\Developer\codeEditor;
use \UI\Forms\templates\simpleForm;
use \UI\Navigation\navigationBar;
use \DEV\Documentation\classDocEditor;
use \APP\Main\manualObject;
use \APP\Main\manualManager;

// Create Application Content
$appContent = new APPContent($appID);
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "phpDocumentor_editorContainer", TRUE);

// Create input php editor
$editorContainer = HTML::select(".phpDocumentor_editor .phpEditor")->item(0);

// Create Editor "Form"
$form = new simpleForm("", TRUE);
$editorForm = $form->build("", FALSE)->engageApp("getDocumentation")->get();
DOM::append($editorContainer, $editorForm);

// Create a navigation toolbar
$navb = new navigationBar();
$navBar = $navb->build($dock = navigationBar::TOP, $editorContainer, $id = "", $class = "")->get();
$form->append($navBar);

// Add play icon
$playTool = DOM::create("button", "", "", "docTool play");
DOM::attr($saveTool, "type", "submit");
$navb->insertToolbarItem($playTool);


// Load document or set for testing
$objectType = engine::getVar("type");
$objectID = engine::getVar("oid");
switch ($objectType)
{
	case "test":
		$phpInput = "// Write your php code here and hit the documentation button.";
		$phpManual = "";
		break;
	case manualManager::ACCOUNT_MODE:
		if (empty($objectID))
		{
			$phpInput = "// Write your php code here and hit the documentation button.";
			$phpManual = "";
		}
		else
		{
			// Initialize object and get content
			$mObject = new manualObject($objectID, manualObject::ACCOUNT_MODE);
			$phpInput = $mObject->getPhpInput();
			$phpManual = $mObject->getManual();
			
			// Get object name
			$mManager = new manualManager(manualManager::ACCOUNT_MODE, $objectID);
			$objectName = $mManager->getName();
		}
		break;
	case manualManager::TEAM_MODE:
		if (empty($objectID))
		{
			$phpInput = "// Write your php code here and hit the documentation button.";
			$phpManual = "";
		}
		else
		{
			// Initialize object and get content
			$mObject = new manualObject($objectID, manualObject::TEAM_MODE);
			$phpInput = $mObject->getPhpInput();
			$phpManual = $mObject->getManual();
			
			// Get object name
			$mManager = new manualManager(manualManager::TEAM_MODE, $objectID);
			$objectName = $mManager->getName();
		}
		break;
}

// Add object id (if not empty)
if (empty($objectID))
{
	// Create new object ID
	$objectID = "pm_man_".time()."_".mt_rand();
	$newObject = TRUE;
	
	// Create hidden input for new object
	$input = $form->getInput($type = "hidden", $name = "new_object", $value = 1, $class = "", $autofocus = FALSE, $required = FALSE);
	$form->append($input);
}

// Add object id hidden input
$input = $form->getInput($type = "hidden", $name = "oid", $value = $objectID, $class = "", $autofocus = FALSE, $required = FALSE);
$form->append($input);

// Add input for object name
switch ($objectType)
{
	case manualManager::ACCOUNT_MODE:
	case manualManager::TEAM_MODE:
		// Add object type
		$input = $form->getInput($type = "hidden", $name = "type", $value = $objectType, $class = "", $autofocus = FALSE, $required = TRUE);
		$form->append($input);
		
		// Add delete item (if not new object)
		if (!$newObject)
		{
			// Add delete icon
			$deleteTool = DOM::create("div", "", "", "docTool delete");
			$navb->insertToolbarItem($deleteTool);
			$attr = array();
			$attr['oid'] = $objectID;
			$attr['type'] = $objectType;
			$actionFactory->setAction($deleteTool, $viewName = "deleteObjectDialog", $holder = "", $attr, $loading = TRUE);
		}
		
		// Add object name
		$input = $form->getInput($type = "text", $name = "oname", $value = $objectName, $class = "toolbar_input", $autofocus = FALSE, $required = TRUE);
		HTML::attr($input, "placeholder", "Object Name");
		$navb->insertToolbarItem($input);
		break;
}

// Build editor
$ce = new codeEditor();
$phpEditor = $ce->build($type = codeEditor::PHP, $phpInput, $name = "phpInput", $editable = TRUE)->get();

// Class Documentor Container
$classDocEditor = new classDocEditor($phpEditor);
$docWrapper = $classDocEditor->build($phpManual, $name = "docManual")->get();
$form->append($docWrapper);

// Return output
return $appContent->getReport();
//#section_end#
?>