<?php
/** @package    Projects::Model */

/** import supporting libraries */
require_once("DAO/TimeEntryDAO.php");
require_once("TimeEntryCriteria.php");

/**
 * The TimeEntry class extends TimeEntryDAO which provides the access
 * to the datastore.
 *
 * @package Projects::Model
 * @author ClassBuilder
 * @version 1.0
 */
class TimeEntry extends TimeEntryDAO
{

	/**
	 * Override default validation
	 * @see Phreezable::Validate()
	 */
	public function Validate()
	{
		// force re-validation
		$this->ResetValidationErrors();
		
		$is_valid = (!$this->HasValidationErrors());
		
		// do not accept negative durations
		if ((strtotime($this->End) - strtotime($this->Start)) < 0){
			$this->AddValidationError('End', 'Please select correct time duration');
			$this->AddValidationError('Start', '');
		}
		
		// if validation fails, remove this object from the cache otherwise invalid values can
		// hang around and cause troubles.
		if (!$is_valid)
		{
			$this->_phreezer->DeleteCache(get_class($this), $this->GetPrimaryKeyValue());
		}
		
		return $is_valid;

	}

	/**
	 * @see Phreezable::OnSave()
	 */
	public function OnSave($insert)
	{
		// the controller create/update methods validate before saving.  this will be a
		// redundant validation check, however it will ensure data integrity at the model
		// level based on validation rules.  comment this line out if this is not desired
		if (!$this->Validate()) throw new Exception('Unable to Save TimeEntry: ' .  implode(', ', $this->GetValidationErrors()));

		// OnSave must return true or eles Phreeze will cancel the save operation
		return true;
	}

}

