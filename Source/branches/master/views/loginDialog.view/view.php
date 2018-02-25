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
importer::import("UI", "Login");

// Import APP Packages
//#section_end#
//#section#[view]
use \UI\Login\loginDialog;

// Create the dialog
$lg = new loginDialog();

// The dialog follows the UIObjectPrototype, so we build
// We can set a predefined username and a return url if needed
// We set the login type to be from application in order to refresh the page afterwards
$lg->build($username = "", $logintype = loginDialog::LGN_TYPE_APP, $return_url = "");

// This is supposed to be a view that will return the dialog,
// so we return the report
return $lg->getReport();
//#section_end#
?>