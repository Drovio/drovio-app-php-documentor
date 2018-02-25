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
importer::import("AEL", "Resources");
importer::import("API", "Profile");
importer::import("UI", "Apps");

// Import APP Packages
application::import("Main");
//#section_end#
//#section#[view]
use \AEL\Resources\DOMParser;
use \API\Profile\account;
use \API\Profile\team;
use \UI\Apps\APPContent;
use \APP\Main\manualManager;

// Create Application Content
$appContent = new APPContent($appID);
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "phpDocumentor_objectExplorerContainer", TRUE);

// Add action to test object
$testItem = HTML::select(".objectExplorer .listContainer.test .listitem.test")->item(0);
$attr = array();
$attr['type'] = "test";
$actionFactory->setAction($testItem, $viewName = "editorContainer", $holder = ".codeEditorContainer", $attr, $loading = TRUE);

// Check if there is an account
if (account::validate())
{
	// Remove no account container
	$noAccount = HTML::select(".objectExplorer .noAccount")->item(0);
	HTML::replace($noAccount, NULL);
	
	// Add action to test object
	$newItem = HTML::select(".objectExplorer .listContainer.account .listitem.new")->item(0);
	$attr = array();
	$attr['type'] = manualManager::ACCOUNT_MODE;
	$actionFactory->setAction($newItem, $viewName = "editorContainer", $holder = ".codeEditorContainer", $attr, $loading = TRUE);
	
	// List all account objects
	$listContainer = HTML::select(".objectExplorer .listContainer.account .list")->item(0);
	$manManager = new manualManager($mode = manualManager::ACCOUNT_MODE);
	$objectList = $manManager->getObjectList();
	foreach ($objectList as $objectID => $objectName)
	{
		// Create list item
		$listitem = DOM::create("div", $objectName, $objectID, "listitem");
		DOM::append($listContainer, $listitem);
		
		// Set list item action
		$attr = array();
		$attr['type'] = manualManager::ACCOUNT_MODE;
		$attr['oid'] = $objectID;
		$actionFactory->setAction($listitem, $viewName = "editorContainer", $holder = ".codeEditorContainer", $attr, $loading = TRUE);
	}
	
	// Check for boss environment
	if (!application::onBOSS())
	{
		$listContainer = HTML::select(".objectExplorer .listContainer.team")->item(0);
		HTML::replace($listContainer, NULL);
	}
	else if (team::validate())
	{
		// Add action to test object
		$newItem = HTML::select(".objectExplorer .listContainer.team .listitem.new")->item(0);
		$attr = array();
		$attr['type'] = manualManager::TEAM_MODE;
		$actionFactory->setAction($newItem, $viewName = "editorContainer", $holder = ".codeEditorContainer", $attr, $loading = TRUE);
	
		$listContainer = HTML::select(".objectExplorer .listContainer.team .list")->item(0);
		$manManager = new manualManager($mode = manualManager::TEAM_MODE);
		$objectList = $manManager->getObjectList();
		foreach ($objectList as $objectID => $objectName)
		{
			// Create list item
			$listitem = DOM::create("div", $objectName, $objectID, "listitem");
			DOM::append($listContainer, $listitem);
			
			// Set list item action
			$attr = array();
			$attr['type'] = manualManager::TEAM_MODE;
			$attr['oid'] = $objectID;
			$actionFactory->setAction($listitem, $viewName = "editorContainer", $holder = ".codeEditorContainer", $attr, $loading = TRUE);
		}
	}
}
else
{
	// Remove lists
	$listContainer = HTML::select(".objectExplorer .listContainer.account")->item(0);
	HTML::replace($listContainer, NULL);
	$listContainer = HTML::select(".objectExplorer .listContainer.team")->item(0);
	HTML::replace($listContainer, NULL);
	
	// Add action to login button
	$loginButton = HTML::select(".objectExplorer .noAccount .lbutton")->item(0);
	$actionFactory->setAction($loginButton, $viewName = "loginDialog", $holder = "", $attr = array(), $loading = FALSE);
}

// Return output
return $appContent->getReport();
//#section_end#
?>