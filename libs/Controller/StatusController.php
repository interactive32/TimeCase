<?php
/** @package    PROJECTS::Controller */

/** import supporting libraries */
require_once("AppBaseController.php");
require_once("Model/Status.php");

/**
 * StatusController is the controller class for the Status object.  The
 * controller is responsible for processing input from the user, reading/updating
 * the model as necessary and displaying the appropriate view.
 */
class StatusController extends AppBaseController
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
				self::$ROLE_ADMIN, 'User.LoginForm');
		
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
				self::$ROLE_BASIC_USER |
				self::$ROLE_CUSTOMER, 'User.LoginForm');
		
		try
		{
			$criteria = new StatusCriteria();
			
			$filter = RequestUtil::Get('filter');
			if ($filter) $criteria->AddFilter(
				new CriteriaFilter('Id,Description'
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

				$statuses = $this->Phreezer->Query('Status',$criteria)->GetDataPage($page, $pagesize);
				$output->rows = $statuses->ToObjectArray(true,$this->SimpleObjectParams());
				$output->totalResults = $statuses->TotalResults;
				$output->totalPages = $statuses->TotalPages;
				$output->pageSize = $statuses->PageSize;
				$output->currentPage = $statuses->CurrentPage;
			}
			else
			{
				// return all results
				$statuses = $this->Phreezer->Query('Status',$criteria);
				$output->rows = $statuses->ToObjectArray(true, $this->SimpleObjectParams());
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
				self::$ROLE_ADMIN, 'User.LoginForm');
		
		try
		{
			$pk = $this->GetRouter()->GetUrlParam('id');
			$status = $this->Phreezer->Get('Status',$pk);
			$this->RenderJSON($status, $this->JSONPCallback(), true, $this->SimpleObjectParams());
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

			$status = new Status($this->Phreezer);

			$status->Description = $this->SafeGetVal($json, 'description');

			$status->Validate();
			$errors = $status->GetValidationErrors();

			if (count($errors) > 0)
			{
				$this->RenderErrorJSON('Please check the form for errors',$errors);
			}
			else
			{
				$status->Save();
				$this->RenderJSON($status, $this->JSONPCallback(), true, $this->SimpleObjectParams());
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
			
			if ($pk <= 3){
				$this->RenderErrorJSON('Default types cannot be changed or deleted.');
				return;
			}
			
			$status = $this->Phreezer->Get('Status',$pk);

			$status->Description = $this->SafeGetVal($json, 'description', $status->Description);

			$status->Validate();
			$errors = $status->GetValidationErrors();

			if (count($errors) > 0)
			{
				$this->RenderErrorJSON('Please check the form for errors',$errors);
			}
			else
			{
				$status->Save();
				$this->RenderJSON($status, $this->JSONPCallback(), true, $this->SimpleObjectParams());
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
			
			if ($pk <= 3){
				$this->RenderErrorJSON('Default types cannot be changed or deleted.');
				return;
			}
			
			$status = $this->Phreezer->Get('Status',$pk);

			$status->Delete();

			$output = new stdClass();

			$this->RenderJSON($output, $this->JSONPCallback());

		}
		catch (Exception $ex)
		{
			$this->RenderExceptionJSON($ex);
		}
	}
}
