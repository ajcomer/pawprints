<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php include("../includes/layouts/header.php"); ?>
<?php $park_set = find_all_parks(); ?>

  <?php echo message(); ?>
  
  <h2>Dog Parks</h2>

  - The public can only see the below chart<br/>
  - Users can REQUEST that a park be added<br/>
  - Users can see which dogs exactly are registered to each park, and their profiles<br/>
  - Users can see the owner's profile too (listed on the dog's profile page), if it's not set to "private"<br/>
  - Allow users to search the dog park directory for dog parks closest to a zip code, based on what they want (lots of small dogs, big dogs, calm dogs, etc)<br/>
  - <br/>
  <table>
      <tr>
        <th style="text-align: left; width: 150px;">Park Name</th>
        <th style="text-align: left; width: 300px;">Location</th>
        <th style="text-align: left; width: 120px;">Dogs Registered</th>
      </tr>
    <?php while($park = mysqli_fetch_assoc($park_set)) { ?>
      <tr>
        <td><?php echo htmlentities($park["park_name"]); ?></td>
        <td>
        	<?php
        		echo htmlentities($park["street"]) . ", " . htmlentities($park["city"]) . ", " . 
        		htmlentities($park["state"]) . " " . htmlentities($park["zip_code"]) . " " . htmlentities($park["country"]);
        	?>
        </td>
        <td>
        	<?php
            $dog_count = find_dogs_in_park($park["park_id"]);
            echo $dog_count;
          ?>
        </td>
      </tr>
    <?php } ?>
    </table>

<?php include("../includes/layouts/footer.php"); ?>