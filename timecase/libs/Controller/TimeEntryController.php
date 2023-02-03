<?php
/** @package    PROJECTS::Controller */

/** import supporting libraries */
require_once("AppBaseController.php");
require_once("Model/TimeEntry.php");
require_once("Model/User.php");

/**
 * TimeEntryController is the controller class for the TimeEntry object.  The
 * controller is responsible for processing input from the user, reading/updating
 * the model as necessary and displaying the appropriate view.
 */
class TimeEntryController extends AppBaseController
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
				self::$ROLE_MANAGER |
				self::$ROLE_ADVANCED_USER |
				self::$ROLE_BASIC_USER, 'User.LoginForm');
		
		$this->Render();
	}

	/**
	 * API Method queries for records and render as JSON
	 */
	public function Query()
	{
		$this->RequirePermission(
				self::$ROLE_ADMIN |
				self::$ROLE_MANAGER |
				self::$ROLE_ADVANCED_USER |
				self::$ROLE_BASIC_USER, 'User.LoginForm');
		
		try
		{
			$criteria = new TimeEntryCriteria();
			
				
			// if not admin or manager then show only current user's rows
			if (!($this->IsAuthorized(self::$ROLE_ADMIN | self::$ROLE_MANAGER))){
			
				$criteria->AddFilter(new CriteriaFilter('UserId', $this->GetCurrentUser()->UserId));
			}
			
			
			$filter = RequestUtil::Get('filter');
			if ($filter) $criteria->AddFilter(
				new CriteriaFilter('Id,ProjectId,UserId,CategoryId,Start,End,Description'
				, '%'.$filter.'%')
			);

			foreach (array_keys($_REQUEST) as $prop)
			{
				if ($prop === 'start') continue;
				
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
 				$criteria->SetOrder('Id', true);
 			}

			$page = RequestUtil::Get('page');

			if ($page != '')
			{
				// if page is specified, use this instead (at the expense of one extra count query)
				$pagesize = $this->GetDefaultPageSize();

				$timeentries = $this->Phreezer->Query('TimeEntryReporter',$criteria)->GetDataPage($page, $pagesize);
				$output->rows = $timeentries->ToObjectArray(true,$this->SimpleObjectParams());
				$output->totalResults = $timeentries->TotalResults;
				$output->totalPages = $timeentries->TotalPages;
				$output->pageSize = $timeentries->PageSize;
				$output->currentPage = $timeentries->CurrentPage;
			}
			else
			{
				// return all results
				$timeentries = $this->Phreezer->Query('TimeEntryReporter',$criteria);
				$output->rows = $timeentries->ToObjectArray(true, $this->SimpleObjectParams());
				$output->totalResults = count($output->rows);
				$output->totalPages = 1;
				$output->pageSize = $output->totalResults;
				$output->currentPage = 1;
			}


			$this->RenderJSON($output, $this->JSONPCallback());
		}
		catch (Exception $ex)
		{
			$this->RenderExceptionJSON($ex);
		}
	}

	/**
	 * API Method retrieves a single record and render as JSON
	 */
	public function Read()
	{
		$this->RequirePermission(
				self::$ROLE_ADMIN |
				self::$ROLE_MANAGER |
				self::$ROLE_ADVANCED_USER |
				self::$ROLE_BASIC_USER, 'User.LoginForm');
		
		try
		{
			$pk = $this->GetRouter()->GetUrlParam('id');
			$timeentry = $this->Phreezer->Get('TimeEntry',$pk);
			$this->RenderJSON($timeentry, $this->JSONPCallback(), true, $this->SimpleObjectParams());
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
				self::$ROLE_MANAGER |
				self::$ROLE_ADVANCED_USER |
				self::$ROLE_BASIC_USER, 'User.LoginForm');
		
		try
		{
						
			$json = json_decode(RequestUtil::GetBody());

			if (!$json)
			{
				throw new Exception('The request body does not contain valid JSON');
			}

			$timeentry = new TimeEntry($this->Phreezer);

			$timeentry->ProjectId = $this->SafeGetVal($json, 'projectId');
			$timeentry->CategoryId = $this->SafeGetVal($json, 'categoryId');
			$timeentry->Description = $this->SafeGetVal($json, 'description');
			$timeentry->Location = $_SERVER['REMOTE_ADDR'];
			
			if ($this->IsAuthorized(self::$ROLE_ADMIN | self::$ROLE_MANAGER)){
				$timeentry->UserId = $this->SafeGetVal($json, 'userId', $timeentry->UserId);
			}else{
				$timeentry->UserId = $this->GetCurrentUser()->UserId;
			}

			if (!($this->IsAuthorized(self::$ROLE_BASIC_USER))){
				$timeentry->Start = date('Y-m-d H:i:s',strtotime($this->SafeGetVal($json, 'start', $timeentry->Start)));
				$timeentry->End = date('Y-m-d H:i:s',strtotime($this->SafeGetVal($json, 'end', $timeentry->End)));
			}
			
			$timeentry->Validate();
			$errors = $timeentry->GetValidationErrors();

			if (count($errors) > 0)
			{
				$this->RenderErrorJSON('Please check the form for errors',$errors);
			}
			else
			{
				if ($this->IsAuthorized(self::$ROLE_BASIC_USER)){
					// now we can stop the timer for basic_user
					$time_spent = $this->StopTimeTracking(false);
					$timeentry->Start = date('Y-m-d H:i:s', time() - $time_spent);
					$timeentry->End = date('Y-m-d H:i:s', time());
				}
				
				$timeentry->Save();
				$this->RenderJSON($timeentry, $this->JSONPCallback(), true, $this->SimpleObjectParams());
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
				self::$ROLE_MANAGER |
				self::$ROLE_ADVANCED_USER |
				self::$ROLE_BASIC_USER, 'User.LoginForm');
		
		try
		{
						
			$json = json_decode(RequestUtil::GetBody());

			if (!$json)
			{
				throw new Exception('The request body does not contain valid JSON');
			}

			$pk = $this->GetRouter()->GetUrlParam('id');
			$timeentry = $this->Phreezer->Get('TimeEntry',$pk);

			$timeentry->ProjectId = $this->SafeGetVal($json, 'projectId', $timeentry->ProjectId);
			$timeentry->CategoryId = $this->SafeGetVal($json, 'categoryId', $timeentry->CategoryId);
			$timeentry->Description = $this->SafeGetVal($json, 'description', $timeentry->Description);
			$timeentry->Location = $_SERVER['REMOTE_ADDR'];
			
			if ($this->IsAuthorized(self::$ROLE_ADMIN | self::$ROLE_MANAGER)){
				$timeentry->UserId = $this->SafeGetVal($json, 'userId', $timeentry->UserId);
			}
			
			if ($this->IsAuthorized(self::$ROLE_ADMIN | self::$ROLE_MANAGER | self::$ROLE_ADVANCED_USER)){
				$timeentry->Start = date('Y-m-d H:i:s',strtotime($this->SafeGetVal($json, 'start', $timeentry->Start)));
				$timeentry->End = date('Y-m-d H:i:s',strtotime($this->SafeGetVal($json, 'end', $timeentry->End)));
			}
			

			$timeentry->Validate();
			$errors = $timeentry->GetValidationErrors();

			if (count($errors) > 0)
			{
				$this->RenderErrorJSON('Please check the form for errors',$errors);
			}
			else
			{
				$timeentry->Save();
				$this->RenderJSON($timeentry, $this->JSONPCallback(), true, $this->SimpleObjectParams());
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
				self::$ROLE_MANAGER |
				self::$ROLE_ADVANCED_USER, 'User.LoginForm');
		
		try
		{
						
			$pk = $this->GetRouter()->GetUrlParam('id');
			$timeentry = $this->Phreezer->Get('TimeEntry',$pk);

			$timeentry->Delete();

			$output = new stdClass();

			$this->RenderJSON($output, $this->JSONPCallback());

		}
		catch (Exception $ex)
		{
			$this->RenderExceptionJSON($ex);
		}
	}
		
	/**
	 * API Method for starting timetracking
	 */
	public function StartTimeTracking()
	{
		$this->RequirePermission(
				self::$ROLE_ADMIN |
				self::$ROLE_MANAGER |
				self::$ROLE_ADVANCED_USER |
				self::$ROLE_BASIC_USER, 'User.LoginForm');
		
		// session-based timer
		if (self::$USE_SESSION_TIMER){
			
			$_SESSION['timetracking'] = time();
			$this->RenderJSON('started');
			
			return;
		}

		// database timer
		$pk = $this->GetCurrentUser()->UserId;
		$user = $this->Phreezer->Get('User',$pk);
		
		$user->Timer = date('Y-m-d H:i:s', time());

		$user->Save();
		$this->RenderJSON('started');
		return;
		
	}
	
	
	/**
	 * API Method for stopping timetracking and return time spent in seconds
	 */
	public function StopTimeTracking($returnJSON = true)
	{
		$this->RequirePermission(
				self::$ROLE_ADMIN |
				self::$ROLE_MANAGER |
				self::$ROLE_ADVANCED_USER |
				self::$ROLE_BASIC_USER, 'User.LoginForm');
		
		// session-based timer
		if (self::$USE_SESSION_TIMER){
			
			if (!isset($_SESSION['timetracking'])){
			
				if ($returnJSON) $this->RenderJSON(0);
				return false;
			}
			
			$time_spent = time() - $_SESSION['timetracking'];
			
			unset($_SESSION['timetracking']);
			
			if ($returnJSON) $this->RenderJSON($time_spent);
			
			return $time_spent;
		}
		
		// database timer
		$pk = $this->GetCurrentUser()->UserId;
		$user = $this->Phreezer->Get('User',$pk);
		
		if (!$user->Timer){
			if ($returnJSON) $this->RenderJSON(0);
			return false;
		}
		
		$time_spent = time() - strtotime($user->Timer);
		
		$user->Timer = null;
		$user->Save();
			
		if ($returnJSON) $this->RenderJSON($time_spent);
			
		return $time_spent;
	}
	
	
	/**
	 * API Method for current timetracking check (in seconds)
	 */
	public function CheckTimeTracking()
	{
		$this->RequirePermission(
				self::$ROLE_ADMIN |
				self::$ROLE_MANAGER |
				self::$ROLE_ADVANCED_USER |
				self::$ROLE_BASIC_USER, 'User.LoginForm');
		
		// session-based timer
		if (self::$USE_SESSION_TIMER){
			
			if (isset($_SESSION['timetracking'])){
				$this->RenderJSON(time() - $_SESSION['timetracking']);
			}else{
				$this->RenderJSON(-1);
			}
			
			return;
		}
		
		// database timer
		$pk = $this->GetCurrentUser()->UserId;
		$user = $this->Phreezer->Get('User',$pk);
		
		if (!$user->Timer){
			$this->RenderJSON(-1);
			return;
		}
		
		$time_spent = time() - strtotime($user->Timer);
			
		$this->RenderJSON($time_spent);
			
		return;

	}
	
	
	/**
	 * API Method updates default project & category for current user
	 */
	public function UpdateDefaults()
	{
		$this->RequirePermission(
				self::$ROLE_ADMIN |
				self::$ROLE_MANAGER |
				self::$ROLE_ADVANCED_USER |
				self::$ROLE_BASIC_USER, 'User.LoginForm');
		
		try
		{
	
			$json = json_decode(RequestUtil::GetBody());
	
			if (!$json)
			{
				throw new Exception('The request body does not contain valid JSON');
			}
	
			$pk = $this->GetCurrentUser()->UserId;
			$user = $this->Phreezer->Get('User',$pk);
	
			$user->CurrentProject = $this->SafeGetVal($json, 'currentProject', null);
			$user->CurrentCategory = $this->SafeGetVal($json, 'currentCategory', null);
				
			if ($user->CurrentProject == '') $user->CurrentProject = null;
			if ($user->CurrentCategory == '') $user->CurrentCategory = null;
				
			// refresh seesion user data
			$cUser = $this->GetCurrentUser();
			$cUser->CurrentProject = $user->CurrentProject;
			$cUser->CurrentCategory = $user->CurrentCategory;
			$this->SetCurrentUser($cUser);
				
			$user->Validate();
			$errors = $user->GetValidationErrors();
	
			if (count($errors) > 0)
			{
				$this->RenderErrorJSON('Please check the form for errors',$errors);
			}
			else
			{
				$user->Save();
				$this->RenderJSON($user, $this->JSONPCallback(), true, $this->SimpleObjectParams());
			}
	
	
		}
		catch (Exception $ex)
		{
			$this->RenderExceptionJSON($ex);
		}
	}
	
}
