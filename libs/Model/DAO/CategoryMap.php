<?php
/** @package    Projects::Model::DAO */

/** import supporting libraries */
require_once("verysimple/Phreeze/IDaoMap.php");

/**
 * CategoryMap is a static class with functions used to get FieldMap and KeyMap information that
 * is used by Phreeze to map the CategoryDAO to the categories datastore.
 *
 * @package Projects::Model::DAO
 * @author ClassBuilder
 * @version 1.0
 */
class CategoryMap implements IDaoMap
{
	/**
	 * Returns a singleton array of FieldMaps for the Category object
	 *
	 * @access public
	 * @return array of FieldMaps
	 */
	public static function GetFieldMaps()
	{
		static $fm = null;
		if ($fm == null)
		{
			$fm = Array();
			$fm["Id"] = new FieldMap("Id","categories","id",true,FM_TYPE_INT,10,null,true);
			$fm["Name"] = new FieldMap("Name","categories","name",false,FM_TYPE_VARCHAR,255,null,false);
		}
		return $fm;
	}

	/**
	 * Returns a singleton array of KeyMaps for the Category object
	 *
	 * @access public
	 * @return array of KeyMaps
	 */
	public static function GetKeyMaps()
	{
		static $km = null;
		if ($km == null)
		{
			$km = Array();
			$km["FK_time_entries_categories"] = new KeyMap("FK_time_entries_categories", "Id", "TimeEntries", "CategoryId", KM_TYPE_ONETOMANY, KM_LOAD_LAZY);  // use KM_LOAD_EAGER with caution here (one-to-one relationships only)
		}
		return $km;
	}

}

