<?php
/** @package    PROJECTS::Controller */

/** import supporting libraries */
require_once("AppBaseController.php");
require_once("Model/Level.php");

/**
 * LevelController is the controller class for the Level object.  The
 * controller is responsible for processing input from the user, reading/updating
 * the model as necessary and displaying the appropriate view.
 */
class LevelController extends AppBaseController
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
	 * API Method queries for records and render as JSON
	 */
	public function Query()
	{
		$this->RequirePermission(
				self::$ROLE_ADMIN |
				self::$ROLE_MANAGER |
				self::$ROLE_BASIC_USER |
				self::$ROLE_ADVANCED_USER |
				self::$ROLE_CUSTOMER, 'User.LoginForm');
		
		try
		{
			$criteria = new LevelCriteria();

			$output = new stdClass();


 			$output->orderBy = 'Id';
 	
 			// do not allow customers to be selected
 			$criteria->Id_NotEquals = self::$ROLE_CUSTOMER;

			// return  results
			$levels = $this->Phreezer->Query('Level',$criteria);
			$output->rows = $levels->ToObjectArray(true, $this->SimpleObjectParams());
			$output->totalResults = count($output->rows);
			$output->totalPages = 1;
			$output->pageSize = $output->totalResults;
			$output->currentPage = 1;

			$this->RenderJSON($output, $this->JSONPCallback());
		}
		catch (Exception $ex)
		{
			$this->RenderExceptionJSON($ex);
		}
	}

}
