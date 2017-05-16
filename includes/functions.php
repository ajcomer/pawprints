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

/* 


BELOW IS ALL WIDGET_CORP FUNCTIONS


*/

	function find_all_subjects($public=true) { // public refers to the layout context; public is default. admin is the only other option.
		global $connection;
		
		$query  = "SELECT * ";
		$query .= "FROM subjects ";
		if ($public) { // if layout context is public, then ONLY show visible items
			$query .= "WHERE visible = 1 ";
		}
		$query .= "ORDER BY position ASC";
		$subject_set = mysqli_query($connection, $query);
		confirm_query($subject_set); // we call this variable a SET because it is a RESOURCE that we get back from this query
		return $subject_set;
	}
	
	function find_pages_for_subject($subject_id, $public=true) {
		global $connection;
		
		$safe_subject_id = mysqli_real_escape_string($connection, $subject_id); // mysqli_real_escape_string makes "special characters"
																				// safe. It makes single ', ", ;, etc, completely
																				// harmless. It's important to do this with input from
																				// the user that goes into the database, for STRING
																				// fields ONLY.
		
		$query  = "SELECT * ";
		$query .= "FROM pages ";
		$query .= "WHERE subject_id = {$safe_subject_id} ";
		if ($public) {
			$query .= "AND visible = 1 ";
		}
		$query .= "ORDER BY position ASC";
		$page_set = mysqli_query($connection, $query);
		confirm_query($page_set);
		return $page_set;
	}
	
	/*function find_all_admins() {
		global $connection;
		
		$query  = "SELECT * ";
		$query .= "FROM admins ";
		$query .= "ORDER BY username ASC";
		$admin_set = mysqli_query($connection, $query);
		confirm_query($admin_set);
		return $admin_set;
	}*/
	
	function find_subject_by_id($subject_id, $public=true) {
		global $connection;
		
		$safe_subject_id = mysqli_real_escape_string($connection, $subject_id);
		
		$query  = "SELECT * ";
		$query .= "FROM subjects ";
		$query .= "WHERE id = {$safe_subject_id} ";
		if ($public) {
			$query .= "AND visible = 1 ";
		}
		$query .= "LIMIT 1"; // we are only ever going to get one row back, since we are requesting by ID, which is unique
		$subject_set = mysqli_query($connection, $query);
		confirm_query($subject_set);
		if($subject = mysqli_fetch_assoc($subject_set)) { // mysqli_fetch_assoc is normally in a while loop to loop through
															// the fetched rows in a result set, but there's only ONE row in
															// THIS result set, so we can just set it equal to a variable and
															// use it that way! 
			return $subject;
		} else { // if mysqli_fetch_assoc doesn't find anything
			return null;
		}
	}

	function find_page_by_id($page_id, $public=true) {
		global $connection;
		
		// Since we get SUBJECT from the URL that the user has access to, it is prone to SQL injection.
		// So, we must use mysqli_real_escape_string to make it safe and render "special characters" harmless.
		$safe_page_id = mysqli_real_escape_string($connection, $page_id);
		
		$query  = "SELECT * ";
		$query .= "FROM pages ";
		$query .= "WHERE id = {$safe_page_id} ";
		if ($public) {
			$query .= "AND visible = 1 ";
		}
		$query .= "LIMIT 1";
		$page_set = mysqli_query($connection, $query);
		confirm_query($page_set);
		if($page = mysqli_fetch_assoc($page_set)) {
			return $page;
		} else {
			return null;
		}
	}
	
