<?php 
/***************************************************************************
 *                               orders.php
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
	// Prints the status of an order
	//=========================================================
	function printOrderStatus($statusID) {
		switch($statusID) {
			case STATUS_ORDER_PAID:
				return STATUS_ORDER_PAID_STATUS_TXT;
				break;
			case STATUS_ORDER_VOID:
				return STATUS_ORDER_VOID_STATUS_TXT;
				break;
			default:
				return STATUS_ORDER_AWAITING_PAYMENT_STATUS_TXT;
				break;
		}
	}
	
	//=========================================================
	// Prints the type of payment used
	//=========================================================
	function printOrderPaymentType($paymenttype) {
		global $FTS_PAYMENTTYPES;
		
		if ($paymenttype >= count($FTS_PAYMENTTYPES)) { return "N/A"; }
		else { return $FTS_PAYMENTTYPES[$paymenttype]; }
	}
	
	//=========================================================
	// Gets a total of an orders products from a orderid
	//=========================================================
	function getOrderProductsTotal($orderID) {
		$orderTotal = 0;
		
		$sql = "SELECT SUM((price + profit + shipping) * qty) AS productsTotal FROM `" . DBTABLEPREFIX . "orders_products` WHERE order_id = '" . $orderID . "'";
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
	// Gets a total of an order values from a clientid
	//=========================================================
	function getTotalOrderSumByClientID($clientID) {
		$orderTotal = 0;
		
		$sql = "SELECT SUM((op.price + op.profit + op.shipping) * op.qty) - o.discount AS productsTotal FROM `" . DBTABLEPREFIX . "orders_products` op LEFT JOIN `" . DBTABLEPREFIX . "orders` o ON o.id = op.order_id WHERE o.client_id = '" . $clientID . "'";
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
	// Prints the HTML of an order
	//=========================================================
	function printOrder($orderID) {
		global $menuvar, $clms_config;
		$orderTotal = 0;
		
		$sql = "SELECT * FROM `" . DBTABLEPREFIX . "orders` WHERE id = '" . $orderID . "' LIMIT 1";
		$result = mysql_query($sql);
		
		// Check and build our order
		if (!$result || mysql_num_rows($result) == 0) {
			$orderHTML = "Order #" . $orderID . " does not exist!";
		}
		else {
			while ($row = mysql_fetch_array($result)) {
				$sql2 = "SELECT * FROM `" . DBTABLEPREFIX . "orders_products` WHERE order_id = '" . $orderID . "' ORDER BY name ASC";
				$result2 = mysql_query($sql2);
				
				// Create our new table
				$table = new tableClass(1, 1, 1, "contentBox", "orderProductsTable");
				
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
					$table->addNewRow(array(array("data" => "There are no products for this order.", "colspan" => "4")), "", "greenRow");
				}
				else {
					$x = 1;
					while ($row2 = mysql_fetch_array($result2)) {
						$baseProductPrice = $row2['price'] + $row2['profit'] + $row2['shipping'];
						$lineTotal = $baseProductPrice * $row2['qty'];
						$orderTotal += $lineTotal;
						
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
				$amountPaid = getOrderTotalAmountPaid($orderID);
				$totalDue = $orderTotal - $row['discount'] - $amountPaid;
				
				// Return the table's HTML
				$orderHTML = "
					<div id=\"order\">
						<div id=\"companyInfoBlock\">" . returnCompanyInfoBlock() . "</div>
						<div>
							<div id=\"orderDetailsBlock\">
								<span>Order #</span> " . $orderID . "<br />
								<span>Order Date</span> " . makeDate($row['datetimestamp']) . "<br />
								<strong><span>Amount Due</span> <span class=\"" . $row['id'] . "_totalDue noFloat\">" . formatCurrency($totalDue) . "</span></strong>
							</div>
							<div id=\"clientInfoBlock\">" . returnClientInfoBlock($row['client_id']) . "</div>
						</div>
						" . $table->returnTableHTML() . "
						<div id=\"orderTotalsBlockWrapper\">
							<div id=\"orderTotalsBlock\">
									<span>Subtotal</span> <span id=\"" . $row['id'] . "_subtotal\" class=\"noFloat\">" . formatCurrency($orderTotal) . "</span><br />
									<span>Discount</span> <span id=\"" . $row['id'] . "_discount\" class=\"noFloat\">" . formatCurrency($row['discount']) . "</span><br />
									<span>Paid</span> " . formatCurrency($amountPaid) . "<br />
									<strong><span>Amount Due</span> <span class=\"" . $row['id'] . "_totalDue noFloat\">" . formatCurrency($totalDue) . "</span></strong>
							</div>
						</div>
					</div>";				
			}
			mysql_free_result($result);
		}
		
		return $orderHTML;
	}
	
	//=================================================
	// Returns the JQuery functions used to run the 
	// order
	//=================================================
	function returnOrderJQuery($orderID = "", $allowModification = 1) {
		$JQueryReadyScripts = "
				$('#ordersTable').tablesorter({ widgets: ['zebra'], headers: { 7: { sorter: false } } });";
		
		// Only allow modification of rows if we have permission
		if ($allowModification == 1) {			
			$sql = "SELECT id FROM `" . DBTABLEPREFIX . "orders` WHERE id = '" . $orderID . "' LIMIT 1";
			$result = mysql_query($sql);
	
			if ($result && mysql_num_rows($result) > 0) {
				while ($row = mysql_fetch_array($result)) {
					$JQueryReadyScripts .= returnEditInPlaceJQuery($row['id'], "description", "orders");
					$JQueryReadyScripts .= returnEditInPlaceJQuery($row['id'], "discount", "orders", "", 
						"callback: function(value, settings) {
							updateOrderTotalDueAmount(" . $row['id'] . ", '" . progressSpinnerHTML() . "');
						}"
					);
					
					$sql2 = "SELECT id FROM `" . DBTABLEPREFIX . "orders_products` WHERE order_id = '" . $orderID . "' ORDER BY name ASC";
					$result2 = mysql_query($sql2);
					
					if ($result2 && mysql_num_rows($result2) > 0) {
						while ($row2 = mysql_fetch_array($result2)) {
							$JQueryReadyScripts .= returnEditInPlaceJQuery($row2['id'], "qty", "orders_products", "", 
								"callback: function(value, settings) {
									updateOrderLineTotalAmount(" . $row2['id'] . ", '" . progressSpinnerHTML() . "');
									updateOrderTotals(" . $row['id'] . ", '" . progressSpinnerHTML() . "');
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
	// Prints the HTML of an emailable order
	//=========================================================
	function printEmailOrder($orderID) {
		global $menuvar, $clms_config;
		$orderTotal = 0;
		
		$sql = "SELECT * FROM `" . DBTABLEPREFIX . "orders` WHERE id = '" . $orderID . "' LIMIT 1";
		$result = mysql_query($sql);
		
		// Check and build our order
		if (!$result || mysql_num_rows($result) == 0) {
			$orderHTML = "Order #" . $orderID . " does not exist!";
		}
		else {
			while ($row = mysql_fetch_array($result)) {
				$sql2 = "SELECT * FROM `" . DBTABLEPREFIX . "orders_products` WHERE order_id = '" . $orderID . "' ORDER BY name ASC";
				$result2 = mysql_query($sql2);
				
				// Create our new table
				$table = new tableClass(1, 1, 1, "contentBox", "orderProductsTable");
				
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
					$table->addNewRow(array(array("data" => "There are no products for this order.", "colspan" => "4")), "", "greenRow");
				}
				else {
					$x = 1;
					while ($row2 = mysql_fetch_array($result2)) {
						$baseProductPrice = $row2['price'] + $row2['profit'] + $row2['shipping'];
						$lineTotal = $baseProductPrice * $row2['qty'];
						$orderTotal += $lineTotal;
						
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
				$amountPaid = getOrderTotalAmountPaid($orderID);
				$totalDue = $orderTotal - $row['discount'] - $amountPaid;
				
				// Return the table's HTML
				$orderHTML = "
					<div id=\"order\">
						<div id=\"companyInfoBlock\">" . returnCompanyInfoBlock() . "</div>
						<br /><br />
						<div id=\"clientInfoBlock\">" . returnClientInfoBlock($row['client_id']) . "</div>
						<br /><br />
						<div id=\"orderDetailsBlock\">
							<span>Order #</span> " . $orderID . "<br />
							<span>Order Date</span> " . makeDate($row['datetimestamp']) . "<br />
							<strong><span>Amount Due</span> <span class=\"" . $row['id'] . "_totalDue noFloat\">" . formatCurrency($totalDue) . "</span></strong>
						</div>
						<br /><br />
						" . $table->returnTableHTML() . "
						<br /><br />
						<div id=\"orderTotalsBlockWrapper\">
							<div id=\"orderTotalsBlock\">
									<span>Subtotal</span> <span id=\"" . $row['id'] . "_subtotal\" class=\"noFloat\">" . formatCurrency($orderTotal) . "</span><br />
									<span>Discount</span> <span id=\"" . $row['id'] . "_discount\" class=\"noFloat\">" . formatCurrency($row['discount']) . "</span><br />
									<span>Paid</span> " . formatCurrency($amountPaid) . "<br />
									<strong><span>Amount Due</span> <span class=\"" . $row['id'] . "_totalDue noFloat\">" . formatCurrency($totalDue) . "</span></strong>
							</div>
						</div>
					</div>";				
			}
			mysql_free_result($result);
		}
		
		return $orderHTML;
	}
	
	//=========================================================
	// Gets a total amount paid on an order from a orderid
	//=========================================================
	function getOrderTotalAmountPaid($orderID) {
		$sql = "SELECT SUM(paid) AS totalPaid FROM `" . DBTABLEPREFIX . "orders_payments` WHERE order_id='" . $orderID . "'";
		$result = mysql_query($sql);
		
		if ($result && mysql_num_rows($result) > 0) {
			while ($row = mysql_fetch_array($result)) {
				return $row['totalPaid'];
			}	
			mysql_free_result($result);
		}
	}
	
	//=================================================
	// Print the Orders Table
	//=================================================
	function printOrdersTable($clientID = "", $allowModification = 1) {
		global $menuvar, $clms_config;
		
		$extraSQL = ($clientID != "") ? " AND c.id ='" . $clientID . "'" : "";
		
		$sql = "SELECT i.* FROM `" . DBTABLEPREFIX . "orders` i, `" . DBTABLEPREFIX . "clients` c WHERE c.id = i.client_id" . $extraSQL . " ORDER BY c.last_name, i.datetimestamp ASC";
		$result = mysql_query($sql);
			
		// Create our new table
		$table = new tableClass(1, 1, 1, "contentBox tablesorter", "ordersTable");
		
		// Create table title
		$table->addNewRow(array(array("data" => "Orders", "colspan" => "8")), "", "title1", "thead");
		
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
			$table->addNewRow(array(array("data" => "There are no orders for this client.", "colspan" => "8")), "ordersTableDefaultRow", "greenRow");
		}
		else {
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
				$amountPaid = getOrderTotalAmountPaid($row['id']);
				$totalDue = $orderTotal - $row['discount'] - $amountPaid;
				
				// Build our final column array
				$rowDataArray = array(
					array("data" => makeDateTime($row['datetimestamp'])),
					array("data" => "<div id=\"" . $row['id'] . "_description\">" . $row['description'] . "</div>"),
					array("data" => formatCurrency($orderTotal)),
					array("data" => "<div id=\"" . $row['id'] . "_discount\">" . formatCurrency($row['discount']) . "</div>"),
					array("data" => formatCurrency($amountPaid)),
					array("data" => "<div id=\"" . $row['id'] . "_totalDue\">" . formatCurrency($totalDue) . "</div>"),
					array("data" => printOrderStatus($row['status']))
				);
				
				$viewLink = "<a href=\"" . $menuvar['VIEWORDER'] . "&id=" . $row['id'] . "\"><img src=\"themes/" . $clms_config['ftsclms_theme'] . "/icons/table_green.png\" alt=\"View Order\" /></a> <a href=\"" . $menuvar['ORDERPAYMENT'] . "&id=" . $row['id'] . "\"><img src=\"themes/" . $clms_config['ftsclms_theme'] . "/icons/cash.png\" alt=\"Payment History\" /></a> <a href=\"" . $menuvar['EMAILORDER'] . "&id=" . $row['id'] . "\"><img src=\"themes/" . $clms_config['ftsclms_theme'] . "/icons/message.png\" alt=\"Email Order\" /></a> ";
				
				if ($allowModification == 1) array_push($rowDataArray, array("data" => $viewLink . createDeleteLinkWithImage($row['id'], $row['id'] . "_row", "orders", "order"), "class" => "center"));
				else array_push($rowDataArray, array("data" => $viewLink));
				
				$table->addNewRow($rowDataArray, $row['id'] . "_row", "");
			}
			mysql_free_result($result);
		}
		
		// Return the table's HTML
		return $table->returnTableHTML() . "
				<div id=\"ordersTableUpdateNotice\"></div>";
	}
	
	//=================================================
	// Returns the JQuery functions used to run the 
	// client orders table
	//=================================================
	function returnOrdersTableJQuery($clientID = "", $allowModification = 1) {
		$JQueryReadyScripts = "
				$('#ordersTable').tablesorter({ widgets: ['zebra'], headers: { 7: { sorter: false } } });";
		
		// Only allow modification of rows if we have permission
		if ($allowModification == 1) {
			$extraSQL = ($clientID != "") ? " WHERE client_id = '" . $clientID . "'" : "";
			
			$sql = "SELECT id FROM `" . DBTABLEPREFIX . "orders`" . $extraSQL;
			$result = mysql_query($sql);
	
			if ($result && mysql_num_rows($result) > 0) {
				while ($row = mysql_fetch_array($result)) {	
					$JQueryReadyScripts .= returnEditInPlaceJQuery($row['id'], "description", "orders");
					$JQueryReadyScripts .= returnEditInPlaceJQuery($row['id'], "discount", "orders", "", 
						"callback: function(value, settings) {
							updateOrderTotalDueAmount(" . $row['id'] . ", '" . progressSpinnerHTML() . "');
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
	function printNewOrderForm($clientID = "") {
		global $menuvar, $clms_config;

		$clientIDSelect = ($clientID != "") ? "<input type=\"hidden\" name=\"client_id\" value=\"" . $clientID . "\" />" : "<div><label for=\"client_id\">Client <span>- Required</span></label> " . createDropdown("clients", "client_id", "", "") . "</div>";
		
		// Create our new table
		$table = new tableClass(0, 1, 1, "", "addOrderProductsTable");
		
		// Create column headers
		$table->addNewRow(
			array(
				array("type" => "th", "data" => "Item"),
				array("type" => "th", "data" => "Qty"),
				array("type" => "th", "data" => "")
			), "", ""
		);
		
		$content .= "
			<div id=\"newOrderResponse\">
			</div>
			<form name=\"newOrderForm\" id=\"newOrderForm\" action=\"" . $menuvar['DOWNLOADS'] . "\" method=\"post\" class=\"inputForm\" onsubmit=\"return false;\">
					<fieldset>
						<legend>New Order</legend>
						" . $clientIDSelect . "
						<div><label for=\"description\">Description <span>- Required</span></label> <input type=\"text\" name=\"description\" id=\"description\" size=\"60\" class=\"required\" /></div>
						<div>
							<label for=\"products\">Order Products <span>- Required</span></label> 
							<div id=\"products\" class=\"floatLeft\">
								" . $table->returnTableHTML() . "
							</div>
						</div>
						<div><label for=\"discount\">Discount </label> <input type=\"text\" name=\"discount\" id=\"discount\" size=\"60\" /></div>
						<div><label for=\"note\">Note </label> <textarea name=\"note\" id=\"note\" rows=\"10\" cols=\"50\"></textarea></div>
						<div class=\"center\"><input type=\"submit\" class=\"button\" value=\"Create Order\" /></div>
					</fieldset>
				</form>";

		return $content;
	}
	
	//=================================================
	// Returns the JQuery functions used to run the 
	// new order form
	//=================================================
	function returnNewOrderFormJQuery($reprintTable = 0, $allowModification = 1) {
		$extraJQuery = ($reprintTable == 0) ? "
  						// Update the proper div with the returned data
						$('#newOrderResponse').html('" . progressSpinnerHTML() . "');
						$('#newOrderResponse').html(data);
						$('#newOrderResponse').effect('highlight',{},500);" 
						: "
						// Clear the default row
						$('#ordersTableDefaultRow').remove();
  						// Update the table with the new row
						$('#ordersTable > tbody:last').append(data);
						$('#ordersTableUpdateNotice').html('" . tableUpdateNoticeHTML() . "');
						// Show a success message
						$('#newOrderResponse').html('" . progressSpinnerHTML() . "');
						$('#newOrderResponse').html(returnSuccessMessage('order'));";
						
		$JQueryReadyScripts = "
			ordersAddProductRow(" . $allowModification . ");
			var v = jQuery(\"#newOrderForm\").validate({
				errorElement: \"div\",
				errorClass: \"validation-advice\",
				submitHandler: function(form) {			
					jQuery.get('ajax.php?action=createOrder&reprinttable=" . $reprintTable . "&showButtons=" . $allowModification . "', $('#newOrderForm').serialize(), function(data) {
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
	// Returns the table row HTML for the order 
	// products table
	//=================================================
	function returnOrderProductTableRowHTML($rowNumber, $productID = "", $qty = 1) {
		$content = "
			<tr>
				<td>" . createDropdown("productswithprice", "products[" . $rowNumber . "]", $productID, "") . "</td>
				<td><input type=\"text\" name=\"qty[" . $rowNumber . "]\" id=\"qty\" size=\"10\" value=\"" . $qty . "\" /></td>
				<td><a href=\"\" class=\"addProduct\" onclick=\"ordersAddProductRow(this); return false;\"><img src=\"themes/default/icons/add.png\" alt=\"add\" /></a> <a href=\"\" class=\"deleteProduct\" onclick=\"ordersRemoveProductRow(this); return false;\"><img src=\"themes/default/icons/delete.png\" alt=\"delete\" /></a><span class=\"spinner\" style=\"display: none;\">" . progressSpinnerHTML() . "</span></td>
			</tr>";
		
		return $content;
	}
	
	//=================================================
	// Create a form to add new orders
	//
	// Used so that we can display it in many places
	//=================================================
	function printEmailOrderForm($orderID = "") {
		global $menuvar, $clms_config;

		$content .= "
			<div id=\"emailOrderResponse\">
			</div>
			<form name=\"emailOrderForm\" id=\"emailOrderForm\" action=\"" . $menuvar['ORDERS'] . "\" method=\"post\" class=\"inputForm\" onsubmit=\"return false;\">
					<fieldset>
						<legend>Email Order</legend>
						<input type=\"hidden\" name=\"id\" value=\"" . $orderID . "\" />
						<div><label for=\"email_address\">Email Address <span>- Required</span></label> <input type=\"text\" name=\"email_address\" id=\"email_address\" size=\"60\" class=\"required\" value=\"" . getClientEmailAddressFromOrderID($orderID) . "\" /></div>
						<div><label for=\"message\">Message <span>- Required</span></label> <textarea name=\"message\" id=\"message\" rows=\"10\" cols=\"50\" class=\"required\"></textarea></div>
						<div class=\"center\"><input type=\"submit\" class=\"button\" value=\"Email Order\" /></div>
					</fieldset>
				</form>";

		return $content;
	}
	
	//=================================================
	// Returns the JQuery functions used to run the 
	// new order form
	//=================================================
	function returnEmailOrderFormJQuery() {
		$JQueryReadyScripts = "
			var v = jQuery(\"#emailOrderForm\").validate({
				errorElement: \"div\",
				errorClass: \"validation-advice\",
				submitHandler: function(form) {			
					jQuery.get('ajax.php?action=emailOrder', $('#emailOrderForm').serialize(), function(data) {
						// Update the proper div with the returned data
						$('#emailOrderResponse').html('" . progressSpinnerHTML() . "');
						$('#emailOrderResponse').html(data);
						$('#emailOrderResponse').effect('highlight',{},500);
						// Clear the form
						$('#description').val = '';
						$('#note').val = '';
					});
				}
			});";
		
		return $JQueryReadyScripts;
	}
	
	//=================================================
	// Print the Orders Payments Table
	//=================================================
	function printOrderPaymentsTable($orderID = "", $allowModification = 1) {
		global $menuvar, $clms_config;
		
		$sql = "SELECT * FROM `" . DBTABLEPREFIX . "orders_payments` WHERE order_id = '" . $orderID . "' ORDER BY datetimestamp ASC";
		$result = mysql_query($sql);
			
		// Create our new table
		$table = new tableClass(1, 1, 1, "contentBox tablesorter", "orderPaymentsTable");
		
		// Create table title
		$table->addNewRow(array(array("data" => "Order Payment History", "colspan" => "4")), "", "title1", "thead");
		
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
			$table->addNewRow(array(array("data" => "There are no payments for this order.", "colspan" => "4")), "orderPaymentsTableDefaultRow", "greenRow");
		}
		else {
			while ($row = mysql_fetch_array($result)) {
				$rowDataArray = array(
					array("data" => makeDateTime($row['datetimestamp'])),
					array("data" => printOrderPaymentType($row['type'])),
					array("data" => formatCurrency($row['paid']))
				);
				
				if ($allowModification == 1) array_push($rowDataArray, array("data" => createOrderPaymentDeleteLinkWithImage($row['id'], $row['id'] . "_row", "orders_payments", "payment", $orderID), "class" => "center"));
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
	// client orders table
	//=================================================
	function returnOrderPaymentsTableJQuery() {							
		$JQueryReadyScripts = "
				$('#orderPaymentsTable').tablesorter({ widgets: ['zebra'], headers: { 3: { sorter: false } } });";
		
		return $JQueryReadyScripts;
	}
	
	//=================================================
	// Create a form to add new orders
	//
	// Used so that we can display it in many places
	//=================================================
	function printMakeOrderPaymentForm($orderID = "") {
		global $menuvar, $clms_config;

		$content .= "
			<div id=\"makeOrderPaymentResponse\">
			</div>
			<form name=\"makeOrderPaymentForm\" id=\"makeOrderPaymentForm\" action=\"" . $menuvar['ORDERS'] . "\" method=\"post\" class=\"inputForm\" onsubmit=\"return false;\">
					<fieldset>
						<legend>Make a Payment</legend>
						<input type=\"hidden\" name=\"id\" value=\"" . $orderID . "\" />
						<div><label for=\"type\">Payment Type <span>- Required</span></label> " . createDropdown("paymenttypes", "type", "", "") . "</div>
						<div><label for=\"paid\">Amount Paid <span>- Required</span></label> <input type=\"text\" name=\"paid\" id=\"paid\" size=\"60\" class=\"required\" /></div>
						<div class=\"center\"><input type=\"submit\" class=\"button\" value=\"Make Payment\" /></div>
					</fieldset>
				</form>";

		return $content;
	}
	
	//=================================================
	// Returns the JQuery functions used to run the 
	// new order form
	//=================================================
	function returnMakeOrderPaymentFormJQuery($orderID = "", $reprintTable = 0, $allowModification = 1) {
		$extraJQuery = ($reprintTable == 0) ? "
  						// Update the proper div with the returned data
						$('#makeOrderPaymentResponse').html('" . progressSpinnerHTML() . "');
						$('#makeOrderPaymentResponse').html(data);
						$('#makeOrderPaymentResponse').effect('highlight',{},500);" 
						: "
						// Clear the default row
						$('#orderPaymentsTableDefaultRow').remove();
  						// Update the table with the new row
						$('#orderPaymentsTable > tbody:last').append(data);
						// Update the order to show the payment
						jQuery.get('ajax.php?action=reprintOrder&id=" . $orderID . "', function(data) {
							$('#updateMeViewOrder').html(data);
						});
						// Show a success message
						$('#makeOrderPaymentResponse').html('" . progressSpinnerHTML() . "');
						$('#makeOrderPaymentResponse').html(returnSuccessMessage('payment'));";
						
		$JQueryReadyScripts = "
			var v = jQuery(\"#makeOrderPaymentForm\").validate({
				errorElement: \"div\",
				errorClass: \"validation-advice\",
				submitHandler: function(form) {			
					jQuery.get('ajax.php?action=createOrderPayment&reprinttable=" . $reprintTable . "&showButtons=" . $allowModification . "', $('#makeOrderPaymentForm').serialize(), function(data) {
						" . $extraJQuery . "
						// Clear the form
						$('#paid').val = '';
					});
				}
			});";
		
		return $JQueryReadyScripts;
	}

	//=================================================
	// Print the Largest Orders Table
	//=================================================
	function printLargestOrdersTable($orderLimit = 5) {
		global $menuvar, $clms_config;
		
		$sql = "SELECT o.id, o.datetimestamp, o.client_id, SUM((op.price + op.profit + op.shipping) * op.qty) AS total FROM `" . DBTABLEPREFIX . "orders` o LEFT JOIN `" . DBTABLEPREFIX . "orders_products` op ON op.order_id = o.id ORDER BY SUM((op.price + op.profit) * op.qty) DESC LIMIT " . $orderLimit;
		$result = mysql_query($sql);
		
		//echo $sql;
		
		// Create our new table
		$table = new tableClass(1, 1, 1, "contentBox tablesorter", "largestOrdersTable");
		
		// Create table title
		$table->addNewRow(array(array("data" => "Largest Orders", "colspan" => "5")), "", "title1", "thead");
		
		// Create column headers
		$table->addNewRow(
			array(
				array("type" => "th", "data" => "Date and Time"),
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
						array("data" => makeDateTime($row['datetimestamp'])),
						array("data" => getClientNameFromID($row['client_id'])),
						array("data" => formatCurrency($row['total']))
					), $row['id'] . "_row", ""
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
	function returnLargestOrdersTableJQuery() {							
		$JQueryReadyScripts = "
				$('#largestOrdersTable').tablesorter({ widgets: ['zebra'] });";
		
		return $JQueryReadyScripts;
	}

?>