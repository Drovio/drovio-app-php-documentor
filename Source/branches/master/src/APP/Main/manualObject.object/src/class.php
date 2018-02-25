<?php
//#section#[header]
// Namespace
namespace APP\Main;

require_once($_SERVER['DOCUMENT_ROOT'].'/_domainConfig.php');

// Use Important Headers
use \API\Platform\importer;
use \Exception;

// Check Platform Existance
if (!defined('_RB_PLATFORM_')) throw new Exception("Platform is not defined!");

// Import application loader
importer::import("AEL", "Platform", "application");
use \AEL\Platform\application;
//#section_end#
//#section#[class]
/**
 * @library	APP
 * @package	Main
 * 
 * @copyright	Copyright (C) 2015 PHPDocumentor. All rights reserved.
 */

importer::import("AEL", "Resources", "DOMParser");
importer::import("AEL", "Resources", "filesystem/fileManager");
importer::import("AEL", "Resources", "filesystem/folderManager");

use \AEL\Resources\DOMParser;
use \AEL\Resources\filesystem\fileManager;
use \AEL\Resources\filesystem\folderManager;

/**
 * Manual Object
 * 
 * Handles a manual full object including php code and manual xml.
 * 
 * @version	0.1-1
 * @created	June 10, 2015, 13:38 (EEST)
 * @updated	June 10, 2015, 13:38 (EEST)
 */
class manualObject
{
	/**
	 * The account mode for the library manager.
	 * 
	 * @type	string
	 */
	const ACCOUNT_MODE = "account";
	
	/**
	 * The team mode for the library manager.
	 * 
	 * @type	string
	 */
	const TEAM_MODE = "team";
	
	/**
	 * The object file manager.
	 * 
	 * @type	fileManager
	 */
	private $fm;
	
	/**
	 * The object folder manager.
	 * 
	 * @type	folderManager
	 */
	private $fdm;
	
	/**
	 * The current instance mode.
	 * 
	 * @type	string
	 */
	private $mode;
	
	/**
	 * The object id.
	 * 
	 * @type	string
	 */
	private $objectID = "";
	
	/**
	 * Initialize manual object.
	 * 
	 * @param	string	$objectID
	 * 		The object id.
	 * 
	 * @param	string	$mode
	 * 		The object mode.
	 * 		See class constants for selection.
	 * 		Default value is account mode.
	 * 
	 * @return	void
	 */
	public function __construct($objectID, $mode = self::ACCOUNT_MODE)
	{
		// Set manual mode
		$mode = ($mode == self::ACCOUNT_MODE ? $mode : self::TEAM_MODE);
		$this->mode = $mode;
		
		// Initialize filemanager
		$fm_mode = ($this->mode == self::ACCOUNT_MODE ? fileManager::ACCOUNT_MODE : fileManager::TEAM_MODE);
		$this->fm = new fileManager($fm_mode);
		
		$fdm_mode = ($this->mode == self::ACCOUNT_MODE ? folderManager::ACCOUNT_MODE : folderManager::TEAM_MODE);
		$this->fdm = new folderManager($fdm_mode);
		
		// Set object id
		$this->objectID = $objectID;
	}
	
	/**
	 * Update the object's php and manual
	 * 
	 * @param	string	$phpInput
	 * 		The php code.
	 * 
	 * @param	string	$manual
	 * 		The manual xml.
	 * 
	 * @return	boolean
	 * 		True on success, false on failure.
	 */
	public function update($phpInput, $manual)
	{
		// Initialize file manger
		$fm = $this->fm;
		
		// Create php file
		$filePath = $this->getObjectFolder()."/code.php";
		$fm->create($filePath, $phpInput, $recursive = TRUE);
		
		// Create manual file
		$filePath = $this->getObjectFolder()."/manual.xml";
		$fm->create($filePath, $manual, $recursive = TRUE);
		
		return TRUE;
	}
	
	/**
	 * Get the object's php code.
	 * 
	 * @return	string
	 * 		The generated object's php code.
	 */
	public function getPhpInput()
	{
		// Initialize file manger
		$fm = $this->fm;
		
		// Read file and return
		$filePath = $this->getObjectFolder()."/code.php";
		return $fm->get($filePath);
	}
	
	/**
	 * Get the object's manual generated xml code.
	 * 
	 * @return	string
	 * 		The manual xml.
	 */
	public function getManual()
	{
		// Initialize file manger
		$fm = $this->fm;
		
		// Read file and return
		$filePath = $this->getObjectFolder()."/manual.xml";
		return $fm->get($filePath);
	}
	
	/**
	 * Remove the object's files from the app.
	 * 
	 * @return	boolean
	 * 		True on success, false on failure.
	 */
	public function remove()
	{
		// Initialize folder manger
		$fdm = $this->fdm;
		
		// Remove entire object folder
		$objectFolder = $this->getObjectFolder();
		return $fdm->remove($objectFolder, $name = "", $recursive = TRUE);
	}
	
	/**
	 * Get the object's folder name based on the object id.
	 * 
	 * @return	string
	 * 		The object's folder name.
	 */
	private function getObjectFolder()
	{
		return "pm_".$this->objectID.".object";
	}
}
//#section_end#
?>