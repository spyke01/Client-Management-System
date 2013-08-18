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
$requested_style = $_GET['style'];
$requested_report = $_GET['report'];

$actual_page_id = ($requested_page_id == "" || !isset($requested_page_id)) ? 1 : $requested_page_id;
$actual_page_id = parseurl($actual_page_id);
$actual_section = parseurl($requested_section);
$actual_id = parseurl($requested_id);
$actual_action = parseurl($requested_action);
$actual_action2 = parseurl($requested_action2);
$actual_style = parseurl($requested_style);
$actual_report = parseurl($requested_report);
$page_content = "";

// Warn the user if the install.php script is present
if (file_exists('install.php')) {
	$page_content = "<div class=\"errorMessage\">Warning: install.php is present, please remove this file for security reasons.</div>";
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
	//Destroy Session Cookie
	$cookiename = $clms_config['ftsclms_cookie_name'];
	setcookie($cookiename, false, time()-2592000); //set cookie to delete back for 1 month
	
	//Destroy Session
	session_destroy();
	if(!session_is_registered('userid')){
		header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/index.php");	
		exit();
	}
}

// Top Menus
$page->makeMenuItem("top", "<img src=\"images/ftsLogo.png\" alt=\"Fast Track Sites Logo\" />", "", "logo");
$page->makeMenuItem("top", "Home", "index.php", "");
		
