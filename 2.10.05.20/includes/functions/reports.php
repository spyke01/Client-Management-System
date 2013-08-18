<?php 
/***************************************************************************
 *                               reports.php
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
 
	//=================================================
	// Print the Accounts Aging Report
	//=================================================
	function printInvoiceAccountsAgingReport() {
		$sql = " SELECT i.client_id,
			    SUM(
					IF(DATEDIFF(CURDATE(), DATE(FROM_UNIXTIME(i.datetimestamp))) BETWEEN 0 AND 30, 
						(
							coalesce((SELECT SUM((ip.price + ip.profit + ip.shipping ) * ip.qty) - i.discount FROM `" . DBTABLEPREFIX . "invoices_products` ip WHERE ip.invoice_id = i.id), 0)
							- 
							coalesce((SELECT SUM(ipa.paid) FROM `" . DBTABLEPREFIX . "invoices_payments` ipa WHERE ipa.invoice_id = i.id), 0)
						),
						0
					)
				) AS pastDueAmount_1,
				SUM(IF(DATEDIFF(CURDATE(), DATE(FROM_UNIXTIME(i.datetimestamp))) BETWEEN 31 AND 60, 
						(
							coalesce((SELECT SUM((ip.price + ip.profit + ip.shipping ) * ip.qty) - i.discount FROM `" . DBTABLEPREFIX . "invoices_products` ip WHERE ip.invoice_id = i.id), 0)
							- 
							coalesce((SELECT SUM(ipa.paid) FROM `" . DBTABLEPREFIX . "invoices_payments` ipa WHERE ipa.invoice_id = i.id), 0)
						),
						0
					)
				) AS pastDueAmount_2,
			    SUM(IF(DATEDIFF(CURDATE(), DATE(FROM_UNIXTIME(i.datetimestamp))) BETWEEN 61 AND 90, 
						(
							coalesce((SELECT SUM((ip.price + ip.profit + ip.shipping ) * ip.qty) - i.discount FROM `" . DBTABLEPREFIX . "invoices_products` ip WHERE ip.invoice_id = i.id), 0)
							- 
							coalesce((SELECT SUM(ipa.paid) FROM `" . DBTABLEPREFIX . "invoices_payments` ipa WHERE ipa.invoice_id = i.id), 0)
						),
						0
					)
				) AS pastDueAmount_3,
			    SUM(IF(DATEDIFF(CURDATE(), DATE(FROM_UNIXTIME(i.datetimestamp))) > 90,
						(
							coalesce((SELECT SUM((ip.price + ip.profit + ip.shipping ) * ip.qty) - i.discount FROM `" . DBTABLEPREFIX . "invoices_products` ip WHERE ip.invoice_id = i.id), 0)
							- 
							coalesce((SELECT SUM(ipa.paid) FROM `" . DBTABLEPREFIX . "invoices_payments` ipa WHERE ipa.invoice_id = i.id), 0)
						),
						0
					)
				) AS pastDueAmount_4 
			FROM `" . DBTABLEPREFIX . "invoices` i WHERE i.status = '" . STATUS_INVOICE_AWAITING_PAYMENT . "' GROUP BY i.client_id";
		$result = mysql_query($sql);
		//echo $sql;
			
		// Create our new table
		$table = new tableClass(1, 1, 1, "contentBox tablesorter", "accountsAgingReportTable");
		
		// Create table title
		$table->addNewRow(array(array("data" => "Accounts Aging", "colspan" => "5")), "", "title1", "thead");
		
		// Create column headers
		$table->addNewRow(
			array(
				array("type" => "th", "data" => "Client"),
				array("type" => "th", "data" => "< 30 Days"),
				array("type" => "th", "data" => "31 - 60 Days"),
				array("type" => "th", "data" => "61 - 90 Days"),
				array("type" => "th", "data" => "> 90 Days")
			), "", "title2", "thead"
		);
							
		// Add our data
		if (!$result || mysql_num_rows($result) == 0) {
			$table->addNewRow(array(array("data" => "There are no unpaid invoices.", "colspan" => "5")), "accountsAgingReportTableDefaultRow", "greenRow");
		}
		else {
			while ($row = mysql_fetch_array($result)) {				
				$table->addNewRow(array(
					array("data" => getClientNameFromID($row['client_id'])),
					array("data" => formatCurrency($row['pastDueAmount_1'])),
					array("data" => formatCurrency($row['pastDueAmount_2'])),
					array("data" => formatCurrency($row['pastDueAmount_3'])),
					array("data" => formatCurrency($row['pastDueAmount_4']))
				), "", "");
			}
			mysql_free_result($result);
		}
		
		// Return the table's HTML
		return $table->returnTableHTML();
	}
	
	//=================================================
	// Returns the JQuery functions used to run the 
	// accounts aging report
	//=================================================
	function returnInvoiceAccountsAgingReportJQuery() {		
		$JQueryReadyScripts = "
				$('#accountsAgingReportTable').tablesorter({ widgets: ['zebra'] });";
		
		return $JQueryReadyScripts;
	}
 
	//=================================================
	// Print the Accounts Aging Report
	//=================================================
	function printOrderAccountsAgingReport() {
		$sql = " SELECT o.client_id,
			    SUM(
					IF(DATEDIFF(CURDATE(), DATE(FROM_UNIXTIME(o.datetimestamp))) BETWEEN 0 AND 30, 
						(
							coalesce((SELECT SUM((op.price + op.profit + op.shipping ) * op.qty) - o.discount FROM `" . DBTABLEPREFIX . "orders_products` op WHERE op.order_id = o.id), 0)
							- 
							coalesce((SELECT SUM(opa.paid) FROM `" . DBTABLEPREFIX . "orders_payments` opa WHERE opa.order_id = o.id), 0)
						),
						0
					)
				) AS pastDueAmount_1,
				SUM(IF(DATEDIFF(CURDATE(), DATE(FROM_UNIXTIME(o.datetimestamp))) BETWEEN 31 AND 60, 
						(
							coalesce((SELECT SUM((op.price + op.profit + op.shipping ) * op.qty) - o.discount FROM `" . DBTABLEPREFIX . "orders_products` op WHERE op.order_id = o.id), 0)
							- 
							coalesce((SELECT SUM(opa.paid) FROM `" . DBTABLEPREFIX . "orders_payments` opa WHERE opa.order_id = o.id), 0)
						),
						0
					)
				) AS pastDueAmount_2,
			    SUM(IF(DATEDIFF(CURDATE(), DATE(FROM_UNIXTIME(o.datetimestamp))) BETWEEN 61 AND 90, 
						(
							coalesce((SELECT SUM((op.price + op.profit + op.shipping ) * op.qty) - o.discount FROM `" . DBTABLEPREFIX . "orders_products` op WHERE op.order_id = o.id), 0)
							- 
							coalesce((SELECT SUM(opa.paid) FROM `" . DBTABLEPREFIX . "orders_payments` opa WHERE opa.order_id = o.id), 0)
						),
						0
					)
				) AS pastDueAmount_3,
			    SUM(IF(DATEDIFF(CURDATE(), DATE(FROM_UNIXTIME(o.datetimestamp))) > 90,
						(
							coalesce((SELECT SUM((op.price + op.profit + op.shipping ) * op.qty) - o.discount FROM `" . DBTABLEPREFIX . "orders_products` op WHERE op.order_id = o.id), 0)
							- 
							coalesce((SELECT SUM(opa.paid) FROM `" . DBTABLEPREFIX . "orders_payments` opa WHERE opa.order_id = o.id), 0)
						),
						0
					)
				) AS pastDueAmount_4 
			FROM `" . DBTABLEPREFIX . "orders` o WHERE o.status = '" . STATUS_ORDER_AWAITING_PAYMENT . "' GROUP BY o.client_id";
		$result = mysql_query($sql);
		//echo $sql;
			
		// Create our new table
		$table = new tableClass(1, 1, 1, "contentBox tablesorter", "accountsAgingReportTable");
		
		// Create table title
		$table->addNewRow(array(array("data" => "Accounts Aging", "colspan" => "5")), "", "title1", "thead");
		
		// Create column headers
		$table->addNewRow(
			array(
				array("type" => "th", "data" => "Client"),
				array("type" => "th", "data" => "< 30 Days"),
				array("type" => "th", "data" => "31 - 60 Days"),
				array("type" => "th", "data" => "61 - 90 Days"),
				array("type" => "th", "data" => "> 90 Days")
			), "", "title2", "thead"
		);
							
		// Add our data
		if (!$result || mysql_num_rows($result) == 0) {
			$table->addNewRow(array(array("data" => "There are no unpaid orders.", "colspan" => "5")), "accountsAgingReportTableDefaultRow", "greenRow");
		}
		else {
			while ($row = mysql_fetch_array($result)) {				
				$table->addNewRow(array(
					array("data" => getClientNameFromID($row['client_id'])),
					array("data" => formatCurrency($row['pastDueAmount_1'])),
					array("data" => formatCurrency($row['pastDueAmount_2'])),
					array("data" => formatCurrency($row['pastDueAmount_3'])),
					array("data" => formatCurrency($row['pastDueAmount_4']))
				), "", "");
			}
			mysql_free_result($result);
		}
		
		// Return the table's HTML
		return $table->returnTableHTML();
	}
	
	//=================================================
	// Returns the JQuery functions used to run the 
	// accounts aging report
	//=================================================
	function returnOrderAccountsAgingReportJQuery() {		
		$JQueryReadyScripts = "
				$('#accountsAgingReportTable').tablesorter({ widgets: ['zebra'] });";
		
		return $JQueryReadyScripts;
	}
	
	//=================================================
	// Print the Client Details Report
	//=================================================
	function printClientDetailsReport() {
		$sql = "SELECT c.*, cat.name FROM `" . DBTABLEPREFIX . "clients` c LEFT JOIN `" . DBTABLEPREFIX . "categories` cat ON c.cat_id = cat.id ORDER BY cat.name, c.last_name, c.first_name";
		$result = mysql_query($sql);
			
		// Create our new table
		$table = new tableClass(1, 1, 1, "contentBox tablesorter", "clientDetailsReportTable");
		
		// Create table title
		$table->addNewRow(array(array("data" => "Client Details", "colspan" => "18")), "", "title1", "thead");
		
		// Create column headers
		$table->addNewRow(
			array(
				array("type" => "th", "data" => "Client Category"),
				array("type" => "th", "data" => "Last Name"),
				array("type" => "th", "data" => "First Name"),
				array("type" => "th", "data" => "Title"),
				array("type" => "th", "data" => "Company"),
				array("type" => "th", "data" => "Address 1"),
				array("type" => "th", "data" => "Address 2"),
				array("type" => "th", "data" => "City"),
				array("type" => "th", "data" => "State"),
				array("type" => "th", "data" => "Zip"),
				array("type" => "th", "data" => "Daytime Phone"),
				array("type" => "th", "data" => "Nighttime Phone"),
				array("type" => "th", "data" => "Cell Phone"),
				array("type" => "th", "data" => "Email Address"),
				array("type" => "th", "data" => "Website"),
				array("type" => "th", "data" => "Username"),
				array("type" => "th", "data" => "Preffered Client"),
				array("type" => "th", "data" => "Found Us Through")
			), "", "title2 noWrap", "thead"
		);
		
		// Add our data
		if (!$result || mysql_num_rows($result) == 0) {
			$table->addNewRow(array(array("data" => "There are no clients in the system.", "colspan" => "18")), "clientDetailsReportTableDefaultRow", "greenRow");
		}
		else {
			while ($row = mysql_fetch_array($result)) {				
				$table->addNewRow(array(
					array("data" => $row['name']),
					array("data" => $row['last_name']),
					array("data" => $row['first_name']),
					array("data" => $row['title']),
					array("data" => $row['company']),
					array("data" => $row['street1']),
					array("data" => $row['street2']),
					array("data" => $row['city']),
					array("data" => $row['state']),
					array("data" => $row['zip']),
					array("data" => $row['daytime_phone']),
					array("data" => $row['nighttime_phone']),
					array("data" => $row['cell_phone']),
					array("data" => $row['email_address']),
					array("data" => $row['website']),
					array("data" => $row['username']),
					array("data" => $row['preffered_client']),
					array("data" => returnYesNo($row['found_us_through']))
				), "", "noWrap");
			}
			mysql_free_result($result);
		}
		
		// Return the table's HTML
		return $table->returnTableHTML();
	}
	
	//=================================================
	// Returns the JQuery functions used to run the 
	// Client Details report
	//=================================================
	function returnClientDetailsReportJQuery() {		
		$JQueryReadyScripts = "
				$('#clientDetailsReportTable').tablesorter({ widgets: ['zebra'] });";
		
		return $JQueryReadyScripts;
	}
	
	//=================================================
	// Print the Invoices Report
	//=================================================
	function printInvoicesReport() {
		$sql = "SELECT i.*, c.first_name, c.last_name FROM `" . DBTABLEPREFIX . "invoices` i LEFT JOIN `" . DBTABLEPREFIX . "clients` c ON i.client_id = c.id ORDER BY i.status, c.last_name, c.first_name";
		$result = mysql_query($sql);
			
		// Create our new table
		$table = new tableClass(1, 1, 1, "contentBox tablesorter", "invoicesReportTable");
		
		// Create table title
		$table->addNewRow(array(array("data" => "Invoices", "colspan" => "9")), "", "title1", "thead");
		
		// Create column headers
		$table->addNewRow(
			array(
				array("type" => "th", "data" => "Date"),
				array("type" => "th", "data" => "Invoice Number"),
				array("type" => "th", "data" => "Status"),
				array("type" => "th", "data" => "Client"),
				array("type" => "th", "data" => "Description"),
				array("type" => "th", "data" => "Note"),
				array("type" => "th", "data" => "Invoice Total"),
				array("type" => "th", "data" => "Total Paid"),
				array("type" => "th", "data" => "Balance")
			), "", "title2 noWrap", "thead"
		);
		
		// Add our data
		if (!$result || mysql_num_rows($result) == 0) {
			$table->addNewRow(array(array("data" => "There are no invoices in the system.", "colspan" => "9")), "invoicesReportTableDefaultRow", "greenRow");
		}
		else {
			while ($row = mysql_fetch_array($result)) {
				$invoiceTotal = getInvoiceProductsTotal($row['id']) - $row['discount'];
				$totalPaid = getInvoiceTotalAmountPaid($row['id']);
				
				$table->addNewRow(array(
					array("data" => makeShortDate($row['datetimestamp'])),
					array("data" => $row['id']),
					array("data" => printInvoiceStatus($row['status'])),
					array("data" => $row['last_name'] . ", " . $row['first_name']),
					array("data" => $row['description']),
					array("data" => bbcode($row['note'])),
					array("data" => formatCurrency($invoiceTotal)),
					array("data" => formatCurrency($totalPaid)),
					array("data" => formatCurrency($invoiceTotal - $totalPaid))
				), "", "noWrap");
			}
			mysql_free_result($result);
		}
		
		// Return the table's HTML
		return $table->returnTableHTML();
	}
	
	//=================================================
	// Returns the JQuery functions used to run the 
	// Invoices report
	//=================================================
	function returnInvoicesReportJQuery() {		
		$JQueryReadyScripts = "
				$('#invoicesReportTable').tablesorter({ widgets: ['zebra'] });";
		
		return $JQueryReadyScripts;
	}
	
	//=================================================
	// Print the Invoice Payments Report
	//=================================================
	function printInvoicePaymentsReport() {
		$sql = "SELECT ipa.*, i.id AS invoiceID, c.first_name, c.last_name FROM `" . DBTABLEPREFIX . "invoices_payments` ipa LEFT JOIN `" . DBTABLEPREFIX . "invoices` i ON ipa.invoice_id = i.id LEFT JOIN `" . DBTABLEPREFIX . "clients` c ON i.client_id = c.id ORDER BY i.status, c.last_name, c.first_name, ipa.datetimestamp";
		$result = mysql_query($sql);
			
		// Create our new table
		$table = new tableClass(1, 1, 1, "contentBox tablesorter", "invoicePaymentsReportTable");
		
		// Create table title
		$table->addNewRow(array(array("data" => "Invoice Payments", "colspan" => "9")), "", "title1", "thead");
		
		// Create column headers
		$table->addNewRow(
			array(
				array("type" => "th", "data" => "Date"),
				array("type" => "th", "data" => "Invoice Number"),
				array("type" => "th", "data" => "Client"),
				array("type" => "th", "data" => "Payment Type"),
				array("type" => "th", "data" => "Amount Paid")
			), "", "title2 noWrap", "thead"
		);
		
		// Add our data
		if (!$result || mysql_num_rows($result) == 0) {
			$table->addNewRow(array(array("data" => "There are no payments in the system.", "colspan" => "9")), "invoicePaymentsReportTableDefaultRow", "greenRow");
		}
		else {
			while ($row = mysql_fetch_array($result)) {
				$table->addNewRow(array(
					array("data" => makeShortDateTime($row['datetimestamp'])),
					array("data" => $row['invoiceID']),
					array("data" => $row['last_name'] . ", " . $row['first_name']),
					array("data" => printInvoicePaymentType($row['type'])),
					array("data" => formatCurrency($row['paid']))
				), "", "noWrap");
			}
			mysql_free_result($result);
		}
		
		// Return the table's HTML
		return $table->returnTableHTML();
	}
	
	//=================================================
	// Returns the JQuery functions used to run the 
	// Invoice Payments report
	//=================================================
	function returnInvoicePaymentsReportJQuery() {		
		$JQueryReadyScripts = "
				$('#invoicePaymentsReportTable').tablesorter({ widgets: ['zebra'] });";
		
		return $JQueryReadyScripts;
	}
	
	//=================================================
	// Print the Serial Numbers Report
	//=================================================
	function printSerialNumbersReport() {
		$sql = "SELECT c.first_name, c.last_name, d.name, d.serial_number, d.datetimestamp FROM `" . DBTABLEPREFIX . "downloads` d LEFT JOIN `" . DBTABLEPREFIX . "clients` c ON d.client_id = c.id ORDER BY c.last_name, c.first_name";
		$result = mysql_query($sql);
			
		// Create our new table
		$table = new tableClass(1, 1, 1, "contentBox tablesorter", "serialNumbersReportTable");
		
		// Create table title
		$table->addNewRow(array(array("data" => "Serial Numbers", "colspan" => "5")), "", "title1", "thead");
		
		// Create column headers
		$table->addNewRow(
			array(
				array("type" => "th", "data" => "Client"),
				array("type" => "th", "data" => "Download Name"),
				array("type" => "th", "data" => "Serial Number"),
				array("type" => "th", "data" => "Uploaded On")
			), "", "title2", "thead"
		);
							
		// Add our data
		if (!$result || mysql_num_rows($result) == 0) {
			$table->addNewRow(array(array("data" => "There are no serial numbers in the system.", "colspan" => "5")), "serialNumbersReportTableDefaultRow", "greenRow");
		}
		else {
			while ($row = mysql_fetch_array($result)) {				
				$table->addNewRow(array(
					array("data" => $row['last_name'] . ", " . $row['first_name']),
					array("data" => $row['name']),
					array("data" => $row['serial_number']),
					array("data" => makeShortDate($row['datetimestamp']))
				), "", "");
			}
			mysql_free_result($result);
		}
		
		// Return the table's HTML
		return $table->returnTableHTML();
	}
	
	//=================================================
	// Returns the JQuery functions used to run the 
	// serial numbers report
	//=================================================
	function returnSerialNumbersReportJQuery() {		
		$JQueryReadyScripts = "
				$('#serialNumbersReportTable').tablesorter({ widgets: ['zebra'] });";
		
		return $JQueryReadyScripts;
	}
	
	//=================================================
	// Print the User Details Report
	//=================================================
	function printUserDetailsReport() {
		$sql = "SELECT * FROM `" . DBTABLEPREFIX . "users` ORDER BY user_level, last_name, first_name";
		$result = mysql_query($sql);
			
		// Create our new table
		$table = new tableClass(1, 1, 1, "contentBox tablesorter", "userDetailsReportTable");
		
		// Create table title
		$table->addNewRow(array(array("data" => "User Details", "colspan" => "18")), "", "title1", "thead");
		
		// Create column headers
		$table->addNewRow(
			array(
				array("type" => "th", "data" => "Access Level"),
				array("type" => "th", "data" => "Last Name"),
				array("type" => "th", "data" => "First Name"),
				array("type" => "th", "data" => "Company"),
				array("type" => "th", "data" => "Email Address"),
				array("type" => "th", "data" => "Website"),
				array("type" => "th", "data" => "Username"),
				array("type" => "th", "data" => "Notes")
			), "", "title2 noWrap", "thead"
		);
		
		// Add our data
		if (!$result || mysql_num_rows($result) == 0) {
			$table->addNewRow(array(array("data" => "There are no users in the system.", "colspan" => "18")), "userDetailsReportTableDefaultRow", "greenRow");
		}
		else {
			while ($row = mysql_fetch_array($result)) {				
				$table->addNewRow(array(
					array("data" => $row['user_level']),
					array("data" => $row['last_name']),
					array("data" => $row['first_name']),
					array("data" => $row['company']),
					array("data" => $row['email_address']),
					array("data" => $row['website']),
					array("data" => $row['username']),
					array("data" => bbcode($row['notes']))
				), "", "noWrap");
			}
			mysql_free_result($result);
		}
		
		// Return the table's HTML
		return $table->returnTableHTML();
	}
	
	//=================================================
	// Returns the JQuery functions used to run the 
	// User Details report
	//=================================================
	function returnUserDetailsReportJQuery() {		
		$JQueryReadyScripts = "
				$('#userDetailsReportTable').tablesorter({ widgets: ['zebra'] });";
		
		return $JQueryReadyScripts;
	}

?>