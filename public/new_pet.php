<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php require_once("../includes/validation_functions.php"); ?>
<?php confirm_logged_in(); ?>

<?php
if (isset($_POST['submit'])) {
  // Process the form
  
  // validations
  $required_fields = array("first_name", "primary_breed");
  validate_presences($required_fields);
  
  $fields_with_max_lengths = array("first_name" => 30);
  validate_max_lengths($fields_with_max_lengths);
  
  if (empty($errors))
  {
    // Perform Create

    $pet_username = mysql_prep($_POST["pet_username"]);
    $first_name = mysql_prep($_POST["first_name"]);
    $last_name = mysql_prep($_POST["last_name"]);
    $breed1 = mysql_prep($_POST["primary_breed"]);
    $breed2 = mysql_prep($_POST["secondary_breed"]);
    $breed3 = mysql_prep($_POST["tertiary_breed"]);
    $weight = mysql_prep($_POST["weight"]);
    //$dog_park = mysql_prep($_POST["dog_park"]);
    $birth_day = mysql_prep($_POST["birth_day"]);
    $birth_month = mysql_prep($_POST["birth_month"]);
    $birth_year = mysql_prep($_POST["birth_year"]);
    $birthdate = $birth_year . "-" . $birth_month . "-" . $birth_day;
    $profile_pic = mysql_prep($_POST["profile_pic"]);
    $likes = mysql_prep($_POST["likes"]);
    $dislikes = mysql_prep($_POST["dislikes"]);
    
    $query  = "INSERT INTO pets (";
    $query .= " username, first_name, last_name, birthdate, breed1, breed2, breed3, weight, profile_pic, likes, dislikes ";
    $query .= ") VALUES (";
    $query .= " '{$pet_username}', '{$first_name}', '{$last_name}', '{$birthdate}', '{$breed1}', '{$breed2}', '{$breed3}', '{$weight}', '{$profile_pic}', '{$likes}', '{$dislikes}'";
    $query .= ")";
    $result = mysqli_query($connection, $query);

    if ($result)
    {
      $_SESSION["message"] = "Dog profile created.";
    }
    else
    {
      // Failure
      $_SESSION["message"] = "Dog profile creation failed.";
    }

    // Setting up ways to get resources from tables
    $parks = find_park_by_name($_POST["dog_park"]);
    $pets = find_pet_by_username($_POST["pet_username"]);
    $user = find_user_by_id($_SESSION["user_id"]);

    foreach($_POST["dog_park"] as $selectedOption)
    {
      // Create pets_parks_intersection association
      $query = "INSERT INTO pets_parks_intersection (";
      $query .= " pet_id, park_id";
      $query .= ") VALUES (";
      $query .= " {$pets["pet_id"]}, {$selectedOption}"; // selectedOption used to be $parks["park_id"]
      $query .= ")";
      $result = mysqli_query($connection, $query);

      if ($result)
      {
        //$_SESSION["message"] .= "Park/pets link success.";
      }
      else
      {
        // Failure
        $_SESSION["message"] .= " Park/pets link failed.";
      }
    } // end foreach

    // Create users_pets_intersection association
    $query = "INSERT INTO users_pets_intersection (";
    $query .= " user_id, pet_id";
    $query .= ") VALUES (";
    $query .= " {$user["user_id"]}, {$pets["pet_id"]}";
    $query .= ")";
    $result = mysqli_query($connection, $query);

    if ($result)
    {
      //$_SESSION["message"] .= "User/pets link success.";
    }
    else
    {
      // Failure
      $_SESSION["message"] .= " User/pets link failed.";
    }

    redirect_to("manage_dogs.php");
  }
} else {
  // This is probably a GET request
  
} // end: if (isset($_POST['submit']))

?>