//Check to see if advanced options are allowed or not
if (version_functions("advancedOptions") == true) {
	// If the system is locked, then only a moderator or admin should be able to view it
	if ($_SESSION['user_level'] != SYSTEM_ADMIN && $_SESSION['user_level'] != CLIENT_ADMIN && $clms_config['ftsclms_active'] != ACTIVE) {
		if ($actual_page_id == "login") {
			include 'login.php';
		}
		else {	
			$page->setTemplateVar("PageTitle", 'Currently Disabled');
			$page->setTemplateVar("PageContent", bbcode($clms_config['ftsclms_inactive_msg']));
		
			// Let us login so we can access the system during a shutdown
			$page->setTemplateVar("PageTitle", "Login");	
			$page->addBreadCrumb("Login", $menuvar['LOGIN']);
			include 'login.php';
					
			// We want to show all of our menus by default
			$page->setTemplateVar("aOLm_active", INACTIVE);
			$page->makeMenuItem("userOptionsLeft", "Login", "index.php?p=login", "");
		}
	}
	else {
		//========================================
		// Admin panel options
		//========================================
		if ($actual_page_id == "admin") {
			// Add breadcrumb pointing home
			$page->addBreadCrumb("Admin", $menuvar['SYSTEM_ADMIN']);
			
			if (!$_SESSION['username']) { include 'login.php'; }
			else {
				if ($_SESSION['user_level'] == CLIENT_ADMIN || $_SESSION['user_level'] == SYSTEM_ADMIN) {
					if ($actual_section == "" || !isset($actual_section)) {
						$page->setTemplateVar("PageTitle", "Admin Panel");
						include 'admin.php'; 
					}
					elseif ($actual_section == "appointments") {
						$page->setTemplateVar("PageTitle", "Appointments");	
						$page->addBreadCrumb("Appointments", $menuvar['APPOINTMENTS']);
						include 'appointments.php';
					}
					elseif ($actual_section == "categories") {
						$page->setTemplateVar("PageTitle", "Client Categories");	
						$page->addBreadCrumb("Categories", $menuvar['CATEGORIES']);
						include 'categories.php';
					}
					elseif ($actual_section == "clients") {
						$page->setTemplateVar("PageTitle", "Clients");		
						$page->addBreadCrumb("Clients", $menuvar['CLIENTS']);
						include 'clients.php';
					}
					elseif ($actual_section == "graphs") {
						$page->setTemplateVar("PageTitle", "Graphs");		
						$page->addBreadCrumb("Graphs", $menuvar['GRAPHS']);
						include 'graphs.php';
					}
					elseif ($actual_section == "products") {
						$page->setTemplateVar("PageTitle", "Products");		
						$page->addBreadCrumb("Products", $menuvar['PRODUCTS']);
						include 'products.php';
					}
					elseif ($actual_section == "reports") {
						$page->setTemplateVar("PageTitle", "Reports");		
						$page->addBreadCrumb("Reports", $menuvar['REPORTS']);
						include 'reports.php';
					}
					elseif ($actual_section == "settings") {
						$page->setTemplateVar("PageTitle", "Settings");
						$page->addBreadCrumb("Settings", $menuvar['SETTINGS']);
						include 'settings.php';
					}
					elseif ($actual_section == "themes") {
						$page->setTemplateVar("PageTitle", "Themes");
						$page->addBreadCrumb("Themes", $menuvar['THEMES']);
						include 'themes.php';
					}
					elseif ($actual_section == "users") {
						$page->setTemplateVar("PageTitle", "Users");	
						$page->addBreadCrumb("Users", $menuvar['USERS']);
						include 'users.php';
					}
				}
				else { $page->setTemplateVar("PageContent", "You are not authorized to access the admin panel."); }
			}
		}
		elseif ($actual_page_id == "invoices") {
			$page->setTemplateVar("PageTitle", "Invoices");	
			$page->addBreadCrumb("Home", $menuvar['HOME']);
			include 'invoices.php';			
		}
		elseif ($actual_page_id == "login") {
			$page->setTemplateVar("PageTitle", "Login");	
			$page->addBreadCrumb("Home", $menuvar['HOME']);
			$page->addBreadCrumb("Login", $menuvar['LOGIN']);
			include 'login.php';
		}
		elseif ($actual_page_id == "mydownloads") {
			$page->setTemplateVar("PageTitle", "My Downloads");	
			$page->addBreadCrumb("Home", $menuvar['HOME']);
			$page->addBreadCrumb("My Downloads", $menuvar['MYDOWNLOADS']);
			include 'mydownloads.php';			
		}
		elseif ($actual_page_id == "myinvoices") {
			$page->setTemplateVar("PageTitle", "My Invoices");	
			$page->addBreadCrumb("Home", $menuvar['HOME']);
			$page->addBreadCrumb("My Invoices", $menuvar['MYINVOICES']);
			include 'myinvoices.php';
		}
		elseif ($actual_page_id == "mynotes") {
			$page->setTemplateVar("PageTitle", "My Notes");	
			$page->addBreadCrumb("Home", $menuvar['HOME']);
			$page->addBreadCrumb("My Notes", $menuvar['MYNOTES']);
			include 'mynotes.php';
		}
		elseif ($actual_page_id == "myorders") {
			$page->setTemplateVar("PageTitle", "My Orders");	
			$page->addBreadCrumb("Home", $menuvar['HOME']);
			$page->addBreadCrumb("My Orders", $menuvar['MYORDERS']);
			include 'myorders.php';
		}
		elseif ($actual_page_id == "orders") {
			$page->setTemplateVar("PageTitle", "Orders");	
			$page->addBreadCrumb("Home", $menuvar['HOME']);
			include 'orders.php';			
		}
		elseif ($actual_page_id == "version") {
			$page->setTemplateVar("PageTitle", "Version Information");	
			$page->addBreadCrumb("Home", $menuvar['HOME']);
			$page->addBreadCrumb("Version Information", "");
			
			include('_license.php');
		
			$page_content .= "
				<div class=\"roundedBox\">
					<h1>Version Information</h1>
					<strong>Application:</strong> " . A_NAME . "<br />
					<strong>Version:</strong> " . A_VERSION . "<br />
					<strong>Registered to:</strong> " . $A_Licensed_To . "<br />
					<strong>Serial:</strong> " . $A_License . "
				</div>";
			
			$page->setTemplateVar("PageContent", $page_content);	
		}
		else {			
			// Only let Admins and Mods view the admin homepage
			if ($_SESSION['user_level'] == CLIENT_ADMIN || $_SESSION['user_level'] == SYSTEM_ADMIN) {
				$page->setTemplateVar("PageTitle", "Admin Panel");
				$page->addBreadCrumb("Admin", $menuvar['SYSTEM_ADMIN']);
				include 'admin.php'; 
			}
			else {
				$page->setTemplateVar("PageTitle", "Home");	
				$page->addBreadCrumb("Home", $menuvar['HOME']);
				
				if (!isset($_SESSION['username'])) {
					$page->setTemplateVar("PageTitle", "Login");	
					$page->addBreadCrumb("Login", $menuvar['LOGIN']);
					include 'login.php';
				}
				else {
					$page_content .= "
						<h1>Welcome to the Fast Track Sites Client Management System</h1>
						Please use the links at the left to access any downloads, invoices, orders, or any other items that are associated with your account.";
					
					$page->setTemplateVar("PageContent", $page_content);	
				}
			}
		}
	
		//================================================
		// Get Menus
		//================================================		
		// Admin Options Menu
		if ($_SESSION['user_level'] == CLIENT_ADMIN || $_SESSION['user_level'] == SYSTEM_ADMIN) {		
			$page->makeMenuItem("adminOptionsLeft", "Clients", $menuvar['CLIENTS'], "");
			$page->makeMenuItem("adminOptionsLeft", "Client Categories", $menuvar['CATEGORIES'], "");
			$page->makeMenuItem("adminOptionsLeft", "Appointments", $menuvar['APPOINTMENTS'], "");
		}
		if ($_SESSION['user_level'] == SYSTEM_ADMIN) {
			$page->makeMenuItem("top", "Configure", $menuvar['SETTINGS'], "");
		
			$page->makeMenuItem("adminOptionsLeft", "Products", $menuvar['PRODUCTS'], "");
			$page->makeMenuItem("adminOptionsLeft", "Reports", $menuvar['REPORTS'], "");
			$page->makeMenuItem("adminOptionsLeft", "Graphs", $menuvar['GRAPHS'], "");
			$page->makeMenuItem("adminOptionsLeft", "Themes", $menuvar['THEMES'], "");
			$page->makeMenuItem("adminOptionsLeft", "User Administration", $menuvar['USERS'], "");
		}
		
		// User Options Menu
		if (!isset($_SESSION['username'])) {
			$page->makeMenuItem("userOptionsLeft", "Login", $menuvar['LOGIN'], "");
		}
		else {
			$page->makeMenuItem("userOptionsLeft", "My Invoices", $menuvar['MYINVOICES'], "");
			$page->makeMenuItem("userOptionsLeft", "My Orders", $menuvar['MYORDERS'], "");
			$page->makeMenuItem("userOptionsLeft", "My Downloads", $menuvar['MYDOWNLOADS'], "");
			$page->makeMenuItem("userOptionsLeft", "My Notes", $menuvar['MYNOTES'], "");
			$page->makeMenuItem("userOptionsLeft", "Logout", $menuvar['LOGOUT'], "");
		}
	}
}
else { $page->setTemplateVar("PageContent", version_functions("advancedOptionsText")); }

version_functions("no");
if (isset($actual_style) && $actual_style == "printerFriendly") { include "themes/" . $clms_config['ftsclms_theme'] . "/printerFriendlyTemplate.php"; }
else { include "themes/" . $clms_config['ftsclms_theme'] . "/template.php"; }
?>