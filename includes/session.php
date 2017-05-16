<?php

	session_start();
	
	function message() {
		// on the first refresh, message below will be shown...
		if (isset($_SESSION["message"])) { // if "message" key exists in $_SESSION superglobal...
			$output = "<div class=\"message\">";
			$output .= htmlentities($_SESSION["message"]); // makes all text in "message" safe...
			$output .= "</div>";
			
			// clear message after use
			// on the second refresh, message below will be shown (clear).
			$_SESSION["message"] = null;
			
			return $output;
		}
	}

	function errors() {
		if (isset($_SESSION["errors"])) {
			$errors = $_SESSION["errors"];
			
			// clear message after use
			$_SESSION["errors"] = null;
			
			return $errors;
		}
	}
	
?>