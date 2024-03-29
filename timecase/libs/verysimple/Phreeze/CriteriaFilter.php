<?php
/** @package    verysimple::Phreeze */

/** import supporting libraries */


/**
 * CriteriaFilter allows arbitrary filtering based on one or more fields
 *
 * @package    verysimple::Phreeze
 * @author     VerySimple Inc.
 * @copyright  1997-2007 VerySimple, Inc.
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version    2.2
 */
class CriteriaFilter
{
	static $TYPE_SEARCH = 1;
	
	public $propertyNames;
	public $Value;
	public $Type;
	
	/**
	 * 
	 * @param variant $propertyNames comma-delimited string or array of property names
	 * @param string $value criteria value
	 * @param int $type (default CriteriaFilter::TYPE_SEARCH)
	 */
	public function __construct($propertyNames,$value,$type = null)
	{
		$this->PropertyNames = $propertyNames;
		$this->Value = $value;
		$this->Type = ($type == null) ? self::$TYPE_SEARCH : $type;
	}
	
	/**
	 * 
	 * @param unknown_type $criteria
	 * @param unknown_type $whereDelim
	 */
	public function GetWhere($criteria)
	{
		if ($this->Type != self::$TYPE_SEARCH) throw new Exception('Unsupported Filter Type');
		
		// normalize property names as an array
		$propertyNames = (is_array($this->PropertyNames)) ? $this->PropertyNames : explode(',', $this->PropertyNames);
		
		$where = ' (';
		$orDelim = '';
		foreach ($propertyNames as $propName)
		{
			$dbfield = $criteria->GetFieldFromProp($propName);
			$where .= $orDelim . $criteria->Escape($dbfield) ." like '". $criteria->Escape($this->Value) . "'";
			$orDelim = ' or ';
		}
		$where .= ') ';
		
		return $where;
	}
}

?>