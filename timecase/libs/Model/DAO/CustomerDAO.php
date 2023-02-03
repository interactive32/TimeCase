<?php
/** @package Projects::Model::DAO */

/** import supporting libraries */
require_once("verysimple/Phreeze/Phreezable.php");
require_once("CustomerMap.php");

/**
 * CustomerDAO provides object-oriented access to the customers table.
 *
 * @package Projects::Model::DAO
 * @author ClassBuilder
 * @version 1.0
 */
class CustomerDAO extends Phreezable
{
	/** @var int */
	public $Id;

	/** @var string */
	public $Name;

	/** @var string */
	public $ContactPerson;

	/** @var string */
	public $Email;
	
	/** @var string */
	public $Password;
	
	/** @var int */
	public $AllowLogin;

	/** @var string */
	public $Address;

	/** @var string */
	public $Location;

	/** @var string */
	public $Web;

	/** @var string */
	public $Tel;

	/** @var string */
	public $Tel2;

	/** @var int */
	public $StatusId;

	/** @var string */
	public $Description;


	/**
	 * Returns a dataset of Projects objects with matching CustomerId
	 * @param Criteria
	 * @return DataSet
	 */
	public function GetCustomerProjectss($criteria = null)
	{
		return $this->_phreezer->GetOneToMany($this, "FK_projects_customers", $criteria);
	}

	/**
	 * Returns the foreign object based on the value of StatusId
	 * @return Statuses
	 */
	public function GetStatusStatuses()
	{
		return $this->_phreezer->GetManyToOne($this, "FK_customers_statuses");
	}


}
