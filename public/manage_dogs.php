<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php include("../includes/layouts/header.php"); ?>

<?php confirm_logged_in(); ?>
<?php $pet_set = find_all_pets(); ?>

    <?php echo message(); ?>
    <h2>Manage Dogs</h2>
    <table>
      <tr>
        <th style="text-align: left; width: 50px;">Pet ID</th>
        <th style="text-align: left; width: 50px">Owner ID</th>
        <th style="text-align: left; width: 120px;">Pet Name</th>

        <th colspan="3" style="text-align: left;">Actions</th>
      </tr>
    <?php while($pet = mysqli_fetch_assoc($pet_set)) { ?>
      <tr>
        <td><?php echo htmlentities($pet["pet_id"]); ?></td>
        <td><?php
                $user_set = find_user_of_pet($pet["pet_id"]);
                if ($user = mysqli_fetch_assoc($user_set))
                    echo $user["user_id"];
            ?></td>
        <td><?php echo htmlentities($pet["first_name"]); ?></td>

        <td><a href="view_pet_profile.php?pet_id=<?php echo urlencode($pet["pet_id"]); ?>">View Profile</a></td>
        <td><a href="edit_pets.php?pet_id=<?php echo urlencode($pet["pet_id"]); ?>">Edit</a></td>
        <td><a href="delete_pets.php?pet_id=<?php echo urlencode($pet["pet_id"]); ?>" onclick="return confirm('Are you sure?');">Delete</a></td>
      </tr>
    <?php } ?>
    </table>
    <br />
    <a href="new_pet.php">+ Add new dog</a>

<?php include("../includes/layouts/footer.php"); ?>
