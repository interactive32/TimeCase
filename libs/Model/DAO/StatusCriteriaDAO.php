<?php
/** @package    Projects::Model::DAO */

/** import supporting libraries */
require_once("verysimple/Phreeze/Criteria.php");

/**
 * StatusCriteria allows custom querying for the Status object.
 *
 * @inheritdocs
 * @package Projects::Model::DAO
 * @author ClassBuilder
 * @version 1.0
 */
class StatusCriteriaDAO extends Criteria
{

	public $Id_Equals;
	public $Id_NotEquals;
	public $Id_IsLike;
	public $Id_IsNotLike;
	public $Id_BeginsWith;
	public $Id_EndWith;
	public $Id_GreaterThan;
	public $Id_GreaterThanOrEqual;
	public $Id_LessThan;
	public $Id_LessThanOrEqual;
	public $Id_In;
	public $Id_IsNotEmpty;
	public $Id_IsEmpty;
	public $Id_BitwiseOr;
	public $Id_BitwiseAnd;
	public $Description_Equals;
	public $Description_NotEquals;
	public $Description_IsLike;
	public $Description_IsNotLike;
	public $Description_BeginsWith;
	public $Description_EndWith;
	public $Description_GreaterThan;
	public $Description_GreaterThanOrEqual;
	public $Description_LessThan;
	public $Description_LessThanOrEqual;
	public $Description_In;
	public $Description_IsNotEmpty;
	public $Description_IsEmpty;
	public $Description_BitwiseOr;
	public $Description_BitwiseAnd;

}

