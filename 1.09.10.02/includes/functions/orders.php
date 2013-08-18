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
 *
 * This program is licensed under the Fast Track Sites Program license 
 * located inside the license.txt file included with this program. This is a 
 * legally binding license, and is protected by all applicable laws, by 
 * editing this page you fall subject to these licensing terms.
 *
 ***************************************************************************/
 	//=================================================
	// Returns Total Amount of all Orders for a Client
	//=================================================
	function getTotalOrderSumByClientID($clientID) {
		global $DBTABLEPREFIX, $menuvar, $clms_config;
		$total_order_value = 0;
		
		$sql = "SELECT sum(orders_total) AS total_ordered FROM `" . $DBTABLEPREFIX . "orders` WHERE orders_client_id = '" . $clientID . "'";
		$result = mysql_query($sql);
				
		if ($result && mysql_num_rows($result) > 0) {
			while ($row = mysql_fetch_array($result)) {	
				$total_order_value = $row['total_ordered'];
			}
			mysql_free_result($result);
		}
		
		return $total_order_value;
	}
	
 	//=================================================
	// Print the Orders Table
	//=================================================
	function printOrdersTable($clientID = "") {
		global $DBTABLEPREFIX, $menuvar, $clms_config;
		
		$extraSQL = ($clientID != "") ? " AND c.clients_id ='" . $clientID . "'" : "";
		
		$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "orders` o, `" . $DBTABLEPREFIX . "clients` c WHERE c.clients_id = o.orders_client_id" . $extraSQL . " ORDER BY orders_id ASC";
		$result = mysql_query($sql);
			
		$x = 1; //reset the variable we use for our row colors	
			
		$content = "
						<table class=\"contentBox\" cellpadding=\"1\" cellspacing=\"1\" width=\"100%\">
							<tr class=\"title1\">
								<td colspan=\"8\">Orders</td>
							</tr>
							<tr class=\"title2\">
								<td><strong>Order Number</strong></td><td><strong>Client</strong></td><td><strong>Date Ordered</strong></td><td><strong>Date Shipped</strong></td><td><strong>Total</strong></td><td><strong>Tracking No</strong></td><td><strong>Shipped By</strong></td><td></td>
							</tr>";
							
		$orderids = array();
		if (!$result || mysql_num_rows($result) == 0) { // No orders yet!
			$content .= "
							<tr class=\"greenRow\">
								<td colspan=\"8\">There are no orders in the database.</td>
							</tr>";	
		}
		else {	 // Print all our orders								
			while ($row = mysql_fetch_array($result)) {
				$number = (trim($row['orders_number']) == "") ? "N/A" : $row['orders_number'];
				$date_ordered = (trim($row['orders_date_ordered']) == "") ? "N/A" : makeDateTime($row['orders_date_ordered']);
				$date_shipped = (trim($row['orders_date_shipped']) == "") ? "N/A" : makeDateTime($row['orders_date_shipped']);
				$total = (trim($row['orders_total']) == "") ? "N/A" : formatCurrency($row['orders_total']);
				$tracking_no = (trim($row['orders_tracking_no']) == "") ? "N/A" : $row['orders_tracking_no'];
				$shipped_by = (trim($row['orders_shipped_by']) == "") ? "N/A" : $row['orders_shipped_by'];
				
				$content .=	"					
							<tr id=\"" . $row['orders_id'] . "_row\" class=\"row" . $x . "\">
								<td><div id=\"" . $row['orders_id'] . "_number\">" . $number . "</div></td>
								<td>" . $row['clients_first_name'] . " " . $row['clients_last_name'] . "</td>
								<td><div id=\"" . $row['orders_id'] . "_date_ordered\">" . $date_ordered . "</div></td>
								<td><div id=\"" . $row['orders_id'] . "_date_shipped\">" . $date_shipped . "</div></td>
								<td><div id=\"" . $row['orders_id'] . "_total\">" . $total . "</div></td>
								<td><div id=\"" . $row['orders_id'] . "_tracking_no\">" . $tracking_no . "</div></td>
								<td><div id=\"" . $row['orders_id'] . "_shipped_by\">" . $shipped_by . "</div></td>
								<td><span class=\"center\"><a style=\"cursor: pointer; cursor: hand;\" onclick=\"ajaxDeleteNotifier('" . $row['orders_id'] . "ordersSpinner', 'ajax.php?action=deleteitem&table=orders&id=" . $row['orders_id'] . "', 'order', '" . $row['orders_id'] . "_row');\"><img src=\"themes/" . $clms_config['ftsclms_theme'] . "/icons/delete.png\" alt=\"Delete Order\" /></a><span id=\"" . $row['orders_id'] . "ordersSpinner\" style=\"display: none;\"><img src=\"themes/" . $clms_config['ftsclms_theme'] . "/icons/indicator.gif\" alt=\"spinner\" /></span></span></td>
							</tr>";
							
				$orderids[$row['orders_id']] = "";					
				$x = ($x==2) ? 1 : 2;
			}
		}
		mysql_free_result($result);
			
		
		$content .=	"					
						</table>
						<script type=\"text/javascript\">";
		
		$x = 1; //reset the variable we use for our highlight colors
		foreach($orderids as $key => $value) {
			$highlightColors = ($x == 1) ? "highlightcolor:'#CBD5DC',highlightendcolor:'#5194B6'" : "highlightcolor:'#5194B6',highlightendcolor:'#CBD5DC'";
			
			$content .= "
							new Ajax.InPlaceEditor('" . $key . "_number', 'ajax.php?action=updateitem&table=orders&item=number&id=" . $key . "', {rows:1,cols:30," . $highlightColors . ",loadTextURL:'ajax.php?action=getitem&table=orders&item=number&id=" . $key . "'});
							new Ajax.InPlaceEditor('" . $key . "_date_ordered', 'ajax.php?action=updateitem&table=orders&item=date_ordered&id=" . $key . "', {rows:1,cols:30," . $highlightColors . ",loadTextURL:'ajax.php?action=getitem&table=orders&item=date_ordered&id=" . $key . "'});
							new Ajax.InPlaceEditor('" . $key . "_date_shipped', 'ajax.php?action=updateitem&table=orders&item=date_shipped&id=" . $key . "', {rows:1,cols:30," . $highlightColors . ",loadTextURL:'ajax.php?action=getitem&table=orders&item=date_shipped&id=" . $key . "'});
							new Ajax.InPlaceEditor('" . $key . "_total', 'ajax.php?action=updateitem&table=orders&item=total&id=" . $key . "', {rows:1,cols:30," . $highlightColors . ",loadTextURL:'ajax.php?action=getitem&table=orders&item=total&id=" . $key . "'});
							new Ajax.InPlaceEditor('" . $key . "_tracking_no', 'ajax.php?action=updateitem&table=orders&item=tracking_no&id=" . $key . "', {rows:1,cols:30," . $highlightColors . ",loadTextURL:'ajax.php?action=getitem&table=orders&item=tracking_no&id=" . $key . "'});
							new Ajax.InPlaceEditor('" . $key . "_shipped_by', 'ajax.php?action=updateitem&table=orders&item=shipped_by&id=" . $key . "', {rows:1,cols:30," . $highlightColors . ",loadTextURL:'ajax.php?action=getitem&table=orders&item=shipped_by&id=" . $key . "'});";
			
			$x = ($x==2) ? 1 : 2;
		}
		
		$content .= "
						</script>";
		
		return $content;
	}

?>