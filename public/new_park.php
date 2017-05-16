<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php require_once("../includes/validation_functions.php"); ?>
<?php confirm_admin(); ?>

<?php
if (isset($_POST['submit'])) {
  // Process the form
  
  // validations
  $required_fields = array("parkName", "zip_code", "country");
  validate_presences($required_fields);
  
  $fields_with_max_lengths = array("parkName" => 30);
  validate_max_lengths($fields_with_max_lengths);
  
  if (empty($errors))
  {
    // Perform Create

    $parkName = mysql_prep($_POST["parkName"]);
    $street = mysql_prep($_POST["street"]);
    $city = mysql_prep($_POST["city"]);
    $state = mysql_prep($_POST["state"]);
    $zip_code = mysql_prep($_POST["zip_code"]);
    $country = mysql_prep($_POST["country"]);
    $sepSections = $_POST["separateSections"];
    $water = $_POST["water"];
    $description = mysql_prep($_POST["parkDescription"]);
    
    $query  = "INSERT INTO parks (";
    $query .= "  park_name, street, city, state, zip_code, country, separateSections, waterAvailable, parkDescription";
    $query .= ") VALUES (";
    $query .= "  '{$parkName}', '{$street}', '{$city}', '{$state}', '{$zip_code}', '{$country}', {$sepSections}, {$water}, '{$description}'";
    $query .= ")";
    $result_parks = mysqli_query($connection, $query);

    if ($result_parks)
    {
        $_SESSION["message"] = "Park created.";
    }
    else
    {
      // Failure
      $_SESSION["message"] = "Park creation failed.";
    }

    redirect_to("manage_parks.php");
  }
} else {
  // This is probably a GET request
  
} // end: if (isset($_POST['submit']))

?>

<?php include("../includes/layouts/header.php"); ?>

    <?php echo message(); ?>
    <?php echo form_errors($errors); ?>
    
    <h2>Create Park</h2>
    <form action="new_park.php" method="post">
      <table>
        <tr>
          <td><b>Park Name:</b></td>
          <td><input type="text" name="parkName" value="" /></td>
        </tr>
        <tr>
          <td><b>Street:</b></td>
          <td><input type="text" name="street" value="" /></td>
        </tr>
        <tr>
          <td><b>City:</b></td>
          <td><input type="text" name="city" value="" /></td>
        </tr>
         <tr>
          <td><b>State:</b></td>
          <td><input type="text" name="state" value="" /></td>
        </tr>
         <tr>
          <td><b>Zip code:</b></td>
          <td><input type="text" name="zip_code" value="" /></td>
        </tr>
         <tr>
          <td><b>Country:</b></td>
          <td><input type="text" name="country" value="" /></td>
        </tr>
         <tr>
          <td><b>Small/large dogs are separated</b></td>
            <td><input type="radio" name="separateSections" value="null" /> No&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="separateSections" value="1" /> Yes</td>
        </tr>
        <tr>
          <td><b>Water bowls available:</b></td>
          <td><input type="radio" name="water" value="null" /> No&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="water" value="1" /> Yes</td>
         </tr>
         <tr>
          <td><b>Park Description:</b><br/>(Parking instructions, <br/>
          opening/closing times, etc.)</td>
          <td><textarea name="parkDescription" rows="5" cols="25"></textarea></td>
        </tr>
        </table>
      <input type="submit" class="button" name="submit" value="Create New Park" />
    </form>
    <br />
    <a href="manage_users.php">Cancel</a>

<?php include("../includes/layouts/footer.php"); ?>
