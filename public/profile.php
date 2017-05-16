<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php require_once("../includes/validation_functions.php"); ?>
<?php confirm_logged_in(); ?>

<?php
  $user = find_user_by_username($_SESSION["username"]);
  
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

<?php include("../includes/layouts/header.php"); ?>

    <?php echo message(); ?>
    <?php echo form_errors($errors); ?>
    
    <h2>View Profile: <?php echo $user["username"]; ?></h2>
    <table>
      <tr>
        <td><b>First Name:</b></td>
        <td>
          <?php
            if (isset($profile["first_name"]))
              echo htmlentities($profile["first_name"]);
            else
              echo "";
          ?>
        </td>
      </tr>
      <tr>
        <td><b>Last Name:</b></td>
        <td>
          <?php
            if (isset($profile["last_name"]))
              echo htmlentities($profile["last_name"]);
            else
              echo "";
          ?>
        </td>
      </tr>
      <tr>
        <td><b>Email Address:</b></td>
        <td>
          <?php
            if (isset($profile["email_address"]))
              echo $profile["email_address"];
            else
              echo "";
          ?>
        </td>
      </tr>
      <tr>
        <td><b>Phone Number:</b></td>
        <td>
          <?php
            if (isset($profile["phone_number"]))
              echo $profile["phone_number"];
            else
              echo "";
          ?>
        </td>
      </tr>
      <tr>
        <td><b>Address:</b></td>
        <td>
          <?php
            if (isset($profile["address"]))
              echo $profile["address"];
            else
              echo "";
          ?>
        </td>
      </tr>
      <tr>
        <td><b>City:</b></td>
        <td>
          <?php
            if (isset($profile["city"]))
              echo $profile["city"];
            else
              echo "";
          ?>
        </td>
      </tr>

      <tr>
        <td><b>State:</b></td>
        <td>
          <?php
            if (isset($profile["state"]))
              echo $profile["state"];
            else
              echo "";
          ?>
        </td>
      </tr>

      <tr>
        <td><b>Zip Code:</b></td>
        <td>
          <?php
            if (isset($profile["zip_code"]))
              echo $profile["zip_code"];
            else
              echo "";
          ?>
        </td>
      </tr>

      <tr>
        <td><b>Country:</b></td>
        <td>
          <?php
            if (isset($profile["country"]))
              echo $profile["country"];
            else
              echo "";
          ?>
        </td>
      </tr>

      <tr>
        <td><b>Profile Privacy:</b></td>
        <td>
          <?php
            if (isset($profile["visible"]))
            {
              if ($profile["visible"] == 0)
                echo "Public";
              elseif ($profile["visible"] == 1)
                echo "Private";
            }
            else
              echo "";
          ?>
        </td>
      </tr>

      <tr>
        <td><b>Admin Access:</b></td>
        <td>
          <?php
            if (isset($user["admin_access"]))
            {
              echo "Yes";
            }
            else
              echo "No";
          ?>
        </td>
      </tr>
      <tr>
        <td><a href="edit_profile.php?user_id=<?php echo urlencode($user["user_id"]); ?>">Edit Profile</a></td>
      </tr>
    </table>

<?php include("../includes/layouts/footer.php"); ?>
