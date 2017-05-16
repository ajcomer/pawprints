<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php require_once("../includes/validation_functions.php"); ?>
<?php confirm_logged_in(); ?>

<?php
  $pet = find_pet_by_id($_GET["pet_id"]);
  
  if (!$pet) {
    // pet_id was missing or invalid or 
    // pet couldn't be found in database
    redirect_to("index.php");
  }
?>

<?php
if (isset($_POST['submit'])) {
  // Process the form
  
  // validations
  $required_fields = array("first_name", "primary_breed");
  validate_presences($required_fields);
  
  $fields_with_max_lengths = array("first_name" => 30);
  validate_max_lengths($fields_with_max_lengths);
  
  if (empty($errors)) {
    
    // Perform Update
    $pet_id = $pet["pet_id"];
    $first_name = mysql_prep($_POST["first_name"]);
    $last_name = mysql_prep($_POST["last_name"]);
    $breed1 = mysql_prep($_POST["primary_breed"]);
    $breed2 = mysql_prep($_POST["secondary_breed"]);
    $breed3 = mysql_prep($_POST["tertiary_breed"]);
    $weight = mysql_prep($_POST["weight"]);
    $dog_park = mysql_prep($_POST["dog_park"]);
    $birth_day = mysql_prep($_POST["birth_day"]);
    $birth_month = mysql_prep($_POST["birth_month"]);
    $birth_year = mysql_prep($_POST["birth_year"]);
    $birthdate = $birth_year . "-" . $birth_month . "-" . $birth_day;
    $profile_pic = mysql_prep($_POST["profile_pic"]);
    $likes = mysql_prep($_POST["likes"]);
    $dislikes = mysql_prep($_POST["dislikes"]);
  
    $query  = "UPDATE pets SET ";
    $query .= "first_name = '{$first_name}', ";
    $query .= "last_name = '{$last_name}', ";
    $query .= "birthdate = '{$birthdate}', ";
    $query .= "breed1 = '{$breed1}', ";
    $query .= "breed2 = '{$breed2}', ";
    $query .= "breed3 = '{$breed3}', ";
    $query .= "weight = '{$weight}', ";
    //$query .= "dog_park = '{$dog_park}', ";
    $query .= "profile_pic = '{$profile_pic}', ";
    $query .= "likes = '{$likes}', ";
    $query .= "dislikes = '{$dislikes}' ";
    $query .= "WHERE pet_id = {$pet_id} ";
    $query .= "LIMIT 1";
    $result = mysqli_query($connection, $query);

    if ($result && mysqli_affected_rows($connection) >= 1)
    {
      // Success
      $_SESSION["message"] = "Pet profile updated.";
    }
    elseif ($result && mysqli_affected_rows($connection) == 0)
    {
      $_SESSION["message"] = "No changes made.";
    }
    else
    {
      // Failure
      $_SESSION["message"] = "Pet profile update failed. {$query}";

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
    
    <h2>Edit Profile: <?php echo htmlentities($pet["username"]); ?></h2>
    <form action="edit_pets.php?pet_id=<?php echo urlencode($pet["pet_id"]); ?>" method="post">
      <table>
        <tr>
          <td><b>First Name:</b></td>
          <td>
            <input type="text" name="first_name" value="<?php
              if (isset($pet["first_name"]))
                echo htmlentities($pet["first_name"]);
              else
                echo "";
            ?>" />
          </td>
        </tr>
        <tr>
          <td><b>Last Name:</b></td>
          <td>
            <input type="text" name="last_name" value="<?php
              if (isset($pet["last_name"]))
                echo htmlentities($pet["last_name"]);
              else
                echo "";
            ?>" />
          </td>
        </tr>
          <td><b>Primary Breed:</b></td>
          <td>
            <select name="primary_breed">
            <?php

              $breeds = find_all_breeds();
              if (isset($pet["breed1"]))
                echo "<option selected=\"selected\">" . htmlentities($pet["breed1"]) ."</option>";
              else
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
              if (isset($pet["breed2"]))
                echo "<option selected=\"selected\">" . htmlentities($pet["breed2"]) ."</option>";
              else
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
              if (isset($pet["breed3"]))
                echo "<option selected=\"selected\">" . htmlentities($pet["breed3"]) ."</option>";
              else
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
          <td>
              <input type="text" name="weight" value="<?php
              if (isset($pet["weight"]))
                echo htmlentities($pet["weight"]);
              else
                echo "";
            ?>" />
          </td>
        </tr>
        <tr>
          <td><b>Registered at:</b></td>
          <td>
            <?php
              $park_set = find_all_parks_of_pet($pet["pet_id"]);
              while($nextPark = mysqli_fetch_assoc($park_set)) {
              echo $nextPark["park_name"] . "<br/>"; }
            ?>
            <!--<select name="dog_park" size="3">
            <?php
              $parks = find_all_parks();
              if (isset($pet["dog_park"]))
                echo "<option selected=\"selected\">" . htmlentities($pet["dog_park"]) ."</option>";
              else
                echo "<option selected=\"selected\"></option>";
              while($park = mysqli_fetch_assoc($parks))
                {
                  echo "<option value = \"" . $park["park_name"] . "\">" . $park["park_name"] . "</option>";
                }
            ?>
            </select>-->
            <br/>Click <a href="#">here</a> to register or <br/> de-register from certain parks.
          </td>
        </tr>
        <tr>
          <td><b>Profile picture:</b><br/>
                (Direct URL only)</td>
          <td><input type="text" name="profile_pic" value="<?php
              if (isset($pet["profile_pic"]))
                echo htmlentities($pet["profile_pic"]);
              else
                echo "";
            ?>" />
          </td>
        </tr>
        <tr>
          <td><b>Likes:</b></td>
          <td>
            <textarea name="likes" rows="5" cols="25"><?php
                if (isset($pet["likes"]))
                  echo htmlentities($pet["likes"]);
                else
                  echo "";
              ?>
            </textarea>
          </td>
        </tr>
        <tr>
          <td><b>Dislikes:</b></td>
          <td>
            <textarea name="dislikes" rows="5" cols="25"><?php
                if (isset($pet["dislikes"]))
                  echo htmlentities($pet["dislikes"]);
                else
                  echo "";
              ?>
            </textarea>
          </td>
        </tr>
      </table>
      <input type="submit" class="button" name="submit" value="Edit Pet Profile" />
    </form>
    <br />
    <?php
      $url = htmlspecialchars($_SERVER['HTTP_REFERER']);
      echo "<a href='$url'>Cancel</a>"; 
    ?>

<?php include("../includes/layouts/footer.php"); ?>
