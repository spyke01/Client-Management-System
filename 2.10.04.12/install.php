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
Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:
    * Redistributions of source code must retain the above copyright
      notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright
      notice, this list of conditions and the following disclaimer in the
      documentation and/or other materials provided with the distribution.
    * Neither the name of the <organization> nor the
      names of its contributors may be used to endorse or promote products
      derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
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
			$page->setTemplateVar("PageTitle", INSTALLER_SCRIPT_NAME . " Step 3 - Create database Tables");	
			
			// Create our database connection file
			$dbServer = keepsafe($_POST['dbServer']); 
			$dbName = keepsafe($_POST['dbName']); 
			$dbUsername = keepsafe($_POST['dbUsername']); 
			$dbPassword = keepsafe($_POST['dbPassword']); 
			$DBTABLEPREFIX = keepsafe($_POST['dbTablePrefix']); 
			
			$str = "<?PHP\n\n// Connect to the database\n\n\$server = \"" . $dbServer . "\";\n\$dbuser = \"" . $dbUsername . "\";\n\$dbpass = \"" . $dbPassword . "\";\n\$dbname = \"" . $dbName . "\";\ndefine('DBTABLEPREFIX', '" . $DBTABLEPREFIX . "');\ndefine('USERSDBTABLEPREFIX', '" . $DBTABLEPREFIX . "');\n\n\$connect = mysql_connect(\$server,\$dbuser,\$dbpass);\n\n//display error if connection fails\nif (\$connect==FALSE) {\n   print 'Unable to connect to database: '.mysql_error();\n   exit;\n}\n\nmysql_select_db(\$dbname); // select database\n\n?>";
		
			$fp=fopen("_db.php","w");
			$result = fwrite($fp,$str);
			fclose($fp);	
	
			// Print this page
			$page_content = "
					<h2>Database Connection Results</h2>";
			
			if (!$result || $result == "") {
				$page_content .= "
					<span class=\"actionFailed\"><span class=\"iconText20px\"><img src=\"themes/installer/icons/delete_20px.png\" alt=\"Action Failed\" /></span> <span class=\"iconText20px\">Unable to create database connection file.</span></span>";
			}
			else {
				$page_content .= "
					<span class=\"actionSucceeded\"><span class=\"iconText20px\"><img src=\"themes/installer/icons/check_20px.png\" alt=\"Action Succeeded\" /></span> <span class=\"iconText20px\">Successfully created database connection file.</span></span>";
			}
			
			$page_content .= "
					<br /><br />
					<h2><span class=\"iconText38px\"><img src=\"themes/installer/icons/table_38px.png\" alt=\"Create Tables\" /></span> <span class=\"iconText38px\">Create database Tables</span></h2>
					Press Next to create the database tables.
					<br /><br />
					<a href=\"install.php?step=4\" class=\"button\">Next</a>";
			break;
		case 4:
			$page->setTemplateVar("PageTitle", INSTALLER_SCRIPT_NAME . " Step 4 - Create Admin Account");	
			
			include('_db.php');
			
			// Create our Database Tables
			$sql = "CREATE TABLE `" . DBTABLEPREFIX . "config` (
					`name` varchar(255) NOT NULL default '',
					`value` varchar(255) NOT NULL default ''
					) ENGINE=MyISAM;";
			$result = mysql_query($sql);
			checkresult($result, $sql, "config");
	  		
			$sql = "CREATE TABLE `" . DBTABLEPREFIX . "appointments` (
					`id` mediumint(8) NOT NULL auto_increment,
					`client_id` mediumint(8) NOT NULL,
					`datetimestamp` varchar(50) NOT NULL default '',
					`place` varchar(100) NOT NULL default '',
					`description` text NOT NULL,
					`urgency` tinyint(1) NOT NULL default '0',
					`attire` varchar(50) NOT NULL default '',
				  	PRIMARY KEY  (`id`)
					) ENGINE=MyISAM AUTO_INCREMENT=1 ;";
			$result = mysql_query($sql);
			checkresult($result, $sql, "appointments");
	
			$sql = "CREATE TABLE `" . DBTABLEPREFIX . "categories` (
					`id` mediumint(8) NOT NULL auto_increment,
					`name` varchar(50) NOT NULL default '',
					PRIMARY KEY  (`id`)
					) ENGINE=MyISAM AUTO_INCREMENT=1 ;";
			$result = mysql_query($sql);
			checkresult($result, $sql, "categories");
	
			$sql = "CREATE TABLE `" . DBTABLEPREFIX . "clients` (
					`id` mediumint(8) NOT NULL auto_increment,
					`cat_id` mediumint(8) NOT NULL,
				  	`username` varchar(25) NOT NULL default '',
				  	`password` varchar(32) NOT NULL default '',
					`first_name` varchar(50) NOT NULL default '',
					`last_name` varchar(50) NOT NULL default '',
					`title` varchar(50) NOT NULL default '',
					`company` varchar(50) NOT NULL default '',
					`street1` varchar(50) NOT NULL default '',
					`street2` varchar(50) NOT NULL default '',
					`city` varchar(50) NOT NULL default '',
					`state` varchar(50) NOT NULL default '',
					`zip` varchar(50) NOT NULL default '',
					`daytime_phone` varchar(50) NOT NULL default '',
					`nighttime_phone` varchar(50) NOT NULL default '',
					`cell_phone` varchar(50) NOT NULL default '',
					`email_address` varchar(50) NOT NULL default '',
					`website` varchar(50) NOT NULL default '',
					`preffered_client` tinyint(1) NOT NULL default '0',
					`found_us_through` varchar(50) NOT NULL default '',
					PRIMARY KEY  (`id`)
					) ENGINE=MyISAM AUTO_INCREMENT=1 ;";
			$result = mysql_query($sql);
			checkresult($result, $sql, "pages");
	
			$sql = "CREATE TABLE `" . DBTABLEPREFIX . "downloads` (
				  `id` mediumint(8) NOT NULL auto_increment,
				  `client_id` mediumint(8) NOT NULL,
				  `name` varchar(100) NOT NULL default '',
				  `url` varchar(250) NOT NULL default '',
				  `serial_number` varchar(100) NOT NULL default '',
				  `datetimestamp` varchar(50) NOT NULL default '',
					PRIMARY KEY  (`id`)
					) ENGINE=MyISAM AUTO_INCREMENT=1 ;";
			$result = mysql_query($sql);
			checkresult($result, $sql, "downloads");
	
			$sql = "CREATE TABLE `" . DBTABLEPREFIX . "invoices` (
				  `id` mediumint(8) NOT NULL auto_increment,
				  `client_id` mediumint(8) NOT NULL,
				  `datetimestamp` varchar(50) NOT NULL default '',
				  `description` varchar(100) NOT NULL default '',
				  `discount` decimal(12,2) NOT NULL default '0.00',
				  `note` text NOT NULL,
				  `status` tinyint(1) NOT NULL default '0',
					PRIMARY KEY  (`id`)
					) ENGINE=MyISAM AUTO_INCREMENT=1 ;";
			$result = mysql_query($sql);
			checkresult($result, $sql, "invoices");
	
			$sql = "CREATE TABLE `" . DBTABLEPREFIX . "invoices_products` (
				  `id` mediumint(8) NOT NULL auto_increment,
				  `invoice_id` mediumint(8) NOT NULL,
				  `name` varchar(50) NOT NULL default '',
				  `price` decimal(12,2) NOT NULL default '0.00',
				  `profit` decimal(12,2) NOT NULL default '0.00',
				  `qty` mediumint(8) NOT NULL,
				  `shipping` decimal(12,2) NOT NULL default '0.00',
					PRIMARY KEY  (`id`)
					) ENGINE=MyISAM AUTO_INCREMENT=1 ;";
			$result = mysql_query($sql);
			checkresult($result, $sql, "invoices_products");
	
			$sql = "CREATE TABLE `" . DBTABLEPREFIX . "invoices_payments` (
				  `id` mediumint(8) NOT NULL auto_increment,
				  `invoice_id` mediumint(8) NOT NULL,
				  `datetimestamp` varchar(50) NOT NULL default '',
				  `type` mediumint(8) NOT NULL,
				  `paid` decimal(12,2) NOT NULL default '0.00',
					PRIMARY KEY  (`id`)
					) ENGINE=MyISAM AUTO_INCREMENT=1 ;";
			$result = mysql_query($sql);
			checkresult($result, $sql, "invoices_payments");
	
			$sql = "CREATE TABLE `" . DBTABLEPREFIX . "notes` (
				  `id` mediumint(8) NOT NULL auto_increment,
				  `client_id` mediumint(8) NOT NULL,
				  `datetimestamp` varchar(50) NOT NULL default '',
				  `note` text NOT NULL,
				  `urgency` tinyint(1) NOT NULL default '0',
					PRIMARY KEY  (`id`)
					) ENGINE=MyISAM AUTO_INCREMENT=1 ;";
			$result = mysql_query($sql);
			checkresult($result, $sql, "notes");
	
			$sql = "CREATE TABLE `" . DBTABLEPREFIX . "orders` (
				  `id` mediumint(8) NOT NULL auto_increment,
				  `client_id` mediumint(8) NOT NULL,
				  `datetimestamp` varchar(50) NOT NULL default '',
				  `description` varchar(100) NOT NULL default '',
				  `discount` decimal(12,2) NOT NULL default '0.00',
				  `note` text NOT NULL,
				  `status` tinyint(1) NOT NULL default '0',
					PRIMARY KEY  (`id`)
					) ENGINE=MyISAM AUTO_INCREMENT=1 ;";
			$result = mysql_query($sql);
			checkresult($result, $sql, "orders");
	
			$sql = "CREATE TABLE `" . DBTABLEPREFIX . "orders_products` (
				  `id` mediumint(8) NOT NULL auto_increment,
				  `order_id` mediumint(8) NOT NULL,
				  `name` varchar(50) NOT NULL default '',
				  `price` decimal(12,2) NOT NULL default '0.00',
				  `profit` decimal(12,2) NOT NULL default '0.00',
				  `qty` mediumint(8) NOT NULL,
				  `shipping` decimal(12,2) NOT NULL default '0.00',
					PRIMARY KEY  (`id`)
					) ENGINE=MyISAM AUTO_INCREMENT=1 ;";
			$result = mysql_query($sql);
			checkresult($result, $sql, "orders_products");
	
			$sql = "CREATE TABLE `" . DBTABLEPREFIX . "orders_payments` (
				  `id` mediumint(8) NOT NULL auto_increment,
				  `order_id` mediumint(8) NOT NULL,
				  `datetimestamp` varchar(50) NOT NULL default '',
				  `type` mediumint(8) NOT NULL,
				  `paid` decimal(12,2) NOT NULL default '0.00',
					PRIMARY KEY  (`id`)
					) ENGINE=MyISAM AUTO_INCREMENT=1 ;";
			$result = mysql_query($sql);
			checkresult($result, $sql, "orders_payments");
	
			$sql = "CREATE TABLE `" . DBTABLEPREFIX . "products` (
				  `id` mediumint(8) NOT NULL auto_increment,
				  `name` varchar(100) NOT NULL default '',
				  `price` decimal(12,2) NOT NULL default '0.00',
				  `profit` decimal(12,2) NOT NULL default '0.00',
				  `shipping` decimal(12,2) NOT NULL default '0.00',
					PRIMARY KEY  (`id`)
					) ENGINE=MyISAM AUTO_INCREMENT=1 ;";
			$result = mysql_query($sql);
			checkresult($result, $sql, "products");
	
			$sql = "CREATE TABLE `" . USERSDBTABLEPREFIX . "users` (
				  `id` mediumint(8) NOT NULL auto_increment,
				  `active` tinyint(1) NOT NULL default '1',
				  `user_level` tinyint(4) NOT NULL default '0',
				  `username` varchar(25) NOT NULL default '',
				  `password` varchar(32) NOT NULL default '',
				  `first_name` varchar(50) NOT NULL default '',
				  `last_name` varchar(50) NOT NULL default '',
				  `email_address` varchar(35) NOT NULL default '',
				  `website` varchar(100) NOT NULL default '',
				  `company` varchar(50) NOT NULL,
				  `signup_date` int(11) default NULL,
				  `notes` text NOT NULL,
					PRIMARY KEY  (`id`)
					) ENGINE=MyISAM AUTO_INCREMENT=1 ;";
			$result = mysql_query($sql);
			checkresult($result, $sql, "users");
	
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftsclms_theme', 'default');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "configinsert1");
	
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftsclms_cookie_name', 'ftsclms');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "configinsert2");
	
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftsclms_active', '1');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "configinsert3");
	
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftsclms_inactive_msg', '');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "configinsert4");
	
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftsclms_currency_type', '$');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "configinsert5");
	
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftsclms_time_zone', '-4.00');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "configinsert6");
	
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftsclms_invoice_company_name', '');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "configinsert7");
	
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftsclms_invoice_address', '');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "configinsert8");
	
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftsclms_invoice_city', '');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "configinsert9");
	
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftsclms_invoice_state', '');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "configinsert10");
	
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftsclms_invoice_zip', '');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "configinsert11");
	
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftsclms_invoice_phone_number', '');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "configinsert12");
	
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftsclms_invoice_fax', '');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "configinsert13");
	
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftsclms_invoice_email_address', '');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "configinsert14");
	
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftsclms_invoice_website', '');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "configinsert15");
	
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftsclms_sales_tax', '');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "configinsert16");
		
			// Print this page
			$page_content = "
					<h2>Insert Table Results</h2>";
			
			if ($totalfailure == 1) {
				$page_content .= "
					<span class=\"actionFailed\"><span class=\"iconText20px\"><img src=\"themes/installer/icons/delete_20px.png\" alt=\"Action Failed\" /></span> <span class=\"iconText20px\">Unable to create database tables.</span></span>";
			}
			else {
				$page_content .= "
					<span class=\"actionSucceeded\"><span class=\"iconText20px\"><img src=\"themes/installer/icons/check_20px.png\" alt=\"Action Succeeded\" /></span> <span class=\"iconText20px\">Successfully created database tables.</span></span>";
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
		
	    	$sql = "INSERT INTO `" . USERSDBTABLEPREFIX . "users` (`username`, `password`, `email_address`, `signup_date`, `notes`, `user_level`) VALUES ('" . $usrUsername . "', '" . $usrPassword . "', '" . $usrEmailAddress . "', '" . time() . "', '', '1');";
	    	$result = mysql_query($sql);
		    checkresult($result, $sql, "AdminUser");
	
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftstts_admin_email', '" . $postemail . "');";
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