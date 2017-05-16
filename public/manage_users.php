<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php include("../includes/layouts/header.php"); ?>

<?php confirm_admin(); ?>
<?php $user_set = find_all_users(); ?>

    <?php echo message(); ?>
    <h2>Manage Users/Admins</h2>
    <table>
      <tr>
        <th style="text-align: left; width: 150px;">Username</th>
        <th style="text-align: left; width: 100px">Admin</th>
        <th colspan="3" style="text-align: left;">Actions</th>
      </tr>
    <?php while($user = mysqli_fetch_assoc($user_set)) { ?>
      <tr>
        <td><?php echo htmlentities($user["username"]); ?></td>
        <td><?php
              if ($user["admin_access"] == 1)
              {
                echo "Yes";
              }
              else
              {
                echo "No";
              }

            ?>
        <td><a href="view_user_profile.php?user_id=<?php echo urlencode($user["user_id"]); ?>">View Profile</a></td>
        <td><a href="edit_profile.php?user_id=<?php echo urlencode($user["user_id"]); ?>">Edit</a></td>
        <td><a href="delete_users.php?user_id=<?php echo urlencode($user["user_id"]); ?>" onclick="return confirm('Are you sure?');">Delete</a></td>
      </tr>
    <?php } ?>
    </table>
    <br />
    <a href="new_user.php">+ Add new user/admin</a>

<?php include("../includes/layouts/footer.php"); ?>
