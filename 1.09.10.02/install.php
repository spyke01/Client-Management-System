<? 
/***************************************************************************
 *                               install.php
 *                            -------------------
 *   begin                : Tuseday, March 14, 2006
 *   copyright            : (C) 2006 Fast Track Sites
 *   email                : sales@fasttracksites.com
 *
 *
 ***************************************************************************/

/***************************************************************************
 *
 * This program is licensed under the Fast Track Sites Program license 
 * located inside the license.txt file included with this program. This is a 
 * legally binding license, and is protected by all applicable laws, by 
 * editing this page you fall subject to these licensing terms.
 *
 ***************************************************************************/
	ini_set('arg_separator.output','&amp;');
	//error_reporting(E_ALL);
	//ini_set('display_errors', '1');
	
	// Set up our installer
	define('INSTALLER_SCRIPT_NAME', 'Client Management System');
	define('INSTALLER_SCRIPT_DESC', 'The Fast Track Sites Client Management System was designed to allow businesses to keep track of current, past, and prospective clients in an easy to use and intuitive manner. We also wanted to allow them to keep track of their client\'s orders, take notes on the client(Their preferences, phone converstaions, etc), and easily schedule and track appointments. We\'ve made it easy to access multitudes of information on each client just by viewing their information. By doing that you\'ll be able to see all information related to this client from the various parts of the system at once.');
	define('INSTALLER_SCRIPT_IS_PROFESSIONAL_VERSION', 1);
	define('INSTALLER_SCRIPT_DB_PREFIX', 'CLMS_');
	
	// Inlcude the needed files
	include_once ('includes/constants.php');
	if (substr(phpversion(), 0, 1) == 5) { include_once ('includes/php5/pageclass.php'); }
	else { include_once ('includes/php4/pageclass.php'); }

	// Instantiate our page class
	$page = &new pageClass;

	// Handle our variables
	$requested_step = $_GET['step'];

	$actual_step = ($requested_step == "" || !isset($requested_step)) ? 1 : keepsafe($requested_step);
	$page_content = "";
	$failed = 0;
	$totalfailure = 0;
	$failed = array();
	$failedsql = array();
	$currentdate = time();

	
	//========================================
	// Custom Functions for this Page
	//========================================
	function keepsafe($makesafe) {
		$makesafe=strip_tags($makesafe); // strip away any dangerous tags
		$makesafe=str_replace(" ","",$makesafe); // remove spaces from variables
		$makesafe=str_replace("%20","",$makesafe); // remove escaped spaces
		$makesafe = trim(preg_replace('/[^\x09\x0A\x0D\x20-\x7F]/e', '"&#".ord($0).";"', $makesafe)); //encodes all ascii items above #127
		$makesafe = stripslashes($makesafe);
		
		return $makesafe;
	}
	
	function keeptasafe($makesafe) {
		$makesafe=strip_tags($makesafe); // strip away any dangerous tags
		$makesafe = trim(preg_replace('/[^\x09\x0A\x0D\x20-\x7F]/e', '"&#".ord($0).";"', $makesafe)); //encodes all ascii items above #127
		$makesafe = stripslashes($makesafe);
		
		return $makesafe;
	}

	function checkresult($result, $sql, $table) {
		global $failed;
		global $failedsql;
		global $totalfailure;
		
		if (!$result || $result == "") {
			$failed[$table] = "failed";
			$failedsql[$table] = $sql;
			$totalfailure = 1;
		}  
		else {
			$failed[$table] = "succeeded";
			$failedsql[$table] = $sql;
		}	
	}
	
	//========================================
	// Build our Page
	//========================================
	switch ($actual_step) {
		case 1:
			$page->setTemplateVar("PageTitle", INSTALLER_SCRIPT_NAME . " Step 1 - Introduction");	
			
			// Print this page
			$page_content = "
					<h2>Welcome to the Fast Track Sites Script Installer</h2>
					Thank you for downloading the " . INSTALLER_SCRIPT_NAME . " this page will walk you through the setup procedure.
					<br /><br />
					" . INSTALLER_SCRIPT_DESC . "
					<br /><br />
					<h2><span class=\"iconText38px\"><img src=\"themes/installer/icons/paperAndPencil_38px.png\" alt=\"License Agreement\" /></span> <span class=\"iconText38px\">License Agreement</span></h2>
					By installing this application you are agreeing to all the terms and conditions stated in the <a href=\"http://www.fasttracksites.com/ftspl\">Fast Track Sites Program License</a>.
					<br /><br />";
					
			if (INSTALLER_SCRIPT_IS_PROFESSIONAL_VERSION) {
				$page_content .= "
					Please enter your registration information below, failure to do so can result in your application being disabled.
					<br /><br />
					<form id=\"licenseInformationForm\" action=\"install.php?step=2\" method=\"post\">
						<label for=\"serialNumber\">Serial Number</label> <input type=\"text\" name=\"serialNumber\" id=\"serialNumber\" class=\"required\" />
						<label for=\"registeredTo\">Registered To</label> <input type=\"text\" name=\"registeredTo\" id=\"registeredTo\" class=\"required\" />
						<input type=\"submit\" name=\"submit\" class=\"button\" value=\"Next\" />
					</form>
					<script type=\"text/javascript\">
						var valid = new Validation('licenseInformationForm', {immediate : true, useTitles:true});
					</script>";
			}
			else {
				$page_content .= "
					<a href=\"install.php?step=2\" class=\"button\">I Agree</a>";			
			}
			break;
		case 2:
			$page->setTemplateVar("PageTitle", INSTALLER_SCRIPT_NAME . " Step 2 - Database Connection");	
			
			// Create our license file
			if (INSTALLER_SCRIPT_IS_PROFESSIONAL_VERSION) {
				$serialNumber = keepsafe($_POST['serialNumber']);
				$registeredTo = keeptasafe($_POST['registeredTo']);
			}
			else {
				$serialNumber = "Free Edition";
				$registeredTo = "Fast Track Sites";
			}
			
			$str = "<?PHP\n\n\$A_License = \"" . $serialNumber . "\";\n\$A_Licensed_To = \"" . $registeredTo . "\";\n\n?>";
		
			$fp=fopen("_license.php","w");
			$result = fwrite($fp,$str);
			fclose($fp);	
			
			// Print this page
			$page_content = "
					<h2>License File Results</h2>";
			
			if (!$result || $result == "") {
				$page_content .= "
					<span class=\"actionFailed\"><span class=\"iconText20px\"><img src=\"themes/installer/icons/delete_20px.png\" alt=\"Action Failed\" /></span> <span class=\"iconText20px\">Unable to create license file.</span></span>";
			}
			else {
				$page_content .= "
					<span class=\"actionSucceeded\"><span class=\"iconText20px\"><img src=\"themes/installer/icons/check_20px.png\" alt=\"Action Succeeded\" /></span> <span class=\"iconText20px\">Successfully created license file.</span></span>";
			}
			
			$page_content .= "
					<br /><br />
					<h2><span class=\"iconText38px\"><img src=\"themes/installer/icons/addDatabase_38px.png\" alt=\"Add Database\" /></span> <span class=\"iconText38px\">Configure Your Database Connection</span></h2>
					Please enter your database information below:
					<br /><br />
					<form id=\"databaseConnectionForm\" action=\"install.php?step=3\" method=\"post\">
						<label for=\"dbServer\">Server</label> <input type=\"text\" name=\"dbServer\" id=\"dbServer\" class=\"required\" />
						<label for=\"dbName\">Database Name</label> <input type=\"text\" name=\"dbName\" id=\"dbName\" class=\"required\" />
						<label for=\"dbUsername\">Username</label> <input type=\"text\" name=\"dbUsername\" id=\"dbUsername\" class=\"required\" />
						<label for=\"dbPassword\">Password</label> <input type=\"password\" name=\"dbPassword\" id=\"dbPassword\" class=\"required\" />
						<label for=\"dbTablePrefix\">Table Prefix</label> <input type=\"text\" name=\"dbTablePrefix\" id=\"dbTablePrefix\" class=\"required\" value=\"" . INSTALLER_SCRIPT_DB_PREFIX . "\" />
						<input type=\"submit\" name=\"submit\" class=\"button\" value=\"Next\" />
					</form>
					<script type=\"text/javascript\">
						var valid = new Validation('databaseConnectionForm', {immediate : true, useTitles:true});
					</script>";
			break;
		case 3:
			$page->setTemplateVar("PageTitle", INSTALLER_SCRIPT_NAME . " Step 3 - Create Databse Tables");	
			
			// Create our database connection file
			$dbServer = keepsafe($_POST['dbServer']); 
			$dbName = keepsafe($_POST['dbName']); 
			$dbUsername = keepsafe($_POST['dbUsername']); 
			$dbPassword = keepsafe($_POST['dbPassword']); 
			$dbTablePrefix = keepsafe($_POST['dbTablePrefix']); 
			
			$str = "<?PHP\n\n// Connect to the database\n\n\$server = \"" . $dbServer . "\";\n\$dbuser = \"" . $dbUsername . "\";\n\$dbpass = \"" . $dbPassword . "\";\n\$dbname = \"" . $dbName . "\";\n\$DBTABLEPREFIX = \"" . $dbTablePrefix . "\";\n\n\$connect = mysql_connect(\$server,\$dbuser,\$dbpass);\n\n//display error if connection fails\nif (\$connect==FALSE) {\n   print 'Unable to connect to database: '.mysql_error();\n   exit;\n}\n\nmysql_select_db(\$dbname); // select database\n\n?>";
		
			$fp=fopen("_db.php","w");
			$result = fwrite($fp,$str);
			fclose($fp);	
	
			// Print this page
			$page_content = "
					<h2>Database Connection Results</h2>";
			
			if (!$result || $result == "") {
				$page_content .= "
					<span class=\"actionFailed\"><span class=\"iconText20px\"><img src=\"themes/installer/icons/delete_20px.png\" alt=\"Action Failed\" /></span> <span class=\"iconText20px\">Unable to create databse connection file.</span></span>";
			}
			else {
				$page_content .= "
					<span class=\"actionSucceeded\"><span class=\"iconText20px\"><img src=\"themes/installer/icons/check_20px.png\" alt=\"Action Succeeded\" /></span> <span class=\"iconText20px\">Successfully created databse connection file.</span></span>";
			}
			
			$page_content .= "
					<br /><br />
					<h2><span class=\"iconText38px\"><img src=\"themes/installer/icons/table_38px.png\" alt=\"Create Tables\" /></span> <span class=\"iconText38px\">Create Databse Tables</span></h2>
					Press Next to create the database tables.
					<br /><br />
					<a href=\"install.php?step=4\" class=\"button\">Next</a>";
			break;
		case 4:
			$page->setTemplateVar("PageTitle", INSTALLER_SCRIPT_NAME . " Step 4 - Create Admin Account");	
			
			include('_db.php');
			
			// Create our Database Tables
			$sql = "CREATE TABLE `" . $DBTABLEPREFIX . "config` (
					`config_name` varchar(255) NOT NULL default '',
					`config_value` varchar(255) NOT NULL default ''
					) TYPE=MyISAM;";
			$result = mysql_query($sql);
			checkresult($result, $sql, "config");
	  		
			$sql = "CREATE TABLE `" . $DBTABLEPREFIX . "appointments` (
					`appointments_id` mediumint(8) NOT NULL auto_increment,
					`appointments_client_id` mediumint(8) NOT NULL,
					`appointments_datetimestamp` varchar(50) NOT NULL default '',
					`appointments_place` varchar(100) NOT NULL default '',
					`appointments_description` text NOT NULL,
					`appointments_urgency` tinyint(1) NOT NULL default '0',
					`appointments_attire` varchar(50) NOT NULL default '',
				  	PRIMARY KEY  (`appointments_id`)
					) TYPE=MyISAM AUTO_INCREMENT=1 ;";
			$result = mysql_query($sql);
			checkresult($result, $sql, "appointments");
	
			$sql = "CREATE TABLE `" . $DBTABLEPREFIX . "categories` (
					`cat_id` mediumint(8) NOT NULL auto_increment,
					`cat_name` varchar(50) NOT NULL default '',
					PRIMARY KEY  (`cat_id`)
					) TYPE=MyISAM AUTO_INCREMENT=1 ;";
			$result = mysql_query($sql);
			checkresult($result, $sql, "categories");
	
			$sql = "CREATE TABLE `" . $DBTABLEPREFIX . "clients` (
					`clients_id` mediumint(8) NOT NULL auto_increment,
					`clients_cat_id` mediumint(8) NOT NULL,
					`clients_first_name` varchar(50) NOT NULL default '',
					`clients_last_name` varchar(50) NOT NULL default '',
					`clients_title` varchar(50) NOT NULL default '',
					`clients_company` varchar(50) NOT NULL default '',
					`clients_street1` varchar(50) NOT NULL default '',
					`clients_street2` varchar(50) NOT NULL default '',
					`clients_city` varchar(50) NOT NULL default '',
					`clients_state` varchar(50) NOT NULL default '',
					`clients_zip` varchar(50) NOT NULL default '',
					`clients_daytime_phone` varchar(50) NOT NULL default '',
					`clients_nighttime_phone` varchar(50) NOT NULL default '',
					`clients_cell_phone` varchar(50) NOT NULL default '',
					`clients_email_address` varchar(50) NOT NULL default '',
					`clients_website` varchar(50) NOT NULL default '',
					`clients_preffered_client` tinyint(1) NOT NULL default '0',
					`clients_found_us_through` varchar(50) NOT NULL default '',
					`clients_date` varchar(20) NOT NULL default '',
					PRIMARY KEY  (`clients_id`)
					) TYPE=MyISAM AUTO_INCREMENT=1 ;";
			$result = mysql_query($sql);
			checkresult($result, $sql, "pages");
	
			$sql = "CREATE TABLE `" . $DBTABLEPREFIX . "notes` (
				  `notes_id` mediumint(8) NOT NULL auto_increment,
				  `notes_client_id` mediumint(8) NOT NULL,
				  `notes_datetimestamp` varchar(50) NOT NULL default '',
				  `notes_note` text NOT NULL,
				  `notes_urgency` tinyint(1) NOT NULL default '0',
					PRIMARY KEY  (`notes_id`)
					) TYPE=MyISAM AUTO_INCREMENT=1 ;";
			$result = mysql_query($sql);
			checkresult($result, $sql, "notes");
	
			$sql = "CREATE TABLE `" . $DBTABLEPREFIX . "orders` (
				  `orders_id` mediumint(8) NOT NULL auto_increment,
				  `orders_client_id` mediumint(8) NOT NULL,
				  `orders_number` varchar(50) NOT NULL default '',
				  `orders_date_ordered` varchar(50) NOT NULL default '',
				  `orders_date_shipped` varchar(50) NOT NULL default '',
				  `orders_total` decimal(12,2) NOT NULL default '0.00',
				  `orders_tracking_no` varchar(50) NOT NULL default '',
				  `orders_shipped_by` varchar(50) NOT NULL default '',
					PRIMARY KEY  (`orders_id`)
					) TYPE=MyISAM AUTO_INCREMENT=1 ;";
			$result = mysql_query($sql);
			checkresult($result, $sql, "orders");
	
			$sql = "CREATE TABLE `" . $DBTABLEPREFIX . "shippers` (
					`shippers_id` mediumint(8) NOT NULL auto_increment,
					`shippers_name` varchar(50) NOT NULL default '',
					PRIMARY KEY  (`shippers_id`)
					) TYPE=MyISAM AUTO_INCREMENT=1 ;";
			$result = mysql_query($sql);
			checkresult($result, $sql, "shippers");
	
			$sql = "CREATE TABLE `" . $DBTABLEPREFIX . "users` (
				  `users_id` mediumint(8) NOT NULL auto_increment,
				  `users_active` tinyint(1) NOT NULL default '1',
				  `users_user_level` tinyint(4) NOT NULL default '0',
				  `users_username` varchar(25) NOT NULL default '',
				  `users_password` varchar(32) NOT NULL default '',
				  `users_first_name` varchar(50) NOT NULL default '',
				  `users_last_name` varchar(50) NOT NULL default '',
				  `users_email_address` varchar(35) NOT NULL default '',
				  `users_website` varchar(100) NOT NULL default '',
				  `users_company` varchar(50) NOT NULL,
				  `users_signup_date` int(11) default NULL,
				  `users_notes` text NOT NULL,
					PRIMARY KEY  (`users_id`)
					) TYPE=MyISAM AUTO_INCREMENT=1 ;";
			$result = mysql_query($sql);
			checkresult($result, $sql, "users");
	
			$sql = "INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftsclms_theme', 'default');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "configinsert1");
	
			$sql = "INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftsclms_cookie_name', 'ftsclms');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "configinsert2");
	
			$sql = "INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftsclms_active', '1');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "configinsert3");
	
			$sql = "INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftsclms_inactive_msg', '');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "configinsert4");
	
			$sql = "INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftsclms_currency_type', '$');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "configinsert5");
	
			$sql = "INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftsclms_time_zone', '-4.00');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "configinsert6");
		
			// Print this page
			$page_content = "
					<h2>Insert Table Results</h2>";
			
			if ($totalfailure == 1) {
				$page_content .= "
					<span class=\"actionFailed\"><span class=\"iconText20px\"><img src=\"themes/installer/icons/delete_20px.png\" alt=\"Action Failed\" /></span> <span class=\"iconText20px\">Unable to create databse tables.</span></span>";
			}
			else {
				$page_content .= "
					<span class=\"actionSucceeded\"><span class=\"iconText20px\"><img src=\"themes/installer/icons/check_20px.png\" alt=\"Action Succeeded\" /></span> <span class=\"iconText20px\">Successfully created databse tables.</span></span>";
			}
			
			$page_content .= "
					<br /><br />
					<h2><span class=\"iconText38px\"><img src=\"themes/installer/icons/addUser_38px.png\" alt=\"Add User\" /></span> <span class=\"iconText38px\">Create Your Admin Account</span></h2>
					Please enter your admin user information below:
					<form id=\"adminAccountForm\" action=\"install.php?step=5\" method=\"post\">
						<label for=\"usrUsername\">Username</label> <input type=\"text\" name=\"usrUsername\" id=\"usrUsername\" class=\"required validate-alphanum\" />
						<label for=\"usrEmailAddress\">Email Address</label> <input type=\"text\" name=\"usrEmailAddress\" id=\"usrEmailAddress\" class=\"required validate-email\" />
						<label for=\"usrPassword\">Password</label> <input type=\"password\" name=\"usrPassword\" id=\"usrPassword\" class=\"required validate-password\" />
						<label for=\"usrConfirmPassword\">Confirm Password</label> <input type=\"password\" name=\"usrConfirmPassword\" id=\"usrConfirmPassword\" class=\"required validate-password-confirm\" />
						<input type=\"submit\" name=\"submit\" class=\"button\" value=\"Next\" />
					</form>
					<script type=\"text/javascript\">
						var valid = new Validation('adminAccountForm', {immediate : true, useTitles:true});
						Validation.addAllThese([
								['validate-password', 'Your password must be at least 7 characters and cannot be your username, the word password, 1234567, or 0123456.', {
								minLength : 7,
								notOneOf : ['password','PASSWORD','1234567','0123456'],
								notEqualToField : 'usrUsername'
							}],
							['validate-password-confirm', 'Your passwords do not match.', {
								equalToField : 'usrPassword'
							}]
						]);
					</script>";
			break;
		case 5:
			$page->setTemplateVar("PageTitle", INSTALLER_SCRIPT_NAME . " Step 5 - Finish");	
			
			include('_db.php');
				
	    	// Create our admin account
			$usrUsername = keepsafe($_POST['usrUsername']); 
			$usrPassword = md5(keepsafe($_POST['usrPassword']));
			$usrEmailAddress = keepsafe($_POST['usrEmailAddress']);
		
	    	$sql = "INSERT INTO `" . $DBTABLEPREFIX . "users` (`users_username`, `users_password`, `users_email_address`, `users_signup_date`, `users_notes`, `users_user_level`) VALUES ('" . $usrUsername . "', '" . $usrPassword . "', '" . $usrEmailAddress . "', '" . time() . "', '', '1');";
	    	$result = mysql_query($sql);
		    checkresult($result, $sql, "AdminUser");
	
			$sql = "INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftstts_admin_email', '" . $postemail . "');";
		    $result = mysql_query($sql);
		    checkresult($result, $sql, "configInsert");
		
			// Print this page
			$page_content = "
					<h2>Create Your Admin Account Results</h2>";
			
			if ($totalfailure == 1) {
				$page_content .= "
					<span class=\"actionFailed\"><span class=\"iconText20px\"><img src=\"themes/installer/icons/delete_20px.png\" alt=\"Action Failed\" /></span> <span class=\"iconText20px\">Unable to create admin account.</span></span>";
			}
			else {
				$page_content .= "
					<span class=\"actionSucceeded\"><span class=\"iconText20px\"><img src=\"themes/installer/icons/check_20px.png\" alt=\"Action Succeeded\" /></span> <span class=\"iconText20px\">Successfully created admin account.</span></span>";
			}
			
			$page_content .= "
					<h2>Finishing Up</h2>
					Installation is now complete, before using the system please make sure and delete this file (install.php) so that it cannot be reused by someone else.
					<br /><br />
					<a href=\"index.php\" class=\"button\">Finish</a>";
			break;	
	}
	
	// Send out the content
	$page->setTemplateVar("PageContent", $page_content);	
	
	include "themes/installer/template.php";
?>