<?php
/** @package    PROJECTS::Controller */

/** import supporting libraries */
require_once("AppBaseController.php");
require_once("Model/User.php");
require_once("Model/Customer.php");
require_once("util/password.php");

/**
 * UserController is the controller class for the User object.  The
 * controller is responsible for processing input from the user, reading/updating
 * the model as necessary and displaying the appropriate view.
*/
class UserController extends AppBaseController implements IAuthenticatable
{
	/** current user vars **/
	public $Username = '';
	public $UserId = '';
	public $LevelId = '';
	public $CustomerId = null;
	public $CurrentProject = null;
	public $CurrentCategory = null;


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
					self::$ROLE_ADMIN, 'User.LoginForm');

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
			$criteria = new UserCriteria();
				
			// never show default customers account because it's read only
			$criteria->Id_NotEquals = self::$DEFAULT_CUSTOMER_USER;


			// if not admin, manager or customer then show only current user's rows
			if (!($this->IsAuthorized(self::$ROLE_ADMIN | self::$ROLE_MANAGER | self::$ROLE_CUSTOMER))){
				$criteria->AddFilter(new CriteriaFilter('Id', $this->GetCurrentUser()->UserId));
			}

				
			$filter = RequestUtil::Get('filter');
			if ($filter) $criteria->AddFilter(
					new CriteriaFilter('Id,Username,LevelId,FullName,Email,Password,Details'
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
			if ($output->orderBy) $criteria->SetOrder($output->orderBy, $output->orderDesc);

			$page = RequestUtil::Get('page');

			if ($page != '')
			{
				// if page is specified, use this instead (at the expense of one extra count query)
				$pagesize = $this->GetDefaultPageSize();

				$users = $this->Phreezer->Query('UserReporter',$criteria)->GetDataPage($page, $pagesize);
				$output->rows = $users->ToObjectArray(true,$this->SimpleObjectParams());
				$output->totalResults = $users->TotalResults;
				$output->totalPages = $users->TotalPages;
				$output->pageSize = $users->PageSize;
				$output->currentPage = $users->CurrentPage;
			}
			else
			{
				// return all results
				$users = $this->Phreezer->Query('UserReporter',$criteria);
				$output->rows = $users->ToObjectArray(true, $this->SimpleObjectParams());
				$output->totalResults = count($output->rows);
				$output->totalPages = 1;
				$output->pageSize = $output->totalResults;
				$output->currentPage = 1;
			}

			// mask some data
			if (!$this->IsAuthorized(self::$ROLE_ADMIN | self::$ROLE_MANAGER)){
				foreach ($output->rows as &$row){
					foreach ($row as $key => $field)
						if ($key != 'id' && $key != 'username') unset($row->$key);

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
				self::$ROLE_ADMIN, 'User.LoginForm');
		

		try
		{
			$pk = $this->GetRouter()->GetUrlParam('id');
			$user = $this->Phreezer->Get('User',$pk);
			$user->Password = ''; // do not send passowrd
			$this->RenderJSON($user, $this->JSONPCallback(), true, $this->SimpleObjectParams());
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
				self::$ROLE_ADMIN, 'User.LoginForm');

		try
		{

			$json = json_decode(RequestUtil::GetBody());

			if (!$json)
			{
				throw new Exception('The request body does not contain valid JSON');
			}

			$user = new User($this->Phreezer);

			$user->Username = $this->SafeGetVal($json, 'username');
			$user->LevelId = $this->SafeGetVal($json, 'levelId');
			$user->FullName = $this->SafeGetVal($json, 'fullName');
			$user->Email = $this->SafeGetVal($json, 'email');
			$user->Details = $this->SafeGetVal($json, 'details');

			$pw_tmp = $this->SafeGetVal($json, 'password', $user->Password);

			if ($pw_tmp != ''){
				$user->Password = password_hash($pw_tmp, PASSWORD_BCRYPT);
			}

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

	/**
	 * API Method updates an existing record and render response as JSON
	 */
	public function Update()
	{
		$this->RequirePermission(
					self::$ROLE_ADMIN, 'User.LoginForm');

		try
		{

			$json = json_decode(RequestUtil::GetBody());

			if (!$json)
			{
				throw new Exception('The request body does not contain valid JSON');
			}

			$pk = $this->GetRouter()->GetUrlParam('id');
				
			if ($pk == self::$DEFAULT_CUSTOMER_USER){
				$this->RenderErrorJSON('Default customers account cannot be changed or deleted.');
				return;
			}
				
			$user = $this->Phreezer->Get('User',$pk);


			$user->Username = $this->SafeGetVal($json, 'username', $user->Username);
			$user->FullName = $this->SafeGetVal($json, 'fullName', $user->FullName);
			$user->Email = $this->SafeGetVal($json, 'email', $user->Email);
			$user->Details = $this->SafeGetVal($json, 'details', $user->Details);
				
			$user->LevelId = $this->SafeGetVal($json, 'levelId', $user->LevelId);
				
			// do not allow customer to be selected
			if ($user->LevelId == self::$ROLE_CUSTOMER) $user->LevelId = '';

			$pw_tmp = $this->SafeGetVal($json, 'password', $user->Password);

			if ($pw_tmp != ''){
				$user->Password = password_hash($pw_tmp, PASSWORD_BCRYPT);
			}

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


	/**
	 * API Method deletes an existing record and render response as JSON
	 */
	public function Delete()
	{
		$this->RequirePermission(
				self::$ROLE_ADMIN, 'User.LoginForm');
		

		try
		{

			$pk = $this->GetRouter()->GetUrlParam('id');
				
			if ($pk == self::$DEFAULT_CUSTOMER_USER){
				$this->RenderErrorJSON('Default customers account cannot be changed or deleted.');
				return;
			}
				
			$user = $this->Phreezer->Get('User',$pk);

			$user->Delete();

			$output = new stdClass();

			$this->RenderJSON($output, $this->JSONPCallback());

		}
		catch (Exception $ex)
		{
			$this->RenderExceptionJSON($ex);
		}
	}


	/**
	 * Display the login form
	 */
	public function LoginForm()
	{		
		if ($this->GetCurrentUser()){
			$user = $this->GetCurrentUser();
			if ($user->LevelId == self::$ROLE_CUSTOMER)
				$this->Redirect('Reports.ListView');
			else
				$this->Redirect('TimeEntry.ListView');
		}
		
		$this->Assign("currentUser", $this->GetCurrentUser());
		$this->Render("Secure");
	}

	/**
	 * Process the login, create the user session and then redirect to
	 * the appropriate page
	 */
	public function LoginAttempt()
	{
		$user = $this->Login(RequestUtil::Get('username'), RequestUtil::Get('password'));

		if ($user){

			// login success
			$this->SetCurrentUser($this);
				
			if ($this->LevelId == self::$ROLE_CUSTOMER)
				$this->Redirect('Reports.ListView');
			else
				$this->Redirect('TimeEntry.ListView');

		}else
		{
			// login failed
			sleep(2);
			$this->Redirect('User.LoginForm','Unknown username/password combination');
		}

	}


	/**
	 * Attempts to authenticate based on the provided username/password. 
	 *
	 * @param string $username
	 * @param string $password
	 * @return bool true if login was successful
	 */
	function Login($username, $password)
	{

		$user_array = array();

		if ($username == "" || $password == "")
		{
			return false;
		}

		$this->Phreezer->Observe("User.Login Searching For Matching Account...");


		// check if customer login first
		$isCustomer = false;
		$criteria = new CustomerCriteria();
		$criteria->Email_Equals = $username;
		$criteria->AllowLogin_Equals = 1;

		$ds = $this->Phreezer->Query("Customer", $criteria);
		$account = $ds->Next();

		
		if ($account && password_verify($password, $account->Password)){
				
			// ok, we have a customer on board, load default customer user
			$isCustomer = true;
				
			// assign customer id to current user vars
			$this->CustomerId = $account->Id;
				
			// load default user
			$criteria = new UserCriteria();
			$criteria->Id_Equals = self::$DEFAULT_CUSTOMER_USER;

			$ds = $this->Phreezer->Query("User", $criteria);
			$account = $ds->Next();

		}else{

			// check with regular users table
			unset($account);
			$criteria = new UserCriteria();
			$criteria->Username_Equals = $username;
				
			$ds = $this->Phreezer->Query("User", $criteria);
			$account = $ds->Next();
			
			
			// pass default admin password admin123 to allow re-crypt on different crypt algorithm
			if ($username == 'admin' && $password == 'admin123' && $account->Password == ''){
				$admin_init = true;
			}
			 
		}


		if ($account && (password_verify($password, $account->Password) || $isCustomer || isset($admin_init)))
		{
			$this->Username = $account->Username;
			$this->UserId = $account->Id;
			$this->LevelId = $account->LevelId;
			$this->CurrentProject = $account->CurrentProject;
			$this->CurrentCategory = $account->CurrentCategory;

			return true;
		}
		else
		{
			return false;
		}
	}


	/**
	 * Clear the user session and redirect to the login page
	 */
	public function Logout()
	{
		$this->ClearCurrentUser();
		
		//$this->Redirect("Default.Home");
		$this->Redirect("User.LoginForm");
	}



	/**
	 * Display the account settings form
	 */
	public function AccountSettingsForm()
	{
		$this->RequirePermission(
				self::$ROLE_ADMIN |
				self::$ROLE_MANAGER |
				self::$ROLE_ADVANCED_USER |
				self::$ROLE_BASIC_USER, 'User.LoginForm');

		// get current user data from db
		$pk = $this->GetCurrentUser()->UserId;
		$user = $this->Phreezer->Get('User',$pk);

		// assign full user object to view
		$this->Assign("user", $user);

		// first load, display form only and return
		if(RequestUtil::GetMethod() == 'GET'){
			$this->Render("AccountSettings");
			return;
		}

		$user->FullName = RequestUtil::Get('fullname');
		$user->Email = RequestUtil::Get('email');
		$user->Details = RequestUtil::Get('details');

		if (RequestUtil::Get('password') != ''){
			$user->Password = password_hash(RequestUtil::Get('password'), PASSWORD_BCRYPT);
		}
			

		$user->Validate();
		$errors = $user->GetValidationErrors();

		if (count($errors) > 0)
		{
			$this->Assign("feedback", array('text' => $errors, 'type' => 'error'));

		}else{

			if ($user->Save())
				$this->Assign("feedback", array('text' => 'Accout sucessfully updated', 'type' => 'success'));
		}

		$this->Render("AccountSettings");
	}

}
