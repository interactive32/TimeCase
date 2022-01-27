<?php
/** @package    Projects::Model::DAO */

/** import supporting libraries */
require_once("verysimple/Phreeze/IDaoMap.php");

/**
 * UserMap is a static class with functions used to get FieldMap and KeyMap information that
 * is used by Phreeze to map the UserDAO to the users datastore.
 *
 * @package Projects::Model::DAO
 * @author ClassBuilder
 * @version 1.0
 */
class UserMap implements IDaoMap
{
	/**
	 * Returns a singleton array of FieldMaps for the User object
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
			$fm["Id"] = new FieldMap("Id","users","id",true,FM_TYPE_INT,10,null,true);
			$fm["Username"] = new FieldMap("Username","users","username",false,FM_TYPE_VARCHAR,50,true,false);
			$fm["LevelId"] = new FieldMap("LevelId","users","level_id",false,FM_TYPE_INT,10,null,false);
			$fm["FullName"] = new FieldMap("FullName","users","full_name",false,FM_TYPE_VARCHAR,255,null,false);
			$fm["Email"] = new FieldMap("Email","users","email",false,FM_TYPE_VARCHAR,1000,null,false);
			$fm["Password"] = new FieldMap("Password","users","password",false,FM_TYPE_VARCHAR,1000,null,false);
			$fm["Details"] = new FieldMap("Details","users","details",false,FM_TYPE_TEXT,null,null,false);
			$fm["CurrentProject"] = new FieldMap("CurrentProject","users","current_project",false,FM_TYPE_INT,10,null,true);
			$fm["CurrentCategory"] = new FieldMap("CurrentCategory","users","current_category",false,FM_TYPE_INT,10,null,true);
			$fm["Timer"] = new FieldMap("Timer","users","timer",false,FM_TYPE_TIMESTAMP,null,null,false);
		}
		return $fm;
	}

	/**
	 * Returns a singleton array of KeyMaps for the User object
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
			$km["FK_time_entries_users"] = new KeyMap("FK_time_entries_users", "Id", "TimeEntries", "UserId", KM_TYPE_ONETOMANY, KM_LOAD_LAZY);  // use KM_LOAD_EAGER with caution here (one-to-one relationships only)
			$km["FK_users_levels"] = new KeyMap("FK_users_levels", "LevelId", "Levels", "Id", KM_TYPE_MANYTOONE, KM_LOAD_LAZY); // you change to KM_LOAD_EAGER here or (preferrably) make the change in _config.php
		}
		return $km;
	}

}

