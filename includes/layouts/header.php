<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html lang="en">
<head>
  <title>paw prince</title>
  <meta name="description" content="website description" />
  <meta name="keywords" content="website keywords, website keywords" />
  <meta http-equiv="content-type" content="text/html; charset=windows-1252" />
  <link rel="stylesheet" type="text/css" href="stylesheets/style.css" />
</head>

<body>
  <div id="main">
    <div id="header">
      <div id="logo">
        <div id="logo_text">
          <!-- class="logo_colour", allows you to change the colour of the text -->
          <h1><a href="index.php">paw<span class="logo_colour">prince</span></a></h1>
          <h2>meet dogs. make friends. love life.</h2>
        </div>
      </div>
      <div id="menubar">
        <ul id="menu">
          <!-- put class="selected" in the li tag for the selected page - to highlight which page you're on -->
          <li class="selected"><a href="index.php">Home</a></li>
          <li><a href="about.php">About Us</a></li>
          <li><a href="dog_parks.php">Dog Parks</a></li>
          <li><a href="uindex.php">Account</a></li>
          <li><a href="contact.php">Contact Us</a></li>
        </ul>
      </div>
    </div>
    <div id="content_header"></div>
    <div id="site_content">
      <!--<div id="banner"></div>-->
	  <div id="sidebar_container">

        <div class="sidebar">
          <div class="sidebar_top"></div>
          <div class="sidebar_item">
            <!-- insert your sidebar items here -->
            <h3>Latest News</h3>
	        <h4>"PP" Launched</h4>
	        <h5>July 18, 2015</h5>
	        <p>Opened Paw Prince!</p>
          </div>
          <div class="sidebar_base"></div>
        </div>

        <div class="sidebar">
          <div class="sidebar_top"></div>
          <div class="sidebar_item">
	        <?php
	        	if (logged_in_admin() || logged_in_user()) {
	        		$output = "Logged in as <b>" . $_SESSION["username"] . "</b>.";
	        		$output .= "<ul><li><a href=\"logout.php\">Logout</a></li>";
	   				echo $output;
	        	}
	        	else {
	        		$output = "<form action=\"login.php\" method=\"post\">";
	      			$output .= "<p>Username:";
	        		$output .= "<input type=\"text\" name=\"username\" value=\"\" />";
				    $output .= "</p>";
				    $output .= "<p>Password:";
				    $output .= "<input type=\"password\" name=\"password\" value=\"\" />";
				    $output .= "</p>";
				    $output .= "<input type=\"submit\" class=\"button\" name=\"submit\" value=\"Login\" />";
				    $output .= "</form><ul>";
	        		echo $output;
	        	}
	        ?>
          </div>
          <div class="sidebar_base"></div>
        </div>

      </div>
      <div id="content">