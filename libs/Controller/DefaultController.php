<?php
/** @package    PROJECTS::Controller */

/** import supporting libraries */
require_once("AppBaseController.php");

/**
 * DefaultController is the entry point to the application
 */
class DefaultController extends AppBaseController
{

	/**
	 * Override here for any controller-specific functionality
	 */
	protected function Init()
	{
		parent::Init();
	}

	/**
	 * Display the home page for the application
	 */
	public function Home()
	{
		$this->Render();
	}

	/**
	 * Displayed when an invalid route is specified
	 */
	public function Error404()
	{
		$this->Render();
	}

	/**
	 * Display a fatal error message
	 */
	public function ErrorFatal()
	{
		$this->Render();
	}

	public function ErrorApi404()
	{
		$this->RenderErrorJSON('An unknown API endpoint was requested.');
	}
	
	
	/**
	 * API Method retrieves current logged in userID and render as JSON
	 */
	public function ReadCurrentUser()
	{
		$this->RequirePermission(
				self::$ROLE_ADMIN |
				self::$ROLE_MANAGER |
				self::$ROLE_BASIC_USER |
				self::$ROLE_ADVANCED_USER |
				self::$ROLE_CUSTOMER, 'User.LoginForm');
		
		$current_user = $this->GetCurrentUser();
	
		$this->RenderJSON($current_user);
	}
	

}
