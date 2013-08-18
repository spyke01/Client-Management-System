<? 
/***************************************************************************
 *                               clients.php
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
	// Handle editing, adding, and deleting of clients
	//==================================================	
	if ($actual_action == "newclient") {
		if (isset($_POST[submit])) {
			$sql = "INSERT INTO `" . $DBTABLEPREFIX . "clients` (clients_cat_id, clients_first_name, clients_last_name, clients_title, clients_company, clients_street1, clients_street2, clients_city, clients_state, clients_zip, clients_daytime_phone, clients_nighttime_phone, clients_cell_phone, clients_email_address, clients_website, clients_found_us_through, clients_preffered_client) VALUES ('" . keepsafe($_POST['cat_id']) . "','" . keepsafe($_POST['first_name']) . "', '" . keeptasafe($_POST['last_name']) . "', '" . keeptasafe($_POST['title']) . "', '" . keeptasafe($_POST['company']) . "', '" . keeptasafe($_POST['street1']) . "', '" . keeptasafe($_POST['street2']) . "', '" . keeptasafe($_POST['city']) . "', '" . keeptasafe($_POST['state']) . "', '" . keepsafe($_POST['zip']) . "', '" . keeptasafe($_POST['daytime_phone']) . "', '" . keeptasafe($_POST['nighttime_phone']) . "', '" . keeptasafe($_POST['cell_phone']) . "', '" . keepsafe($_POST['email_address']) . "', '" . keepsafe($_POST['website']) . "', '" . keeptasafe($_POST['found_us_through']) . "', '" . keepsafe($_POST['preffered_client']) . "')";
			$result = mysql_query($sql);
			
			if ($result) {
				$content = "<center>Your new client has been added, and you are being redirected to the main page.</center>
							<meta http-equiv=\"refresh\" content=\"1;url=" . $menuvar['CLIENTS'] . "\">";
			}
			else {
				$content = "<center>There was an error while creating your new client. You are being redirected to the main page.</center>
							<meta http-equiv=\"refresh\" content=\"5;url=" . $menuvar['CLIENTS'] . "\">";						
			}
		}
		else {
			$content .= "
						<form name=\"newclientform\" action=\"" . $menuvar['CLIENTS'] . "&action=newclient\" method=\"post\">
							<table class=\"contentBox\" cellpadding=\"1\" cellspacing=\"1\" width=\"100%\">
								<tr>
									<td class=\"title1\" colspan=\"2\">Add A New Client</td>
								</tr>
								<tr>
									<td class=\"title2\" colspan=\"2\">General Information</td>
								</tr>
								<tr class=\"row1\">
									<td><strong>Category:</strong></td>
									<td>
										<select name=\"cat_id\">
											<option value=\"\">Select One</option>";
			$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "categories` ORDER BY cat_name";
			$result = mysql_query($sql);
			
			while ($row = mysql_fetch_array($result)) {							
				$content .= "					<option value=\"" . $row['cat_id'] . "\">" . $row['cat_name'] . "</option>";
			}
			mysql_free_result($result);
											
			$content .= "				</select>									
									</td>
								</tr>
								<tr class=\"row2\">
									<td><strong>First Name:</strong></td><td><input name=\"first_name\" type=\"text\" size=\"60\" /></td>
								</tr>
								<tr class=\"row1\">
									<td><strong>Last Name:</strong></td><td><input name=\"last_name\" type=\"text\" size=\"60\" /></td>
								</tr>
								<tr class=\"row2\">
									<td><strong>Title:</strong></td><td><input name=\"title\" type=\"text\" size=\"60\" /></td>
								</tr>
								<tr class=\"row1\">
									<td><strong>Company:</strong></td><td><input name=\"company\" type=\"text\" size=\"60\" /></td>
								</tr>
								<tr class=\"row2\">
									<td><strong>Street:</strong></td><td><input name=\"street1\" type=\"text\" size=\"60\" /><br /><input name=\"street2\" type=\"text\" size=\"60\" /></td>
								</tr>
								<tr class=\"row1\">
									<td><strong>City:</strong></td><td><input name=\"city\" type=\"text\" size=\"60\" /></td>
								</tr>
								<tr class=\"row2\">
									<td><strong>State:</strong></td><td><input name=\"state\" type=\"text\" size=\"60\" /></td>
								</tr>
								<tr class=\"row1\">
									<td><strong>Zip Code:</strong></td><td><input name=\"zip\" type=\"text\" size=\"60\" /></td>
								</tr>
								<tr>
									<td class=\"title2\" colspan=\"2\">Contact Information</td>
								</tr>
								<tr class=\"row1\">
									<td><strong>Daytime Phone:</strong></td><td><input name=\"daytime_phone\" type=\"text\" size=\"60\" /></td>
								</tr>
								<tr class=\"row2\">
									<td><strong>Nighttime Phone:</strong></td><td><input name=\"nighttime_phone\" type=\"text\" size=\"60\" /></td>
								</tr>
								<tr class=\"row1\">
									<td><strong>Cell Phone:</strong></td><td><input name=\"cell_phone\" type=\"text\" size=\"60\" /></td>
								</tr>
								<tr class=\"row2\">
									<td><strong>Email Address:</strong></td><td><input name=\"email_address\" type=\"text\" size=\"60\" /></td>
								</tr>
								<tr class=\"row1\">
									<td><strong>Website:</strong></td><td><input name=\"website\" type=\"text\" size=\"60\" /></td>
								</tr>
								<tr class=\"row2\">
									<td><strong>Found Us Through:</strong></td><td><input name=\"found_us_through\" type=\"text\" size=\"60\" /></td>
								</tr>
								<tr class=\"row1\">
									<td><strong>Preferred Customer?</strong></td><td><input name=\"preffered_client\" type=\"checkbox\" value=\"1\" /></td>
								</tr>
							</table>									
							<br />
							<center><input type=\"submit\" name=\"submit\" class=\"button\" value=\"Add Client\" /></center>
						</form>";
		}
	}	
	elseif ($actual_action == "editclient" && isset($actual_id)) {
		if (isset($_POST[submit])) {
			$sql = "UPDATE `" . $DBTABLEPREFIX . "clients` SET clients_cat_id = '" . keepsafe($_POST['cat_id']) . "', clients_first_name = '" . keepsafe($_POST['first_name']) . "', clients_last_name = '" . keeptasafe($_POST['last_name']) . "', clients_title = '" . keeptasafe($_POST['title']) . "', clients_company = '" . keeptasafe($_POST['company']) . "', clients_street1 = '" . keeptasafe($_POST['street1']) . "', clients_street2 = '" . keeptasafe($_POST['street2']) . "', clients_city = '" . keeptasafe($_POST['city']) . "', clients_state = '" . keeptasafe($_POST['state']) . "', clients_zip = '" . keepsafe($_POST['zip']) . "', clients_daytime_phone = '" . keeptasafe($_POST['daytime_phone']) . "', clients_nighttime_phone = '" . keeptasafe($_POST['nighttime_phone']) . "', clients_cell_phone = '" . keeptasafe($_POST['cell_phone']) . "', clients_email_address = '" . keepsafe($_POST['email_address']) . "', clients_website = '" . keeptasafe($_POST['website']) . "', clients_found_us_through = '" . keeptasafe($_POST['found_us_through']) . "', clients_preffered_client = '" . keeptasafe($_POST['preffered_client']) . "' WHERE clients_id = '" . $actual_id . "'";
			$result = mysql_query($sql);
			
			if ($result) {
				$content = "<center>Your client's details have been updated, and you are being redirected to the main page.</center>
							<meta http-equiv=\"refresh\" content=\"1;url=" . $menuvar['CLIENTS'] . "\">";
			}
			else {
				$content = "<center>There was an error while updating your client's details. You are being redirected to the main page.</center>
							http-equiv=\"refresh\" content=\"5;url=" . $menuvar['CLIENTS'] . "\">";						
			}
		}
		else {
			$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "clients` WHERE clients_id = '" . $actual_id . "' LIMIT 1";
			$result = mysql_query($sql);
			
			if (!$result || mysql_num_rows($result) == 0) {
				$content = "<center>There was an error while accessing the client's details you are trying to update. You are being redirected to the main page.</center>
							<meta http-equiv='refresh' content='5;url=" . $menuvar['CLIENTS'] . "'>";	
			}
			else {
				$row = mysql_fetch_array($result);
				
				$content .= "
							<form name=\"newpageform\" action=\"" . $menuvar['CLIENTS'] . "&action=editclient&id=$row[clients_id]\" method=\"post\">
								<table class=\"contentBox\" cellpadding=\"1\" cellspacing=\"1\" width=\"100%\">
									<tr>
										<td class=\"title1\" colspan=\"2\">Edit Client's Details</td>
									</tr>
									<tr>
										<td class=\"title2\" colspan=\"2\">General Information</td>
									</tr>
									<tr class=\"row1\">
										<td><strong>Category:</strong></td>
										<td>
											" . createDropdown("categories", "cat_id", $row['clients_cat_id'], "") . "
										</td>
									</tr>
									<tr class=\"row2\">
										<td><strong>First Name:</strong></td>
										<td><input name=\"first_name\" type=\"text\" size=\"60\" value=\"" . $row['clients_first_name'] . "\" /></td>
									</tr>
									<tr class=\"row1\">
										<td><strong>Last Name:</strong></td>
										<td><input name=\"last_name\" type=\"text\" size=\"60\" value=\"" . $row['clients_last_name'] . "\" /></td>
									</tr>
									<tr class=\"row2\">
										<td><strong>Title:</strong></td>
										<td><input name=\"title\" type=\"text\" size=\"60\" value=\"" . $row['clients_title'] . "\" /></td>
									</tr>
									<tr class=\"row1\">
										<td><strong>Company:</strong></td>
										<td><input name=\"company\" type=\"text\" size=\"60\" value=\"" . $row['clients_company'] . "\" /></td>
									</tr>
									<tr class=\"row2\">
										<td><strong>Street:</strong></td>
										<td>
											<input name=\"street1\" type=\"text\" size=\"60\" value=\"" . $row['clients_street1'] . "\" /><br />
											<input name=\"street2\" type=\"text\" size=\"60\" value=\"" . $row['clients_street2'] . "\" />
										</td>
									</tr>
									<tr class=\"row1\">
										<td><strong>City:</strong></td>
										<td><input name=\"city\" type=\"text\" size=\"60\" value=\"" . $row['clients_city'] . "\" /></td>
									</tr>
									<tr class=\"row2\">
										<td><strong>State:</strong></td>
										<td><input name=\"state\" type=\"text\" size=\"60\" value=\"" . $row['clients_state'] . "\" /></td>
									</tr>
									<tr class=\"row1\">
										<td><strong>Zip Code:</strong></td>
										<td><input name=\"zip\" type=\"text\" size=\"60\" value=\"" . $row['clients_zip'] . "\" /></td>
									</tr>
									<tr>
										<td class=\"title2\" colspan=\"2\">Contact Information</td>
									</tr>
									<tr class=\"row1\">
										<td><strong>Daytime Phone:</strong></td>
										<td><input name=\"daytime_phone\" type=\"text\" size=\"60\" value=\"" . $row['clients_daytime_phone'] . "\" /></td>
									</tr>
									<tr class=\"row2\">
										<td><strong>Nighttime Phone:</strong></td>
										<td><input name=\"nighttime_phone\" type=\"text\" size=\"60\" value=\"" . $row['clients_nighttime_phone'] . "\" /></td>
									</tr>
									<tr class=\"row1\">
										<td><strong>Cell Phone:</strong></td>
										<td><input name=\"cell_phone\" type=\"text\" size=\"60\" value=\"" . $row['clients_cell_phone'] . "\" /></td>
									</tr>
									<tr class=\"row2\">
										<td><strong>Email Address:</strong></td>
										<td><input name=\"email_address\" type=\"text\" size=\"60\" value=\"" . $row['clients_email_address'] . "\" /></td>
									</tr>
									<tr class=\"row1\">
										<td><strong>Website:</strong></td>
										<td><input name=\"website\" type=\"text\" size=\"60\" value=\"" . $row['clients_website'] . "\" /></td>
									</tr>
									<tr class=\"row2\">
										<td><strong>Found Us Through:</strong></td>
										<td><input name=\"found_us_through\" type=\"text\" size=\"60\" value=\"" . $row['clients_found_us_through'] . "\" /></td>
									</tr>
									<tr class=\"row1\">
										<td><strong>Preferred Customer?</strong></td>
										<td><input name=\"preffered_client\" type=\"checkbox\" value=\"1\" value=\"1\"" . testChecked($row['clients_preffered_client'], ACTIVE) . " /></td>
									</tr>
								</table>									
								<br />
								<center><input type=\"submit\" name=\"submit\" class=\"button\" value=\"Update Client's Details\" /></center>
							</form>
							<br /><br />
							" . printOrdersTable($actual_id) . "
							<br /><br />
							" . printClientNotesTable($actual_id) . "
							<br /><br />
							" . printClientAppointmentsTable($actual_id);
			}			
		}
	}
	else {
		if ($actual_action == "deleteclient") {
			$sql = "DELETE FROM `" . $DBTABLEPREFIX . "clients` WHERE clients_id='" . $actual_id . "' LIMIT 1";
			$result = mysql_query($sql);
		}		
		
		//==================================================
		// Print out our clients table
		//==================================================
		$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "clients` ORDER BY clients_last_name ASC";
		$result = mysql_query($sql);
		
		$x = 1; //reset the variable we use for our row colors	
		
		$content = "
						<table class=\"contentBox\" cellpadding=\"1\" cellspacing=\"1\" width=\"100%\">
							<tr>
								<td class=\"title1\" colspan=\"4\">
									<div class=\"floatRight\"><a href=\"" . $menuvar['CLIENTS'] . "&action=newclient\"><img src=\"themes/" . $clms_config['ftsclms_theme'] . "/icons/add.png\" alt=\"Add a new client\" /></a></div>
									Current Clients
								</td>
							</tr>							
							<tr class=\"title2\">
								<td><strong>Full Name</strong></td><td><strong>Type of Client</strong></td><td><strong>Total Order Value</strong></td><td></td>
							</tr>";
							
		if (!$result || mysql_num_rows($result) == 0) { // No orders yet!
			$content .= "
							<tr class=\"greenRow\">
								<td colspan=\"4\">There are no clients in the database.</td>
							</tr>";	
		}
		else {	 // Print all our clients	
			while ($row = mysql_fetch_array($result)) {				
				$content .=	"
								<tr id=\"" . $row['clients_id'] . "\" class=\"row" . $x . "\">
									<td>" . $row['clients_last_name'] . ", " . $row['clients_first_name'] . "</td>
									<td>" . getCatNameByID($row['clients_cat_id']) . "</td>
									<td class=\"right\">" . formatCurrency(getTotalOrderSumByClientID($row['clients_id'])) . "</td>
									<td>
										<center><a href=\"" . $menuvar['CLIENTS'] . "&action=editclient&id=" . $row['clients_id'] . "\"><img src=\"themes/" . $clms_config['ftsclms_theme'] . "/icons/check.png\" alt=\"Edit Client Details\" /></a> <a style=\"cursor: pointer; cursor: hand;\" onclick=\"new Ajax.Request('ajax.php?action=deleteitem&table=clients&id=" . $row['clients_id'] . "', {asynchronous:true, onSuccess:function(){ new Effect.SlideUp('" . $row['clients_id'] . "');}});\"><img src=\"themes/" . $clms_config['ftsclms_theme'] . "/icons/delete.png\" alt=\"Delete Order\" /></a></center>
									</td>
								</tr>";
				$x = ($x==2) ? 1 : 2;
			}
			mysql_free_result($result);
		}
	
		$content .=		"</table>";
	}
	$page->setTemplateVar("PageContent", $content);
}
else {
	$page->setTemplateVar("PageContent", "\nYou Are Not Authorized To Access This Area. Please Refrain From Trying To Do So Again.");
}
?>