<?php
/** @package    Projects::Model */

/** import supporting libraries */
require_once("DAO/LevelCriteriaDAO.php");

/**
 * The LevelCriteria class extends LevelDAOCriteria and is used
 * to query the database for objects and collections
 * 
 * @inheritdocs
 * @package Projects::Model
 * @author ClassBuilder
 * @version 1.0
 */
class LevelCriteria extends LevelCriteriaDAO
{
	
	/**
	 * For custom query logic, you may override OnProcess and set the $this->_where to whatever
	 * sql code is necessary.  If you choose to manually set _where then Phreeze will not touch
	 * your where clause at all and so any of the standard property names will be ignored
	 */
	/*
	function OnPrepare()
	{
		if ($this->MyCustomField == "special value")
		{
			// _where must begin with "where"
			$this->_where = "where db_field ....";
		}
	}
	*/

}
