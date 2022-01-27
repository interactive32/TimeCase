<?php
/** @package    PROJECTS::Controller */

/** import supporting libraries */
require_once("verysimple/Phreeze/Controller.php");
require_once("Model/User.php");

/**
 * AppBaseController is a base class Controller class from which
 * the front controllers inherit.  
 *
*/
class AppBaseController extends Controller
{
	public static $USE_SESSION_TIMER;
	public static $DEFAULT_PAGE_SIZE;
	public static $DEFAULT_CUSTOMER_USER = 1;

	/** bitwise roles - in conjunction with levels.id field  */
	public static $ROLE_ADMIN = 1;
	public static $ROLE_MANAGER = 2;
	public static $ROLE_ADVANCED_USER = 4;
	public static $ROLE_CUSTOMER = 8;
	public static $ROLE_BASIC_USER = 16;

	/**
	 * Init is called by the base controller before the action method
	 * is called.  This provided an oportunity to hook into the system
	 * for all application actions.  
	 */
	protected function Init()
	{
		self::$USE_SESSION_TIMER = GlobalConfig::$USE_SESSION_TIMER;
		self::$DEFAULT_PAGE_SIZE = GlobalConfig::$DEFAULT_PAGE_SIZE;

		$timespent = false;

		if ($this->GetCurrentUser()){
				
			// database timer
			$pk = $this->GetCurrentUser()->UserId;
			$user = $this->Phreezer->Get('User',$pk);
				
			if ($user && $user->Timer){
				$timespent = time() - strtotime($user->Timer);
			}
		}

		// assign user and timer to views
		$this->Assign("currentUser", $this->GetCurrentUser());
		$this->Assign("timer", $timespent);
		$this->Assign("useSessionTimer", self::$USE_SESSION_TIMER);

		// assign roles to views and js
		$this->Assign("ROLE_ADMIN",self::$ROLE_ADMIN);
		$this->Assign("ROLE_MANAGER",self::$ROLE_MANAGER);
		$this->Assign("ROLE_ADVANCED_USER",self::$ROLE_ADVANCED_USER);
		$this->Assign("ROLE_BASIC_USER",self::$ROLE_BASIC_USER);
		$this->Assign("ROLE_CUSTOMER",self::$ROLE_CUSTOMER);

	}

	/**
	 * Returns the number of records to return per page
	 * when pagination is used
	 */
	protected function GetDefaultPageSize()
	{
		return self::$DEFAULT_PAGE_SIZE;
	}

	/**
	 * Returns the name of the JSONP callback function (if allowed)
	 */
	protected function JSONPCallback()
	{
		// TODO: uncomment to allow JSONP
		// return RequestUtil::Get('callback','');

		return '';
	}

	/**
	 * Return the default SimpleObject params used when rendering objects as JSON
	 * @return array
	 */
	protected function SimpleObjectParams()
	{
		return array('camelCase'=>true);
	}

	/**
	 * Helper method to get values from stdClass without throwing errors
	 * @param stdClass $json
	 * @param string $prop
	 * @param string $default
	 */
	protected function SafeGetVal($json, $prop, $default='')
	{
		return (property_exists($json,$prop))
		? $json->$prop
		: $default;
	}

	/**
	 * Helper utility that calls RenderErrorJSON
	 * @param Exception
	 */
	protected function RenderExceptionJSON(Exception $exception)
	{
		$this->RenderErrorJSON($exception->getMessage(),null,$exception);
	}

	/**
	 * Output a Json error message to the browser
	 * @param string $message
	 * @param array key/value pairs where the key is the fieldname and the value is the error
	 */
	protected function RenderErrorJSON($message, $errors = null, $exception = null)
	{
		if (strpos(strtolower($message),'constraint fails') !== false)
			$message = 'This record is in use.';
			
		$err = new stdClass();
		$err->success = false;
		$err->message = $message;
		$err->errors = array();

		if ($errors != null)
		{
			foreach ($errors as $key=>$val)
			{
				$err->errors[lcfirst($key)] = $val;
			}
		}

		if ($exception)
		{
			$err->stackTrace = explode("\n#", substr($exception->getTraceAsString(),1) );
		}

		@header('HTTP/1.1 401 Unauthorized');
		$this->RenderJSON($err,RequestUtil::Get('callback'));
	}



	/**
	 * Returns true if the user is anonymous (not logged in)
	 * @see IAuthenticatable
	 */
	public function IsAnonymous()
	{
		return $this->GetCurrentUser()->Username == '';
	}


	/**
	 * Checking for permissions.
	 *
	 * @see IAuthenticatable
	 * @param int $permission
	 */
	public function IsAuthorized($permission)
	{

		if ($this->GetCurrentUser()->LevelId & $permission) return true;

		return false;
	}


}
