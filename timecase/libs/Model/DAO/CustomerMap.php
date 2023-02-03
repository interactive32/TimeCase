<?php
/** @package    Projects::Model::DAO */

/** import supporting libraries */
require_once("verysimple/Phreeze/IDaoMap.php");

/**
 * CustomerMap is a static class with functions used to get FieldMap and KeyMap information that
 * is used by Phreeze to map the CustomerDAO to the customers datastore.
 *
 * @package Projects::Model::DAO
 * @author ClassBuilder
 * @version 1.0
 */
class CustomerMap implements IDaoMap
{
	/**
	 * Returns a singleton array of FieldMaps for the Customer object
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
			$fm["Id"] = new FieldMap("Id","customers","id",true,FM_TYPE_INT,10,null,true);
			$fm["Name"] = new FieldMap("Name","customers","name",false,FM_TYPE_VARCHAR,1000,true,false);
			$fm["ContactPerson"] = new FieldMap("ContactPerson","customers","contact_person",false,FM_TYPE_VARCHAR,1000,null,false);
			$fm["Email"] = new FieldMap("Email","customers","email",false,FM_TYPE_VARCHAR,1000,null,false);
			$fm["Password"] = new FieldMap("Password","customers","password",false,FM_TYPE_VARCHAR,200,null,false);
			$fm["AllowLogin"] = new FieldMap("AllowLogin","customers","allow_login",false,FM_TYPE_INT,1,0,false);
			$fm["Address"] = new FieldMap("Address","customers","address",false,FM_TYPE_VARCHAR,1000,null,false);
			$fm["Location"] = new FieldMap("Location","customers","location",false,FM_TYPE_VARCHAR,1000,null,false);
			$fm["Web"] = new FieldMap("Web","customers","web",false,FM_TYPE_VARCHAR,1000,null,false);
			$fm["Tel"] = new FieldMap("Tel","customers","tel",false,FM_TYPE_VARCHAR,1000,null,false);
			$fm["Tel2"] = new FieldMap("Tel2","customers","tel2",false,FM_TYPE_VARCHAR,1000,null,false);
			$fm["StatusId"] = new FieldMap("StatusId","customers","status_id",false,FM_TYPE_INT,10,null,false);
			$fm["Description"] = new FieldMap("Description","customers","description",false,FM_TYPE_TEXT,null,null,false);
		}
		return $fm;
	}

	/**
	 * Returns a singleton array of KeyMaps for the Customer object
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
			$km["FK_projects_customers"] = new KeyMap("FK_projects_customers", "Id", "Projects", "CustomerId", KM_TYPE_ONETOMANY, KM_LOAD_LAZY);  // use KM_LOAD_EAGER with caution here (one-to-one relationships only)
			$km["FK_customers_statuses"] = new KeyMap("FK_customers_statuses", "StatusId", "Statuses", "Id", KM_TYPE_MANYTOONE, KM_LOAD_LAZY); // you change to KM_LOAD_EAGER here or (preferrably) make the change in _config.php
		}
		return $km;
	}

}

