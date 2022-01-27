<?php

/**
 *  common, app wide helpers
 *
 */

class common 
{
	

	/**
	 *  return formatted duration
	 *
	 */
	public static function formatDuration($duration)
	{
		
		$negative = '';
		if ($duration < 0){
			$duration = $duration * -1;
			$negative = '-';
		}
		
		
		$minutes = $duration % 60;
		$hours = floor($duration / 60);
		if ($hours < 10) $hours = "0".$hours;
		if ($minutes < 10) $minutes = "0".$minutes;
		$ret = $negative . $hours . ":" . $minutes;
	
		return $ret;
	}
	
	
	/**
	 * 
	 * built html select element
	 * 
	 */
	public static function getOptionHtml($value, $label, $selected = false)
	{

		return '<option value="' . $value. '"' . ($selected ? 'selected="selected"' : '') . '>'
					. $label .
					'</option>';

	}

	
}
