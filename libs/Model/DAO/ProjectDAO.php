<?php
/** @package Projects::Model::DAO */

/** import supporting libraries */
require_once("verysimple/Phreeze/Phreezable.php");
require_once("ProjectMap.php");

/**
 * ProjectDAO provides object-oriented access to the projects table.
 *
 * @package Projects::Model::DAO
 * @author ClassBuilder
 * @version 1.0
 */
class ProjectDAO extends Phreezable
{
	/** @var int */
	public $Id;

	/** @var string */
	public $Title;

	/** @var int */
	public $CustomerId;

	/** @var timestamp */
	public $Created;

	/** @var timestamp */
	public $Closed;

	/** @var timestamp */
	public $Deadline;

	/** @var int */
	public $Progress;

	/** @var int */
	public $StatusId;

	/** @var string */
	public $Description;


	/**
	 * Returns a dataset of ProjectDocuments objects with matching ProjectId
	 * @param Criteria
	 * @return DataSet
	 */
	public function GetProjectProjectDocumentss($criteria = null)
	{
		return $this->_phreezer->GetOneToMany($this, "FK_project_documents_projects", $criteria);
	}

	/**
	 * Returns a dataset of TimeEntries objects with matching ProjectId
	 * @param Criteria
	 * @return DataSet
	 */
	public function GetProjectTimeEntriess($criteria = null)
	{
		return $this->_phreezer->GetOneToMany($this, "FK_time_entries_projects", $criteria);
	}

	/**
	 * Returns the foreign object based on the value of CustomerId
	 * @return Customers
	 */
	public function GetCustomerCustomers()
	{
		return $this->_phreezer->GetManyToOne($this, "FK_projects_customers");
	}

	/**
	 * Returns the foreign object based on the value of StatusId
	 * @return Statuses
	 */
	public function GetStatusStatuses()
	{
		return $this->_phreezer->GetManyToOne($this, "FK_projects_statuses");
	}


}
