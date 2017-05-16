<?php

$errors = array(); // now we don't have to initialize this anywhere else; it is always ready to go for the
					// functions below

function fieldname_as_text($fieldname) {
  $fieldname = str_replace("_", " ", $fieldname); // takes the underscore out of column names like "menu_name"
  $fieldname = ucfirst($fieldname); // capitalizes column names like "menu_name"
  return $fieldname;
}

// * presence
// use trim() so empty spaces don't count
// use === to avoid false positives
// empty() would consider "0" to be empty
function has_presence($value) {
	return isset($value) && $value !== ""; // this returns true or false
}

function validate_presences($required_fields) {
  global $errors;
  foreach($required_fields as $field) {
    $value = $_POST[$field];
  	if (!has_presence($value)) {
  		$errors[$field] = fieldname_as_text($field) . " can't be blank";
  	}
  }
}

// * string length
// max length
function has_max_length($value, $max) {
	return strlen($value) <= $max; // this returns true or false
}

function validate_max_lengths($fields_with_max_lengths) {
	global $errors;
	// Expects an assoc. array
	foreach($fields_with_max_lengths as $field => $max) {
		$value = trim($_POST[$field]);
	  if (!has_max_length($value, $max)) {
	    $errors[$field] = fieldname_as_text($field) . " is too long";
	  }
	}
}

// * inclusion in a set
function has_inclusion_in($value, $set) {
	return in_array($value, $set);
}

?>