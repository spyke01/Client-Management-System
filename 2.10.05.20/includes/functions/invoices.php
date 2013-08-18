<?php 
/***************************************************************************
 *                               invoices.php
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
	// Prints the status of an invoice
	//=========================================================
	function printInvoiceStatus($statusID) {
		switch($statusID) {
			case STATUS_INVOICE_PAID:
				return STATUS_INVOICE_PAID_STATUS_TXT;
				break;
			case STATUS_INVOICE_VOID:
				return STATUS_INVOICE_VOID_STATUS_TXT;
				break;
			default:
				return STATUS_INVOICE_AWAITING_PAYMENT_STATUS_TXT;
				break;
		}
	}
	
	//=========================================================
	// Prints the type of payment used
	//=========================================================
	function printInvoicePaymentType($paymenttype) {
		global $FTS_PAYMENTTYPES;
		
		if ($paymenttype >= count($FTS_PAYMENTTYPES)) { return "N/A"; }
		else { return $FTS_PAYMENTTYPES[$paymenttype]; }
	}
	
	//=========================================================
	// Gets a total of an invoices products from a invoiceid
	//=========================================================
	function getInvoiceProductsTotal($invoiceID) {
		$invoiceTotal = 0;
		
		$sql = "SELECT SUM((price + profit + shipping) * qty) AS productsTotal FROM `" . DBTABLEPREFIX . "invoices_products` WHERE invoice_id = '" . $invoiceID . "'";
		$result = mysql_query($sql);
		
		if ($result && mysql_num_rows($result) > 0) {
			while ($row = mysql_fetch_array($result)) {
				$invoiceTotal +=  $row['productsTotal'];
			}	
			mysql_free_result($result);
		}
		
		return $invoiceTotal;
	}
	
	//=========================================================
	// Gets a total of an invoice values from a clientid
	//=========================================================
	function getTotalInvoiceSumByClientID($clientID) {
		$orderTotal = 0;
		
		$sql = "SELECT SUM((ip.price + ip.profit + ip.shipping) * ip.qty) - i.discount AS productsTotal FROM `" . DBTABLEPREFIX . "invoices_products` ip LEFT JOIN `" . DBTABLEPREFIX . "invoices` i ON i.id = ip.invoice_id WHERE i.client_id = '" . $clientID . "'";
		$result = mysql_query($sql);
		
		if ($result && mysql_num_rows($result) > 0) {
			while ($row = mysql_fetch_array($result)) {
				$orderTotal +=  $row['productsTotal'];
			}	
			mysql_free_result($result);
		}
		
		return $orderTotal;
	}
	
	//=========================================================
	// Prints the HTML of an invoice
	//=========================================================
	function printInvoice($invoiceID) {
		global $menuvar, $clms_config;
		$invoiceTotal = 0;
		
		$sql = "SELECT * FROM `" . DBTABLEPREFIX . "invoices` WHERE id = '" . $invoiceID . "' LIMIT 1";
		$result = mysql_query($sql);
		
		// Check and build our invoice
		if (!$result || mysql_num_rows($result) == 0) {
			$invoiceHTML = "Invoice #" . $invoiceID . " does not exist!";
		}
		else {
			while ($row = mysql_fetch_array($result)) {
				$sql2 = "SELECT * FROM `" . DBTABLEPREFIX . "invoices_products` WHERE invoice_id = '" . $invoiceID . "' ORDER BY name ASC";
				$result2 = mysql_query($sql2);
				
				// Create our new table
				$table = new tableClass(1, 1, 1, "contentBox", "invoiceProductsTable");
				
				// Create column headers
				$table->addNewRow(
					array(
						array("type" => "th", "data" => "Item"),
						array("type" => "th", "data" => "Price"),
						array("type" => "th", "data" => "Qty"),
						array("type" => "th", "data" => "Total")
					), "", "title1", "thead"
				);
									
				// Add our data
				if (!$result2 || mysql_num_rows($result2) == 0) {
					$table->addNewRow(array(array("data" => "There are no products for this invoice.", "colspan" => "4")), "", "greenRow");
				}
				else {
					$x = 1;
					while ($row2 = mysql_fetch_array($result2)) {
						$baseProductPrice = $row2['price'] + $row2['profit'] + $row2['shipping'];
						$lineTotal = $baseProductPrice * $row2['qty'];
						$invoiceTotal += $lineTotal;
						
						$table->addNewRow(
							array(
							array("data" => $row2['name']),
							array("data" => formatCurrency($baseProductPrice)),
							array("data" => "<div id=\"" . $row2['id'] . "_qty\">" . $row2['qty'] . "</div>"),
							array("data" => "<div id=\"" . $row2['id'] . "_lineTotal\">" . formatCurrency($lineTotal) . "</div>")
							), "", "row" . $x
						);
						
						$x = ($x == 1) ? 2 : 1;
					}
					mysql_free_result($result2);
				}
				
				// Tally up our total
				$amountPaid = getInvoiceTotalAmountPaid($invoiceID);
				$totalDue = $invoiceTotal - $row['discount'] - $amountPaid;
				
				// Return the table's HTML
				$invoiceHTML = "
					<div id=\"invoice\">
						<div id=\"companyInfoBlock\">" . returnCompanyInfoBlock() . "</div>
						<div>
							<div id=\"invoiceDetailsBlock\">
								<span>Invoice #</span> " . $invoiceID . "<br />
								<span>Invoice Date</span> " . makeDate($row['datetimestamp']) . "<br />
								<strong><span>Amount Due</span> <span class=\"" . $row['id'] . "_totalDue noFloat\">" . formatCurrency($totalDue) . "</span></strong>
							</div>
							<div id=\"clientInfoBlock\">" . returnClientInfoBlock($row['client_id']) . "</div>
						</div>
						" . $table->returnTableHTML() . "
						<div id=\"invoiceTotalsBlockWrapper\">
							<div id=\"invoiceTotalsBlock\">
									<span>Subtotal</span> <span id=\"" . $row['id'] . "_subtotal\" class=\"noFloat\">" . formatCurrency($invoiceTotal) . "</span><br />
									<span>Discount</span> <span id=\"" . $row['id'] . "_discount\" class=\"noFloat\">" . formatCurrency($row['discount']) . "</span><br />
									<span>Paid</span> " . formatCurrency($amountPaid) . "<br />
									<strong><span>Amount Due</span> <span class=\"" . $row['id'] . "_totalDue noFloat\">" . formatCurrency($totalDue) . "</span></strong>
							</div>
						</div>
					</div>";				
			}
			mysql_free_result($result);
		}
		
		return $invoiceHTML;
	}
	
	//=================================================
	// Returns the JQuery functions used to run the 
	// invoice
	//=================================================
	function returnInvoiceJQuery($invoiceID = "", $allowModification = 1) {
		$JQueryReadyScripts = "
				$('#invoicesTable').tablesorter({ widgets: ['zebra'], headers: { 7: { sorter: false } } });";
		
		// Only allow modification of rows if we have permission
		if ($allowModification == 1) {			
			$sql = "SELECT id FROM `" . DBTABLEPREFIX . "invoices` WHERE id = '" . $invoiceID . "' LIMIT 1";
			$result = mysql_query($sql);
	
			if ($result && mysql_num_rows($result) > 0) {
				while ($row = mysql_fetch_array($result)) {
					$JQueryReadyScripts .= returnEditInPlaceJQuery($row['id'], "description", "invoices");
					$JQueryReadyScripts .= returnEditInPlaceJQuery($row['id'], "discount", "invoices", "", 
						"callback: function(value, settings) {
							updateInvoiceTotalDueAmount(" . $row['id'] . ", '" . progressSpinnerHTML() . "');
						}"
					);
					
					$sql2 = "SELECT id FROM `" . DBTABLEPREFIX . "invoices_products` WHERE invoice_id = '" . $invoiceID . "' ORDER BY name ASC";
					$result2 = mysql_query($sql2);
					
					if ($result2 && mysql_num_rows($result2) > 0) {
						while ($row2 = mysql_fetch_array($result2)) {
							$JQueryReadyScripts .= returnEditInPlaceJQuery($row2['id'], "qty", "invoices_products", "", 
								"callback: function(value, settings) {
									updateInvoiceLineTotalAmount(" . $row2['id'] . ", '" . progressSpinnerHTML() . "');
									updateInvoiceTotals(" . $row['id'] . ", '" . progressSpinnerHTML() . "');
								}"
							);
						}
						mysql_free_result($result2);
					}
				}
				mysql_free_result($result);
			}
		}
		
		return $JQueryReadyScripts;
	}
	
	//=========================================================
	// Prints the HTML of an emailable invoice
	//=========================================================
	function printEmailInvoice($invoiceID) {
		global $menuvar, $clms_config;
		$invoiceTotal = 0;
		
		$sql = "SELECT * FROM `" . DBTABLEPREFIX . "invoices` WHERE id = '" . $invoiceID . "' LIMIT 1";
		$result = mysql_query($sql);
		
		// Check and build our invoice
		if (!$result || mysql_num_rows($result) == 0) {
			$invoiceHTML = "Invoice #" . $invoiceID . " does not exist!";
		}
		else {
			while ($row = mysql_fetch_array($result)) {
				$sql2 = "SELECT * FROM `" . DBTABLEPREFIX . "invoices_products` WHERE invoice_id = '" . $invoiceID . "' ORDER BY name ASC";
				$result2 = mysql_query($sql2);
				
				// Create our new table
				$table = new tableClass(1, 1, 1, "contentBox", "invoiceProductsTable");
				
				// Create column headers
				$table->addNewRow(
					array(
						array("type" => "th", "data" => "Item"),
						array("type" => "th", "data" => "Price"),
						array("type" => "th", "data" => "Qty"),
						array("type" => "th", "data" => "Total")
					), "", "title1", "thead"
				);
									
				// Add our data
				if (!$result2 || mysql_num_rows($result2) == 0) {
					$table->addNewRow(array(array("data" => "There are no products for this invoice.", "colspan" => "4")), "", "greenRow");
				}
				else {
					$x = 1;
					while ($row2 = mysql_fetch_array($result2)) {
						$baseProductPrice = $row2['price'] + $row2['profit'] + $row2['shipping'];
						$lineTotal = $baseProductPrice * $row2['qty'];
						$invoiceTotal += $lineTotal;
						
						$table->addNewRow(
							array(
							array("data" => $row2['name']),
							array("data" => formatCurrency($baseProductPrice)),
							array("data" => "<div id=\"" . $row2['id'] . "_qty\">" . $row2['qty'] . "</div>"),
							array("data" => "<div id=\"" . $row2['id'] . "_lineTotal\">" . formatCurrency($lineTotal) . "</div>")
							), "", "row" . $x
						);
						
						$x = ($x == 1) ? 2 : 1;
					}
					mysql_free_result($result2);
				}
				
				// Tally up our total
				$amountPaid = getInvoiceTotalAmountPaid($invoiceID);
				$totalDue = $invoiceTotal - $row['discount'] - $amountPaid;
				
				// Return the table's HTML
				$invoiceHTML = "
					<div id=\"invoice\">
						<div id=\"companyInfoBlock\">" . returnCompanyInfoBlock() . "</div>
						<br /><br />
						<div id=\"clientInfoBlock\">" . returnClientInfoBlock($row['client_id']) . "</div>
						<br /><br />
						<div id=\"invoiceDetailsBlock\">
							<span>Invoice #</span> " . $invoiceID . "<br />
							<span>Invoice Date</span> " . makeDate($row['datetimestamp']) . "<br />
							<strong><span>Amount Due</span> <span class=\"" . $row['id'] . "_totalDue noFloat\">" . formatCurrency($totalDue) . "</span></strong>
						</div>
						<br /><br />
						" . $table->returnTableHTML() . "
						<br /><br />
						<div id=\"invoiceTotalsBlockWrapper\">
							<div id=\"invoiceTotalsBlock\">
									<span>Subtotal</span> <span id=\"" . $row['id'] . "_subtotal\" class=\"noFloat\">" . formatCurrency($invoiceTotal) . "</span><br />
									<span>Discount</span> <span id=\"" . $row['id'] . "_discount\" class=\"noFloat\">" . formatCurrency($row['discount']) . "</span><br />
									<span>Paid</span> " . formatCurrency($amountPaid) . "<br />
									<strong><span>Amount Due</span> <span class=\"" . $row['id'] . "_totalDue noFloat\">" . formatCurrency($totalDue) . "</span></strong>
							</div>
						</div>
					</div>";				
			}
			mysql_free_result($result);
		}
		
		return $invoiceHTML;
	}
	
	//=========================================================
	// Gets a total amount paid on an invoice from a invoiceid
	//=========================================================
	function getInvoiceTotalAmountPaid($invoiceID) {
		$sql = "SELECT SUM(paid) AS totalPaid FROM `" . DBTABLEPREFIX . "invoices_payments` WHERE invoice_id='" . $invoiceID . "'";
		$result = mysql_query($sql);
		
		if ($result && mysql_num_rows($result) > 0) {
			while ($row = mysql_fetch_array($result)) {
				return $row['totalPaid'];
			}	
			mysql_free_result($result);
		}
	}
	
	//=================================================
	// Print the Invoices Table
	//=================================================
	function printInvoicesTable($clientID = "", $allowModification = 1) {
		global $menuvar, $clms_config;
		
		$extraSQL = ($clientID != "") ? " AND c.id ='" . $clientID . "'" : "";
		
		$sql = "SELECT i.* FROM `" . DBTABLEPREFIX . "invoices` i, `" . DBTABLEPREFIX . "clients` c WHERE c.id = i.client_id" . $extraSQL . " ORDER BY c.last_name, i.datetimestamp ASC";
		$result = mysql_query($sql);
			
		// Create our new table
		$table = new tableClass(1, 1, 1, "contentBox tablesorter", "invoicesTable");
		
		// Create table title
		$table->addNewRow(array(array("data" => "Invoices", "colspan" => "8")), "", "title1", "thead");
		
		// Create column headers
		$table->addNewRow(
			array(
				array("type" => "th", "data" => "Date"),
				array("type" => "th", "data" => "Description"),
				array("type" => "th", "data" => "Total Cost"),
				array("type" => "th", "data" => "Discount"),
				array("type" => "th", "data" => "Total Paid"),
				array("type" => "th", "data" => "Total Left"),
				array("type" => "th", "data" => "Status"),
				array("type" => "th", "data" => "")
			), "", "title2", "thead"
		);
							
		// Add our data
		if (!$result || mysql_num_rows($result) == 0) {
			$table->addNewRow(array(array("data" => "There are no invoices for this client.", "colspan" => "8")), "invoicesTableDefaultRow", "greenRow");
		}
		else {
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
				$amountPaid = getInvoiceTotalAmountPaid($row['id']);
				$totalDue = $invoiceTotal - $row['discount'] - $amountPaid;
				
				// Build our final column array
				$rowDataArray = array(
					array("data" => makeDateTime($row['datetimestamp'])),
					array("data" => "<div id=\"" . $row['id'] . "_description\">" . $row['description'] . "</div>"),
					array("data" => formatCurrency($invoiceTotal)),
					array("data" => "<div id=\"" . $row['id'] . "_discount\">" . formatCurrency($row['discount']) . "</div>"),
					array("data" => formatCurrency($amountPaid)),
					array("data" => "<div id=\"" . $row['id'] . "_totalDue\">" . formatCurrency($totalDue) . "</div>"),
					array("data" => printInvoiceStatus($row['status']))
				);
				
				$viewLink = "<a href=\"" . $menuvar['VIEWINVOICE'] . "&id=" . $row['id'] . "\"><img src=\"themes/" . $clms_config['ftsclms_theme'] . "/icons/table_green.png\" alt=\"View Invoice\" /></a> <a href=\"" . $menuvar['INVOICEPAYMENT'] . "&id=" . $row['id'] . "\"><img src=\"themes/" . $clms_config['ftsclms_theme'] . "/icons/cash.png\" alt=\"Payment History\" /></a> <a href=\"" . $menuvar['EMAILINVOICE'] . "&id=" . $row['id'] . "\"><img src=\"themes/" . $clms_config['ftsclms_theme'] . "/icons/message.png\" alt=\"Email Invoice\" /></a> ";
				
				if ($allowModification == 1) array_push($rowDataArray, array("data" => $viewLink . createDeleteLinkWithImage($row['id'], $row['id'] . "_row", "invoices", "invoice"), "class" => "center"));
				else array_push($rowDataArray, array("data" => $viewLink));
				
				$table->addNewRow($rowDataArray, $row['id'] . "_row", "");
			}
			mysql_free_result($result);
		}
		
		// Return the table's HTML
		return $table->returnTableHTML() . "
				<div id=\"invoicesTableUpdateNotice\"></div>";
	}
	
	//=================================================
	// Returns the JQuery functions used to run the 
	// client invoices table
	//=================================================
	function returnInvoicesTableJQuery($clientID = "", $allowModification = 1) {
		$JQueryReadyScripts = "
				$('#invoicesTable').tablesorter({ widgets: ['zebra'], headers: { 7: { sorter: false } } });";
		
		// Only allow modification of rows if we have permission
		if ($allowModification == 1) {
			$extraSQL = ($clientID != "") ? " WHERE client_id = '" . $clientID . "'" : "";
			
			$sql = "SELECT id FROM `" . DBTABLEPREFIX . "invoices`" . $extraSQL;
			$result = mysql_query($sql);
	
			if ($result && mysql_num_rows($result) > 0) {
				while ($row = mysql_fetch_array($result)) {	
					$JQueryReadyScripts .= returnEditInPlaceJQuery($row['id'], "description", "invoices");
					$JQueryReadyScripts .= returnEditInPlaceJQuery($row['id'], "discount", "invoices", "", 
						"callback: function(value, settings) {
							updateInvoiceTotalDueAmount(" . $row['id'] . ", '" . progressSpinnerHTML() . "');
						}"
					);
				}
				mysql_free_result($result);
			}
		}
		
		return $JQueryReadyScripts;
	}
	
	//=================================================
	// Create a form to add new orders
	//
	// Used so that we can display it in many places
	//=================================================
	function printNewInvoiceForm($clientID = "") {
		global $menuvar, $clms_config;

		$clientIDSelect = ($clientID != "") ? "<input type=\"hidden\" name=\"client_id\" value=\"" . $clientID . "\" />" : "<div><label for=\"client_id\">Client <span>- Required</span></label> " . createDropdown("clients", "client_id", "", "") . "</div>";
		
		// Create our new table
		$table = new tableClass(0, 1, 1, "", "addInvoiceProductsTable");
		
		// Create column headers
		$table->addNewRow(
			array(
				array("type" => "th", "data" => "Item"),
				array("type" => "th", "data" => "Qty"),
				array("type" => "th", "data" => "")
			), "", ""
		);
		
		$content .= "
			<div id=\"newInvoiceResponse\">
			</div>
			<form name=\"newInvoiceForm\" id=\"newInvoiceForm\" action=\"" . $menuvar['DOWNLOADS'] . "\" method=\"post\" class=\"inputForm\" onsubmit=\"return false;\">
					<fieldset>
						<legend>New Invoice</legend>
						" . $clientIDSelect . "
						<div><label for=\"description\">Description <span>- Required</span></label> <input type=\"text\" name=\"description\" id=\"description\" size=\"60\" class=\"required\" /></div>
						<div>
							<label for=\"products\">Invoice Products <span>- Required</span></label> 
							<div id=\"products\" class=\"floatLeft\">
								" . $table->returnTableHTML() . "
							</div>
						</div>
						<div><label for=\"discount\">Discount </label> <input type=\"text\" name=\"discount\" id=\"discount\" size=\"60\" /></div>
						<div><label for=\"note\">Note </label> <textarea name=\"note\" id=\"note\" rows=\"10\" cols=\"50\"></textarea></div>
						<div class=\"center\"><input type=\"submit\" class=\"button\" value=\"Create Invoice\" /></div>
					</fieldset>
				</form>";

		return $content;
	}
	
	//=================================================
	// Returns the JQuery functions used to run the 
	// new invoice form
	//=================================================
	function returnNewInvoiceFormJQuery($reprintTable = 0, $allowModification = 1) {
		$extraJQuery = ($reprintTable == 0) ? "
  						// Update the proper div with the returned data
						$('#newInvoiceResponse').html('" . progressSpinnerHTML() . "');
						$('#newInvoiceResponse').html(data);
						$('#newInvoiceResponse').effect('highlight',{},500);" 
						: "
						// Clear the default row
						$('#invoicesTableDefaultRow').remove();
  						// Update the table with the new row
						$('#invoicesTable > tbody:last').append(data);
						$('#invoicesTableUpdateNotice').html('" . tableUpdateNoticeHTML() . "');
						// Show a success message
						$('#newInvoiceResponse').html('" . progressSpinnerHTML() . "');
						$('#newInvoiceResponse').html(returnSuccessMessage('invoice'));";
						
		$JQueryReadyScripts = "
			invoicesAddProductRow(" . $allowModification . ");
			var v = jQuery(\"#newInvoiceForm\").validate({
				errorElement: \"div\",
				errorClass: \"validation-advice\",
				submitHandler: function(form) {			
					jQuery.get('ajax.php?action=createInvoice&reprinttable=" . $reprintTable . "&showButtons=" . $allowModification . "', $('#newInvoiceForm').serialize(), function(data) {
						" . $extraJQuery . "
						// Clear the form
						$('#description').val = '';
						$('#note').val = '';
					});
				}
			});";
		
		return $JQueryReadyScripts;
	}
	
	//=================================================
	// Returns the table row HTML for the invoice 
	// products table
	//=================================================
	function returnInvoiceProductTableRowHTML($rowNumber, $productID = "", $qty = 1) {
		$content = "
			<tr>
				<td>" . createDropdown("productswithprice", "products[" . $rowNumber . "]", $productID, "") . "</td>
				<td><input type=\"text\" name=\"qty[" . $rowNumber . "]\" id=\"qty\" size=\"10\" value=\"" . $qty . "\" /></td>
				<td><a href=\"\" class=\"addProduct\" onclick=\"invoicesAddProductRow(this); return false;\"><img src=\"themes/default/icons/add.png\" alt=\"add\" /></a> <a href=\"\" class=\"deleteProduct\" onclick=\"invoicesRemoveProductRow(this); return false;\"><img src=\"themes/default/icons/delete.png\" alt=\"delete\" /></a><span class=\"spinner\" style=\"display: none;\">" . progressSpinnerHTML() . "</span></td>
			</tr>";
		
		return $content;
	}
	
	//=================================================
	// Create a form to add new orders
	//
	// Used so that we can display it in many places
	//=================================================
	function printEmailInvoiceForm($invoiceID = "") {
		global $menuvar, $clms_config;

		$content .= "
			<div id=\"emailInvoiceResponse\">
			</div>
			<form name=\"emailInvoiceForm\" id=\"emailInvoiceForm\" action=\"" . $menuvar['INVOICES'] . "\" method=\"post\" class=\"inputForm\" onsubmit=\"return false;\">
					<fieldset>
						<legend>Email Invoice</legend>
						<input type=\"hidden\" name=\"id\" value=\"" . $invoiceID . "\" />
						<div><label for=\"email_address\">Email Address <span>- Required</span></label> <input type=\"text\" name=\"email_address\" id=\"email_address\" size=\"60\" class=\"required\" value=\"" . getClientEmailAddressFromInvoiceID($invoiceID) . "\" /></div>
						<div><label for=\"message\">Message <span>- Required</span></label> <textarea name=\"message\" id=\"message\" rows=\"10\" cols=\"50\" class=\"required\"></textarea></div>
						<div class=\"center\"><input type=\"submit\" class=\"button\" value=\"Email Invoice\" /></div>
					</fieldset>
				</form>";

		return $content;
	}
	
	//=================================================
	// Returns the JQuery functions used to run the 
	// new invoice form
	//=================================================
	function returnEmailInvoiceFormJQuery() {
		$JQueryReadyScripts = "
			var v = jQuery(\"#emailInvoiceForm\").validate({
				errorElement: \"div\",
				errorClass: \"validation-advice\",
				submitHandler: function(form) {			
					jQuery.get('ajax.php?action=emailInvoice', $('#emailInvoiceForm').serialize(), function(data) {
						// Update the proper div with the returned data
						$('#emailInvoiceResponse').html('" . progressSpinnerHTML() . "');
						$('#emailInvoiceResponse').html(data);
						$('#emailInvoiceResponse').effect('highlight',{},500);
						// Clear the form
						$('#description').val = '';
						$('#note').val = '';
					});
				}
			});";
		
		return $JQueryReadyScripts;
	}
	
	//=================================================
	// Print the Invoices Payments Table
	//=================================================
	function printInvoicePaymentsTable($invoiceID = "", $allowModification = 1) {
		global $menuvar, $clms_config;
		
		$sql = "SELECT * FROM `" . DBTABLEPREFIX . "invoices_payments` WHERE invoice_id = '" . $invoiceID . "' ORDER BY datetimestamp ASC";
		$result = mysql_query($sql);
			
		// Create our new table
		$table = new tableClass(1, 1, 1, "contentBox tablesorter", "invoicePaymentsTable");
		
		// Create table title
		$table->addNewRow(array(array("data" => "Invoice Payment History", "colspan" => "4")), "", "title1", "thead");
		
		// Create column headers
		$table->addNewRow(
			array(
				array("type" => "th", "data" => "Date"),
				array("type" => "th", "data" => "Payment Type"),
				array("type" => "th", "data" => "Total Paid"),
				array("type" => "th", "data" => "")
			), "", "title2", "thead"
		);
							
		// Add our data
		if (!$result || mysql_num_rows($result) == 0) {
			$table->addNewRow(array(array("data" => "There are no payments for this invoice.", "colspan" => "4")), "invoicePaymentsTableDefaultRow", "greenRow");
		}
		else {
			while ($row = mysql_fetch_array($result)) {
				$rowDataArray = array(
					array("data" => makeDateTime($row['datetimestamp'])),
					array("data" => printInvoicePaymentType($row['type'])),
					array("data" => formatCurrency($row['paid']))
				);
				
				if ($allowModification == 1) array_push($rowDataArray, array("data" => createInvoicePaymentDeleteLinkWithImage($row['id'], $row['id'] . "_row", "invoices_payments", "payment", $invoiceID), "class" => "center"));
				else array_push($rowDataArray, array("data" => ""));
				
				$table->addNewRow($rowDataArray, $row['id'] . "_row", "");
			}
			mysql_free_result($result);
		}
		
		// Return the table's HTML
		return $table->returnTableHTML();
	}
	
	//=================================================
	// Returns the JQuery functions used to run the 
	// client invoices table
	//=================================================
	function returnInvoicePaymentsTableJQuery() {							
		$JQueryReadyScripts = "
				$('#invoicePaymentsTable').tablesorter({ widgets: ['zebra'], headers: { 3: { sorter: false } } });";
		
		return $JQueryReadyScripts;
	}
	
	//=================================================
	// Create a form to add new orders
	//
	// Used so that we can display it in many places
	//=================================================
	function printMakeInvoicePaymentForm($invoiceID = "") {
		global $menuvar, $clms_config;

		$content .= "
			<div id=\"makeInvoicePaymentResponse\">
			</div>
			<form name=\"makeInvoicePaymentForm\" id=\"makeInvoicePaymentForm\" action=\"" . $menuvar['INVOICES'] . "\" method=\"post\" class=\"inputForm\" onsubmit=\"return false;\">
					<fieldset>
						<legend>Make a Payment</legend>
						<input type=\"hidden\" name=\"id\" value=\"" . $invoiceID . "\" />
						<div><label for=\"type\">Payment Type <span>- Required</span></label> " . createDropdown("paymenttypes", "type", "", "") . "</div>
						<div><label for=\"paid\">Amount Paid <span>- Required</span></label> <input type=\"text\" name=\"paid\" id=\"paid\" size=\"60\" class=\"required\" /></div>
						<div class=\"center\"><input type=\"submit\" class=\"button\" value=\"Make Payment\" /></div>
					</fieldset>
				</form>";

		return $content;
	}
	
	//=================================================
	// Returns the JQuery functions used to run the 
	// new invoice form
	//=================================================
	function returnMakeInvoicePaymentFormJQuery($invoiceID = "", $reprintTable = 0, $allowModification = 1) {
		$extraJQuery = ($reprintTable == 0) ? "
  						// Update the proper div with the returned data
						$('#makeInvoicePaymentResponse').html('" . progressSpinnerHTML() . "');
						$('#makeInvoicePaymentResponse').html(data);
						$('#makeInvoicePaymentResponse').effect('highlight',{},500);" 
						: "
						// Clear the default row
						$('#invoicePaymentsTableDefaultRow').remove();
  						// Update the table with the new row
						$('#invoicePaymentsTable > tbody:last').append(data);
						// Update the invoice to show the payment
						jQuery.get('ajax.php?action=reprintInvoice&id=" . $invoiceID . "', function(data) {
							$('#updateMeViewInvoice').html(data);
						});
						// Show a success message
						$('#makeInvoicePaymentResponse').html('" . progressSpinnerHTML() . "');
						$('#makeInvoicePaymentResponse').html(returnSuccessMessage('payment'));";
						
		$JQueryReadyScripts = "
			var v = jQuery(\"#makeInvoicePaymentForm\").validate({
				errorElement: \"div\",
				errorClass: \"validation-advice\",
				submitHandler: function(form) {			
					jQuery.get('ajax.php?action=createInvoicePayment&reprinttable=" . $reprintTable . "&showButtons=" . $allowModification . "', $('#makeInvoicePaymentForm').serialize(), function(data) {
						" . $extraJQuery . "
						// Clear the form
						$('#paid').val = '';
					});
				}
			});";
		
		return $JQueryReadyScripts;
	}

?>