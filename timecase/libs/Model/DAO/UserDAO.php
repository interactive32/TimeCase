<?php
/** @package Projects::Model::DAO */

/** import supporting libraries */
require_once("verysimple/Phreeze/Phreezable.php");
require_once("UserMap.php");

/**
 * UserDAO provides object-oriented access to the users table.
 *
 * @package Projects::Model::DAO
 * @author ClassBuilder
 * @version 1.0
 */
class UserDAO extends Phreezable
{
	/** @var int */
	public $Id;

	/** @var string */
	public $Username;

	/** @var int */
	public $LevelId;

	/** @var string */
	public $FullName;

	/** @var string */
	public $Email;

	/** @var string */
	public $Password;
	
	/** @var string */
	public $Details;
	
	/** @var int */
	public $CurrentProject;
	
	/** @var int */
	public $CurrentCategory;
	
	/** @var timestamp */
	public $Timer;



	/**
	 * Returns a dataset of TimeEntries objects with matching UserId
	 * @param Criteria
	 * @return DataSet
	 */
	public function GetUserTimeEntriess($criteria = null)
	{
		return $this->_phreezer->GetOneToMany($this, "FK_time_entries_users", $criteria);
	}

	/**
	 * Returns the foreign object based on the value of LevelId
	 * @return Levels
	 */
	public function GetLevelLevels()
	{
		return $this->_phreezer->GetManyToOne($this, "FK_users_levels");
	}



}