<?php include("../includes/layouts/header.php"); ?>

    <?php echo message(); ?>
    <?php echo form_errors($errors); ?>
    
    <h2>Create Dog Profile</h2>
    <form action="new_pet.php" method="post">
      <table>
        <tr>
          <td><b>Pet Username:</b></td>
          <td><input type="text" name="pet_username" value="" /></td>
        </tr>
        <tr>
          <td><b>First Name:</b></td>
          <td><input type="text" name="first_name" value="" /></td>
        </tr>
        <tr>
          <td><b>Last Name:</b></td>
          <td><input type="text" name="last_name" value="" /></td>
        </tr>
        <tr>
          <td><b>Primary Breed:</b></td>
          <td>
            <select name="primary_breed">
            <?php

              $breeds = find_all_breeds();
              echo "<option selected=\"selected\"></option>";
              while($breed = mysqli_fetch_assoc($breeds))
                {
                  echo "<option value = \"" . $breed["breed_name"] . "\">" . $breed["breed_name"] . "</option>";
                }
            ?>
            </select>
          </td>
        </tr>
        <tr>
          <td><b>Secondary Breed:</b></td>
          <td>
            <select name="secondary_breed">
            <?php

              $breeds = find_all_breeds();
              echo "<option selected=\"selected\"></option>";
              while($breed = mysqli_fetch_assoc($breeds))
                {
                  echo "<option value = \"" . $breed["breed_name"] . "\">" . $breed["breed_name"] . "</option>";
                }
            ?>
            </select>
          </td>
        </tr>
        <tr>
          <td><b>Tertiary Breed:</b></td>
          <td>
            <select name="tertiary_breed">
            <?php

              $breeds = find_all_breeds();
              echo "<option selected=\"selected\"></option>";
              while($breed = mysqli_fetch_assoc($breeds))
                {
                  echo "<option value = \"" . $breed["breed_name"] . "\">" . $breed["breed_name"] . "</option>";
                }
            ?>
            </select>
          </td>
        </tr>
        <tr>
          <td><b>Birth date:</b></td>
          <td>
            <select name="birth_month">
              <option value="1">January</option>
              <option value="2">February</option>
              <option value="3">March</option>
              <option value="4">April</option>
              <option value="5">May</option>
              <option value="6">June</option>
              <option value="7">July</option>
              <option value="8">August</option>
              <option value="9">September</option>
              <option value="10">October</option>
              <option value="11">November</option>
              <option value="12">December</option>
            </select>
            <select name="birth_day">
                <?php
                  $count = 1;
                  while ($count <= 31)
                  {
                    echo "<option value = \"" . $count . "\">" . $count . "</option>";
                    $count++;
                  }
                ?>
              </select>
              <select name="birth_year">
                <?php
                  $year = date("Y") - 25;
                  while ($year <= date("Y"))
                  {
                    echo "<option value = \"" . $year . "\">" . $year . "</option>";
                    $year++;
                  }
                ?>
              </select>
          </td>
        </tr>
        <tr>
          <td><b>Weight:</b></td>
          <td><input type="text" name="weight" value=""/></td>
        </tr>
        <tr>
          <td><b>Dog Park:</b></td>
          <td>
            <select name="dog_park[]" multiple="multiple" size="3">
            <?php
              $parks = find_all_parks();
              echo "<option selected=\"selected\"></option>";
              while($park = mysqli_fetch_assoc($parks))
                {
                  echo "<option value = \"" . $park["park_id"] . "\">" . $park["park_name"] . "</option>";
                }
            ?>
            </select>
          </td>
        </tr>
        <tr>
          <td><b>Profile picture:</b><br/>
                (Direct URL only)</td>
          <td><input type="text" name="profile_pic" value="" /></td>
        </tr>
        <tr>
          <td><b>Likes:</b></td>
          <td><textarea name="likes" rows="5" cols="25"></textarea></td>
        </tr>
        <tr>
          <td><b>Dislikes:</b></td>
          <td><textarea name="dislikes" rows="5" cols="25"></textarea></td>
        </tr>
      </table>
      <input type="submit" class="button" name="submit" value="Add Your Dog!" />
    </form>
    <br />
    <a href="manage_dogs.php">Cancel</a>

<?php include("../includes/layouts/footer.php"); ?>
