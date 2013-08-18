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
	define('UPDATER_SCRIPT_NAME', 'Client Management System');
	
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
			$page->setTemplateVar("PageTitle", UPDATER_SCRIPT_NAME . " Step 1 - Update Database Tables");	
			
			// Print this page
			$page_content = "
					<h2>Welcome to the Fast Track Sites Script Updater</h2>
					Thank you for downloading the " . UPDATER_SCRIPT_NAME . " this page will walk you through the update procedure.
					<br /><br />
					<h2><span class=\"iconText38px\"><img src=\"themes/installer/icons/table_38px.png\" alt=\"Update Tables\" /></span> <span class=\"iconText38px\">Update Databse Tables</span></h2>
					Press Next to update the database tables.
					<br /><br />
					<a href=\"update.php?step=2\" class=\"button\">Next</a>";
			break;
		case 2:
			$page->setTemplateVar("PageTitle", UPDATER_SCRIPT_NAME . " Step 2 - Finish");	
			
			include('_db.php');
			
			// Update our Database Tables	
			$sql = "ALTER TABLE `" . $DBTABLEPREFIX . "orders` ADD `orders_number` VARCHAR(50) NOT NULL;";
			$result = mysql_query($sql);
			checkresult($result, $sql, "orders");
			
			$sql = "ALTER TABLE `" . $DBTABLEPREFIX . "users` ADD `users_first_name` VARCHAR(50) NOT NULL ,
					ADD `users_last_name` VARCHAR(50) NOT NULL ,
					ADD `users_company` VARCHAR(50) NOT NULL ,
					ADD `users_signup_date` INT(11) NOT NULL ,
					ADD `users_notes` TEXT NOT NULL ;";
			$result = mysql_query($sql);
			checkresult($result, $sql, "users");
	
			$sql = "INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftsclms_currency_type', '$');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "configinsert1");
	
			$sql = "INSERT INTO `" . $DBTABLEPREFIX . "config` VALUES ('ftsclms_time_zone', '-4.00');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "configinsert2");
		
			// Print this page
			$page_content = "
					<h2>Update Database Tables Results</h2>";
			
			if ($totalfailure == 1) {
				$page_content .= "
					<span class=\"actionFailed\"><span class=\"iconText20px\"><img src=\"themes/installer/icons/delete_20px.png\" alt=\"Action Failed\" /></span> <span class=\"iconText20px\">Unable to update databse tables.</span></span>";
			}
			else {
				$page_content .= "
					<span class=\"actionSucceeded\"><span class=\"iconText20px\"><img src=\"themes/installer/icons/check_20px.png\" alt=\"Action Succeeded\" /></span> <span class=\"iconText20px\">Successfully updated databse tables.</span></span>";
			}
			
			$page_content .= "
					<br /><br />
					<h2>Finishing Up</h2>
					Update is now complete, before using the system please make sure and delete this file (update.php) so that it cannot be reused by someone else.
					<br /><br />
					<a href=\"index.php\" class=\"button\">Finish</a>";
			break;	
	}
	
	// Send out the content
	$page->setTemplateVar("PageContent", $page_content);	
	
	include "themes/installer/updatertemplate.php";
?>