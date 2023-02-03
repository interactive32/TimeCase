<?php
/** @package    Projects::Model::DAO */

/** import supporting libraries */
require_once("verysimple/Phreeze/IDaoMap.php");

/**
 * LevelMap is a static class with functions used to get FieldMap and KeyMap information that
 * is used by Phreeze to map the LevelDAO to the levels datastore.
 *
 * @package Projects::Model::DAO
 * @author ClassBuilder
 * @version 1.0
 */
class LevelMap implements IDaoMap
{
	/**
	 * Returns a singleton array of FieldMaps for the Level object
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
			$fm["Id"] = new FieldMap("Id","levels","id",true,FM_TYPE_INT,10,null,null);
			$fm["Name"] = new FieldMap("Name","levels","name",false,FM_TYPE_VARCHAR,255,null,false);
		}
		return $fm;
	}

	/**
	 * Returns a singleton array of KeyMaps for the Level object
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
			$km["FK_users_levels"] = new KeyMap("FK_users_levels", "Id", "Users", "LevelId", KM_TYPE_ONETOMANY, KM_LOAD_LAZY);  // use KM_LOAD_EAGER with caution here (one-to-one relationships only)
		}
		return $km;
	}

}

