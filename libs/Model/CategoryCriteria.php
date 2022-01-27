<?php
/** @package    Projects::Model */

/** import supporting libraries */
require_once("DAO/CategoryCriteriaDAO.php");

/**
 * The CategoryCriteria class extends CategoryDAOCriteria and is used
 * to query the database for objects and collections
 * 
 * @inheritdocs
 * @package Projects::Model
 * @author ClassBuilder
 * @version 1.0
 */
class CategoryCriteria extends CategoryCriteriaDAO
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
