<?php

	function redirect_to($new_location) {
	  header("Location: " . $new_location);
	  exit;
	}

	function mysql_prep($string) {
		// this function just makes it easier to do mysqli_real_escape_string easier, since
		// that function name is pretty long
		global $connection;
		
		$escaped_string = mysqli_real_escape_string($connection, $string);
		return $escaped_string;
	}
	
	function confirm_query($result_set) {
		if (!$result_set) {
			die("Database query failed.");	// This doesn't mean "No results showed up" (which would just return empty string),
											// it means there's probably a typo in your query. Invalid SQL is somewhere.
		}
	}

	function form_errors($errors=array()) {
		$output = "";
		if (!empty($errors)) {
		  $output .= "<div class=\"error\">";
		  $output .= "Please fix the following errors:";
		  $output .= "<ul>";
		  foreach ($errors as $key => $error) {
		    $output .= "<li>";
				$output .= htmlentities($error);
				$output .= "</li>";
		  }
		  $output .= "</ul>";
		  $output .= "</div>";
		}
		return $output;
	}

	function password_encrypt($password) 
	// in PHP v5.5, password_hash() will do the same thing as this function
	{
  	  $hash_format = "$2y$10$";   // Tells PHP to use Blowfish with a "cost" of 10. Larger the cost, the slower it is.
  	  								// Slower is good for passwords so hackers can't quickly test a bajillion passwords per minute.
	  $salt_length = 22; 					// Blowfish salts should be 22-characters or more
	  $salt = generate_salt($salt_length);
	  $format_and_salt = $hash_format . $salt;
	  $hash = crypt($password, $format_and_salt);
		return $hash;
	}
	
	function generate_salt($length) {
	  // Not 100% unique, not 100% random, but good enough for a salt
	  // MD5 returns 32 characters
	  $unique_random_string = md5(uniqid(mt_rand(), true));
	  
		// Valid characters for a salt are [a-zA-Z0-9./]
	  $base64_string = base64_encode($unique_random_string);
	  
		// But not '+' which is valid in base64 encoding
	  $modified_base64_string = str_replace('+', '.', $base64_string);
	  
		// Truncate string to the correct length
	  $salt = substr($modified_base64_string, 0, $length);
	  
		return $salt;
	}
	
	function password_check($password, $existing_hash)
	// in PHP v5.5, password_verify() will do the same thing as this function
	{
		// existing hash contains format and salt at start
		// existing hash from database
	  $hash = crypt($password, $existing_hash); // hash the password typed in by the user
	  if ($hash === $existing_hash) { // when the typed in password is hashed, does the hashed/encrypted version match
	  									// the $existing_hash stored in the database?
	    return true;
	  } else {
	    return false;
	  }
	}

	function attempt_login($username, $password)
	{
		$user = find_user_by_username($username);
		if ($user)
		{
			// found user, now check password
			if (password_check($password, $user["hashed_password"]))
			{
				// if password_check() returns true, password matches
				return $user;
			}
			else
			{
				// password does not match
				return false;
			}
		}
		else
		{
			// user not found
			return false;
		}
	}

	function logged_in_admin()
	// this is useful to have as a function, even though it is one line,
	// because now we can do things like, "if (logged_in), then display
	// "Log Out?" link. if (!logged_in), then display "Log In?" link. etc.
	{
		if ((isset($_SESSION['user_id'])) && (isset($_SESSION['admin_access'])))
			return true;
		else
			return false;
	}
	
	function logged_in_user()
	{
		if ((isset($_SESSION['user_id'])) && !(isset($_SESSION['admin_access'])))
			return true;
		else
			return false;
	}

	function confirm_logged_in() {
		if (!(logged_in_admin()) && !(logged_in_user())) {
			redirect_to("login.php");
		}
	}

	function confirm_admin()
	{
		if (logged_in_admin() && !(logged_in_user()))
			return true;
		else
			redirect_to("index.php");
	}

	function navigate()
	{
		
		if (logged_in_admin() || logged_in_user())
		{
			$output = "<img src=\"images\user_icon.png\">";
			$output .= "<b>{$_SESSION['username']}</b>";

			if (isset($_SESSION['admin_access']))
			{
				$output .= 
				$output .= "<ul>";
				$output .= "<li><a href=\"aindex.php\">Home</a></li>";
			}
			else
			{
				$output .= "<ul>";
				$output .= "<li><a href=\"uindex.php\">Home</a></li>";
			}
		}
		else
		{
			$output = "<img src=\"images\user_icon.png\">";
			$output .= "<li><a href=\"login.php\">Login</a></li>";
		}

		if (logged_in_admin() || logged_in_user())
		{
			$output .= "<li><a href=\"profile.php\">Profile</li>";
			$output .= "<li><a href=\"manage_dogs.php\">Manage Dogs</a></li>";
		}

		if (logged_in_admin() && !(logged_in_user()))
		{
			// you have to differentiate between public, user, and admin
			
		}

		$output .= "<li><a href=\"dog_parks.php\">Dog parks</a></li>";

		if (logged_in_admin() || logged_in_user())
		{
			$output .= "<li><a href=\"logout.php\">Logout</a></li>";
		}

		return $output;
	}

	function find_all_users() {
		global $connection;
		
		$query  = "SELECT * ";
		$query .= "FROM users ";
		$query .= "ORDER BY username ASC";
		$user_set = mysqli_query($connection, $query);
		confirm_query($user_set);
		return $user_set;
	}

	function find_all_pets() {
		global $connection;
		
		$query  = "SELECT * ";
		$query .= "FROM pets ";
		$query .= "ORDER BY first_name ASC";
		$pet_set = mysqli_query($connection, $query);
		confirm_query($pet_set);
		return $pet_set;
	}

	function find_all_pets_of_user($user_id) {
		global $connection;
		
		$safe_user_id = mysqli_real_escape_string($connection, $user_id);

		$query = "SELECT * ";
		$query .= "FROM pets ";
			$query .= "LEFT JOIN users_pets_intersection ";
			$query .= "ON users_pets_intersection.pet_id = pets.pet_id ";
		        $query .= "LEFT JOIN users ";
		        $query .= "ON users_pets_intersection.user_id = users.user_id ";
		        	$query .= "WHERE users.user_id = $safe_user_id";
		$pet_set = mysqli_query($connection, $query);
		confirm_query($pet_set);
		return $pet_set;
	}

	function find_user_of_pet($pet_id) {
		global $connection;

		$safe_pet_id = mysqli_real_escape_string($connection, $pet_id);

		$query = "SELECT * ";
		$query .= "FROM users ";
			$query .= "LEFT JOIN users_pets_intersection ";
			$query .= "ON users_pets_intersection.user_id = users.user_id ";
				$query .= "LEFT JOIN pets ";
				$query .= "ON users_pets_intersection.pet_id = pets.pet_id ";
					$query .= "WHERE pets.pet_id = $safe_pet_id";
		$user_set = mysqli_query($connection, $query);
		confirm_query($user_set);
		return $user_set;
	}

	function find_all_parks_of_pet($pet_id)
	{
		global $connection;

		$safe_pet_id = mysqli_real_escape_string($connection, $pet_id);

		$query = "SELECT * ";
		$query .= "FROM pets_parks_intersection ";
			$query .= "LEFT JOIN parks ";
			$query .= "ON pets_parks_intersection.park_id = parks.park_id ";
		        $query .= "LEFT JOIN pets ";
		        $query .= "ON pets_parks_intersection.pet_id = pets.pet_id ";
		        	$query .= "WHERE pets.pet_id = $safe_pet_id";
		$park_set = mysqli_query($connection, $query);
		confirm_query($park_set);
		return $park_set;
	}

	function find_all_parks() {
		global $connection;
		
		$query  = "SELECT * ";
		$query .= "FROM parks ";
		$query .= "ORDER BY park_name ASC";
		$park_set = mysqli_query($connection, $query);
		confirm_query($park_set);
		return $park_set;
	}

	function find_all_breeds() {
		global $connection;
		
		$query  = "SELECT * ";
		$query .= "FROM breeds ";
		$query .= "ORDER BY breed_name ASC";
		$breed_set = mysqli_query($connection, $query);
		confirm_query($breed_set);
		return $breed_set;
	}

	function find_user_by_username($username) {
		global $connection;
		
		$safe_username = mysqli_real_escape_string($connection, $username);
		
		$query  = "SELECT * ";
		$query .= "FROM users ";
		$query .= "WHERE username = '{$safe_username}' ";
		$query .= "LIMIT 1";
		$user_set = mysqli_query($connection, $query);
		confirm_query($user_set);
		if($user = mysqli_fetch_assoc($user_set)) {
			return $user;
		} else {
			return null;
		}
	}

	function find_park_by_name($park_name) {
		global $connection;
		
		$safe_park_name = mysqli_real_escape_string($connection, $park_name);
		
		$query  = "SELECT * ";
		$query .= "FROM parks ";
		$query .= "WHERE park_name = '{$safe_park_name}' ";
		$query .= "LIMIT 1";
		$park_set = mysqli_query($connection, $query);
		confirm_query($park_set);
		if($park = mysqli_fetch_assoc($park_set)) {
			return $park;
		} else {
			return null;
		}
	}

	function find_pet_by_username($pet_name) {
		global $connection;
		
		$safe_pet_name = mysqli_real_escape_string($connection, $pet_name);
		
		$query  = "SELECT * ";
		$query .= "FROM pets ";
		$query .= "WHERE username = '{$safe_pet_name}' ";
		$query .= "LIMIT 1";
		$pet_set = mysqli_query($connection, $query);
		confirm_query($pet_set);
		if($pet = mysqli_fetch_assoc($pet_set)) {
			return $pet;
		} else {
			return null;
		}
	}

	function find_profile_by_user_id($username) {
		global $connection;
		
		$safe_user_id = mysqli_real_escape_string($connection, $username);
		
		$query  = "SELECT * ";
		$query .= "FROM profile ";
		$query .= "WHERE user_id = {$safe_user_id} ";
		$query .= "LIMIT 1";
		$user_set = mysqli_query($connection, $query);
		confirm_query($user_set);
		if($user = mysqli_fetch_assoc($user_set)) {
			return $user;
		} else {
			return null;
		}
	}

	function find_user_by_id($user_id) {
		global $connection;
		
		$safe_user_id = mysqli_real_escape_string($connection, $user_id);
		
		$query  = "SELECT * ";
		$query .= "FROM users ";
		$query .= "WHERE user_id = {$safe_user_id} ";
		$query .= "LIMIT 1";
		$user_set = mysqli_query($connection, $query);
		confirm_query($user_set);
		if($user = mysqli_fetch_assoc($user_set)) {
			return $user;
		} else {
			return null;
		}
	}

	function find_pet_by_id($pet_id) {
		global $connection;
		
		$safe_pet_id = mysqli_real_escape_string($connection, $pet_id);
		
		$query  = "SELECT * ";
		$query .= "FROM pets ";
		$query .= "WHERE pet_id = {$safe_pet_id} ";
		$query .= "LIMIT 1";
		$pet_set = mysqli_query($connection, $query);
		confirm_query($pet_set);
		if($pet = mysqli_fetch_assoc($pet_set)) {
			return $pet;
		} else {
			return null;
		}
	}

	function find_park_by_id($park_id) {
		global $connection;
		
		$safe_park_id = mysqli_real_escape_string($connection, $park_id);
		
		$query  = "SELECT * ";
		$query .= "FROM parks ";
		$query .= "WHERE park_id = {$safe_park_id} ";
		$query .= "LIMIT 1";
		$park_set = mysqli_query($connection, $query);
		confirm_query($park_set);
		if($park = mysqli_fetch_assoc($park_set)) {
			return $park;
		} else {
			return null;
		}
	}

	function find_dogs_in_park($park_id)
	{
		global $connection;

		$query = "SELECT * ";
		$query .= "FROM pets_parks_intersection ";
		$query .= "WHERE park_id = {$park_id}";
		$dog_set = mysqli_query($connection, $query);
		confirm_query($dog_set);
		$dog_count = 0;
		while($dog = mysqli_fetch_assoc($dog_set))
		{
			$dog_count++;
		}
		
		return $dog_count;
	}

?>