/*	function find_admin_by_id($admin_id) {
		global $connection;
		
		$safe_admin_id = mysqli_real_escape_string($connection, $admin_id);
		
		$query  = "SELECT * ";
		$query .= "FROM admins ";
		$query .= "WHERE id = {$safe_admin_id} ";
		$query .= "LIMIT 1";
		$admin_set = mysqli_query($connection, $query);
		confirm_query($admin_set);
		if($admin = mysqli_fetch_assoc($admin_set)) {
			return $admin;
		} else {
			return null;
		}
	}*/

	// function find_admin_by_username($username) {
	// 	global $connection;
		
	// 	$safe_username = mysqli_real_escape_string($connection, $username);
		
	// 	$query  = "SELECT * ";
	// 	$query .= "FROM admins ";
	// 	$query .= "WHERE username = '{$safe_username}' ";
	// 	$query .= "LIMIT 1";
	// 	$admin_set = mysqli_query($connection, $query);
	// 	confirm_query($admin_set);
	// 	if($admin = mysqli_fetch_assoc($admin_set)) {
	// 		return $admin;
	// 	} else {
	// 		return null;
	// 	}
	// }

	function find_default_page_for_subject($subject_id) {
		$page_set = find_pages_for_subject($subject_id);
		if($first_page = mysqli_fetch_assoc($page_set)) {
			// returns the very first item in the page resource
			return $first_page;
		} else {
			return null;
		}
	}
	
	function find_selected_page($public=false) {
		global $current_subject;
		global $current_page;
		
		if (isset($_GET["subject"])) { // If "subject" is set... give "page" a default value (null)
			$current_subject = find_subject_by_id($_GET["subject"], $public);
			if ($current_subject && $public) {
				$current_page = find_default_page_for_subject($current_subject["id"]); // sets the default page when you click
																						// on each subject.
			} else {
				$current_page = null; // leaves all subjects expanded to show all pages, whether we are currently clicking on them or not
			}
		} elseif (isset($_GET["page"])) { // if "page" is set... give "subject" a default value (null)
			$current_subject = null;
			$current_page = find_page_by_id($_GET["page"], $public);
		} else { // if you go to a page like admin.php, with no ?subject= or ?page=, we still need the link to work.
					// so set "subject" and "page" to default values (null).
			$current_subject = null;
			$current_page = null;
		}
	}

	// navigation takes 2 arguments
	// - the current subject array or null
	// - the current page array or null
	function navigation($subject_array, $page_array) {
		// had to put all of the HTML in a string called "output" because this entire document is in PHP
		// otherwise, the HTML wouldn't work
		$output = "<ul class=\"subjects\">";
		$subject_set = find_all_subjects(false); // this is set to FALSE. this means we have ADMIN rights here. so we see all visible
													// AND non-visible subjects and pages.
													// if this were TRUE, we would only have PUBLIC rights. then, we would only see the
													// visible items.
		while($subject = mysqli_fetch_assoc($subject_set)) {
			$output .= "<li";
			if ($subject_array && $subject["id"] == $subject_array["id"]) {
				$output .= " class=\"selected\""; // class="selected" is from inside public.css
													// which means when this link is selected, it
													// will turn BOLD, so the user knows where he or she
													// is on the site.
			}
			$output .= ">";
			$output .= "<a href=\"manage_content.php?subject=";
			$output .= urlencode($subject["id"]);
			$output .= "\">";
			$output .= htmlentities($subject["menu_name"]);
			$output .= "</a>";
			
			$page_set = find_pages_for_subject($subject["id"], false);
			$output .= "<ul class=\"pages\">";
			while($page = mysqli_fetch_assoc($page_set)) {
				$output .= "<li";
				if ($page_array && $page["id"] == $page_array["id"]) {
					$output .= " class=\"selected\"";
				}
				$output .= ">";
				$output .= "<a href=\"manage_content.php?page=";
				$output .= urlencode($page["id"]);
				$output .= "\">";
				$output .= htmlentities($page["menu_name"]);
				$output .= "</a></li>";
			}
			mysqli_free_result($page_set);
			$output .= "</ul></li>";
		}
		mysqli_free_result($subject_set);
		$output .= "</ul>";
		return $output;
	}

	function public_navigation($subject_array, $page_array) {
		$output = "<ul class=\"subjects\">";
		$subject_set = find_all_subjects();
		while($subject = mysqli_fetch_assoc($subject_set)) {
			$output .= "<li";
			if ($subject_array && $subject["id"] == $subject_array["id"]) {
				$output .= " class=\"selected\"";
			}
			$output .= ">";
			$output .= "<a href=\"index.php?subject=";
			$output .= urlencode($subject["id"]);
			$output .= "\">";
			$output .= htmlentities($subject["menu_name"]);
			$output .= "</a>";
			
			if ($subject_array["id"] == $subject["id"] || 
					$page_array["subject_id"] == $subject["id"]) 
					// As you click a "subject" in the navigation, all of the "pages" of that "subject" are listed below it.
					// All remaining "subjects" do not have their pages listed below it.

					// Same goes for clicking on a "page" within a "subject" - the "subject" for that "page" and the other "pages"
					// within that "subject" are still shown.
			{
				$page_set = find_pages_for_subject($subject["id"]);
				$output .= "<ul class=\"pages\">";
				while($page = mysqli_fetch_assoc($page_set)) {
					$output .= "<li";
					if ($page_array && $page["id"] == $page_array["id"]) {
						$output .= " class=\"selected\"";
					}
					$output .= ">";
					$output .= "<a href=\"index.php?page=";
					$output .= urlencode($page["id"]);
					$output .= "\">";
					$output .= htmlentities($page["menu_name"]);
					$output .= "</a></li>";
				}
				$output .= "</ul>";
				mysqli_free_result($page_set);
			}

			$output .= "</li>"; // end of the subject <li>
		}
		mysqli_free_result($subject_set);
		$output .= "</ul>";
		return $output;
	}

?>

