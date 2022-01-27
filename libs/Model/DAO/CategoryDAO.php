<?php
/** @package Projects::Model::DAO */

/** import supporting libraries */
require_once("verysimple/Phreeze/Phreezable.php");
require_once("CategoryMap.php");

/**
 * CategoryDAO provides object-oriented access to the categories table. 
 *
 * @package Projects::Model::DAO
 * @author ClassBuilder
 * @version 1.0
 */
class CategoryDAO extends Phreezable
{
	/** @var int */
	public $Id;

	/** @var string */
	public $Name;


	/**
	 * Returns a dataset of TimeEntries objects with matching CategoryId
	 * @param Criteria
	 * @return DataSet
	 */
	public function GetCategoryTimeEntriess($criteria = null)
	{
		return $this->_phreezer->GetOneToMany($this, "FK_time_entries_categories", $criteria);
	}


}
