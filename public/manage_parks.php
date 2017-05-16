<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php include("../includes/layouts/header.php"); ?>

<?php confirm_logged_in(); ?>
<?php $park_set = find_all_parks(); ?>

    <?php echo message(); ?>
    <h2>Manage Parks</h2>
    <table>
      <tr>
        <th style="text-align: left; width: 50px;">Park ID</th>
        <th style="text-align: left; width: 150px">Park Name</th>
        <th style="text-align: left; width: 300px;">Location</th>
        <th colspan="2" style="text-align: left;">Actions</th>
      </tr>
    <?php while($park = mysqli_fetch_assoc($park_set)) { ?>
      <tr>
        <td><?php echo htmlentities($park["park_id"]); ?></td>
        <td><?php echo htmlentities($park["park_name"]); ?></td>
        <td>
            <?php
                echo htmlentities($park["street"]) . ", " . htmlentities($park["city"]) . ", " . 
                htmlentities($park["state"]) . " " . htmlentities($park["zip_code"]) . " " . htmlentities($park["country"]);
            ?>
        </td>
        <td><a href="edit_parks.php?park_id=<?php echo urlencode($park["park_id"]); ?>">Edit</a></td>
        <td><a href="delete_parks.php?park_id=<?php echo urlencode($park["park_id"]); ?>" onclick="return confirm('Are you sure?');">Delete</a></td>
      </tr>
    <?php } ?>
    </table>
    <br />
    <a href="new_park.php">+ Add new park</a>

<?php include("../includes/layouts/footer.php"); ?>
