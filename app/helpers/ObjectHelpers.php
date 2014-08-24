<?php

class ObjectHelpers {
	
	// get object property if it exists otherwise return $default if null returned anywhere along the line
	// args are $default first then each part of the object
	// ie getProp("default val", $obj, "prop", "propinprop", "something");
	// if you want to call a function instead prepend with !
	// ie getProp("default val", $obj, "prop", "!fn");
	public static function getProp() {
		$args = func_get_args();
		$default = array_shift($args);
		$obj = array_shift($args);
		$value = $obj;
		
		foreach ($args as $a) {
			if (substr($a, 0, 1) === "!") {
				// treat as function
				// strip off !
				$a = substr($a, 1);
				$value = $value->$a();
			}
			else {
				// treat as property
				$value = $value[$a];
			}
			
			if (is_null($value)) {
				return $default;
			}
		}
		return $value;
	}
}