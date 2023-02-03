<?php
/** @package    PROJECTS::Controller */

/** import supporting libraries */
require_once("AppBaseController.php");
require_once("Model/Category.php");

/**
 * CategoryController is the controller class for the Category object.  The
 * controller is responsible for processing input from the user, reading/updating
 * the model as necessary and displaying the appropriate view.
*/
class CategoryController extends AppBaseController
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
			$criteria = new CategoryCriteria();
				
			$filter = RequestUtil::Get('filter');
			if ($filter) $criteria->AddFilter(
					new CriteriaFilter('Id,Name'
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

				$categories = $this->Phreezer->Query('Category',$criteria)->GetDataPage($page, $pagesize);
				$output->rows = $categories->ToObjectArray(true,$this->SimpleObjectParams());
				$output->totalResults = $categories->TotalResults;
				$output->totalPages = $categories->TotalPages;
				$output->pageSize = $categories->PageSize;
				$output->currentPage = $categories->CurrentPage;
			}
			else
			{
				// return all results
				$categories = $this->Phreezer->Query('Category',$criteria);
				$output->rows = $categories->ToObjectArray(true, $this->SimpleObjectParams());
				$output->totalResults = count($output->rows);
				$output->totalPages = 1;
				$output->pageSize = $output->totalResults;
				$output->currentPage = 1;
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
			$category = $this->Phreezer->Get('Category',$pk);
			$this->RenderJSON($category, $this->JSONPCallback(), true, $this->SimpleObjectParams());
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

			$category = new Category($this->Phreezer);

			$category->Name = $this->SafeGetVal($json, 'name');

			$category->Validate();
			$errors = $category->GetValidationErrors();

			if (count($errors) > 0)
			{
				$this->RenderErrorJSON('Please check the form for errors',$errors);
			}
			else
			{
				$category->Save();
				$this->RenderJSON($category, $this->JSONPCallback(), true, $this->SimpleObjectParams());
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
			$category = $this->Phreezer->Get('Category',$pk);

			$category->Name = $this->SafeGetVal($json, 'name', $category->Name);

			$category->Validate();
			$errors = $category->GetValidationErrors();

			if (count($errors) > 0)
			{
				$this->RenderErrorJSON('Please check the form for errors',$errors);
			}
			else
			{
				$category->Save();
				$this->RenderJSON($category, $this->JSONPCallback(), true, $this->SimpleObjectParams());
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
			$category = $this->Phreezer->Get('Category',$pk);

			$category->Delete();

			$output = new stdClass();

			$this->RenderJSON($output, $this->JSONPCallback());

		}
		catch (Exception $ex)
		{
			$this->RenderExceptionJSON($ex);
		}
	}
}

