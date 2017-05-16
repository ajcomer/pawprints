<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php require_once("../includes/validation_functions.php"); ?>
<?php confirm_admin(); ?>

<?php
if (isset($_POST['submit'])) {
  // Process the form
  
  // validations
  $required_fields = array("username", "password", "admin_access"); // password is not hashed here
  validate_presences($required_fields);
  
  $fields_with_max_lengths = array("username" => 30);
  validate_max_lengths($fields_with_max_lengths);
  
  if (empty($errors))
  {
    // Perform Create

    $username = mysql_prep($_POST["username"]);
    $hashed_password = password_encrypt($_POST["password"]); // hash the password here
    $admin_access = $_POST["admin_access"];
    
    $query  = "INSERT INTO users (";
    $query .= "  username, hashed_password, admin_access";
    $query .= ") VALUES (";
    $query .= "  '{$username}', '{$hashed_password}', {$admin_access}";
    $query .= ")";
    $result_users = mysqli_query($connection, $query);

    if ($result_users)
    {
        if ($admin_access == 1)
        {
          $_SESSION["message"] = "Admin created.";
        }
        else
        {
          $_SESSION["message"] = "User created.";
        }
    }
    else
    {
      // Failure
      $_SESSION["message"] = "User creation failed.";
    }

    // create defaults for Profile
    $default = "";
    $user = find_user_by_username($_POST["username"]);

    $query  = "INSERT INTO profile (";
    $query .= "user_id, first_name, last_name, email_address, phone_number, address, city, state, zip_code, country, visible";
    $query .= ") VALUES (";
    $query .= "{$user["user_id"]}, '{$default}', '{$default}', '{$default}', '{$default}', '{$default}', '{$default}', '{$default}', '{$default}', '{$default}', '1'";
    $query .= ")";
    $result_profile = mysqli_query($connection, $query);

    if (!($result_profile))
    {
      $_SESSION["message"] .= "Profile creation failed. {$user["user_id"]}";
    }

    redirect_to("manage_users.php");
  }
} else {
  // This is probably a GET request
  
} // end: if (isset($_POST['submit']))

?>

<?php include("../includes/layouts/header.php"); ?>

    <?php echo message(); ?>
    <?php echo form_errors($errors); ?>
    
    <h2>Create User</h2>
    <form action="new_user.php" method="post">
      <p>Username:
        <input type="text" name="username" value="" />
      </p>
      <p>Password:
        <input type="password" name="password" value="" />
      </p>
      <p>Admin Access:
        <input type="radio" name="admin_access" value="null" /> No
        &nbsp;
        <input type="radio" name="admin_access" value="1" /> Yes
      </p>
      <input type="submit" class="button" name="submit" value="Create User" />
    </form>
    <br />
    <a href="manage_users.php">Cancel</a>

<?php include("../includes/layouts/footer.php"); ?>
