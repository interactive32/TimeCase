<?php
/** @package Projects::Model::DAO */

/** import supporting libraries */
require_once("verysimple/Phreeze/Phreezable.php");
require_once("StatusMap.php");

/**
 * StatusDAO provides object-oriented access to the statuses table.
 *
 * @package Projects::Model::DAO
 * @author ClassBuilder
 * @version 1.0
 */
class StatusDAO extends Phreezable
{
	/** @var int */
	public $Id;

	/** @var string */
	public $Description;


	/**
	 * Returns a dataset of Customers objects with matching StatusId
	 * @param Criteria
	 * @return DataSet
	 */
	public function GetStatusCustomerss($criteria = null)
	{
		return $this->_phreezer->GetOneToMany($this, "FK_customers_statuses", $criteria);
	}

	/**
	 * Returns a dataset of Projects objects with matching StatusId
	 * @param Criteria
	 * @return DataSet
	 */
	public function GetStatusProjectss($criteria = null)
	{
		return $this->_phreezer->GetOneToMany($this, "FK_projects_statuses", $criteria);
	}

	/**
	 * Returns a dataset of Users objects with matching StatusId
	 * @param Criteria
	 * @return DataSet
	 */
	public function GetStatusUserss($criteria = null)
	{
		return $this->_phreezer->GetOneToMany($this, "FK_users_statuses", $criteria);
	}


}
