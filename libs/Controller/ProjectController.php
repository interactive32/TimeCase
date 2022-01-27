<?php
/** @package    PROJECTS::Controller */

/** import supporting libraries */
require_once("AppBaseController.php");
require_once("Model/Project.php");

/**
 * ProjectController is the controller class for the Project object.  The
 * controller is responsible for processing input from the user, reading/updating
 * the model as necessary and displaying the appropriate view.
*/
class ProjectController extends AppBaseController
{

	/**
	 * Override here for any controller-specific functionality
	 *
	 * @inheritdocs
	 */
	protected function Init()
	{
		parent::Init();
	}

	/**
	 * Displays a list view of objects
	 */
	public function ListView()
	{
		$this->RequirePermission(
				self::$ROLE_ADMIN |
				self::$ROLE_MANAGER, 'User.LoginForm');
		
		$this->Render();
	}

	/**
	 * API Method queries for records and render as JSON
	 */
	public function Query($returnJSON = true)
	{
		$this->RequirePermission(
				self::$ROLE_ADMIN |
				self::$ROLE_MANAGER |
				self::$ROLE_ADVANCED_USER |
				self::$ROLE_BASIC_USER |
				self::$ROLE_CUSTOMER, 'User.LoginForm');

		try
		{
				
			$criteria = new ProjectCriteria();
			
			// skip if calling from another controller (reports)
			if ($returnJSON){
				$url_filer = $this->GetRouter()->GetUrlParam('filter');
			
				// show only active records
				if ($url_filer == 'active')
					$criteria->StatusId_NotEquals = '3';
			}
			
			// for customers show only their projects
			if ($this->IsAuthorized(self::$ROLE_CUSTOMER)){
				$criteria->AddFilter(new CriteriaFilter('CustomerId', $this->GetCurrentUser()->CustomerId));
			}
				
			$filter = RequestUtil::Get('filter');
			if ($filter) $criteria->AddFilter(
					new CriteriaFilter('Id,Title,CustomerId,Created,Closed,Deadline,Progress,StatusId,Description'
							, '%'.$filter.'%')
			);

			foreach (array_keys($_REQUEST) as $prop)
			{
				$prop_normal = ucfirst($prop);
				$prop_equals = $prop_normal.'_Equals';

				if (property_exists($criteria, $prop_normal))
				{
					$criteria->$prop_normal = RequestUtil::Get($prop);
				}
				elseif (property_exists($criteria, $prop_equals))
				{
					// this is a convenience so that the _Equals suffix is not needed
					$criteria->$prop_equals = RequestUtil::Get($prop);
				}
			}
				
			$output = new stdClass();

			// if a sort order was specified then specify in the criteria
			$output->orderBy = RequestUtil::Get('orderBy');
			$output->orderDesc = RequestUtil::Get('orderDesc') != '';

			if ($output->orderBy){
				$criteria->SetOrder($output->orderBy, $output->orderDesc);
			}else{
				$criteria->SetOrder('StatusId', false);
			}

			$page = RequestUtil::Get('page');

			if ($page != '')
			{
				// if page is specified, use this instead (at the expense of one extra count query)
				$pagesize = $this->GetDefaultPageSize();

				$projects = $this->Phreezer->Query('ProjectReporter',$criteria)->GetDataPage($page, $pagesize);
				$output->rows = $projects->ToObjectArray(true,$this->SimpleObjectParams());
				$output->totalResults = $projects->TotalResults;
				$output->totalPages = $projects->TotalPages;
				$output->pageSize = $projects->PageSize;
				$output->currentPage = $projects->CurrentPage;
			}
			else
			{
				// return all results
				$projects = $this->Phreezer->Query('ProjectReporter',$criteria);
				$output->rows = $projects->ToObjectArray(true, $this->SimpleObjectParams());
				$output->totalResults = count($output->rows);
				$output->totalPages = 1;
				$output->pageSize = $output->totalResults;
				$output->currentPage = 1;
			}
				
			// mask some data
			if (!$this->IsAuthorized(self::$ROLE_ADMIN | self::$ROLE_MANAGER)){
				foreach ($output->rows as &$row){
					foreach ($row as $key => $field)
						if ($key != 'id' && $key != 'title' && $key != 'customerId') unset($row->$key);
						
				}
			}

			if ($returnJSON)
				$this->RenderJSON($output, $this->JSONPCallback());
			else
				return $output->rows;

		}
		catch (Exception $ex)
		{
			if ($returnJSON)
				$this->RenderExceptionJSON($ex);
			else
				echo $ex;
		}
	}

