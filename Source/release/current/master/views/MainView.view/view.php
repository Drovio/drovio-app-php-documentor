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
importer::import("UI", "Apps");
importer::import("UI", "Presentation");

// Import APP Packages
//#section_end#
//#section#[view]
use \UI\Apps\APPContent;
use \UI\Presentation\gridSplitter;

// Create Application Content
$appContent = new APPContent($appID);
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "phpDocumentorMainContainer", TRUE);

// Create input php editor
$editorsContainer = HTML::select(".phpDocumentorWrapper .editorsContainer")->item(0);

// Create grid splitter
$splitter = new gridSplitter();
$mainSplitter = $splitter->build($orientation = gridSplitter::ORIENT_HOZ, $layout = gridSplitter::SIDE_LEFT, $closed = FALSE, $sideTitle = "Object Explorer")->get();
DOM::append($editorsContainer, $mainSplitter);

// Create object explorer
$viewContainer = $appContent->getAppViewContainer($viewName = "objectExplorer", $attr = "", $startup = TRUE, $containerID = "objectExplorerContainer", $loading = TRUE, $preload = TRUE);
$splitter->appendToSide($viewContainer);

// Create code editor container
$viewContainer = DOM::create("div", "", "", "codeEditorContainer");
$splitter->appendToMain($viewContainer);

// Add about view action
$aboutItem = HTML::select(".phpDocumentorWrapper .navmenu .navitem.about")->item(0);
$actionFactory->setAction($aboutItem, $viewName = "/info/aboutView", $holder = "", $attr = array(), $loading = TRUE);

// Return output
return $appContent->getReport();
//#section_end#
?>