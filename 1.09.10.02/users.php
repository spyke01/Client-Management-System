<? 
/***************************************************************************
 *                               users.php
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
if ($_SESSION['user_level'] == ADMIN) {
	//==================================================
	// Handle editing, adding, and deleting of users
	//==================================================	
	if ($actual_action == "newuser") {
		if (isset($_POST['submit'])) {
			if ($_POST['password'] == $_POST['password2']) {
				$password = md5(keepsafe($_POST['password']));
								
				$sql = "INSERT INTO `" . $DBTABLEPREFIX . "users` (`users_password`, `users_email_address`, `users_user_level`, `users_first_name`, `users_last_name`, `users_website`) VALUES ('" . $password . "', '" . keepsafe($_POST['emailaddress']) . "', '" . keepsafe($_POST['userlevel']) . "', '" . keeptasafe($_POST['firstname']) . "', '" . keeptasafe($_POST['lastname']) . "', '" . keeptasafe($_POST['website']) . "')";
				$result = mysql_query($sql);
				
				if ($result) {
					$page_content = "<span class=\"center\">Your new user has been added, and you are being redirected to the main page.</span>
								<meta http-equiv=\"refresh\" content=\"1;url=" . $menuvar['USERS'] . "\">";
				}
				else {
					$page_content = "<span class=\"center\">There was an error while creating your new user. You are being redirected to the main page.</span>
								<meta http-equiv=\"refresh\" content=\"5;url=" . $menuvar['USERS'] . "\">";						
				}
			}
			else {
				$page_content = "<span class=\"center\">The passwords you supplied do not match. You are being redirected to the main page.</span>
							<meta http-equiv=\"refresh\" content=\"5;url=" . $menuvar['USERS'] . "\">";			
			}
		}
		else {
			$page_content .= "
						<form name=\"newuserform\" action=\"" . $menuvar['USERS'] . "&amp;action=newuser\" method=\"post\">
							<table class=\"contentBox\" cellpadding=\"1\" cellspacing=\"1\" width=\"100%\">
								<tr>
									<td class=\"title1\" colspan=\"2\">Add A New User</td>
								</tr>
								<tr class=\"row1\">
									<td><strong>First Name:</strong></td><td><input name=\"firstname\" type=\"text\" size=\"60\" /></td>
								</tr>
								<tr class=\"row2\">
									<td><strong>Last Name:</strong></td><td><input name=\"lastname\" type=\"text\" size=\"60\" /></td>
								</tr>
								<tr class=\"row1\">
									<td><strong>Email Address:</strong></td><td><input name=\"emailaddress\" type=\"text\" size=\"60\" /></td>
								</tr>
								<tr class=\"row2\">
									<td><strong>Password:</strong></td><td><input name=\"password\" type=\"password\" size=\"60\" /></td>
								</tr>
								<tr class=\"row1\">
									<td><strong>Confirm Password:</strong></td><td><input name=\"password2\" type=\"password\" size=\"60\" /></td>
								</tr>
								<tr class=\"row2\">
									<td><strong>Website:</strong></td><td><input name=\"website\" type=\"text\" size=\"60\" /></td>
								</tr>
								<tr class=\"row1\">
									<td><strong>User Level:</strong></td><td>
										" . createDropdown("userlevel", "userlevel", "", "") . "
									</td>
								</tr>
							</table>									
							<br />
							<span class=\"center\"><input type=\"submit\" name=\"submit\" class=\"button\" value=\"Add User\" /></span>
						</form>";
		}
	}	
	elseif ($actual_action == "edituser" && isset($actual_id)) {			
		if (isset($_POST['submit'])) {
			$poston_email_list = keepsafe($_POST['on_email_list']);
			$poston_email_list = ($poston_email_list != 1) ? 0 : 1;
			
			// Update our users account	
			if ($_POST['password'] != "") {
				if ($_POST['password'] == $_POST['password2']) {
					$password = md5($_POST['password']);								

					$sql = "UPDATE `" . $DBTABLEPREFIX . "users` SET users_password = '" . $password . "', users_company = '" . keeptasafe($_POST['company']) . "', users_email_address = '" . keepsafe($_POST['emailaddress']) . "', users_user_level = '" . keepsafe($_POST['userlevel']) . "', users_first_name = '" . keeptasafe($_POST['firstname']) . "', users_last_name = '" . keeptasafe($_POST['lastname']) . "', users_website = '" . keeptasafe($_POST['website']) . "', users_notes = '" . keeptasafe($_POST['notes']) . "', `users_on_email_list`='" . $poston_email_list . "' WHERE users_id = '" . $actual_id . "'";
				}
				else {
					$page_content = "<span class=\"center\">The passwords you supplied do not match. You are being redirected to the main page.</span>
								<meta http-equiv=\"refresh\" content=\"5;url=" . $menuvar['USERS'] . "&action=edituser&id=" . $actual_id . "\">";			
				}
			}
			else {
				$sql = "UPDATE `" . $DBTABLEPREFIX . "users` SET users_company = '" . keeptasafe($_POST['company']) . "', users_email_address = '" . keepsafe($_POST['emailaddress']) . "', users_user_level = '" . keepsafe($_POST['userlevel']) . "', users_first_name = '" . keeptasafe($_POST['firstname']) . "', users_last_name = '" . keeptasafe($_POST['lastname']) . "', users_website = '" . keeptasafe($_POST['website']) . "', users_notes = '" . keeptasafe($_POST['notes']) . "', `users_on_email_list`='" . $poston_email_list . "' WHERE users_id = '" . $actual_id . "'";
				}
			$result = mysql_query($sql);
			
			if ($result) {
				$page_content = "<span class=\"center\">Your user's details have been updated, and you are being redirected to the main page.</span>
								<meta http-equiv=\"refresh\" content=\"1;url=" . $menuvar['USERS'] . "&action=edituser&id=" . $actual_id . "\">";
			}
			else {
				$page_content = "<span class=\"center\">There was an error while updating your user's details. You are being redirected to the main page.</span>
								<meta http-equiv=\"refresh\" content=\"5;url=" . $menuvar['USERS'] . "&action=edituser&id=" . $actual_id . "\">";						
			}
		}
		else {
			$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "users` WHERE users_id = '" . $actual_id . "' LIMIT 1";
			$result = mysql_query($sql);
			
			if ($result && mysql_num_rows($result) == 0) {
				$page_content = "<span class=\"center\">There was an error while accessing the user's details you are trying to update. You are being redirected to the main page.</span>
								<meta http-equiv=\"refresh\" content=\"5;url=" . $menuvar['USERS'] . "&action=edituser&id=" . $actual_id . "\">";	
			}
			else {
				$row = mysql_fetch_array($result);
				
				$page_content .= "
							<form name=\"editUsersForm\" id=\"editUsersForm\" action=\"" . $menuvar['USERS'] . "&action=edituser&id=" . $actual_id . "\" method=\"post\">
								<table class=\"contentBox\" cellpadding=\"1\" cellspacing=\"1\" width=\"100%\">
									<tr>
										<td class=\"title1\" colspan=\"2\">Edit User's Details</td>
									</tr>
									<tr class=\"row1\">
										<td><strong>First Name:</strong></td><td><input name=\"firstname\" type=\"text\" size=\"60\" value=\"$row[users_first_name]\" /></td>
									</tr>
									<tr class=\"row2\">
										<td><strong>Last Name:</strong></td><td><input name=\"lastname\" type=\"text\" size=\"60\" value=\"$row[users_last_name]\" /></td>
									</tr>
									<tr class=\"row1\">
										<td><strong>Email Address:</strong></td><td><input name=\"emailaddress\" type=\"text\" size=\"60\" value=\"$row[users_email_address]\" /></td>
									</tr>
									<tr class=\"row2\">
										<td><strong>New Password:</strong></td><td><input name=\"password\" type=\"password\" size=\"60\" /></td>
									</tr>
									<tr class=\"row1\">
										<td><strong>Confirm Password:</strong></td><td><input name=\"password2\" type=\"password\" size=\"60\" /></td>
									</tr>
									<tr class=\"row2\">
										<td><strong>Company:</strong></td><td><input name=\"company\" type=\"text\" size=\"60\" value=\"$row[users_company]\" /></td>
									</tr>	
									<tr class=\"row1\">
										<td><strong>Website:</strong></td><td><input name=\"website\" type=\"text\" size=\"60\" value=\"$row[users_website]\" /></td>
									</tr>
									<tr class=\"row2\">
										<td><strong>User Level:</strong></td><td>
											" . createDropdown("userlevel", "userlevel", $row['users_user_level'], "") . "
										</td>
									</tr>
									<tr class=\"row1\">
										<td><strong>Notes:</strong></td><td>
											<textarea name=\"notes\" cols=\"50\" rows=\"10\">" . $row['users_notes'] . "</textarea>
										</td>
									</tr>
									<tr class=\"row2\"> 
										<td>Place on Email List?</td>
										<td><input name=\"on_email_list\" type=\"checkbox\" value=\"1\"" . testChecked(1, $row['users_on_email_list']) . " /></td>
									</tr>
								</table>";							
			}			
		}
	}
	else {
		if ($actual_action == "deleteuser") {
			$sql = "DELETE FROM `" . $DBTABLEPREFIX . "users` WHERE users_id='" . $_GET['id'] . "' LIMIT 1";
			$result = mysql_query($sql);
		}		
		
		//==================================================
		// Print out our users table
		//==================================================
		$currentTimestamp = time();
		$todayTimestamp = strtotime(gmdate('Y-m-d', $currentTimestamp + (3600 * '-7.00')));
		$tomorrowTimestamp = strtotime(gmdate('Y-m-d', strtotime("+1 day") + (3600 * '-7.00')));
		
		$extraSQL = " WHERE 1";
		$extraSQL .= (isset($_POST['search_username']) && $_POST['search_username'] != "") ? " AND users_username LIKE '%" . $_POST['search_username'] . "%'" : "";
		$extraSQL .= (isset($_POST['search_email_address']) && $_POST['search_email_address'] != "") ? " AND users_email_address LIKE '%" . $_POST['search_email_address'] . "%'" : "";
		$extraSQL .= (isset($_POST['search_first_name']) && $_POST['search_first_name'] != "") ? " AND users_first_name LIKE '%" . $_POST['search_first_name'] . "%'" : "";
		$extraSQL .= (isset($_POST['search_last_name']) && $_POST['search_last_name'] != "") ? " AND users_last_name LIKE '%" . $_POST['search_last_name'] . "%'" : "";
		$extraSQL .= ($actual_action == "viewTodaysUsers") ? " AND users_signup_date > '" . $todayTimestamp . "' AND users_signup_date < '" . $tomorrowTimestamp . "'" : "";
		
		
		$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "users`" . $extraSQL . " ORDER BY users_signup_date DESC";
		$result = mysql_query($sql);
		
		$x = 1; //reset the variable we use for our row colors	
		
		// Allow admins to view only users who registered today or all users
		if ($actual_action == "viewTodaysUsers") {
			// Add breadcrumb
			$page->addBreadCrumb("View Users Who Registered Today", "");
			
			$viewUsersLink = "<a href=\"" . $menuvar['USERS'] . "&action=viewAllUsers\">View All Users</a><br />";
		}
		else {
			$viewUsersLink = "<a href=\"" . $menuvar['USERS'] . "&action=viewTodaysUsers\">View Users Who Registered Today</a><br />";
		}		
		
		$page_content = "						
						<form name=\"searchUsersForm\" id=\"searchUsersForm\" action=\"" . $menuvar['USERS'] . "\" method=\"post\">
							<table class=\"contentBox\" cellpadding=\"1\" cellspacing=\"1\" width=\"100%\">
								<tr>
									<td class=\"title1\" colspan=\"2\">Search Users</td>
								</tr>	
								<tr>
									<td class=\"title2\" colspan=\"2\">Choose any or all of the following to search by.</td>
								</tr>	
								<tr class=\"row1\">
									<td><strong>Username: </strong></td>
									<td><input type=\"text\" name=\"search_username\" size=\"40\" value=\"" . $_POST['search_username'] . "\" /></td>
								</tr>
								<tr class=\"row2\">
									<td><strong>Email Address: </strong></td>
									<td><input type=\"text\" name=\"search_email_address\" size=\"40\" value=\"" . $_POST['search_email_address'] . "\" /></td>
								</tr>
								<tr class=\"row1\">
									<td><strong>First name: </strong></td>
									<td><input type=\"text\" name=\"search_first_name\" size=\"40\" value=\"" . $_POST['search_first_name'] . "\" /></td>
								</tr>
								<tr class=\"row2\">
									<td><strong>Last name: </strong></td>
									<td><input type=\"text\" name=\"search_last_name\" size=\"40\" value=\"" . $_POST['search_last_name'] . "\" /></td>
								</tr>
							</table>
							<br />
							<input type=\"submit\" name=\"submit\" class=\"button\" value=\"Search!\" />
						</form>
						<br /><br />
						" . $viewUsersLink . "
						<table class=\"contentBox\" cellpadding=\"1\" cellspacing=\"1\" width=\"100%\">
							<tr>
								<td class=\"title1\" colspan=\"5\">
									<div class=\"floatRight\">
										<a href=\"" . $menuvar['USERS'] . "&amp;action=newuser\"><img src=\"themes/" . $clms_config['ftsclms_theme'] . "/icons/add.png\" alt=\"Add a new user\" /></a>
									</div>
									Current Users (" . mysql_num_rows($result) . ")
								</td>
							</tr>							
							<tr class=\"title2\">
								<td><strong>Email Address</strong></td><td><strong>Full Name</strong></td><td><strong>Signup Date</strong></td><td><strong>User Level</strong></td><td></td>
							</tr>";
							
		while ($row = mysql_fetch_array($result)) {
			
			$page_content .= "
								<tr id=\"" . $row['users_id'] . "_row\" class=\"row" . $x . "\">
									<td>" . $row['users_email_address'] . "</td>
									<td>" . $row['users_first_name'] . " " . $row['users_last_name'] . "</td>
									<td>" . makeDate($row['users_signup_date']) . "</td>
									<td>" . getUserlevelFromID($row['users_id']) . "</td>
									<td>
										<span class=\"center\"><a href=\"" . $menuvar['USERS'] . "&amp;action=edituser&amp;id=" . $row['users_id'] . "\"><img src=\"themes/" . $clms_config['ftsclms_theme'] . "/icons/check.png\" alt=\"Edit User Details\" /></a> <a href=\"" . $menuvar['USERS'] . "&amp;action=deleteuser&amp;id=" . $row['users_id'] . "\"><img src=\"themes/" . $clms_config['ftsclms_theme'] . "/icons/delete.png\" alt=\"Delete User\" /></a></span>
									</td>
								</tr>";
			$x = ($x==2) ? 1 : 2;
		}
		mysql_free_result($result);
		
	
		$page_content .=		"</table>";
	}
	$page->setTemplateVar("PageContent", $page_content);
}
else {
	$page->setTemplateVar("PageContent", "\nYou Are Not Authorized To Access This Area. Please Refrain From Trying To Do So Again.");
}
?>