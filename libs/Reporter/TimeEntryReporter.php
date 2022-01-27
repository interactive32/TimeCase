<?php
/** @package    Projects::Model::DAO */

/** import supporting libraries */
require_once("verysimple/Phreeze/Reporter.php");
require_once("util/common.php");

/**
 * This is an example Reporter based on the TimeEntry object.  The reporter object
 * allows you to run arbitrary queries that return data which may or may not fith within
 * the data access API.  This can include aggregate data or subsets of data.
 *
 * Note that Reporters are read-only and cannot be used for saving data.
 *
 * @package Projects::Model::DAO
 * @author ClassBuilder
 * @version 1.0
 */
class TimeEntryReporter extends Reporter
{

	// the properties in this class must match the columns returned by GetCustomQuery().
	// 'CustomFieldExample' is an example that is not part of the `time_entries` table
	public $CategoryName;
	public $UserName;
	public $ProjectTitle;
	public $CustomerId;
	public $CustomerName;
	public $Duration;
	public $DurationFormatted;
	public $Location;

	public $Id;
	public $ProjectId;
	public $UserId;
	public $CategoryId;
	public $Start;
	public $End;
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
		if (isset($criteria->totalDurationOnly) && $criteria->totalDurationOnly == true){
			
			$sql = "select sum(TIMESTAMPDIFF(MINUTE, time_entries.start, time_entries.end)) as Duration
			from `time_entries`
			inner join categories on categories.id = time_entries.category_id
			inner join users on users.id = time_entries.user_id
			inner join projects on projects.id = time_entries.project_id
			inner join customers on customers.id = projects.customer_id";

			
		}else{
			
			$sql = "select
			categories.name as CategoryName,
			users.username as UserName,
			projects.title as ProjectTitle,
			customers.id as CustomerId,
			customers.name as CustomerName,
			time_entries.location as Location
			,`time_entries`.`id` as Id
			,`time_entries`.`project_id` as ProjectId
			,`time_entries`.`user_id` as UserId
			,`time_entries`.`category_id` as CategoryId
			,`time_entries`.`start` as Start
			,`time_entries`.`end` as End
		
			, TIMESTAMPDIFF(MINUTE, time_entries.start, time_entries.end) as Duration
			
			,`time_entries`.`description` as Description
		from `time_entries`
		inner join categories on categories.id = time_entries.category_id
		inner join users on users.id = time_entries.user_id
		inner join projects on projects.id = time_entries.project_id
		inner join customers on customers.id = projects.customer_id";
			
		}


		$classicWhere = $criteria->GetWhere();
		
		$sql .= $classicWhere;

		// custom where logic
		$where_delim = ($classicWhere ? ' and ' : ' where ');
		if (isset($criteria->Special))
			foreach ($criteria->Special as $key => $val){
		
			$sql .= $where_delim . "(" .$criteria->Escape($key) . " like '" . $criteria->Escape($val) ."') ";
			$where_delim = ' and ';
		}
		
		$sql .= $criteria->GetOrder();

		return $sql;
	}
	
	
	/**
	 * Loads the object with data given in the row array.
	 *
	 * @access     public
	 * @param      Array $row
	 */
	function Load(&$row)
	{
		$this->_phreezer->Observe("Loading " . get_class($this),OBSERVE_DEBUG);
	
		foreach (array_keys($row) as $prop)
		{
			if ($prop == 'Duration'){
				
				$this->Duration = $row['Duration'];
				$this->DurationFormatted = common::formatDuration($this->Duration);
				
			}else{
				$this->$prop = $row[$prop];
			}
			
		}
	
		$this->OnLoad();
	}
	
}

