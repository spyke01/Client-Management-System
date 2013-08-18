<?php 
/***************************************************************************
 *                               clients.php
 *                            -------------------
 *   begin                : Saturday, Sept 24, 2005
 *   copyright            : (C) 2005 Paden Clayton - Fast Track Sites
 *   email                : sales@fasttacksites.com
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
 
	//=========================================================
	// Gets a clients name from a clientid
	//=========================================================
	function getClientNameFromID($clientID) {
		$sql = "SELECT first_name, last_name FROM `" . DBTABLEPREFIX . "clients` WHERE id='" . $clientID . "' LIMIT 1";
		$result = mysql_query($sql);
		
		if ($result && mysql_num_rows($result) > 0) {
			while ($row = mysql_fetch_array($result)) {
				return $row['first_name'] . " " . $row['last_name'];
			}	
			mysql_free_result($result);
		}
	}
	
	//=========================================================
	// Gets a username from a userid
	//=========================================================
	function getClientUsernameFromID($clientID) {
		$sql = "SELECT username FROM `" . USERSDBTABLEPREFIX . "users` WHERE id='" . $clientID . "'";
		$result = mysql_query($sql);
		
		if ($result && mysql_num_rows($result) > 0) {
			while ($row = mysql_fetch_array($result)) {
				return $row['username'];
			}	
			mysql_free_result($result);
		}
	}
 
	//=========================================================
	// Gets a clients email address from a clientid
	//=========================================================
	function getClientEmailAddressFromID($clientID) {
		$sql = "SELECT email_address FROM `" . DBTABLEPREFIX . "clients` WHERE id='" . $clientID . "' LIMIT 1";
		$result = mysql_query($sql);
		
		if ($result && mysql_num_rows($result) > 0) {
			while ($row = mysql_fetch_array($result)) {
				return $row['email_address'];
			}	
			mysql_free_result($result);
		}
	}
 
	//=========================================================
	// Gets a clients message from a clientid
	//=========================================================
	function getClientMessageFromID($clientID) {
		$sql = "SELECT message FROM `" . DBTABLEPREFIX . "clients` WHERE id='" . $clientID . "' LIMIT 1";
		$result = mysql_query($sql);
		
		if ($result && mysql_num_rows($result) > 0) {
			while ($row = mysql_fetch_array($result)) {
				return $row['message'];
			}	
			mysql_free_result($result);
		}
	}

	//=========================================================
	// Returns client info block
	//=========================================================
	function returnClientInfoBlock($clientID) {
		global $clms_config;
		
		$clientInfoBlock = "";
		
		$sql = "SELECT * FROM `" . DBTABLEPREFIX . "clients` WHERE id='" . $clientID . "' LIMIT 1";
		$result = mysql_query($sql);
		
		if ($result && mysql_num_rows($result) > 0) {
			while ($row = mysql_fetch_array($result)) {
				$clientInfoBlock .= ($row['first_name'] != "") ? $row['first_name'] . " " . $row['last_name'] . "<br />" : "";
				$clientInfoBlock .= ($row['title'] != "") ? $row['title'] . "<br />" : "";
				$clientInfoBlock .= ($row['company'] != "") ? $row['company'] . "<br />" : "";
				$clientInfoBlock .= ($row['street1'] != "") ? $row['street1'] . "<br />" : "";
				$clientInfoBlock .= ($row['street2'] != "") ? $row['street2'] . "<br />" : "";
				$clientInfoBlock .= ($row['city'] != "") ? $row['city'] . ", " . $row['state'] . " " . $row['zip'] . "<br />" : "";
				$clientInfoBlock .= ($row['daytime_phone'] != "") ? "Daytime Phone: " . $row['daytime_phone'] . "<br />" : "";
				$clientInfoBlock .= ($row['nighttime_phone'] != "") ? "Nighttime Phone: " . $row['nighttime_phone'] . "<br />" : "";
				$clientInfoBlock .= ($row['cell_phone'] != "") ? "Cell Phone: " . $row['cell_phone'] . "<br />" : "";
				$clientInfoBlock .= ($row['ftsclms_invoice_fax'] != "") ? "Fax: " . $row['ftsclms_invoice_fax'] . "<br />" : "";
				$clientInfoBlock .= ($row['email_address'] != "") ? "Email: " . $row['email_address'] . "<br />" : "";
				$clientInfoBlock .= ($row['website'] != "") ? "Website: " . $row['website'] . "<br />" : "";
			}	
			mysql_free_result($result);
		}
		
		return $clientInfoBlock;
	}
	
 	//=================================================
	// Print the Clients Table
	//=================================================
	function printClientsTable() {
		global $menuvar, $clms_config;
		
		$sql = "SELECT * FROM `" . DBTABLEPREFIX . "clients` ORDER BY company ASC";
		$result = mysql_query($sql);
		
		// Create our new table
		$table = new tableClass(1, 1, 1, "contentBox tablesorter", "clientsTable");
		
		// Create table title
		$table->addNewRow(array(array("data" => "Current Clients (" . mysql_num_rows($result) . ")", "colspan" => "5")), "", "title1", "thead");
		
		// Create column headers
		$table->addNewRow(
			array(
				array("type" => "th", "data" => "Comapny Name"),
				array("type" => "th", "data" => "Full Name"),
				array("type" => "th", "data" => "Type of Client"),
				array("type" => "th", "data" => "Total Order Value"),
				array("type" => "th", "data" => "")
			), "", "title2", "thead"
		);
							
		// Add our data
		if (!$result || mysql_num_rows($result) == 0) {
			$table->addNewRow(array(array("data" => "There are no clients in the system.", "colspan" => "5")), "clientsTableDefaultRow", "greenRow");
		}
		else {
			while ($row = mysql_fetch_array($result)) {
				$table->addNewRow(
					array(
						array("data" => $row['company']),
						array("data" => $row['last_name'] . ", " . $row['first_name']),
						array("data" => getCatNameByID($row['cat_id'])),
						array("data" => formatCurrency(getTotalOrderSumByClientID($row['id'])), "class" => "right"),
						array("data" => "<a href=\"" . $menuvar['CLIENTS'] . "&action=editclient&id=" . $row['id'] . "\"><img src=\"themes/" . $clms_config['ftsclms_theme'] . "/icons/check.png\" alt=\"Edit Client Details\" /></a> <a href=\"" . $menuvar['CLIENTS'] . "&action=sendwelcomemessage&id=" . $row['id'] . "\"><img src=\"themes/" . $clms_config['ftsclms_theme'] . "/icons/message.png\" alt=\"Send Welcome Message\" /></a> " . createDeleteLinkWithImage($row['id'], $row['id'] . "_row", "clients", "client"), "class" => "center")
					), $row['id'] . "_row", ""
				);
			}
			mysql_free_result($result);
		}
		
		// Return the table's HTML
		return $table->returnTableHTML() . "
				<div id=\"clientsTableUpdateNotice\"></div>";
	}
	
	//=================================================
	// Returns the JQuery functions used to run the 
	// clients table
	//=================================================
	function returnClientsTableJQuery() {							
		$JQueryReadyScripts = "
				$('#clientsTable').tablesorter({ widgets: ['zebra'], headers: { 3: { sorter: false } } });";
		
		return $JQueryReadyScripts;
	}

	//=================================================
	// Print the Highest Paying Clients Table
	//=================================================
	function printHighestPayingClientsTable($orderLimit = 5) {
		global $menuvar, $clms_config;
		
		$sql = "SELECT sum(total) AS total_ordered, client_id FROM `" . DBTABLEPREFIX . "orders` GROUP BY client_id ORDER BY total_ordered DESC LIMIT " . $orderLimit;
		$result = mysql_query($sql);
		
		//echo $sql;
		
		// Create our new table
		$table = new tableClass(1, 1, 1, "contentBox tablesorter", "highestPayingClientsTable");
		
		// Create table title
		$table->addNewRow(array(array("data" => "Highest Paying Clients", "colspan" => "5")), "", "title1", "thead");
		
		// Create column headers
		$table->addNewRow(
			array(
				array("type" => "th", "data" => "Client"),
				array("type" => "th", "data" => "Total")
			), "", "title2", "thead"
		);
							
		// Add our data
		if (!$result || mysql_num_rows($result) == 0) {
			$table->addNewRow(array(array("data" => "There are no orders in the system.", "colspan" => "3")), "", "greenRow");
		}
		else {
			while ($row = mysql_fetch_array($result)) {				
				$table->addNewRow(
					array(
						array("data" => getClientNameFromID($row['client_id'])),
						array("data" => formatCurrency($row['total_ordered']))
					), "", ""
				);
			}
			mysql_free_result($result);
		}
		
		// Return the table's HTML
		return $table->returnTableHTML();
	}
	
	//=================================================
	// Returns the JQuery functions used to run the 
	// LargestOrders table
	//=================================================
	function returnHighestPayingClientsTableJQuery() {							
		$JQueryReadyScripts = "
				$('#highestPayingClientsTable').tablesorter({ widgets: ['zebra']
				});";
		
		return $JQueryReadyScripts;
	}
	
	//=================================================
	// Create a form to add new clients
	//
	// Used so that we can display it in many places
	//=================================================
	function printNewClientForm() {
		global $menuvar, $clms_config;
		
		$content .= "
				<div id=\"newClientResponse\">
				</div>
				<form name=\"newClientForm\" id=\"newClientForm\" action=\"" . $menuvar['CLIENTS'] . "\" method=\"post\" class=\"inputForm\" onsubmit=\"return false;\">
					<div id=\"newClientTabs\">
						<ul>
							<li><a href=\"#generalInformation\"><span>General Information</span></a></li>
							<li><a href=\"#contactInformation\"><span>Contact Information</span></a></li>
							<li><a href=\"#loginInformation\"><span>Login Information</span></a></li>
							<li><a href=\"#messageInformation\"><span>Message Information</span></a></li>
						</ul>
						<div id=\"generalInformation\">
							<fieldset>
								<div><label for=\"cat_id\">Category <span>- Required</span></label> " . createDropdown("categories", "cat_id", "", "", "required") . "</div>
								<div><label for=\"first_name\">First Name <span>- Required</span></label> <input name=\"first_name\" id=\"first_name\" type=\"text\" size=\"60\" class=\"required\" /></div>
								<div><label for=\"last_name\">Last Name <span>- Required</span></label> <input name=\"last_name\" id=\"last_name\" type=\"text\" size=\"60\" class=\"required\" /></div>
								<div><label for=\"title\">Title </label> <input name=\"title\" id=\"title\" type=\"text\" size=\"60\" /></div>
								<div><label for=\"company\">Company </label> <input name=\"company\" id=\"company\" type=\"text\" size=\"60\" /></div>
								<div><label for=\"street1\">Street (Line 1) </label> <input name=\"street1\" id=\"street1\" type=\"text\" size=\"60\" /></div>
								<div><label for=\"street2\">Street (Line 2) </label> <input name=\"street2\" id=\"street2\" type=\"text\" size=\"60\" /></div>
								<div><label for=\"city\">City </label> <input name=\"city\" id=\"city\" type=\"text\" size=\"60\" /></div>
								<div><label for=\"state\">State </label> <input name=\"state\" id=\"state\" type=\"text\" size=\"60\" /></div>
								<div><label for=\"zip\">Zip Code </label> <input name=\"zip\" id=\"zip\" type=\"text\" size=\"60\" /></div>
							</fieldset>
						</div>
						<div id=\"contactInformation\">
							<fieldset>
								<div><label for=\"daytime_phone\">Daytime Phone </label> <input name=\"daytime_phone\" id=\"daytime_phone\" type=\"text\" size=\"60\" /></div>
								<div><label for=\"nighttime_phone\">Nighttime Phone </label> <input name=\"nighttime_phone\" id=\"nighttime_phone\" type=\"text\" size=\"60\" /></div>
								<div><label for=\"cell_phone\">Cell Phone </label> <input name=\"cell_phone\" id=\"cell_phone\" type=\"text\" size=\"60\" /></div>
								<div><label for=\"email_address\">Email Address </label> <input name=\"email_address\" id=\"email_address\" type=\"text\" size=\"60\" /></div>
								<div><label for=\"website\">Website </label> <input name=\"website\" id=\"website\" type=\"text\" size=\"60\" /></div>
								<div><label for=\"found_us_through\">Found Us Through </label> <input name=\"found_us_through\" id=\"found_us_through\" type=\"text\" size=\"60\" /></div>
								<div><label for=\"preffered_client\">Preferred Customer </label> <input name=\"preffered_client\" type=\"checkbox\" value=\"1\" /></div>
							</fieldset>
						</div>
						<div id=\"loginInformation\">
							<fieldset>
								<div><label for=\"username\">Username </label> <input name=\"username\" id=\"username\" type=\"text\" size=\"60\" /></div>
								<div><label for=\"password\">Password </label> <input name=\"password\" id=\"password\" type=\"password\" size=\"60\" /></div>
								<div><label for=\"password2\">Confirm Password </label> <input name=\"password2\" id=\"password2\" type=\"password\" size=\"60\" /></div>
							</fieldset>
						</div>
						<div id=\"messageInformation\">
							<fieldset>
								<div><label for=\"message\">Client Message </label> <input name=\"message\" id=\"message\" type=\"text\" size=\"60\" /></div>
							</fieldset>
						</div>
					</div>
					<div class=\"center\"><input type=\"submit\" class=\"button\" value=\"Create Client\" /> <input type=\"button\" id=\"clearFormButton\" class=\"button\" value=\"Clear Form\" /></div>
				</form>";
			
		return $content;
	}
	
	//=================================================
	// Returns the JQuery functions used to run the 
	// new client form
	//=================================================
	function returnNewClientFormJQuery($reprintTable = 0, $allowModification = 1) {		
		$extraJQuery = ($reprintTable == 0) ? "
  						// Update the proper div with the returned data
						$('#newClientResponse').html('" . progressSpinnerHTML() . "');
						$('#newClientResponse').html(data);
						$('#newClientResponse').effect('highlight',{},500);" 
						: "
						// Clear the default row
						$('#clientsTableDefaultRow').remove();
  						// Update the table with the new row
						$('#clientsTable > tbody:last').append(data);
						$('#clientsTableUpdateNotice').html('" . tableUpdateNoticeHTML() . "');
						// Show a success message
						$('#newClientResponse').html('" . progressSpinnerHTML() . "');
						$('#newClientResponse').html(returnSuccessMessage('client'));";
							
		$JQueryReadyScripts = "
			var v = jQuery(\"#newClientForm\").validate({
				errorElement: \"div\",
				errorClass: \"validation-advice\",
				rules: {
					password2: {
						equalTo: '#password'
					}
				},
				submitHandler: function(form) {			
					jQuery.get('ajax.php?action=createClient&reprinttable=" . $reprintTable . "&showButtons=" . $allowModification . "', $('#newClientForm').serialize(), function(data) {
  						" . $extraJQuery . "
						// Clear the form
						/*
						$('#first_name').val = '';
						$('#last_name').val = '';
						$('#email_address').val = '';
						$('#username').val = '';
						$('#password').val = '';
						$('#password2').val = '';
						$('#company').val = '';
						$('#website').val = '';
						*/
					});
				}
			});
			$('#clearFormButton').click(function () {
				if (confirm('Are you sure you want to clear this form?')) {
					$('#newClientForm').clearForm();
				}
			});";
		
		return $JQueryReadyScripts;
	}

?>