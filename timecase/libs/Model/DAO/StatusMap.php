<?php
/** @package    Projects::Model::DAO */

/** import supporting libraries */
require_once("verysimple/Phreeze/IDaoMap.php");

/**
 * StatusMap is a static class with functions used to get FieldMap and KeyMap information that
 * is used by Phreeze to map the StatusDAO to the statuses datastore.
 *
 * @package Projects::Model::DAO
 * @author ClassBuilder
 * @version 1.0
 */
class StatusMap implements IDaoMap
{
	/**
	 * Returns a singleton array of FieldMaps for the Status object
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
			$fm["Id"] = new FieldMap("Id","statuses","id",true,FM_TYPE_INT,10,null,true);
			$fm["Description"] = new FieldMap("Description","statuses","description",false,FM_TYPE_VARCHAR,1000,null,false);
		}
		return $fm;
	}

	/**
	 * Returns a singleton array of KeyMaps for the Status object
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
			$km["FK_customers_statuses"] = new KeyMap("FK_customers_statuses", "Id", "Customers", "StatusId", KM_TYPE_ONETOMANY, KM_LOAD_LAZY);  // use KM_LOAD_EAGER with caution here (one-to-one relationships only)
			$km["FK_projects_statuses"] = new KeyMap("FK_projects_statuses", "Id", "Projects", "StatusId", KM_TYPE_ONETOMANY, KM_LOAD_LAZY);  // use KM_LOAD_EAGER with caution here (one-to-one relationships only)
			$km["FK_users_statuses"] = new KeyMap("FK_users_statuses", "Id", "Users", "StatusId", KM_TYPE_ONETOMANY, KM_LOAD_LAZY);  // use KM_LOAD_EAGER with caution here (one-to-one relationships only)
		}
		return $km;
	}

}

