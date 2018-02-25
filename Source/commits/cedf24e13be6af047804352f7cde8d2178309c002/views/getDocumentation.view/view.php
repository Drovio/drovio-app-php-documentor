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
//#section_end#
//#section#[view]
use \UI\Apps\APPContent;
use \UI\Developer\codeEditor;
use \UI\Presentation\notification;
use \DEV\Documentation\classComments;
use \DEV\Documentation\classDocumentor;

// Create Application Content
$appContent = new APPContent($appID);
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "documentationResultsContainer", TRUE);

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
		
		
		// Create a subpanel for the commented code
		$subpanel = DOM::create("div", "", "", "subpanel");
		$appContent->append($subpanel);
		// Build editor
		$ce = new codeEditor();
		$phpResult = $ce->build($type = codeEditor::PHP, $sourceCode_withComments, $name = "", $editable = TRUE)->get();
		DOM::append($subpanel, $phpResult);
		
		// Create a subpanel for the documentation in xml
		$subpanel = DOM::create("div", "", "", "subpanel");
		$appContent->append($subpanel);
		// Build editor
		$ce = new codeEditor();
		$docResult = $ce->build($type = codeEditor::XML, $docManual, $name = "", $editable = TRUE)->get();
		DOM::append($subpanel, $docResult);
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
}

// Return output
return $appContent->getReport(".phpDocumentor_editor .results");
//#section_end#
?>