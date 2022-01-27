<?php
/** @package    Projects::Model::DAO */

/** import supporting libraries */
require_once("verysimple/Phreeze/IDaoMap.php");

/**
 * TimeEntryMap is a static class with functions used to get FieldMap and KeyMap information that
 * is used by Phreeze to map the TimeEntryDAO to the time_entries datastore.
 *
 * @package Projects::Model::DAO
 * @author ClassBuilder
 * @version 1.0
 */
class TimeEntryMap implements IDaoMap
{
	/**
	 * Returns a singleton array of FieldMaps for the TimeEntry object
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
			$fm["Id"] = new FieldMap("Id","time_entries","id",true,FM_TYPE_INT,10,null,true);
			$fm["ProjectId"] = new FieldMap("ProjectId","time_entries","project_id",false,FM_TYPE_INT,10,null,false);
			$fm["UserId"] = new FieldMap("UserId","time_entries","user_id",false,FM_TYPE_INT,10,null,false);
			$fm["CategoryId"] = new FieldMap("CategoryId","time_entries","category_id",false,FM_TYPE_INT,10,null,false);
			$fm["Start"] = new FieldMap("Start","time_entries","start",false,FM_TYPE_TIMESTAMP,null,null,false);
			$fm["End"] = new FieldMap("End","time_entries","end",false,FM_TYPE_TIMESTAMP,null,null,false);
			$fm["Description"] = new FieldMap("Description","time_entries","description",false,FM_TYPE_TEXT,null,null,false);
			$fm["Location"] = new FieldMap("Location","time_entries","location",false,FM_TYPE_VARCHAR,50,null,false);
		}
		return $fm;
	}

	/**
	 * Returns a singleton array of KeyMaps for the TimeEntry object
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
			$km["FK_time_entries_categories"] = new KeyMap("FK_time_entries_categories", "CategoryId", "Categories", "Id", KM_TYPE_MANYTOONE, KM_LOAD_LAZY); // you change to KM_LOAD_EAGER here or (preferrably) make the change in _config.php
			$km["FK_time_entries_projects"] = new KeyMap("FK_time_entries_projects", "ProjectId", "Projects", "Id", KM_TYPE_MANYTOONE, KM_LOAD_LAZY); // you change to KM_LOAD_EAGER here or (preferrably) make the change in _config.php
			$km["FK_time_entries_users"] = new KeyMap("FK_time_entries_users", "UserId", "Users", "Id", KM_TYPE_MANYTOONE, KM_LOAD_LAZY); // you change to KM_LOAD_EAGER here or (preferrably) make the change in _config.php
		}
		return $km;
	}

}

