<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php include("../includes/layouts/header.php"); ?>

<?php confirm_logged_in(); ?>
<?php $pet_set = find_all_pets_of_user($_SESSION["user_id"]); ?>

    <?php echo message(); ?>
    <h2>My Dogs</h2>
    <table>
      <tr>
        <th style="text-align: left; width: 120px;">Pet Name</th>
        <th style="text-align: left; width: 150px">Primary Breed</th>
        <th colspan="3" style="text-align: left;">Actions</th>
      </tr>
    <?php while($pet = mysqli_fetch_assoc($pet_set)) { ?>
      <tr>
        <td><?php echo htmlentities($pet["first_name"]); ?></td>
        <td><?php echo htmlentities($pet["breed1"]); ?></td>
        <td><a href="view_pet_profile.php?pet_id=<?php echo urlencode($pet["pet_id"]); ?>">View Profile</a></td>
        <td><a href="edit_pets.php?pet_id=<?php echo urlencode($pet["pet_id"]); ?>">Edit</a></td>
        <td><a href="delete_pets.php?pet_id=<?php echo urlencode($pet["pet_id"]); ?>" onclick="return confirm('Are you sure?');">Delete</a></td>
      </tr>
    <?php } ?>
    </table>
    <br />
    <a href="new_pet.php">+ Add new dog</a>

<?php include("../includes/layouts/footer.php"); ?>
