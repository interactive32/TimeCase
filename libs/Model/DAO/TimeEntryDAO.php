<?php
/** @package Projects::Model::DAO */

/** import supporting libraries */
require_once("verysimple/Phreeze/Phreezable.php");
require_once("TimeEntryMap.php");

/**
 * TimeEntryDAO provides object-oriented access to the time_entries table.
 *
 * @package Projects::Model::DAO
 * @author ClassBuilder
 * @version 1.0
 */
class TimeEntryDAO extends Phreezable
{
	/** @var int */
	public $Id;

	/** @var int */
	public $ProjectId;

	/** @var int */
	public $UserId;

	/** @var int */
	public $CategoryId;

	/** @var timestamp */
	public $Start;

	/** @var timestamp */
	public $End;

	/** @var string */
	public $Description;
	
	/** @var string */
	public $Location;
	

	/**
	 * Returns the foreign object based on the value of CategoryId
	 * @return Categories
	 */
	public function GetCategoryCategories()
	{
		return $this->_phreezer->GetManyToOne($this, "FK_time_entries_categories");
	}

	/**
	 * Returns the foreign object based on the value of ProjectId
	 * @return Projects
	 */
	public function GetProjectProjects()
	{
		return $this->_phreezer->GetManyToOne($this, "FK_time_entries_projects");
	}

	/**
	 * Returns the foreign object based on the value of UserId
	 * @return Users
	 */
	public function GetUserUsers()
	{
		return $this->_phreezer->GetManyToOne($this, "FK_time_entries_users");
	}


}
