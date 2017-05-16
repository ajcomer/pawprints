<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php require_once("../includes/validation_functions.php"); ?>
<?php include("../includes/layouts/header.php"); ?>

<?php $pet = find_pet_by_id($_GET["pet_id"]); ?>

    <?php echo message(); ?>
    <h1><?php echo $pet["username"]; ?></h1>
    <img src=<?php echo "\"" . htmlentities($pet["profile_pic"]) . "\""; ?> align="right" width="150" height="200" class="profile"/>
    
    <b>About Me:</b><br/>
    My name is <?php echo $pet["first_name"]; ?>! I'm <?php echo (date('Y-m-d') - $pet["birthdate"]); ?> years old, since my birthday is <?php echo $pet["birthdate"]; ?>.   
    <?php
    	// use empty() and not isset() until I can figure out how to make the
    	// default values for the breeds "null" and not just "" (an empty string)
    	if (!(empty($pet["breed1"])) && !(empty($pet["breed2"])) && !(empty($pet["breed3"])))
    		echo "My breeds are " . $pet["breed1"] . ", " . $pet["breed2"] . ", and " . $pet["breed3"] . ".";
    	elseif (!(empty($pet["breed1"])) && !(empty($pet["breed2"])) && (empty($pet["breed3"])))
    		echo "My breeds are " . $pet["breed1"] . " and " . $pet["breed2"] . ".";
    	elseif (!(empty($pet["breed1"])) && (empty($pet["breed2"])) && (empty($pet["breed3"])))
    		echo "I am a purebred " . $pet["breed1"] . ".";

        if (isset($pet["weight"]) && !(empty($pet["weight"])))
            echo " I weigh in at " . $pet["weight"] . ".";
    ?>
    </br><br/>

    <b>My commands:</b><br/>
    </br><br/>

    <b>Likes:</b><br/>
    <?php echo $pet["likes"]; ?></br><br/>

    <b>Dislikes:</b><br/>
    <?php echo $pet["dislikes"]; ?></br><br/>

    <b>I'm registered at the following parks:</b><br/>

    <?php
        $park_set = find_all_parks_of_pet($pet["pet_id"]);
        while($nextPark = mysqli_fetch_assoc($park_set)) {
            echo $nextPark["park_name"] . "<br/>";
        }
    ?>

<?php include("../includes/layouts/footer.php"); ?>