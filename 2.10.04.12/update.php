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
					<h2><span class=\"iconText38px\"><img src=\"themes/installer/icons/table_38px.png\" alt=\"Update Tables\" /></span> <span class=\"iconText38px\">Update database Tables</span></h2>
					Press Next to update the database tables.
					<br /><br />
					<a href=\"update.php?step=2\" class=\"button\">Next</a>";
			break;
		case 2:
			$page->setTemplateVar("PageTitle", UPDATER_SCRIPT_NAME . " Step 2 - Finish");	
			
			include('_db.php');
			
			// Update our Database Tables	
			$sql = "DROP TABLE `" . DBTABLEPREFIX . "orders`;";
			$result = mysql_query($sql);
			checkresult($result, $sql, "droporders");
			
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
		
			// Print this page
			$page_content = "
					<h2>Update Database Tables Results</h2>";
			
			if ($totalfailure == 1) {
				$page_content .= "
					<span class=\"actionFailed\"><span class=\"iconText20px\"><img src=\"themes/installer/icons/delete_20px.png\" alt=\"Action Failed\" /></span> <span class=\"iconText20px\">Unable to update database tables.</span></span>";
			}
			else {
				$page_content .= "
					<span class=\"actionSucceeded\"><span class=\"iconText20px\"><img src=\"themes/installer/icons/check_20px.png\" alt=\"Action Succeeded\" /></span> <span class=\"iconText20px\">Successfully updated database tables.</span></span>";
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