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
if ($_SESSION['user_level'] == SYSTEM_ADMIN || $_SESSION['user_level'] == CLIENT_ADMIN) {
	//==================================================
	// Handle editing, adding, and deleting of clients
	//==================================================	
	if ($actual_action == "editclient" && isset($actual_id)) {
		// Add breadcrumb
		$page->addBreadCrumb("Edit Client", "");
		
		if (isset($_POST['submit'])) {
			$password = keepsafe($_POST['password']);
			$password2 = keepsafe($_POST['password2']);
			
			$passwordSQL = ($password != "" && $password == $password2) ? ", password = '" .  md5($password) . "'": "";
			$warnings = ($password != "" && $password != $password2) ? "Your passwords did not match so the password has not been updated." : "";
			$warnings = ($warnings != "") ? "<div class=\"errorMessage\">" . $warnings . "</div>" : "";
			
			$sql = "UPDATE `" . DBTABLEPREFIX . "clients` SET cat_id = '" . keepsafe($_POST['cat_id']) . "', first_name = '" . keepsafe($_POST['first_name']) . "', last_name = '" . keeptasafe($_POST['last_name']) . "', title = '" . keeptasafe($_POST['title']) . "', company = '" . keeptasafe($_POST['company']) . "', street1 = '" . keeptasafe($_POST['street1']) . "', street2 = '" . keeptasafe($_POST['street2']) . "', city = '" . keeptasafe($_POST['city']) . "', state = '" . keeptasafe($_POST['state']) . "', zip = '" . keepsafe($_POST['zip']) . "', daytime_phone = '" . keeptasafe($_POST['daytime_phone']) . "', nighttime_phone = '" . keeptasafe($_POST['nighttime_phone']) . "', cell_phone = '" . keeptasafe($_POST['cell_phone']) . "', email_address = '" . keepsafe($_POST['email_address']) . "', website = '" . keeptasafe($_POST['website']) . "', found_us_through = '" . keeptasafe($_POST['found_us_through']) . "', preffered_client = '" . keeptasafe($_POST['preffered_client']) . "', username = '" . keeptasafe($_POST['username']) . "'" . $passwordSQL . " WHERE id = '" . $actual_id . "'";
			$result = mysql_query($sql);
			
			if ($result) {
				$page_content .= $warnings . "
					<div class=\"roundedBox\">
						Your client's details have been updated, and you are being redirected to the main page.
						<meta http-equiv=\"refresh\" content=\"1;url=" . $menuvar['CLIENTS'] . "&action=editclient&id=" . $actual_id . "\">
					</div>";
			}
			else {
				$page_content .= $warnings . "
					<div class=\"roundedBox\">
						There was an error while updating your client's details. You are being redirected to the main page.
						http-equiv=\"refresh\" content=\"5;url=" . $menuvar['CLIENTS'] . "&action=editclient&id=" . $actual_id . "\">
					</div>";						
			}
		}
		else {
			$sql = "SELECT * FROM `" . DBTABLEPREFIX . "clients` WHERE id = '" . $actual_id . "' LIMIT 1";
			$result = mysql_query($sql);
			
			if (!$result || mysql_num_rows($result) == 0) {
				$page_content .= "
					<div class=\"roundedBox\">
						There was an error while accessing the client's details you are trying to update. You are being redirected to the main page.
						<meta http-equiv=\"refresh\" content=\"5;url=" . $menuvar['CLIENTS'] . "\">
					</div>";	
			}
			else {
				$row = mysql_fetch_array($result);
				
				$page_content .= "
						<div id=\"tabs\">
							<ul>
								<li><a href=\"#clientDetails\"><span>Client Details</span></a></li>
								<li><a href=\"#notes\"><span>Notes</span></a></li>
								<li><a href=\"#appointments\"><span>Appointments</span></a></li>
								<li><a href=\"#invoices\"><span>Invoices</span></a></li>
								<li><a href=\"#orders\"><span>Orders</span></a></li>
								<li><a href=\"#downloads\"><span>Downloads</span></a></li>
							</ul>
							<div id=\"clientDetails\">
								<form name=\"newpageform\" action=\"" . $menuvar['CLIENTS'] . "&action=editclient&id=" . $actual_id . "\" method=\"post\">
									<table class=\"contentBox\" cellpadding=\"1\" cellspacing=\"1\" width=\"100%\">
										<tr>
											<td class=\"title1\" colspan=\"2\">Edit Client's Details</td>
										</tr>
										<tr>
											<th class=\"title2\" colspan=\"2\">General Information</th>
										</tr>
										<tr class=\"row1\">
											<td><strong>Category:</strong></td>
											<td>
												" . createDropdown("categories", "cat_id", $row['cat_id'], "") . "
											</td>
										</tr>
										<tr class=\"row2\">
											<td><strong>First Name:</strong></td>
											<td><input name=\"first_name\" type=\"text\" size=\"60\" value=\"" . $row['first_name'] . "\" /></td>
										</tr>
										<tr class=\"row1\">
											<td><strong>Last Name:</strong></td>
											<td><input name=\"last_name\" type=\"text\" size=\"60\" value=\"" . $row['last_name'] . "\" /></td>
										</tr>
										<tr class=\"row2\">
											<td><strong>Title:</strong></td>
											<td><input name=\"title\" type=\"text\" size=\"60\" value=\"" . $row['title'] . "\" /></td>
										</tr>
										<tr class=\"row1\">
											<td><strong>Company:</strong></td>
											<td><input name=\"company\" type=\"text\" size=\"60\" value=\"" . $row['company'] . "\" /></td>
										</tr>
										<tr class=\"row2\">
											<td><strong>Street:</strong></td>
											<td>
												<input name=\"street1\" type=\"text\" size=\"60\" value=\"" . $row['street1'] . "\" /><br />
												<input name=\"street2\" type=\"text\" size=\"60\" value=\"" . $row['street2'] . "\" />
											</td>
										</tr>
										<tr class=\"row1\">
											<td><strong>City:</strong></td>
											<td><input name=\"city\" type=\"text\" size=\"60\" value=\"" . $row['city'] . "\" /></td>
										</tr>
										<tr class=\"row2\">
											<td><strong>State:</strong></td>
											<td><input name=\"state\" type=\"text\" size=\"60\" value=\"" . $row['state'] . "\" /></td>
										</tr>
										<tr class=\"row1\">
											<td><strong>Zip Code:</strong></td>
											<td><input name=\"zip\" type=\"text\" size=\"60\" value=\"" . $row['zip'] . "\" /></td>
										</tr>
										<tr>
											<th class=\"title2\" colspan=\"2\">Contact Information</th>
										</tr>
										<tr class=\"row1\">
											<td><strong>Daytime Phone:</strong></td>
											<td><input name=\"daytime_phone\" type=\"text\" size=\"60\" value=\"" . $row['daytime_phone'] . "\" /></td>
										</tr>
										<tr class=\"row2\">
											<td><strong>Nighttime Phone:</strong></td>
											<td><input name=\"nighttime_phone\" type=\"text\" size=\"60\" value=\"" . $row['nighttime_phone'] . "\" /></td>
										</tr>
										<tr class=\"row1\">
											<td><strong>Cell Phone:</strong></td>
											<td><input name=\"cell_phone\" type=\"text\" size=\"60\" value=\"" . $row['cell_phone'] . "\" /></td>
										</tr>
										<tr class=\"row2\">
											<td><strong>Email Address:</strong></td>
											<td><input name=\"email_address\" type=\"text\" size=\"60\" value=\"" . $row['email_address'] . "\" /></td>
										</tr>
										<tr class=\"row1\">
											<td><strong>Website:</strong></td>
											<td><input name=\"website\" type=\"text\" size=\"60\" value=\"" . $row['website'] . "\" /></td>
										</tr>
										<tr class=\"row2\">
											<td><strong>Found Us Through:</strong></td>
											<td><input name=\"found_us_through\" type=\"text\" size=\"60\" value=\"" . $row['found_us_through'] . "\" /></td>
										</tr>
										<tr class=\"row1\">
											<td><strong>Preferred Customer?</strong></td>
											<td><input name=\"preffered_client\" type=\"checkbox\" value=\"1\" value=\"1\"" . testChecked($row['preffered_client'], ACTIVE) . " /></td>
										</tr>
										<tr>
											<th class=\"title2\" colspan=\"2\">Login Information</th>
										</tr>
										<tr class=\"row1\">
											<td><strong>Username:</strong></td>
											<td><input name=\"username\" type=\"text\" size=\"60\" value=\"" . $row['username'] . "\" /></td>
										</tr>
										<tr class=\"row2\">
											<td><strong>Password:</strong></td>
											<td><input name=\"password\" type=\"password\" size=\"60\" /></td>
										</tr>
										<tr class=\"row2\">
											<td><strong>Confirm Password:</strong></td>
											<td><input name=\"password2\" type=\"password\" size=\"60\" /></td>
										</tr>
									</table>									
									<br />
									<center><input type=\"submit\" name=\"submit\" class=\"button\" value=\"Update Client's Details\" /></center>
								</form>
							</div>
							<div id=\"notes\">
								<div id=\"updateMeNotes\">
									" . printNotesTable($actual_id) . "
								</div>
								<br /><br />
								" . printNewNoteForm($actual_id) . "
							</div>
							<div id=\"appointments\">
								<div id=\"updateMeAppointments\">
									" . printAppointmentsTable($actual_id) . "
								</div>
								<br /><br />
								" . printNewAppointmentForm(time(), $actual_id) . "
							</div>
							<div id=\"invoices\">
								<div id=\"updateMeInvoices\">
									" . printInvoicesTable($actual_id) . "
								</div>
								<br /><br />
								" . printNewInvoiceForm($actual_id) . "
							</div>
							<div id=\"orders\">
								<div id=\"updateMeOrders\">
									" . printOrdersTable($actual_id,1,0) . "
								</div>
								<br /><br />
								" . printNewOrderForm($actual_id) . "
							</div>
							<div id=\"downloads\">
								<div id=\"updateMeDownloads\">
									" . printDownloadsTable($actual_id) . "
								</div>
								<br /><br />
								" . printNewDownloadForm($actual_id) . "
							</div>
						</div>";
				
				// Handle our JQuery needs
				$JQueryReadyScripts = returnNotesTableJQuery($actual_id) . returnNewNoteFormJQuery(1) . returnAppointmentsTableJQuery() . returnNewAppointmentFormJQuery(1) . returnInvoicesTableJQuery($actual_id) . returnNewInvoiceFormJQuery(1) . returnOrdersTableJQuery($actual_id,1,0) . returnNewOrderFormJQuery(1, 1, 0) . returnDownloadsTableJQuery($actual_id) . returnNewDownloadFormJQuery(1) . "$(\"#tabs\").tabs();";
			}			
		}
	}
	else {
		//==================================================
		// Print out our clients table
		//==================================================
		$page_content .= "
						<div id=\"tabs\">
							<ul>
								<li><a href=\"#currentClients\"><span>Current Clients</span></a></li>
								<li><a href=\"#createANewClient\"><span>Create a New Client</span></a></li>
							</ul>
							<div id=\"currentClients\">
								<div id=\"updateMeClients\">
									" . printClientsTable() . "
								</div>
							</div>
							<div id=\"createANewClient\">
								" . printNewClientForm() . "
							</div>
						</div>";
				
				// Handle our JQuery needs
				$JQueryReadyScripts = returnClientsTableJQuery() . returnNewClientFormJQuery(1) . "$(\"#tabs\").tabs();$(\"#newClientTabs\").tabs();";
	}
	
	$page->setTemplateVar("PageContent", $page_content);
	$page->setTemplateVar("JQueryReadyScript", $JQueryReadyScripts);
}
else {
	$page->setTemplateVar("PageContent", "\nYou Are Not Authorized To Access This Area. Please Refrain From Trying To Do So Again.");
}
?>