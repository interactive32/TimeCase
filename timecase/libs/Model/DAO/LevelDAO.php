<?php
/** @package Projects::Model::DAO */

/** import supporting libraries */
require_once("verysimple/Phreeze/Phreezable.php");
require_once("LevelMap.php");

/**
 * LevelDAO provides object-oriented access to the levels table.
 *
 * @package Projects::Model::DAO
 * @author ClassBuilder
 * @version 1.0
 */
class LevelDAO extends Phreezable
{
	/** @var int */
	public $Id;

	/** @var string */
	public $Name;


	/**
	 * Returns a dataset of Users objects with matching LevelId
	 * @param Criteria
	 * @return DataSet
	 */
	public function GetLevelUserss($criteria = null)
	{
		return $this->_phreezer->GetOneToMany($this, "FK_users_levels", $criteria);
	}


}
