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
importer::import("UI", "Presentation");

// Import APP Packages
application::import("Main");
//#section_end#
//#section#[view]
use \UI\Apps\APPContent;
use \UI\Developer\codeEditor;
use \UI\Presentation\notification;
use \UI\Presentation\gridSplitter;
use \DEV\Documentation\classComments;
use \DEV\Documentation\classDocumentor;
use \APP\Main\manualObject;
use \APP\Main\manualManager;

// Create Application Content
$appContent = new APPContent($appID);
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "documentationResultsContainer");

if (engine::isPost())
{
	// Get class items
	$library = $_POST['library'];
	$package = $_POST['package'];
	$namespace = $_POST['namespace'];
	$objectName = $_POST['oname'];
	
	// Get posted manual
	$sourceCode = $_POST['phpInput'];
	$docManual = $_POST['docManual'];
	
	// Validate manual and create code with comments
	if (classDocumentor::isValidDocumentation($docManual))
	{
		$classDocumentor = new classDocumentor();
		$classDocumentor->create($library, $package, $namespace);
		
		// Temporary For updating manual files
		$classDocumentor->structUpdate($library, $package, $namespace);
		$classDocumentor->update($objectName, $docManual);
		$docManual = $classDocumentor->getDoc();
		
		// Strip source code from comments and add new ones
		$strippedCode = classComments::stripSourceCode($sourceCode);
		$sourceCode_withComments = classComments::pretifySourceCode($strippedCode, $docManual, $library = "", $package = "", $namespace = "", $copyright = "PHP Documentor");
		
		
		// Create gridSplitter
		$splitter = new gridSplitter();
		$results_splitter = $splitter->build($orientation = gridSplitter::ORIENT_VER, $layout = gridSplitter::SIDE_BOTTOM, $closed = TRUE, $sideTitle = "Documentation XML Model")->get();
		$appContent->append($results_splitter);
		
		// Build generated documentation, append to main
		$ce = new codeEditor();
		$phpResult = $ce->build($type = codeEditor::PHP, $sourceCode_withComments, $name = "", $editable = TRUE)->get();
		$splitter->appendToMain($phpResult);
		
		// Build documentation model, append to side
		$ce = new codeEditor();
		$docResult = $ce->build($type = codeEditor::XML, $docManual, $name = "", $editable = TRUE)->get();
		$splitter->appendToSide($docResult);
		
		
		
		// Create/Update object
		$objectID = engine::getVar("oid");
		$objectName = engine::getVar("oname");
		$objectType = engine::getVar("type");
		$newObject = engine::getVar("new_object");
		$refreshList = FALSE;
		switch ($objectType)
		{
			case manualManager::ACCOUNT_MODE:
				// Check if to create new object
				if ($newObject)
				{
					$mManager = new manualManager(manualManager::ACCOUNT_MODE);
					$mManager->create($objectName, $objectID);
					
					$refreshList = TRUE;
				}
				else
					$mManager = new manualManager(manualManager::ACCOUNT_MODE, $objectID);
				
				// Update object name
				$currentName = $mManager->getName();
				if (!empty($objectName) && $objectName != $currentName)
				{
					$mManager->update($objectName);
					$refreshList = TRUE;
				}
				
				// Update object content
				$mObject = new manualObject($objectID, manualObject::ACCOUNT_MODE);
				$mObject->update($sourceCode_withComments, $docManual);
				break;
			case manualManager::TEAM_MODE:
				// Check if to create new object
				if ($newObject)
				{
					$mManager = new manualManager(manualManager::TEAM_MODE);
					$mManager->create($objectName, $objectID);
				}
				else
					$mManager = new manualManager(manualManager::TEAM_MODE, $objectID);
				
				// Update object name
				$currentName = $mManager->getName();
				if (!empty($objectName) && $objectName != $currentName)
				{
					$mManager->update($objectName);
					$refreshList = TRUE;
				}
				
				// Update object content
				$mObject = new manualObject($objectID, manualObject::TEAM_MODE);
				$mObject->update($sourceCode_withComments, $docManual);
				break;
		}
	}
	else
	{
		// Add invalid documentation notification
		$reportNtf = new notification();
		$reportNtf->build($type = notification::ERROR, $header = TRUE, $timeout = FALSE, $disposable = FALSE);
		
		$reportMessage = $reportNtf->getMessage("error", "err.save_error");
		$reportNtf->append($reportMessage);
		
		$notification = $reportNtf->get();
		$appContent->append($notification);
	}
	
	// Add refresh action
	if ($refreshList)
		$appContent->addReportAction("objectExplorer.refresh");
}

// Return output
return $appContent->getReport(".phpDocumentor_editor .results");
//#section_end#
?>