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
//#section_end#
//#section#[view]
use \UI\Apps\APPContent;
use \UI\Developer\codeEditor;
use \UI\Forms\templates\simpleForm;
use \UI\Navigation\navigationBar;
use \DEV\Documentation\classDocEditor;

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

// Build editor
$ce = new codeEditor();
$phpEditor = $ce->build($type = codeEditor::PHP, $content = "", $name = "phpInput", $editable = TRUE)->get();

// Class Documentor Container
$classDocEditor = new classDocEditor($phpEditor);
$docWrapper = $classDocEditor->build($manual = "", $name = "docManual")->get();
$form->append($docWrapper);

// Return output
return $appContent->getReport();
//#section_end#
?>