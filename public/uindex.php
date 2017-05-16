<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php include("../includes/layouts/header.php"); ?>
<?php confirm_logged_in(); ?>

    <h2>User Menu</h2>

    <ul>
      <li><a href="profile.php">My Profile</a></li>
      <li><a href="my_dogs.php">My Dogs</a></li>
    <?php
      if (logged_in_admin() && !(logged_in_user())) {
        $output = "<h2>Admin Menu</h2>";
        $output .= "<li><a href=\"manage_users.php\">Manage Users</a></li>";
        $output .= "<li><a href=\"manage_dogs.php\">Manage Dogs</a></li>";
        $output .= "<li><a href=\"manage_parks.php\">Manage Parks</a></li>";
        echo $output;
      }
    ?>
    </ul>

<?php include("../includes/layouts/footer.php"); ?>
