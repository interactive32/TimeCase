<?php
/** @package    Projects::Model::DAO */

/** import supporting libraries */
require_once("verysimple/Phreeze/IDaoMap.php");

/**
 * ProjectMap is a static class with functions used to get FieldMap and KeyMap information that
 * is used by Phreeze to map the ProjectDAO to the projects datastore.
 *
 * @package Projects::Model::DAO
 * @author ClassBuilder
 * @version 1.0
 */
class ProjectMap implements IDaoMap
{
	/**
	 * Returns a singleton array of FieldMaps for the Project object
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
			$fm["Id"] = new FieldMap("Id","projects","id",true,FM_TYPE_INT,10,null,true);
			$fm["Title"] = new FieldMap("Title","projects","title",false,FM_TYPE_VARCHAR,255,true,false);
			$fm["CustomerId"] = new FieldMap("CustomerId","projects","customer_id",false,FM_TYPE_INT,10,null,false);
			$fm["Created"] = new FieldMap("Created","projects","created",false,FM_TYPE_TIMESTAMP,null,"CURRENT_TIMESTAMP",false);
			$fm["Closed"] = new FieldMap("Closed","projects","closed",false,FM_TYPE_TIMESTAMP,null,null,false);
			$fm["Deadline"] = new FieldMap("Deadline","projects","deadline",false,FM_TYPE_TIMESTAMP,null,"2020-01-01 00:00:01",false);
			$fm["Progress"] = new FieldMap("Progress","projects","progress",false,FM_TYPE_INT,10,null,false);
			$fm["StatusId"] = new FieldMap("StatusId","projects","status_id",false,FM_TYPE_INT,10,null,false);
			$fm["Description"] = new FieldMap("Description","projects","description",false,FM_TYPE_TEXT,null,null,false);
		}
		return $fm;
	}

	/**
	 * Returns a singleton array of KeyMaps for the Project object
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
			$km["FK_project_documents_projects"] = new KeyMap("FK_project_documents_projects", "Id", "ProjectDocuments", "ProjectId", KM_TYPE_ONETOMANY, KM_LOAD_LAZY);  // use KM_LOAD_EAGER with caution here (one-to-one relationships only)
			$km["FK_time_entries_projects"] = new KeyMap("FK_time_entries_projects", "Id", "TimeEntries", "ProjectId", KM_TYPE_ONETOMANY, KM_LOAD_LAZY);  // use KM_LOAD_EAGER with caution here (one-to-one relationships only)
			$km["FK_projects_customers"] = new KeyMap("FK_projects_customers", "CustomerId", "Customers", "Id", KM_TYPE_MANYTOONE, KM_LOAD_LAZY); // you change to KM_LOAD_EAGER here or (preferrably) make the change in _config.php
			$km["FK_projects_statuses"] = new KeyMap("FK_projects_statuses", "StatusId", "Statuses", "Id", KM_TYPE_MANYTOONE, KM_LOAD_LAZY); // you change to KM_LOAD_EAGER here or (preferrably) make the change in _config.php
		}
		return $km;
	}

}

