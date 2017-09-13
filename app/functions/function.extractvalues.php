<?php
function extractValues($inputstring) {

		$break_values = explode(",",$inputstring);
		$break_values_clean = array();
		foreach($break_values as $index => $value) {
				if($value != NULL) {
						$break_values_clean[] = trim($value);
				}
		}
		
		
		return $break_values_clean;

}

?>