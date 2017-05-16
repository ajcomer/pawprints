<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php require_once("../includes/validation_functions.php"); ?>
<?php confirm_logged_in(); ?>

<?php
  $user = find_user_by_id($_GET["user_id"]);
  
  if (!$user) {
    // username was missing or invalid or 
    // user couldn't be found in database
    redirect_to("index.php");
  }

  $profile = find_profile_by_user_id($user["user_id"]);

  if (!$profile) {
    redirect_to("index.php");
  }
?>

<?php
if (isset($_POST['submit'])) {
  // Process the form
  
  // validations
  $required_fields = array("first_name", "visible");
  validate_presences($required_fields);
  
  $fields_with_max_lengths = array("first_name" => 30);
  validate_max_lengths($fields_with_max_lengths);
  
  if (empty($errors)) {
    
    // Perform Update

    $user_id = $user["user_id"];
    $first_name = mysql_prep($_POST["first_name"]);
    $last_name = mysql_prep($_POST["last_name"]);
    $email_address = mysql_prep($_POST["email_address"]);
    $phone_number = mysql_prep($_POST["phone_number"]);
    $address = mysql_prep($_POST["address"]);
    $city = mysql_prep($_POST["city"]);
    $state = mysql_prep($_POST["state"]);
    $zip_code = mysql_prep($_POST["zip_code"]);
    $country = mysql_prep($_POST["country"]);
    $visible = $_POST["visible"];
    $admin_access = $_POST["admin_access"];
  
    $query  = "UPDATE profile SET ";
    $query .= "first_name = '{$first_name}', ";
    $query .= "last_name = '{$last_name}', ";
    $query .= "email_address = '{$email_address}', ";
    $query .= "phone_number = '{$phone_number}', ";
    $query .= "address = '{$address}', ";
    $query .= "city = '{$city}', ";
    $query .= "state = '{$state}', ";
    $query .= "zip_code = '{$zip_code}', ";
    $query .= "country = '{$country}', ";
    $query .= "visible = {$visible} ";
    $query .= "WHERE user_id = {$user_id} ";
    $query .= "LIMIT 1";
    $profile_result = mysqli_query($connection, $query);

    // Query for admin_access
      // this part is causing the "no changes made" bug. maybe need to write a new page like "edit_admin_profile"

    /*$query = "UPDATE users SET ";
    $query .= "admin_access = {$admin_access} ";
    $query .= "WHERE user_id = {$user_id} ";
    $query .= "LIMIT 1";
    $user_result = mysqli_query($connection, $query);*/

    if ($profile_result && mysqli_affected_rows($connection) == 1)
    {
      // Success
      $_SESSION["message"] = "Profile updated.";
      redirect_to("profile.php"); // THIS NEEDS TO POINT TO "VIEW PROFILE" SO THAT IT SHOWS THE ACTUAL USER THAT WE ARE
                                  // EDITING'S PROFILE... NOT THE ADMIN WHO IS SIGNED IN.
    }
    elseif ($profile_result && mysqli_affected_rows($connection) == 0)
    {
      $_SESSION["message"] = "No changes made.";
      redirect_to("profile.php");
    }
    else
    {
      // Failure
      $_SESSION["message"] = "Profile update failed.";
    }
  
  }
} else {
  // This is probably a GET request
  
} // end: if (isset($_POST['submit']))

?>

<?php include("../includes/layouts/header.php"); ?>

    <?php echo message(); ?>
    <?php echo form_errors($errors); ?>
    
    <h2>Edit Profile: <?php echo htmlentities($user["username"]); ?></h2>
    <form action="edit_profile.php?user_id=<?php echo urlencode($user["user_id"]); ?>" method="post">
      <table>
        <tr>
          <td><b>First Name:</b></td>
          <td>
            <input type="text" name="first_name" value="<?php
              if (isset($profile["first_name"]))
                echo htmlentities($profile["first_name"]);
              else
                echo "";
            ?>" />
          </td>
        </tr>
        <tr>
          <td><b>Last Name:</b></td>
          <td>
            <input type="text" name="last_name" value="<?php
              if (isset($profile["last_name"]))
                echo htmlentities($profile["last_name"]);
              else
                echo "";
            ?>" />
          </td>
        </tr>
        <tr>
          <td><b>Email Address:</b></td>
          <td>
            <input type="text" name="email_address" value="<?php
              if (isset($profile["email_address"]))
                echo htmlentities($profile["email_address"]);
              else
                echo "";
            ?>" />
          </td>
        </tr>
        <tr>
          <td><b>Phone Number:</b></td>
          <td>
            <input type="text" name="phone_number" value="<?php
              if (isset($profile["phone_number"]))
                echo htmlentities($profile["phone_number"]);
              else
                echo "";
            ?>" />
          </td>
        </tr>
        <tr>
          <td><b>Address:</b></td>
          <td>
            <input type="text" name="address" value="<?php
              if (isset($profile["address"]))
                echo htmlentities($profile["address"]);
              else
                echo "";
            ?>" />
          </td>
        </tr>
        <tr>
          <td><b>City:</b></td>
          <td>
            <input type="text" name="city" value="<?php
              if (isset($profile["city"]))
                echo htmlentities($profile["city"]);
              else
                echo "";
            ?>" />
          </td>
        </tr>
        <tr>
          <td><b>State:</b></td>
          <td>
            <input type="text" name="state" value="<?php
              if (isset($profile["state"]))
                echo htmlentities($profile["state"]);
              else
                echo "";
            ?>" />
          </td>
        </tr>
        <tr>
          <td><b>Zip Code:</b></td>
          <td>
            <input type="text" name="zip_code" value="<?php
              if (isset($profile["zip_code"]))
                echo htmlentities($profile["zip_code"]);
              else
                echo "";
            ?>" />
          </td>
        </tr>
        <tr>
          <td><b>Country:</b></td>
          <td>
            <input type="text" name="country" value="<?php
              if (isset($profile["country"]))
                echo htmlentities($profile["country"]);
              else
                echo "";
            ?>" />
          </td>
        </tr>
        <tr>
          <td><b>Profile Privacy:</b></td>
          <td>
            <input type="radio" name="visible" value="0" <?php if ($profile["visible"] == 0) { echo "checked"; } else { echo ""; }; ?> /> Public
            &nbsp;
            <input type="radio" name="visible" value="1" <?php if ($profile["visible"] == 1) { echo "checked"; } else { echo ""; }; ?> /> Private
          </td>
        </tr>
        <tr>
          <td><b>Admin Access:</b></td>
          <td>
            <input type="radio" name="admin_access" value="null" <?php if ($user["admin_access"] == null) { echo "checked"; } else { echo ""; }; ?> /> No
            &nbsp;
            <input type="radio" name="admin_access" value="1" <?php if ($user["admin_access"] == 1) { echo "checked"; } else { echo ""; }; ?> /> Yes
          </td>
      </table>
      <input type="submit" class="button" name="submit" value="Edit Profile" />
    </form>
    <br />
    <?php
      $url = htmlspecialchars($_SERVER['HTTP_REFERER']);
      echo "<a href='$url'>Cancel</a>"; 
    ?>

<?php include("../includes/layouts/footer.php"); ?>
