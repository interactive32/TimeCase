<?php
/** @package    PROJECTS::Controller */

/** import supporting libraries */
require_once("AppBaseController.php");
require_once("Model/TimeEntry.php");
require_once("Model/User.php");
require_once("Controller/CustomerController.php");
require_once("Controller/ProjectController.php");
require_once("Controller/CategoryController.php");
require_once("Controller/UserController.php");
require_once("util/common.php");

/**
 * ReportsController is the controller class for all reporting. 
*/
class ReportsController extends AppBaseController
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
				self::$ROLE_CUSTOMER, 'User.LoginForm');
		
		// get customers options for select
		$Customer = new CustomerController($this->Phreezer, $this->RenderEngine);
		$customers = $Customer->Query(false);
		$customerOptions = '<option value=""></option>';
		foreach ($customers as $customer)
			$customerOptions .= common::getOptionHtml($customer->id, $customer->name);
		
		// get projects options for select
		$Project = new ProjectController($this->Phreezer, $this->RenderEngine);
		$projects = $Project->Query(false);
		$projectOptions = '<option value=""></option>';
		foreach ($projects as $project)
			$projectOptions .= common::getOptionHtml($project->id, $project->title);
		
		// get users options for select
		$Users = new UserController($this->Phreezer, $this->RenderEngine);
		$users = $Users->Query(false);
		$userOptions = '<option value=""></option>';
		foreach ($users as $user)
			$userOptions .= common::getOptionHtml($user->id, $user->username);
		
		// get categories options for select
		$Categories = new CategoryController($this->Phreezer, $this->RenderEngine);
		$categories = $Categories->Query(false);
		$categoryOptions = '<option value=""></option>';
		foreach ($categories as $category)
			$categoryOptions .= common::getOptionHtml($category->id, $category->name);

		$this->Assign("cusomerOptions", $customerOptions);
		$this->Assign("projectOptions", $projectOptions);
		$this->Assign("userOptions", $userOptions);
		$this->Assign("categoryOptions", $categoryOptions);
		
		$this->Render();
	}

	/**
	 * Displays report
	 */
	public function Query()
	{
		$this->RequirePermission(
				self::$ROLE_ADMIN |
				self::$ROLE_MANAGER |
				self::$ROLE_ADVANCED_USER |
				self::$ROLE_CUSTOMER, 'User.LoginForm');
		
		try
		{
			$criteria = new TimeEntryCriteria();

			// if not admin, manager or customer then show only current user's rows
			if (!($this->IsAuthorized(self::$ROLE_ADMIN | self::$ROLE_MANAGER | self::$ROLE_CUSTOMER))){
				$criteria->AddFilter(new CriteriaFilter('UserId', $this->GetCurrentUser()->UserId));
			}

			// for customers show only their projects
			if ($this->IsAuthorized(self::$ROLE_CUSTOMER)){
				$criteria->Special = array('customers.id' => $this->GetCurrentUser()->CustomerId);
			}else{
				$filterByCustomer = RequestUtil::Get('filterByCustomer');
				if ($filterByCustomer) $criteria->Special = array('customers.id' => $filterByCustomer);
			}

			// Limit By Filters
			$filterByTimeStart = RequestUtil::Get('filterByTimeStart');
			$filterByTimeEnd = RequestUtil::Get('filterByTimeEnd');

			if ($filterByTimeStart && $filterByTimeEnd){
				$criteria->End_GreaterThanOrEqual = $filterByTimeStart;
				$criteria->End_LessThanOrEqual = $filterByTimeEnd;

			}
			

			$filterByProject = RequestUtil::Get('filterByProject');
			if ($filterByProject) $criteria->AddFilter(new CriteriaFilter('ProjectId', $filterByProject));

			$filterByUser = RequestUtil::Get('filterByUser');
			if ($filterByUser) $criteria->AddFilter(new CriteriaFilter('UserId', $filterByUser));

			$filterByCategory = RequestUtil::Get('filterByCategory');
			if ($filterByCategory) $criteria->AddFilter(new CriteriaFilter('CategoryId', $filterByCategory));

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
			
			// we need custom sql to calculate total duration
			$criteria->totalDurationOnly = true;
			$timeentries = $this->Phreezer->Query('TimeEntryReporter',$criteria);
			$output->rows = $timeentries->ToObjectArray(true, $this->SimpleObjectParams());
			$output->totalDuration = common::formatDuration($output->rows[0]->duration);
			$criteria->totalDurationOnly = false;

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

			}else
			{
				// get all rows
				$timeentries = $this->Phreezer->Query('TimeEntryReporter',$criteria);
				
				$output->rows = $timeentries->ToObjectArray(true, $this->SimpleObjectParams());

				$output->totalResults = count($output->rows);
				$output->totalPages = 1;
				$output->pageSize = $output->totalResults;
				$output->currentPage = 1;
			}


			$report_type = $this->GetRouter()->GetUrlParam('type');

			switch ($report_type) {
				case 'csv':
					$this->RenderCSV($this->fixData($output->rows));
					break;

				case 'html':
					$this->RenderHTML($this->fixData($output->rows, true));
					break;

				default:
					$this->RenderJSON($output, $this->JSONPCallback());
					break;
			}

			return true;

		}
		catch (Exception $ex)
		{
			$this->RenderExceptionJSON($ex);
		}
	}


	/**
	 * Build & Display csv report
	 */
	public function RenderCSV($data)
	{
		$header = array();

		if (!empty($data)){
			foreach ($data['rows'][0] as $key => $field)
				$header[$key] = $key;
		}

		// output headers so that the file is downloaded rather than displayed
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=data.csv');
		
		// IE7/8 patch
		//header("Cache-Control: must-revalidate");
		//header("Pragma: must-revalidate");
		//header("Content-type: application/vnd.ms-excel");
		//header("Content-disposition: attachment; filename=data.csv");

		// create a file pointer connected to the output stream
		$output = fopen('php://output', 'w');

		fputcsv($output, $header);

		foreach ($data['rows'] as $row){
			fputcsv($output, $row);
		}
			
		die;
	}
	
	
	/**
	 * Build & Display html report
	 */
	public function RenderHTML($data)
	{

		echo '<html>
		<head>
		<meta charset="utf-8">
		<base href="'.GlobalConfig::$ROOT_URL.'">
		<title>Report - TimeCase </title>
		<meta content="width=device-width, initial-scale=1.0" name="viewport">
		<meta content="TimeCase" name="description">
		<meta content="alcalbg | interactive32.com" name="author">
		<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
		<link rel="stylesheet" href="styles/style.css">
		</head>';
		

		echo '<body class="report-html"><div>';
		
		echo '<h3>';
		echo 'Total Duration: '.common::formatDuration($data['total']);
		echo '</h3><h4>';
		echo 'From: '.htmlspecialchars(RequestUtil::Get('filterByTimeStart')).' ';
		echo 'To: '.htmlspecialchars(RequestUtil::Get('filterByTimeEnd'));
		
		$filterByCustomer = RequestUtil::Get('filterByCustomer');
		if ($filterByCustomer){
			$customerCriteria = new CustomerCriteria();
			$customerCriteria->Id_Equals = $filterByCustomer;
			$customer = $this->Phreezer->Query('Customer', $customerCriteria);
			echo '; Customer: '.htmlspecialchars($customer->Next()->Name);
		}
		
		$filterByProject = RequestUtil::Get('filterByProject');
		if ($filterByProject){
			$projectCriteria = new ProjectCriteria();
			$projectCriteria->Id_Equals = $filterByProject;
			$project = $this->Phreezer->Query('Project', $projectCriteria);
			echo '; Project: '.htmlspecialchars($project->Next()->Title);
		}
		
		$filterByUser = RequestUtil::Get('filterByUser');
		if ($filterByUser){
			$userCriteria = new UserCriteria();
			$userCriteria->Id_Equals = $filterByUser;
			$user = $this->Phreezer->Query('User', $userCriteria);
			echo '; User: '.htmlspecialchars($user->Next()->Username);
		}
		
		$filterByCategory = RequestUtil::Get('filterByCategory');
		if ($filterByCategory){
			$categoryCriteria = new CategoryCriteria();
			$categoryCriteria->Id_Equals = $filterByCategory;
			$category = $this->Phreezer->Query('Category', $categoryCriteria);
			echo '; Work Type: '.htmlspecialchars($category->Next()->Name);
		}
	
		echo '</h4>';
		
		echo '<table class="collection table table-bordered"><thead><tr>';
		
		
		if (!empty($data['rows'])){
			foreach ($data['rows'][0] as $key => $field){
				if ($key == 'Hours') continue;
				/* hide filtered field
				if ($key == 'Project' && $filterByCustomer) continue;
				if ($key == 'User' && $filterByUser) continue;
				if ($key == 'Work Type' && $filterByCategory) continue;
				*/
				echo '<th>' .htmlspecialchars($key). '</th>';
			}
		}

		
		echo '</tr></thead><tbody>';
		
		foreach ($data['rows'] as $row){
			echo '<tr>';
			foreach ($row as $key => $col){
				if ($key == 'Hours') continue;
				/* hide filtered field
				if ($key == 'Project' && $filterByCustomer) continue;
				if ($key == 'User' && $filterByUser) continue;
				if ($key == 'Work Type' && $filterByCategory) continue;
				*/
				$col = ($col !== '' ? $col : ' ');
				echo '<td>' .htmlspecialchars($col). '</td>';
			}
			echo '</tr>';
		}
		
		echo '</tbody></table>';
		echo '</div></body>';
		echo '<html>';
			
		die;
	}
	
	
	/**
	 * 
	 * fix data before output
	 */
	public function fixData($data, $hideCustomers = false)
	{
		
		$rows = array();
		$total = 0;
		
		foreach ($data as $row)
		{
			$rows[] = array(
					'Customer' => $row->customerName,
					'Project' => $row->projectTitle,					
					'User' => $row->userName,
					'Work Type' => $row->categoryName,
					'Description' => $row->description,
					'Location' => $row->location,
					'Start' => $row->start,
					'End' => $row->end,
					'Duration' => $row->durationFormatted,
					'Hours' => number_format($row->duration / 60, 2));
			
			
			
			$total += $row->duration;
		}
		
		if($hideCustomers){
			foreach($rows as &$row)
				unset($row['Customer']);
		}
		
		return array('rows' => $rows, 'total' => $total);
	}


}
