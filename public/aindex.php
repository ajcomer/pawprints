<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php include("../includes/layouts/header.php"); ?>

<?php confirm_admin(); ?>

    <h2>Admin Menu</h2>

    <p>Welcome to the admin area, <?php echo htmlentities($_SESSION["username"]); // SESSION["username"] is set & saved in the SESSION in login.php ?>.</p>
    <ul>
      <li><a href="profile.php">Profile</a></li>
    </ul>

<?php include("../includes/layouts/footer.php"); ?>
