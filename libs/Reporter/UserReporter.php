<?php
/** @package    Projects::Model::DAO */

/** import supporting libraries */
require_once("verysimple/Phreeze/Reporter.php");

/**
 * This is an example Reporter based on the User object.  The reporter object
 * allows you to run arbitrary queries that return data which may or may not fith within
 * the data access API.  This can include aggregate data or subsets of data.
 *
 * Note that Reporters are read-only and cannot be used for saving data.
 *
 * @package Projects::Model::DAO
 * @author ClassBuilder
 * @version 1.0
 */
class UserReporter extends Reporter
{

	// the properties in this class must match the columns returned by GetCustomQuery().
	// 'CustomFieldExample' is an example that is not part of the `users` table
	public $LevelName;

	public $Id;
	public $Username;
	public $LevelId;
	public $FullName;
	public $Email;


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
			levels.name as LevelName
			,`users`.`id` as Id
			,`users`.`username` as Username
			,`users`.`level_id` as LevelId
			,`users`.`full_name` as FullName
			,`users`.`email` as Email
		from `users`
		inner join levels on levels.id = users.level_id";

		// the criteria can be used or you can write your own custom logic.
		// be sure to escape any user input with $criteria->Escape()
		$sql .= $criteria->GetWhere();
		$sql .= $criteria->GetOrder();

		return $sql;
	}
}