	/**
	 * API Method retrieves a single record and render as JSON
	 */
	public function Read()
	{
		$this->RequirePermission(
				self::$ROLE_ADMIN |
				self::$ROLE_MANAGER, 'User.LoginForm');
		
		try
		{
			$pk = $this->GetRouter()->GetUrlParam('id');
			$project = $this->Phreezer->Get('Project',$pk);
			$this->RenderJSON($project, $this->JSONPCallback(), true, $this->SimpleObjectParams());
		}
		catch (Exception $ex)
		{
			$this->RenderExceptionJSON($ex);
		}
	}

	/**
	 * API Method inserts a new record and render response as JSON
	 */
	public function Create()
	{
		$this->RequirePermission(
				self::$ROLE_ADMIN |
				self::$ROLE_MANAGER, 'User.LoginForm');
		
		try
		{

			$json = json_decode(RequestUtil::GetBody());

			if (!$json)
			{
				throw new Exception('The request body does not contain valid JSON');
			}

			$project = new Project($this->Phreezer);

			$project->Title = $this->SafeGetVal($json, 'title');
			$project->CustomerId = $this->SafeGetVal($json, 'customerId');
			$project->Created = date('Y-m-d H:i:s',strtotime($this->SafeGetVal($json, 'created')));
			$project->Closed = date('Y-m-d H:i:s',strtotime($this->SafeGetVal($json, 'closed')));
			$project->Deadline = date('Y-m-d H:i:s',strtotime($this->SafeGetVal($json, 'deadline')));
			$project->Progress = $this->SafeGetVal($json, 'progress');
			$project->StatusId = $this->SafeGetVal($json, 'statusId');
			$project->Description = $this->SafeGetVal($json, 'description');

			$project->Validate();
			$errors = $project->GetValidationErrors();

			if (count($errors) > 0)
			{
				$this->RenderErrorJSON('Please check the form for errors',$errors);
			}
			else
			{
				$project->Save();
				$this->RenderJSON($project, $this->JSONPCallback(), true, $this->SimpleObjectParams());
			}

		}
		catch (Exception $ex)
		{
			$this->RenderExceptionJSON($ex);
		}
	}

	/**
	 * API Method updates an existing record and render response as JSON
	 */
	public function Update()
	{
		$this->RequirePermission(
				self::$ROLE_ADMIN |
				self::$ROLE_MANAGER, 'User.LoginForm');
		
		try
		{

			$json = json_decode(RequestUtil::GetBody());

			if (!$json)
			{
				throw new Exception('The request body does not contain valid JSON');
			}

			$pk = $this->GetRouter()->GetUrlParam('id');
			$project = $this->Phreezer->Get('Project',$pk);

			$project->Title = $this->SafeGetVal($json, 'title', $project->Title);
			$project->CustomerId = $this->SafeGetVal($json, 'customerId', $project->CustomerId);
			$project->Created = date('Y-m-d H:i:s',strtotime($this->SafeGetVal($json, 'created', $project->Created)));
			$project->Closed = date('Y-m-d H:i:s',strtotime($this->SafeGetVal($json, 'closed', $project->Closed)));
			$project->Deadline = date('Y-m-d H:i:s',strtotime($this->SafeGetVal($json, 'deadline', $project->Deadline)));
			$project->Progress = $this->SafeGetVal($json, 'progress', $project->Progress);
			$project->StatusId = $this->SafeGetVal($json, 'statusId', $project->StatusId);
			$project->Description = $this->SafeGetVal($json, 'description', $project->Description);

			$project->Validate();
			$errors = $project->GetValidationErrors();

			if (count($errors) > 0)
			{
				$this->RenderErrorJSON('Please check the form for errors',$errors);
			}
			else
			{
				$project->Save();
				$this->RenderJSON($project, $this->JSONPCallback(), true, $this->SimpleObjectParams());
			}


		}
		catch (Exception $ex)
		{


			$this->RenderExceptionJSON($ex);
		}
	}

	/**
	 * API Method deletes an existing record and render response as JSON
	 */
	public function Delete()
	{
		$this->RequirePermission(
				self::$ROLE_ADMIN |
				self::$ROLE_MANAGER, 'User.LoginForm');
		
		try
		{

			$pk = $this->GetRouter()->GetUrlParam('id');
			$project = $this->Phreezer->Get('Project',$pk);

			$project->Delete();

			$output = new stdClass();

			$this->RenderJSON($output, $this->JSONPCallback());

		}
		catch (Exception $ex)
		{
			$this->RenderExceptionJSON($ex);
		}
	}
}


