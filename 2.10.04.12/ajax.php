<? 
/***************************************************************************
 *                               ajax.php
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
	include 'includes/header.php';
	
	$actual_id = keepsafe($_GET['id']);
	$actual_action = parseurl($_GET['action']);
	$actual_value = parseurl($_GET['value']);
	$actual_type = parseurl($_GET['type']);
	$actual_showButtons = parseurl($_GET['showButtons']);
	$actual_showClient = parseurl($_GET['showClient']);
	
	// Only admoins should be able to utilize any of these functions
	if ($_SESSION['user_level'] == SYSTEM_ADMIN || $_SESSION['user_level'] == CLIENT_ADMIN) {		
		//================================================
		// Main updater and get functions
		//================================================
		// Update an item in a DB table
		if ($actual_action == "updateitem") {
			$item = parseurl($_GET['item']);
			$table = parseurl($_GET['table']);
			$updateto = ($table == "notes" && $item == "text") ? preg_replace('/\<br(\s*)?\/?\>/i', "\n", $updateto) : $updateto;
			$updateto = ($item == "datetimestamp" || $item == "date_ordered" || $item == "date_shipped") ? strtotime(keeptasafe($_REQUEST['value'])) : keeptasafe($_REQUEST['value']);
			
			// Client admins can only modify certain tables
			if ($_SESSION['user_level'] == SYSTEM_ADMIN || ($_SESSION['user_level'] == CLIENT_ADMIN && ($table != "config" || $table != "products" || $table != "users"))) {
				$table = ($table == "users") ? USERSDBTABLEPREFIX . $table : DBTABLEPREFIX . $table;
				
				$sql = "UPDATE `" . $table . "` SET " . $item ." = '" . $updateto . "' WHERE id = '" . $actual_id . "'";
				$result = mysql_query($sql);		
				
				if ($item == "datetimestamp" || $item == "date_ordered" || $item == "date_shipped") { 
					$result =  (trim($updateto) != "") ? makeDateTime($updateto) : "";
					echo $result;
				}
				elseif ($item == "discount") { 
					echo formatCurrency($updateto);
				}
				elseif ($item == "note") { 
					echo ajaxnl2br($updateto);
				}
				else { echo stripslashes($updateto); }
			}
		}
		
		// Get an item from a DB table
		elseif ($actual_action == "getitem") {
			$item = parseurl($_GET['item']);
			$table = parseurl($_GET['table']);
			
			// Client admins can only modify certain tables
			if ($_SESSION['user_level'] == SYSTEM_ADMIN || ($_SESSION['user_level'] == CLIENT_ADMIN && ($table != "config" || $table != "products" || $table != "users"))) {
				$table = ($table == "users") ? USERSDBTABLEPREFIX . $table : DBTABLEPREFIX . $table;
			
				$sql = "SELECT " . $item ." FROM `" . $table . "` WHERE id = '" . $actual_id . "'";
				$result = mysql_query($sql);
				
				if ($result && mysql_num_rows($result) > 0) {
					while ($row = mysql_fetch_array($result)) {	
						if ($item == "datetimestamp" || $item == "date_ordered" || $item == "date_shipped") { 
							$returnVar =  (trim($row[$item]) != "") ? makeShortDateTime($row[$item]) : ""; 
							echo $returnVar;
						}
						elseif ($item == "note") { 
							echo $row[$item];
						}
						else { echo bbcode($row[$item]); }
					}
					mysql_free_result($result);
				}
			}
		}	
		
		// Delete a row from a DB table
		elseif ($actual_action == "deleteitem") {
			$table = parseurl($_GET['table']);
			$errorCount = 0;
			
			// Client admins can only modify certain tables
			if ($_SESSION['user_level'] == SYSTEM_ADMIN || ($_SESSION['user_level'] == CLIENT_ADMIN && ($table != "config" || $table != "products" || $table != "users"))) {
				// Delete and associated foreign items
				if ($table == "clients") {
					// Delete Appointments
					$sql = "DELETE FROM `" . DBTABLEPREFIX . "appointments` WHERE client_id = '" . $actual_id . "'";
					$result = mysql_query($sql);
					$errorCount += ($result) ? 0 : 1;
					
					// Select all associated Invoices so we can kill their foreign items
					$sql = "SELECT id FROM `" . DBTABLEPREFIX . "invoices` WHERE client_id = '" . $actual_id . "'";
					$result = mysql_query($sql);
					$errorCount += ($result) ? 0 : 1;
					
					if ($result && mysql_num_rows($result) > 0) {
						while ($row = mysql_fetch_array($result)) {
							// Delete Payments
							$sql = "DELETE FROM `" . DBTABLEPREFIX . "invoices_payments` WHERE invoice_id = '" . $row['id'] . "'";
							$result = mysql_query($sql);
							$errorCount += ($result) ? 0 : 1;
							
							// Delete Invoice Products
							$sql = "DELETE FROM `" . DBTABLEPREFIX . "invoices_products` WHERE invoice_id = '" . $row['id'] . "'";
							$result = mysql_query($sql);
							$errorCount += ($result) ? 0 : 1;
						}		
						mysql_free_result($result);
					}
					
					// Select all associated Orders so we can kill their foreign items
					$sql = "SELECT id FROM `" . DBTABLEPREFIX . "orders` WHERE client_id = '" . $actual_id . "'";
					$result = mysql_query($sql);
					$errorCount += ($result) ? 0 : 1;
					
					if ($result && mysql_num_rows($result) > 0) {
						while ($row = mysql_fetch_array($result)) {
							// Delete Payments
							$sql = "DELETE FROM `" . DBTABLEPREFIX . "orders_payments` WHERE order_id = '" . $row['id'] . "'";
							$result = mysql_query($sql);
							$errorCount += ($result) ? 0 : 1;
							
							// Delete Order Products
							$sql = "DELETE FROM `" . DBTABLEPREFIX . "orders_products` WHERE order_id = '" . $row['id'] . "'";
							$result = mysql_query($sql);
							$errorCount += ($result) ? 0 : 1;
						}		
						mysql_free_result($result);
					}
					
					// Delete Invoices
					$sql = "DELETE FROM `" . DBTABLEPREFIX . "invoices` WHERE client_id = '" . $actual_id . "'";
					$result = mysql_query($sql);
					$errorCount += ($result) ? 0 : 1;
					
					// Delete Notes
					$sql = "DELETE FROM `" . DBTABLEPREFIX . "notes` WHERE client_id = '" . $actual_id . "'";
					$result = mysql_query($sql);
					$errorCount += ($result) ? 0 : 1;
					
					// Delete Orders
					$sql = "DELETE FROM `" . DBTABLEPREFIX . "orders` WHERE client_id = '" . $actual_id . "'";
					$result = mysql_query($sql);
					$errorCount += ($result) ? 0 : 1;
				}
				if ($table == "invoices") {
					// Delete Payments
					$sql = "DELETE FROM `" . DBTABLEPREFIX . "invoices_payments` WHERE invoice_id = '" . $actual_id . "'";
					$result = mysql_query($sql);
					$errorCount += ($result) ? 0 : 1;
					
					// Delete Invoice Products
					$sql = "DELETE FROM `" . DBTABLEPREFIX . "invoices_products` WHERE invoice_id = '" . $actual_id . "'";
					$result = mysql_query($sql);
					$errorCount += ($result) ? 0 : 1;
				}
				if ($table == "invoices_payments") {
					// Check to see if our invoice is no longer paid in full and if so then change its status
					$sql = "UPDATE `" . DBTABLEPREFIX . "invoices` i SET status = '" . STATUS_INVOICE_AWAITING_PAYMENT . "' WHERE coalesce((SELECT SUM((ip.price + ip.profit + ip.shipping ) * ip.qty) - i.discount FROM `" . DBTABLEPREFIX . "invoices_products` ip WHERE ip.invoice_id = i.id), 0) - coalesce((SELECT SUM(ipa.paid) FROM `" . DBTABLEPREFIX . "invoices_payments` ipa WHERE ipa.invoice_id = i.id), 0) > 0";
					$result = mysql_query($sql);
					$errorCount += ($result) ? 0 : 1;
				}
				if ($table == "orders") {
					// Delete Payments
					$sql = "DELETE FROM `" . DBTABLEPREFIX . "orders_payments` WHERE order_id = '" . $actual_id . "'";
					$result = mysql_query($sql);
					$errorCount += ($result) ? 0 : 1;
					
					// Delete Order Products
					$sql = "DELETE FROM `" . DBTABLEPREFIX . "orders_products` WHERE order_id = '" . $actual_id . "'";
					$result = mysql_query($sql);
					$errorCount += ($result) ? 0 : 1;
				}
				if ($table == "orders_payments") {
					// Check to see if our order is no longer paid in full and if so then change its status
					$sql = "UPDATE `" . DBTABLEPREFIX . "orders` i SET status = '" . STATUS_INVOICE_AWAITING_PAYMENT . "' WHERE coalesce((SELECT SUM((ip.price + ip.profit + ip.shipping ) * ip.qty) - i.discount FROM `" . DBTABLEPREFIX . "orders_products` ip WHERE ip.order_id = i.id), 0) - coalesce((SELECT SUM(ipa.paid) FROM `" . DBTABLEPREFIX . "orders_payments` ipa WHERE ipa.order_id = i.id), 0) > 0";
					$result = mysql_query($sql);
					$errorCount += ($result) ? 0 : 1;
				}
				
				$table = ($table == "users") ? USERSDBTABLEPREFIX . $table : DBTABLEPREFIX . $table;
			
				// Delete actual table row
				$sql = "DELETE FROM `" . $table . "` WHERE id = '" . $actual_id . "'";
				$result = mysql_query($sql);
				$errorCount += ($result) ? 0 : 1;
				
				$success = ($errorCount == 0) ? 1 : 0;
				
				echo $success;
			}
		}
			
		//================================================
		// Update our appointments in the database
		//================================================
		elseif ($actual_action == "createAppointment") {
			$datetimestamp = strtotime($_GET['datetimestamp']);
			$client_id = keeptasafe($_GET['client_id']);
			$place = keeptasafe($_GET['place']);
			$attire = keeptasafe($_GET['attire']);
			$description = keeptasafe($_GET['description']);
			$urgency = keeptasafe($_GET['urgency']);	
			
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "appointments` (`datetimestamp`, `client_id`, `place`, `attire`, `description`, `urgency`) VALUES ('" . $datetimestamp . "', '" . $client_id . "', '" . $place . "', '" . $attire . "', '" . $description . "', '" . $urgency . "')";
			$result = mysql_query($sql);
			$appointmentID = mysql_insert_id();
			
			$content = ($result) ? "	<span class=\"greenText bold\">Successfully created appointment!</span>" : "	<span class=\"redText bold\">Failed to create appointment!!!<br />$sql</span>";
			
			$rowColor = ($urgency != LOW) ? "redRow" : "greenRow";
			$rowColor = ($urgency != HIGH && $rowColor == "redRow") ? "yellowRow" : $rowColor;
					
			switch(keepsafe($_GET['reprinttable'])) {
				case 1:
					$finalColumnData = ($actual_showButtons == 1) ? createDeleteLinkWithImage($appointmentID, $appointmentID . "_row", "appointments", "appointment") : "";
					
					$tableHTML = "
						<tr class=\"" . $rowColor . "\" id=\"" . $appointmentID . "_row\">
							<td>" . makeDateTime($datetimestamp) . "</td>
							<td>" . $place . "</td>
							<td>" . $attire . "</td>
							<td>" . $description . "</td>
							<td class=\"center\">" . $finalColumnData . "</td>
						</tr>";
						
					echo $tableHTML;
					break;
				case 2:
					$finalColumnData = ($actual_showButtons == 1) ? createDeleteLinkWithImage($appointmentID, $appointmentID . "_row", "invoices", "appointment") : "";
					
					$tableHTML = "
						<tr class=\"even\" id=\"" . $appointmentID . "_row\">
							<td>" . makeDateTime($datetimestamp) . "</td>
							<td>" . $place . "</td>
							<td>" . $attire . "</td>
							<td>" . getClientNameFromID($client_id) . "</td>
							<td>" . $description . "</td>
							<td class=\"center\">" . $finalColumnData . "</td>
						</tr>";
						
					echo $tableHTML;
					break;
				case 3:
					echo printAppointmentCalendar();
					break;
				default:
					echo $content;
					break;
			}
		}
			
		//================================================
		// Update our calendar
		//================================================
		elseif ($actual_action == "printAppointmentCalendar") {
			echo printAppointmentCalendar();
		}
		
		//================================================
		// Add our cats to the database
		//================================================
		elseif ($actual_action == "createCategory") {
			$name = keeptasafe($_GET['catname']);	
			
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "categories` (`name`) VALUES ('" . $name . "')";
			$result = mysql_query($sql);
			$categoryID = mysql_insert_id();
			
			$content = ($result) ? "	<span class=\"greenText bold\">Successfully created category!</span>" : "	<span class=\"redText bold\">Failed to create category!!!</span>";
			
			switch(keepsafe($_GET['reprinttable'])) {
				case 1:
					$finalColumnData = ($actual_showButtons == 1) ? createDeleteLinkWithImage($categoryID, $categoryID . "_row", "categories", "category") : "";
					
					$tableHTML = "
						<tr class=\"even\" id=\"" . $categoryID . "_row\">
							<td>" . $name . "</td>
							<td class=\"center\">" . $finalColumnData . "</td>
						</tr>";
						
					echo $tableHTML;
					break;
				default:
					echo $content;
					break;
			}
		}
			
		//================================================
		// Add our client to the database
		//================================================
		elseif ($actual_action == "createClient") {
			$cat_id = keepsafe($_GET['cat_id']);
			$first_name = keeptasafe($_GET['first_name']);
			$last_name = keeptasafe($_GET['last_name']);
			$title = keeptasafe($_GET['title']);
			$company = keeptasafe($_GET['company']);
			$street1 = keeptasafe($_GET['street1']);
			$street2 = keeptasafe($_GET['street2']);
			$city = keeptasafe($_GET['city']);
			$state = keeptasafe($_GET['state']);
			$zip = keeptasafe($_GET['zip']);
			$daytime_phone = keeptasafe($_GET['daytime_phone']);
			$nighttime_phone = keeptasafe($_GET['nighttime_phone']);
			$cell_phone = keeptasafe($_GET['cell_phone']);
			$email_address = keeptasafe($_GET['email_address']);
			$website = keeptasafe($_GET['website']);
			$found_us_through = keeptasafe($_GET['found_us_through']);
			$preffered_client = keeptasafe($_GET['preffered_client']);
			$username = keeptasafe($_GET['username']);
			$password = keeptasafe($_GET['password']);
			$password2 = keeptasafe($_GET['password2']);
			
			if ($password == $password2) {
				$password = ($password != "") ? md5($password) : "";
				
				$sql = "INSERT INTO `" . DBTABLEPREFIX . "clients` (cat_id, first_name, last_name, title, company, street1, street2, city, state, zip, daytime_phone, nighttime_phone, cell_phone, email_address, website, found_us_through, preffered_client, username, password) VALUES ('" . $cat_id . "','" . $first_name . "', '" . $last_name . "', '" . $title . "', '" . $company . "', '" . $street1 . "', '" . $street2 . "', '" . $city . "', '" . $state . "', '" . $zip . "', '" . $daytime_phone . "', '" . $nighttime_phone . "', '" . $cell_phone . "', '" . $email_address . "', '" . $website . "', '" . $found_us_through . "', '" . $preffered_client . "', '" . $username . "', '" . $password . "')";
				$result = mysql_query($sql);
				$clientID = mysql_insert_id();
				
				$content = ($result) ? "	<span class=\"greenText bold\">Successfully created client!</span>" : "	<span class=\"redText bold\">Failed to create client!!!</span>";
				
				switch(keepsafe($_GET['reprinttable'])) {
					case 1:				
						$finalColumnData = ($actual_showButtons == 1) ? "<a href=\"" . $menuvar['CLIENTS'] . "&action=editclient&id=" . $clientID . "\"><img src=\"themes/" . $clms_config['ftsclms_theme'] . "/icons/check.png\" alt=\"Edit Client Details\" /></a> " . createDeleteLinkWithImage($clientID, $clientID . "_row", "clients", "client") : "";
						
						$tableHTML = "
							<tr class=\"even\" id=\"" . $clientID . "_row\">
								<td>" . $last_name . ", " . $first_name . "</td>
								<td>" . getCatNameByID($cat_id) . "</td>
								<td>" . formatCurrency(0) . "</td>
								<td class=\"center\">" . $finalColumnData . "</td>
							</tr>";
							
						echo $tableHTML;
						break;
					default:
						echo $content;
						break;
				}
			}
			else {
				$content = "<span class=\"redText bold\">The passwords you supplied do not match. Please fix this.</span>";			
				echo $content;
			}	
		}
			
		//================================================
		// Update our client in the database
		//================================================
		elseif ($actual_action == "editClient") {
			$id = keepsafe($_GET['id']);
			$user_id = keeptasafe($_GET['user_id']);
			$title = keeptasafe($_GET['title']);
			$company = keeptasafe($_GET['company']);
			$street1 = keeptasafe($_GET['street1']);
			$street2 = keeptasafe($_GET['street2']);
			$city = keeptasafe($_GET['city']);
			$state = keeptasafe($_GET['state']);
			$zip = keeptasafe($_GET['zip']);
			$daytime_phone = keeptasafe($_GET['daytime_phone']);
			$nighttime_phone = keeptasafe($_GET['nighttime_phone']);
			$cell_phone = keeptasafe($_GET['cell_phone']);
			$website = keeptasafe($_GET['website']);
			$found_us_through = keeptasafe($_GET['found_us_through']);
			$preffered_client = keeptasafe($_GET['preffered_client']);
			
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "clients` (id, user_id, title, company, street1, street2, city, state, zip, daytime_phone, nighttime_phone, cell_phone, website, found_us_through, preffered_client) VALUES ('" . $id . "','" . $user_id . "', '" . $title . "', '" . $company . "', '" . $street1 . "', '" . $street2 . "', '" . $city . "', '" . $state . "', '" . $zip . "', '" . $daytime_phone . "', '" . $nighttime_phone . "', '" . $cell_phone . "', '" . $website . "', '" . $found_us_through . "', '" . $preffered_client . "')";
			$result = mysql_query($sql);
			
			$content = ($result) ? "	<span class=\"greenText bold\">Successfully updated client!</span>" : "	<span class=\"redText bold\">Failed to update client!!!</span>";
			
			echo $content;
		}
			
		//================================================
		// Add our downloads to the database
		//================================================
		elseif ($actual_action == "createDownload") {
			$datetimestamp = time();
			$client_id = keeptasafe($_GET['client_id']);
			$name = keeptasafe($_GET['name']);
			$uplodedFilesName = keeptasafe($_GET['uplodedFilesName']);
			$url = keeptasafe($_GET['url']);
			$serial_number = keeptasafe($_GET['serial_number']);
			
			// If we uploaded a file then use it instead of our URL data
			$url = ($uplodedFilesName != "") ? $uplodedFilesName : $url;
			
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "downloads` (`datetimestamp`, `client_id`, `name`, `url`, `serial_number`) VALUES ('" . $datetimestamp . "', '" . $client_id . "', '" . $name . "', '" . $url . "', '" . $serial_number . "')";
			$result = mysql_query($sql);
			$downloadID = mysql_insert_id();
			
			$content = ($result) ? "	<span class=\"greenText bold\">Successfully created download!</span>" : "	<span class=\"redText bold\">Failed to create download!!!</span>";
			
			switch(keepsafe($_GET['reprinttable'])) {
				case 1:	
					$finalColumnData = ($actual_showButtons == 1) ? createDeleteLinkWithImage($downloadID, $downloadID . "_row", "downloads", "download") : "";
					
					$tableHTML = "
						<tr class=\"even\" id=\"" . $downloadID . "_row\">
							<td><a href=\"" . $url . "\">" . $name . "</a></td>
							<td>" . $serial_number . "</td>
							<td>" . makeDateTime($datetimestamp) . "</td>
							<td class=\"center\">" . $finalColumnData . "</td>
						</tr>";
						
					echo $tableHTML;
					break;
				default:
					echo $content;
					break;
			}
		}
			
		//================================================
		// Add our invoices to the database
		//================================================
		elseif ($actual_action == "createInvoice") {
			$invoiceTotal = 0;
			$datetimestamp = time();
			$client_id = keeptasafe($_GET['client_id']);
			$description = keeptasafe($_GET['description']);
			$discount = keeptasafe($_GET['discount']);
			$note = keeptasafe($_GET['note']);
			
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "invoices` (`datetimestamp`, `client_id`, `description`, `discount`, `note`) VALUES ('" . $datetimestamp . "', '" . $client_id . "', '" . $description . "', '" . $discount . "', '" . $note . "')";
			$result = mysql_query($sql);
			$invoiceID = mysql_insert_id();
			
			foreach ($_GET['products'] as $key => $product_id) {
				$qty = keepsafe($_GET['qty'][$key]);
				
				$sql = "SELECT * FROM `" . DBTABLEPREFIX . "products` WHERE id = '" . $product_id . "' LIMIT 1";
				$result = mysql_query($sql);
				//echo $sql . "<br />";
				
				if ($result && mysql_num_rows($result) > 0) {
					while ($row = mysql_fetch_array($result)) {
						$invoiceTotal += ($row['price'] + $row['profit'] + $row['shipping']) * $qty;
						
						$sql2 = "INSERT INTO `" . DBTABLEPREFIX . "invoices_products` (`invoice_id`, `name`, `price`, `profit`, `qty`, `shipping`) VALUES ('" . $invoiceID . "', '" . $row['name'] . "', '" . $row['price'] . "', '" . $row['profit'] . "', '" . $qty . "', '" . $row['shipping'] . "')";
						$result2 = mysql_query($sql2);
						//echo $sql2 . "<br />";
					}
					mysql_free_result($result);
				}
			}
						
			$content = ($result) ? "	<span class=\"greenText bold\">Successfully created invoice!</span>" : "	<span class=\"redText bold\">Failed to create invoice!!!</span>";
			
			switch(keepsafe($_GET['reprinttable'])) {
				case 1:				
					$finalColumnData = ($actual_showButtons == 1) ? createDeleteLinkWithImage($invoiceID, $invoiceID . "_row", "invoices", "invoice") : "";
					
					$tableHTML = "
						<tr class=\"even\" id=\"" . $invoiceID . "_row\">
							<td>" . makeDateTime($datetimestamp) . "</td>
							<td>" . $description . "</td>
							<td>" . formatCurrency($invoiceTotal) . "</td>
							<td>" . formatCurrency($discount) . "</td>
							<td>" . formatCurrency(0) . "</td>
							<td>" . formatCurrency($invoiceTotal - $discount) . "</td>
							<td>" . printInvoiceStatus(STATUS_INVOICE_AWAITING_PAYMENT) . "</td>
							<td class=\"center\"><a href=\"" . $menuvar['VIEWINVOICE'] . "&id=" . $invoiceID . "\"><img src=\"themes/" . $clms_config['ftsclms_theme'] . "/icons/table_green.png\" alt=\"View Invoice\" /></a> <a href=\"" . $menuvar['EMAILINVOICE'] . "&id=" . $invoiceID . "\"><img src=\"themes/" . $clms_config['ftsclms_theme'] . "/icons/message.png\" alt=\"Email Invoice\" /></a> " . $finalColumnData . "</td>
						</tr>";
						
					echo $tableHTML;
					break;
				default:
					echo $content;
					break;
			}
		}
			
		//================================================
		// Returns the table row HTML for the invoice 
		// products table
		//================================================
		elseif ($actual_action == "returnInvoiceProductTableRowHTML") {
			echo returnInvoiceProductTableRowHTML($actual_id);
		}
			
		//================================================
		// Returns the line total on an invoice
		//================================================
		elseif ($actual_action == "getInvoiceLineTotal") {
			$invoiceTotal = 0;
			
			$sql = "SELECT SUM((price + profit + shipping) * qty) AS invoiceTotal FROM `" . DBTABLEPREFIX . "invoices_products` WHERE id = '" . $actual_id . "' LIMIT 1";
			$result = mysql_query($sql);
				
			// Pull our data
			if ($result && mysql_num_rows($result) > 0) {
				while ($row = mysql_fetch_array($result)) {
					$invoiceTotal = $row['invoiceTotal'];
				}
				mysql_free_result($result);
			}
			
			echo formatCurrency($invoiceTotal);
		}
		
		//================================================
		// Returns the subtotal on an invoice
		//================================================
		elseif ($actual_action == "getInvoiceSubtotal") {
			$invoiceSubtotal = 0;
			
			$sql = "SELECT SUM((price + profit + shipping) * qty) AS invoiceTotal FROM `" . DBTABLEPREFIX . "invoices_products` WHERE invoice_id = '" . $actual_id . "'";
			$result = mysql_query($sql);
				
			// Pull our data
			if ($result && mysql_num_rows($result) > 0) {
				while ($row = mysql_fetch_array($result)) {
					$invoiceSubtotal += $row['invoiceTotal'];
				}
				mysql_free_result($result);
			}
			
			echo formatCurrency($invoiceSubtotal);
		}
			
		//================================================
		// Returns the total due on an invoice
		//================================================
		elseif ($actual_action == "getInvoiceTotalDue") {
			$totalDue = 0;
			
			$sql = "SELECT * FROM `" . DBTABLEPREFIX . "invoices` WHERE id = '" . $actual_id . "' LIMIT 1";
			$result = mysql_query($sql);
				
			// Pull our data
			if ($result && mysql_num_rows($result) > 0) {
				while ($row = mysql_fetch_array($result)) {
					$invoiceTotal = 0;
					
					$sql2 = "SELECT SUM((price + profit + shipping) * qty) AS invoiceTotal FROM `" . DBTABLEPREFIX . "invoices_products` WHERE invoice_id = '" . $row['id'] . "'";
					$result2 = mysql_query($sql2);
					
					if ($result2 && mysql_num_rows($result2) > 0) {
						while ($row2 = mysql_fetch_array($result2)) {
							$invoiceTotal = $row2['invoiceTotal'];
						}
						mysql_free_result($result2);
					}
					
					// Tally up our total
					$totalDue = $invoiceTotal - $row['discount'] - getInvoiceTotalAmountPaid($row['id']);
				}
				mysql_free_result($result);
			}
			
			echo formatCurrency($totalDue);
		}
			
		//================================================
		// Update our invoices payments in the database
		//================================================
		elseif ($actual_action == "createInvoicePayment") {
			$datetimestamp = time();
			$id = keepsafe($_GET['id']);
			$type = keepsafe($_GET['type']);
			$paid = keepsafe($_GET['paid']);
			
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "invoices_payments` (`datetimestamp`, `invoice_id`, `type`, `paid`) VALUES ('" . $datetimestamp . "', '" . $id . "', '" . $type . "', '" . $paid . "')";
			$result = mysql_query($sql);
			$invoicePaymentID = mysql_insert_id();
			
			$content = ($result) ? "	<span class=\"greenText bold\">Successfully created invoice payment!</span>" : "	<span class=\"redText bold\">Failed to create invoice payment!!!</span>";
					
			// Check to see if our invoice is now paid and if so then change its status
			$sql = "UPDATE `" . DBTABLEPREFIX . "invoices` i SET status = '" . STATUS_INVOICE_PAID . "' WHERE coalesce((SELECT SUM((ip.price + ip.profit + ip.shipping ) * ip.qty) - i.discount FROM `" . DBTABLEPREFIX . "invoices_products` ip WHERE ip.invoice_id = i.id), 0) - coalesce((SELECT SUM(ipa.paid) FROM `" . DBTABLEPREFIX . "invoices_payments` ipa WHERE ipa.invoice_id = i.id), 0) <= 0";
			$result = mysql_query($sql);
			
			switch(keepsafe($_GET['reprinttable'])) {
				case 1:				
					$finalColumnData = ($actual_showButtons == 1) ? createInvoicePaymentDeleteLinkWithImage($invoicePaymentID, $invoicePaymentID . "_row", "invoices_payments", "payment", $id) : "";
					
					$tableHTML = "
						<tr class=\"even\" id=\"" . $invoicePaymentID . "_row\">
							<td>" . makeDateTime($datetimestamp) . "</td>
							<td>" . printInvoicePaymentType($type) . "</td>
							<td>" . formatCurrency($paid) . "</td>
							<td class=\"center\">" . $finalColumnData . "</td>
						</tr>";
						
					echo $tableHTML;
					break;
				default:
					echo $content;
					break;
			}
		}
		
		//================================================
		// Returns the HTML for the view invoice page
		//================================================
		elseif ($actual_action == "reprintInvoice") {
			echo printInvoice($actual_id);
		}
			
		//================================================
		// Update our notes in the database
		//================================================
		elseif ($actual_action == "createNote") {
			$datetimestamp = time();
			$client_id = keeptasafe($_GET['client_id']);
			$note = keeptasafe($_GET['note']);
			$urgency = keeptasafe($_GET['urgency']);	
			
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "notes` (`datetimestamp`, `client_id`, `note`, `urgency`) VALUES ('" . $datetimestamp . "', '" . $client_id . "', '" . $note . "', '" . $urgency . "')";
			$result = mysql_query($sql);
			$noteID = mysql_insert_id();
			
			$content = ($result) ? "	<span class=\"greenText bold\">Successfully created note!</span>" : "	<span class=\"redText bold\">Failed to create note!!!</span>";
			
			switch(keepsafe($_GET['reprinttable'])) {
				case 1:
					$rowColor = ($urgency != LOW) ? "redRow" : "greenRow";
					$rowColor = ($urgency != HIGH && $rowColor == "redRow") ? "yellowRow" : $rowColor;
					
					$finalColumnData = ($actual_showButtons == 1) ? createDeleteLinkWithImage($noteID, $noteID . "_row", "notes", "note") : "";
					
					$tableHTML = "
						<tr class=\"" . $rowColor . "\" id=\"" . $noteID . "_row\">
							<td>" . bbcode($note) . "</td>
							<td>" . makeDateTime($datetimestamp) . "</td>
							<td class=\"center\">" . $finalColumnData . "</td>
						</tr>";
						
					echo $tableHTML;
					break;
				default:
					echo $content;
					break;
			}
		}
			
		//================================================
		// Add our orders to the database
		//================================================
		elseif ($actual_action == "createOrder") {
			$orderTotal = 0;
			$datetimestamp = time();
			$client_id = keeptasafe($_GET['client_id']);
			$description = keeptasafe($_GET['description']);
			$discount = keeptasafe($_GET['discount']);
			$note = keeptasafe($_GET['note']);
			
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "orders` (`datetimestamp`, `client_id`, `description`, `discount`, `note`) VALUES ('" . $datetimestamp . "', '" . $client_id . "', '" . $description . "', '" . $discount . "', '" . $note . "')";
			$result = mysql_query($sql);
			$orderID = mysql_insert_id();
			
			foreach ($_GET['products'] as $key => $product_id) {
				$qty = keepsafe($_GET['qty'][$key]);
				
				$sql = "SELECT * FROM `" . DBTABLEPREFIX . "products` WHERE id = '" . $product_id . "' LIMIT 1";
				$result = mysql_query($sql);
				//echo $sql . "<br />";
				
				if ($result && mysql_num_rows($result) > 0) {
					while ($row = mysql_fetch_array($result)) {
						$orderTotal += ($row['price'] + $row['profit'] + $row['shipping']) * $qty;
						
						$sql2 = "INSERT INTO `" . DBTABLEPREFIX . "orders_products` (`order_id`, `name`, `price`, `profit`, `qty`, `shipping`) VALUES ('" . $orderID . "', '" . $row['name'] . "', '" . $row['price'] . "', '" . $row['profit'] . "', '" . $qty . "', '" . $row['shipping'] . "')";
						$result2 = mysql_query($sql2);
						//echo $sql2 . "<br />";
					}
					mysql_free_result($result);
				}
			}
						
			$content = ($result) ? "	<span class=\"greenText bold\">Successfully created order!</span>" : "	<span class=\"redText bold\">Failed to create order!!!</span>";
			
			switch(keepsafe($_GET['reprinttable'])) {
				case 1:				
					$finalColumnData = ($actual_showButtons == 1) ? createDeleteLinkWithImage($orderID, $orderID . "_row", "orders", "order") : "";
					
					$tableHTML = "
						<tr class=\"even\" id=\"" . $orderID . "_row\">
							<td>" . makeDateTime($datetimestamp) . "</td>
							<td>" . $description . "</td>
							<td>" . formatCurrency($orderTotal) . "</td>
							<td>" . formatCurrency($discount) . "</td>
							<td>" . formatCurrency(0) . "</td>
							<td>" . formatCurrency($orderTotal - $discount) . "</td>
							<td>" . printOrderStatus(STATUS_ORDER_AWAITING_PAYMENT) . "</td>
							<td class=\"center\"><a href=\"" . $menuvar['VIEWORDER'] . "&id=" . $orderID . "\"><img src=\"themes/" . $clms_config['ftsclms_theme'] . "/icons/table_green.png\" alt=\"View Order\" /></a> <a href=\"" . $menuvar['EMAILORDER'] . "&id=" . $orderID . "\"><img src=\"themes/" . $clms_config['ftsclms_theme'] . "/icons/message.png\" alt=\"Email Order\" /></a> " . $finalColumnData . "</td>
						</tr>";
						
					echo $tableHTML;
					break;
				default:
					echo $content;
					break;
			}
		}
			
		//================================================
		// Returns the table row HTML for the order 
		// products table
		//================================================
		elseif ($actual_action == "returnOrderProductTableRowHTML") {
			echo returnOrderProductTableRowHTML($actual_id);
		}
			
		//================================================
		// Returns the line total on an order
		//================================================
		elseif ($actual_action == "getOrderLineTotal") {
			$orderTotal = 0;
			
			$sql = "SELECT SUM((price + profit + shipping) * qty) AS orderTotal FROM `" . DBTABLEPREFIX . "orders_products` WHERE id = '" . $actual_id . "' LIMIT 1";
			$result = mysql_query($sql);
				
			// Pull our data
			if ($result && mysql_num_rows($result) > 0) {
				while ($row = mysql_fetch_array($result)) {
					$orderTotal = $row['orderTotal'];
				}
				mysql_free_result($result);
			}
			
			echo formatCurrency($orderTotal);
		}
		
		//================================================
		// Returns the subtotal on an order
		//================================================
		elseif ($actual_action == "getOrderSubtotal") {
			$orderSubtotal = 0;
			
			$sql = "SELECT SUM((price + profit + shipping) * qty) AS orderTotal FROM `" . DBTABLEPREFIX . "orders_products` WHERE order_id = '" . $actual_id . "'";
			$result = mysql_query($sql);
				
			// Pull our data
			if ($result && mysql_num_rows($result) > 0) {
				while ($row = mysql_fetch_array($result)) {
					$orderSubtotal += $row['orderTotal'];
				}
				mysql_free_result($result);
			}
			
			echo formatCurrency($orderSubtotal);
		}
			
		//================================================
		// Returns the total due on an order
		//================================================
		elseif ($actual_action == "getOrderTotalDue") {
			$totalDue = 0;
			
			$sql = "SELECT * FROM `" . DBTABLEPREFIX . "orders` WHERE id = '" . $actual_id . "' LIMIT 1";
			$result = mysql_query($sql);
				
			// Pull our data
			if ($result && mysql_num_rows($result) > 0) {
				while ($row = mysql_fetch_array($result)) {
					$orderTotal = 0;
					
					$sql2 = "SELECT SUM((price + profit + shipping) * qty) AS orderTotal FROM `" . DBTABLEPREFIX . "orders_products` WHERE order_id = '" . $row['id'] . "'";
					$result2 = mysql_query($sql2);
					
					if ($result2 && mysql_num_rows($result2) > 0) {
						while ($row2 = mysql_fetch_array($result2)) {
							$orderTotal = $row2['orderTotal'];
						}
						mysql_free_result($result2);
					}
					
					// Tally up our total
					$totalDue = $orderTotal - $row['discount'] - getOrderTotalAmountPaid($row['id']);
				}
				mysql_free_result($result);
			}
			
			echo formatCurrency($totalDue);
		}
			
		//================================================
		// Update our orders payments in the database
		//================================================
		elseif ($actual_action == "createOrderPayment") {
			$datetimestamp = time();
			$id = keepsafe($_GET['id']);
			$type = keepsafe($_GET['type']);
			$paid = keepsafe($_GET['paid']);
			
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "orders_payments` (`datetimestamp`, `order_id`, `type`, `paid`) VALUES ('" . $datetimestamp . "', '" . $id . "', '" . $type . "', '" . $paid . "')";
			$result = mysql_query($sql);
			$orderPaymentID = mysql_insert_id();
			
			$content = ($result) ? "	<span class=\"greenText bold\">Successfully created order payment!</span>" : "	<span class=\"redText bold\">Failed to create order payment!!!</span>";
					
			// Check to see if our order is now paid and if so then change its status
			$sql = "UPDATE `" . DBTABLEPREFIX . "orders` i SET status = '" . STATUS_ORDER_PAID . "' WHERE coalesce((SELECT SUM((ip.price + ip.profit + ip.shipping ) * ip.qty) - i.discount FROM `" . DBTABLEPREFIX . "orders_products` ip WHERE ip.order_id = i.id), 0) - coalesce((SELECT SUM(ipa.paid) FROM `" . DBTABLEPREFIX . "orders_payments` ipa WHERE ipa.order_id = i.id), 0) <= 0";
			$result = mysql_query($sql);
			
			switch(keepsafe($_GET['reprinttable'])) {
				case 1:				
					$finalColumnData = ($actual_showButtons == 1) ? createOrderPaymentDeleteLinkWithImage($orderPaymentID, $orderPaymentID . "_row", "orders_payments", "payment", $id) : "";
					
					$tableHTML = "
						<tr class=\"even\" id=\"" . $orderPaymentID . "_row\">
							<td>" . makeDateTime($datetimestamp) . "</td>
							<td>" . printOrderPaymentType($type) . "</td>
							<td>" . formatCurrency($paid) . "</td>
							<td class=\"center\">" . $finalColumnData . "</td>
						</tr>";
						
					echo $tableHTML;
					break;
				default:
					echo $content;
					break;
			}
		}
		
		//================================================
		// Returns the HTML for the view order page
		//================================================
		elseif ($actual_action == "reprintOrder") {
			echo printOrder($actual_id);
		}
		
		// Only System Admins can utilize these functions
		if ($_SESSION['user_level'] == SYSTEM_ADMIN) {			
			//================================================
			// Update our products in the database
			//================================================
			if ($actual_action == "createProduct") {
				$name = keeptasafe($_GET['name']);
				$price = keepsafe($_GET['price']);
				$profit = keepsafe($_GET['profit']);
				$shipping = keepsafe($_GET['shipping']);
				
				$sql = "INSERT INTO `" . DBTABLEPREFIX . "products` (`name`, `price`, `profit`, `shipping`) VALUES ('" . $name . "', '" . $price . "', '" . $profit . "', '" . $shipping . "')";
				$result = mysql_query($sql);
				$productID = mysql_insert_id();
				
				$content = ($result) ? "	<span class=\"greenText bold\">Successfully created product!</span>" : "	<span class=\"redText bold\">Failed to create product!!!</span>";
				
				switch(keepsafe($_GET['reprinttable'])) {
					case 1:		
						$finalColumnData = ($actual_showButtons == 1) ? createDeleteLinkWithImage($productID, $productID . "_row", "products", "product") : "";
						
						$tableHTML = "
							<tr class=\"even\" id=\"" . $productID . "_row\">
								<td>" . $name . "</td>
								<td>" . formatCurrency($price) . "</td>
								<td>" . formatCurrency($profit) . "</td>
								<td>" . formatCurrency($shipping) . "</td>
								<td>" . formatCurrency($price + $profit + $shipping) . "</td>
								<td class=\"center\">" . $finalColumnData . "</td>
							</tr>";
							
						echo $tableHTML;
						break;
					default:
						echo $content;
						break;
				}
			}
				
			//================================================
			// Add our users to the database
			//================================================
			elseif ($actual_action == "createUser") {
				$datetimestamp = time();
				$first_name = keeptasafe($_GET['first_name']);
				$last_name = keeptasafe($_GET['last_name']);
				$email_address = keeptasafe($_GET['email_address']);
				$username = keeptasafe($_GET['username']);
				$password = keeptasafe($_GET['password']);
				$password2 = keeptasafe($_GET['password2']);
				$company = keeptasafe($_GET['company']);
				$website = keeptasafe($_GET['website']);
				$userlevel = keeptasafe($_GET['userlevel']);
				
				if ($password == $password2) {
					$password = md5($password);
									
					$sql = "INSERT INTO `" . USERSDBTABLEPREFIX . "users` (`username`, `password`, `email_address`, `user_level`, `first_name`, `last_name`, `website`, `signup_date`) VALUES ('" . $username . "', '" . $password . "', '" . $email_address . "', '" . $userlevel . "', '" . $first_name . "', '" . $last_name . "', '" . $website . "', '" . $datetimestamp . "')";
					$result = mysql_query($sql);
					$userID = mysql_insert_id();
					
					$content = ($result) ? "	<span class=\"greenText bold\">Successfully created user!</span>" : "	<span class=\"redText bold\">Failed to create user!!!</span>";
				}
				else {
					$content = "<span class=\"redText bold\">The passwords you supplied do not match. Please fix this.</span>";			
				}
					
				switch(keepsafe($_GET['reprinttable'])) {
					case 1:				
						$finalColumnData = ($actual_showButtons == 1) ? "<a href=\"" . $menuvar['USERS'] . "&amp;action=edituser&amp;id=" . $userID . "\"><img src=\"themes/" . $clms_config['ftsclms_theme'] . "/icons/check.png\" alt=\"Edit User Details\" /></a> " . createDeleteLinkWithImage($userID, $userID . "_row", "users", "user") : "";
						
						$tableHTML = "
							<tr class=\"even\" id=\"" . $userID . "_row\">
								<td>" . $username . "</td>
								<td>" . $email_address . "</td>
								<td>" . $first_name . " " . $last_name . "</td>
								<td>" . makeDate($datetimestamp) . "</td>
								<td>" . getUserlevelFromID($userID) . "</td>
								<td class=\"center\">" . $finalColumnData . "</td>
							</tr>";
							
						echo $tableHTML;
						break;
					default:
						echo $content;
						break;
				}
			}
				
			//================================================
			// Update our users in the database
			//================================================
			elseif ($actual_action == "editUser") {
				$first_name = keeptasafe($_GET['first_name']);
				$last_name = keeptasafe($_GET['last_name']);
				$email_address = keeptasafe($_GET['email_address']);
				$username = keeptasafe($_GET['username']);
				$password = keeptasafe($_GET['password']);
				$password2 = keeptasafe($_GET['password2']);
				$company = keeptasafe($_GET['company']);
				$website = keeptasafe($_GET['website']);
				$userlevel = keeptasafe($_GET['userlevel']);
				
				if ($password == $password2) {
					$passwordSQL = ($password != "") ? " `password` = '" . md5($password) . "', " : "";
					
					$sql = "UPDATE `" . USERSDBTABLEPREFIX . "users` SET `username` = '" . $username . "'," . $passwordSQL . " `email_address` = '" . $email_address . "', `user_level` = '" . $userlevel . "', `first_name` = '" . $first_name . "', `last_name` = '" . $last_name . "', `website` = '" . $website . "' WHERE `id` = '" . $actual_id . "'";
					$result = mysql_query($sql);
					
					$content = ($result) ? "	<span class=\"greenText bold\">Successfully updated user!</span>" : "	<span class=\"redText bold\">Failed to update user!!!</span>";
				}
				else {
					$content = "<span class=\"redText bold\">The passwords you supplied do not match. Please fix this.</span>";			
				}
					
				echo $content;
			}
				
			//================================================
			// Search our user table
			//================================================
			elseif ($actual_action == "searchUsers") {
				echo printUsersTable($_GET, "");
			}
		}
	}
	
	// Allow all users and clients to run these functions
	if ($_SESSION['user_level'] == SYSTEM_ADMIN || $_SESSION['user_level'] == CLIENT_ADMIN || $_SESSION['user_level'] == USER) {	
		//================================================
		// Send an invoice by email
		//================================================
		if ($actual_action == "emailInvoice") {
			$id = keepsafe($_GET['id']);
			$email_address = keepsafe($_GET['email_address']);
			$message = nl2br($_GET['message']);
			
			$result = emailMessage($email_address, $clms_config['ftsclms_invoice_company_name'] . " Invoice #" . $id, $message . "<br /><br />" . printEmailInvoice($id));
			
			$content = ($result) ? "	<span class=\"greenText bold\">Successfully sent invoice by email!</span>" : "	<span class=\"redText bold\">Failed to send invoice by email!!!</span>";
			
			echo $content;
		}
			
		//================================================
		// Send an order by email
		//================================================
		elseif ($actual_action == "emailOrder") {
			$id = keepsafe($_GET['id']);
			$email_address = keepsafe($_GET['email_address']);
			$message = nl2br($_GET['message']);
			
			$result = emailMessage($email_address, $clms_config['ftsclms_order_company_name'] . " Order #" . $id, $message . "<br /><br />" . printEmailOrder($id));
			
			$content = ($result) ? "	<span class=\"greenText bold\">Successfully sent order by email!</span>" : "	<span class=\"redText bold\">Failed to send order by email!!!</span>";
			
			echo $content;
		}
	}
?>
