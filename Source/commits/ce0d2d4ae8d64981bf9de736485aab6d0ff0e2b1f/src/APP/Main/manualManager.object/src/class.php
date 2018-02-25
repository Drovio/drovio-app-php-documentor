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

use \AEL\Resources\DOMParser;
use \AEL\Resources\filesystem\fileManager;

/**
 * Manual Object Library Manager
 * 
 * Manages all objects in the library index.
 * 
 * @version	0.1-1
 * @created	June 10, 2015, 13:22 (EEST)
 * @updated	June 10, 2015, 13:22 (EEST)
 */
class manualManager
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
	 * The index xml parser manager.
	 * 
	 * @type	DOMParser
	 */
	private $parser;
	
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
	 * Initialize the library object manager.
	 * 
	 * @param	string	$mode
	 * 		The library mode.
	 * 		See class constants for selection.
	 * 		Default value is account mode.
	 * 
	 * @param	string	$objectID
	 * 		The object id to manage in the library.
	 * 		Leave empty to create new objects or read all objects.
	 * 		It is empty by default.
	 * 
	 * @return	void
	 */
	public function __construct($mode = self::ACCOUNT_MODE, $objectID = "")
	{
		// Set manual mode
		$mode = ($mode == self::ACCOUNT_MODE ? $mode : self::TEAM_MODE);
		$this->mode = $mode;
		
		// Initialize parser
		$parser_mode = ($this->mode == self::ACCOUNT_MODE ? DOMParser::ACCOUNT_MODE : DOMParser::TEAM_MODE);
		$this->parser = new DOMParser($parser_mode, $shared = FALSE);
		
		// Load library index
		$this->loadIndex();
		
		// Set object id
		$this->objectID = $objectID;
	}
	
	/**
	 * Load library index or create if doesn't exist.
	 * 
	 * @return	void
	 */
	private function loadIndex()
	{
		// Get parser
		$parser = $this->parser;
		
		try
		{
			$status = $parser->load("/index.xml");
			if (!$status)
				$create = TRUE;
		}
		catch (Exception $ex)
		{
			$create = TRUE;
		}
		
		// Check and create file
		if ($create)
		{
			// Create file
			$fm_mode = ($this->mode == self::ACCOUNT_MODE ? fileManager::ACCOUNT_MODE : fileManager::TEAM_MODE);
			$fm = new fileManager($fm_mode);
			$fm->create("/index.xml", "");
			
			// Update index file
			$root = $parser->create("phpmans");
			$parser->append($root);
			$parser->save("/index.xml");
		}
	}
	
	/**
	 * Create a new object in the library index.
	 * 
	 * @param	string	$name
	 * 		The object name.
	 * 
	 * @param	string	$objectID
	 * 		The object unique id.
	 * 
	 * @return	boolean
	 * 		True on success, false on failure or if an object with the same id exists.
	 */
	public function create($name, $objectID = "")
	{
		// Check if parser is initialized
		if (empty($this->parser))
			return FALSE;
		
		// Get parser
		$parser = $this->parser;
		
		// Randomize object id if empty
		$objectID = (empty($objectID) ? "php_man_".mt_rand() : $objectID);
		
		// Check if there is an object with the same id
		$entry = $parser->find($objectID);
		if (!empty($entry))
			return FALSE;
		
		// Add library entry
		$parser = $this->parser;
		$root = $parser->evaluate("//phpmans")->item(0);
		
		// Create entry
		$entry = $parser->create("pman", "", $objectID);
		$parser->attr($entry, "name", $name);
		$parser->append($root, $entry);
		
		// Update file
		return $parser->update();
	}
	
	/**
	 * Get the current object's name.
	 * 
	 * @return	string
	 * 		The object name.
	 */
	public function getName()
	{
		// Check if parser is initialized
		if (empty($this->parser))
			return FALSE;
		
		// Get parser
		$parser = $this->parser;
		
		// Find the entry in the file
		$entry = $parser->find($this->objectID);
		if (empty($entry))
			return FALSE;
		
		// Return name
		return $parser->attr($entry, "name");
	}
	
	/**
	 * Update the current object's name.
	 * 
	 * @param	string	$name
	 * 		The new object's name.
	 * 
	 * @return	boolean
	 * 		True on success, false on failure.
	 */
	public function update($name)
	{
		// Check if parser is initialized
		if (empty($this->parser))
			return FALSE;
		
		// Get parser
		$parser = $this->parser;
		
		// Find the entry in the file
		$entry = $parser->find($this->objectID);
		if (empty($entry))
			return FALSE;
		
		// Update entry name
		$parser->attr($entry, "name", $name);
		
		// Update file
		return $parser->update();
	}
	
	/**
	 * Remove the current object from the library index.
	 * 
	 * @return	boolean
	 * 		True on success, false on failure.
	 */
	public function remove()
	{
		// Check if parser is initialized
		if (empty($this->parser))
			return FALSE;
		
		// Get parser
		$parser = $this->parser;
		
		// Find the entry in the file
		$entry = $parser->find($this->objectID);
		if (empty($entry))
			return FALSE;
		
		// Replace entry with null (remove)
		$parser->replace($entry, NULL);
		
		// Update file
		return $parser->update();
	}
	
	/**
	 * Get all library objects by id and name.
	 * 
	 * @return	array
	 * 		An array of all library objects.
	 * 		[id] => [name].
	 */
	public function getObjectList()
	{
		// Initialize object list
		$objectList = array();
		
		// Check if parser is initialized
		if (empty($this->parser))
			return $objectList;
		
		// Get parser
		$parser = $this->parser;
		
		// Get objects
		$pmans = $parser->evaluate("//pman");
		foreach ($pmans as $pm)
		{
			$objectID = $parser->attr($pm, "id");
			$objectName = $parser->attr($pm, "name");
			$objectList[$objectID] = $objectName;
		}
		
		// Return object list
		return $objectList;
	}
}
//#section_end#
?>