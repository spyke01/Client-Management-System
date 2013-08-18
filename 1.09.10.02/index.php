<? 
/***************************************************************************
 *                               index.php
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
// If the db connection file is missing we should redirect the user to install page
if (!file_exists('_db.php')) {
	header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/install.php");	
	exit();
}

include 'includes/header.php';

$requested_page_id = $_GET['p'];
$requested_section = $_GET['s'];
$requested_id = $_GET['id'];
$requested_action = $_GET['action'];
$requested_action2 = $_GET['action2'];

$actual_page_id = ($requested_page_id == "" || !isset($requested_page_id)) ? 1 : $requested_page_id;
$actual_page_id = parseurl($actual_page_id);
$actual_section = parseurl($requested_section);
$actual_id = parseurl($requested_id);
$actual_action = parseurl($requested_action);
$actual_action2 = parseurl($requested_action2);
$page_content = "";

// Warn the user if the install.php script is present
if (file_exists('install.php')) {
	$page_content = "<strong style=\"color: red;\">Warning: install.php is present, please remove this file for security reasons.</strong><br /><br />";
}

// We want to show all of our menus by default
$page->setTemplateVar("uOLm_active", ACTIVE);
$page->setTemplateVar("aOLm_active", ACTIVE);

//========================================
// Logout Function
//========================================
// Prevent spanning between apps to avoid a user getting more acces that they are allowed
if ($_SESSION['script_locale'] != rtrim(dirname($_SERVER['PHP_SELF']), '/\\') && session_is_registered('userid')) {
	session_destroy();
}

if ($actual_page_id == "logout") {
	define('IN_FTSCLMS', true);
	include '_db.php';
	include_once ('includes/menu.php');
	include_once ('config.php');
	global $clms_config;
	
	//Destroy Session Cookie
	$cookiename = $clms_config['ftsclms_cookie_name'];
	setcookie($cookiename, false, time()-2592000); //set cookie to delete back for 1 month
	
	//Destroy Session
	session_destroy();
	if(!session_is_registered('first_name')){
		header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/index.php");	
		exit();
	}
}

//Check to see if advanced options are allowed or not
if (version_functions("advancedOptions") == true) {
	// If the system is locked, then only a moderator or admin should be able to view it
	if ($_SESSION['user_level'] != ADMIN && $_SESSION['user_level'] != MOD && $clms_config['ftsclms_active'] != ACTIVE) {
		if ($actual_page_id == "login") {
			include 'login.php';
		}
		else {	
			$page->setTemplateVar("PageTitle", 'Currently Disabled');
			$page->setTemplateVar("PageContent", bbcode($clms_config['ftsclms_inactive_msg']));
		}
	}
	else {
		//========================================
		// Admin panel options
		//========================================
		if ($actual_page_id == "admin") {
			if (!$_SESSION['username']) { include 'login.php'; }
			else {
				if ($_SESSION['user_level'] == MOD || $_SESSION['user_level'] == ADMIN) {
					if ($actual_section == "" || !isset($actual_section)) {
						include 'admin.php'; 
						$page->setTemplateVar("PageTitle", "Admin Panel");
					}
					elseif ($actual_section == "settings") {
						include 'settings.php';				
						$page->setTemplateVar("PageTitle", "Settings");
					}
					elseif ($actual_section == "clients") {
						include 'clients.php';		
						$page->setTemplateVar("PageTitle", "Clients");		
					}
					elseif ($actual_section == "orders") {
						include 'orders.php';		
						$page->setTemplateVar("PageTitle", "Orders");		
					}
					elseif ($actual_section == "notes") {
						include 'notes.php';				
						$page->setTemplateVar("PageTitle", "Notes");
					}
					elseif ($actual_section == "appointments") {
						include 'appointments.php';			
						$page->setTemplateVar("PageTitle", "Appointments");	
					}
					elseif ($actual_section == "categories") {
						include 'categories.php';			
						$page->setTemplateVar("PageTitle", "Categories");	
					}
					elseif ($actual_section == "themes") {
						include 'themes.php';		
						$page->setTemplateVar("PageTitle", "Themes");		
					}
					elseif ($actual_section == "users") {
						include 'users.php';			
						$page->setTemplateVar("PageTitle", "Users");	
					}
				}
				else { setTemplateVar("PageContent", "You are not authorized to access the admin panel."); }
			}
		}
		elseif ($actual_page_id == "login") {
			include 'login.php';
		}
		elseif ($actual_page_id == "viewentry" && $actual_id != "") {
			include 'viewentry.php';
			
		}
		else {
			// Only let Admins and Mods view data
			if ($_SESSION['user_level'] == MOD || $_SESSION['user_level'] == ADMIN) {
				//=================================================
				// Print Todays Appointments
				//=================================================
				$today = mktime(0, 0, 0, date("m"), date("d"),  date("Y"));
				$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "appointments` WHERE SUBSTRING(FROM_UNIXTIME(`appointments_datetimestamp`) FROM 1 FOR 10) = SUBSTRING(FROM_UNIXTIME(" . time() . ") FROM 1 FOR 10) ORDER BY appointments_datetimestamp ASC";
				$result = mysql_query($sql);
				
				$page_content .= "
															<table border=\"0\" cellspacing=\"1\" cellpadding=\"1\" class=\"contentBox\">
																<tr>
																	<td colspan=\"5\" class=\"title1 full\">Todays Appointments</td>
																</tr>
																<tr>
																	<td class=\"title2\">Time</td><td class=\"title2\">Place</td><td class=\"title2\">Attire</td><td class=\"title2\">Client</td><td class=\"title2\">Description</td>
																</tr>";
				
				if ($result || mysql_num_rows($result) == 0) {
					$page_content .= "
																<tr>
																	<td colspan=\"5\" class=\"row1\">There are no appointments scheduled for today.</td>
																</tr>";			
				}
				else{
					while ($row = mysql_fetch_array($result)) {
						$sql2 = "SELECT clients_first_name, clients_last_name FROM `" . $DBTABLEPREFIX . "clients` WHERE clients_id = '" . $row['appointments_client_id'] . "' LIMIT 1";
						$result2 = mysql_query($sql2);		
						
						$row2 = mysql_fetch_array($result2);
						mysql_free_result($result2);
						
						$rowColor = ($row['appointments_urgency'] != LOW) ? "redRow" : "greenRow";
						$rowColor = ($row['appointments_urgency'] != HIGH && $rowColor == "redRow") ? "yellowRow" : $rowColor;
						
						$page_content .= "							
																<tr class=\"" . $rowColor . "\">
																	<td>" . makeTime($row['appointments_datetimestamp']) . "</td>
																	<td>" . $row['appointments_place'] . "</td>
																	<td>" . $row['appointments_attire'] . "</td>
																	<td>" . $row2['clients_first_name'] . " " . $row2['clients_last_name'] . "</td>
																	<td>" . $row['appointments_description'] . "</td>
																</tr>";	
					}
					mysql_free_result($result);	
				}
				
				$page_content .= "
															</table>
															<br /><br />";
					
				
				//=================================================
				// Print Highest Orders
				//=================================================
				$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "orders` ORDER BY orders_total DESC LIMIT 5";
				$result = mysql_query($sql);
				
				$page_content .= "						
															<div style=\"float: left;\" class=\"half\">
																<table border=\"0\" cellspacing=\"1\" cellpadding=\"1\" class=\"contentBox\">
																	<tr>
																		<td colspan=\"3\" class=\"title1 full\">Largest Orders</td>
																	</tr>
																	<tr>
																		<td class=\"title2\">Date and Time</td><td class=\"title2\">Client</td><td class=\"title2\">Amount</td>
																	</tr>";
				
				$x = 1;
				
				if ($result || mysql_num_rows($result) == 0) {
					$page_content .= "							
																	<tr>
																		<td colspan=\"3\" class=\"row1\">There are no orders on file.</td>
																	</tr>";			
				}
				else{		
					while ($row = mysql_fetch_array($result)) {
						$sql2 = "SELECT clients_first_name, clients_last_name FROM `" . $DBTABLEPREFIX . "clients` WHERE clients_id = '" . $row['orders_client_id'] . "' LIMIT 1";
						$result2 = mysql_query($sql2);		
						
						$row2 = mysql_fetch_array($result2);
						mysql_free_result($result2);	
						
						$page_content .= "
																	<tr class=\"row" . $x . "\">
																		<td>" . makeDateTime($row['orders_date_ordered']) . "</td>
																		<td>" . $row2['clients_first_name'] . " " . $row2['clients_last_name'] . "</td>
																		<td class=\"right\">" . formatCurrency($row['orders_total']) . "</td>
																	</tr>";	
						
						$x = ($x == 1) ? 2 : 1;
					}
					mysql_free_result($result);
				}
				
				$page_content .= "						
																</table>
															</div>";
				
				
				//=================================================
				// Print Users Who Ordered the Most
				//=================================================
				$sql = "SELECT sum(o.orders_total) AS total_ordered, c.clients_id, c.clients_first_name, c.clients_first_name FROM `" . $DBTABLEPREFIX . "orders` o, `" . $DBTABLEPREFIX . "clients` c WHERE c.clients_id = o.orders_client_id GROUP BY c.clients_id ORDER BY total_ordered DESC LIMIT 5";
				$result = mysql_query($sql);
				
				$page_content .= "
															<div style=\"float: left;\" class=\"half\">
																<table border=\"0\" cellspacing=\"1\" cellpadding=\"1\" class=\"contentBox\">
																	<tr>
																		<td colspan=\"2\" class=\"title1 full\">Gold Member Clients</td>
																	</tr>
																	<tr>
																		<td class=\"title2\">Client</td><td class=\"title2\">Total Amount Ordered</td>
																	</tr>";
				
				$x = 1;
				
				if (mysql_num_rows($result) == 0) {
					$page_content .= "
																	<tr>
																		<td colspan=\"3\" class=\"row1\">There are no orders on file.</td>
																	</tr>";			
				}
				else{		
					while ($row = mysql_fetch_array($result)) {						
						$page_content .= "
																	<tr class=\"row" . $x . "\">
																		<td>" . $row['clients_first_name'] . " " . $row['clients_last_name'] . "</td>
																		<td class=\"right\">" . formatCurrency($row['total_ordered']) . "</td>
																	</tr>";	
						
						$x = ($x == 1) ? 2 : 1;
					}
				}
				
				$page_content .= "						
																</table>
															</div>";
				
				mysql_free_result($result);
			}
			else {
				$page_content = "\n						You do not have the required access to view this page. If you are an Administrator of Moderator of this system, then please login using the link to your left.";
			}
				
			$page->setTemplateVar("PageTitle", "Home");
			$page->setTemplateVar("PageContent", $page_content);	
	
		}
	
		//================================================
		// Get Menus
		//================================================
		
		// Top Menus
		$page->makeMenuItem("top", "<img src=\"images/logo.gif\" alt=\"Fast Track Sites Logo\" />", "", "logo");
		$page->makeMenuItem("top", "Home", "index.php", "");
		
		// Make usermanagement menu items
		if ($_SESSION['user_level'] == MOD || $_SESSION['user_level'] == ADMIN) {
			$page->makeMenuItem("top", "Configure", "index.php?p=admin&s=settings", "");
		
			$page->makeMenuItem("adminOptionsLeft", "Clients", "index.php?p=admin&s=clients", "");
			$page->makeMenuItem("adminOptionsLeft", "Orders", "index.php?p=admin&s=orders", "");
			$page->makeMenuItem("adminOptionsLeft", "Notes", "index.php?p=admin&s=notes", "");
			$page->makeMenuItem("adminOptionsLeft", "Appointments", "index.php?p=admin&s=appointments", "");
			$page->makeMenuItem("adminOptionsLeft", "Categories", "index.php?p=admin&s=categories", "");
			$page->makeMenuItem("adminOptionsLeft", "User Administration", "index.php?p=admin&s=users", "");
		}
		
		// User Options Menu
		if (!isset($_SESSION['username'])) {
			$page->makeMenuItem("userOptionsLeft", "Login", "index.php?p=login", "");
		}
		else {
			$page->makeMenuItem("userOptionsLeft", "Logout", "index.php?p=logout", "");
		}
	}
}
else { $page->setTemplateVar("PageContent", version_functions("advancedOptionsText")); }

version_functions("no");
include "themes/" . $clms_config['ftsclms_theme'] . "/template.php";
?>