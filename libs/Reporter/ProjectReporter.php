<?php
/** @package    Projects::Model::DAO */

/** import supporting libraries */
require_once("verysimple/Phreeze/Reporter.php");

/**
 * This is an example Reporter based on the Project object.  The reporter object
 * allows you to run arbitrary queries that return data which may or may not fith within
 * the data access API.  This can include aggregate data or subsets of data.
 *
 * Note that Reporters are read-only and cannot be used for saving data.
 *
 * @package Projects::Model::DAO
 * @author ClassBuilder
 * @version 1.0
 */
class ProjectReporter extends Reporter
{

	// the properties in this class must match the columns returned by GetCustomQuery().
	// 'CustomFieldExample' is an example that is not part of the `projects` table
	public $StatusDescription;
	public $CustomerName;

	public $Id;
	public $Title;
	public $CustomerId;
	public $Created;
	public $Closed;
	public $Deadline;
	public $Progress;
	public $StatusId;
	public $Description;

	/*
	* GetCustomQuery returns a fully formed SQL statement.  The result columns
	* must match with the properties of this reporter object.
	*
	* @param Criteria $criteria
	* @return string SQL statement
	*/
	static function GetCustomQuery($criteria)
	{
		$sql = "select
			statuses.description as StatusDescription,
			customers.name as CustomerName
			,`projects`.`id` as Id
			,`projects`.`title` as Title
			,`projects`.`customer_id` as CustomerId
			,`projects`.`created` as Created
			,`projects`.`closed` as Closed
			,`projects`.`deadline` as Deadline
			,`projects`.`progress` as Progress
			,`projects`.`status_id` as StatusId
			,`projects`.`description` as Description
		from `projects`
		inner join statuses on statuses.id = projects.status_id
		inner join customers on customers.id = projects.customer_id";

		// the criteria can be used or you can write your own custom logic.
		// be sure to escape any user input with $criteria->Escape()
		$sql .= $criteria->GetWhere();
		$sql .= $criteria->GetOrder();

		return $sql;
	}
}

