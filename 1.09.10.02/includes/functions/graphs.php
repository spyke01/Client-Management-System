<?php 
/***************************************************************************
 *                               graphs.php
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

//=========================================================
// Gets the number of systems sold based on a date range and id
//=========================================================
function getNumOfProductsSold($startDatetimestamp, $stopDatetimestamp) {
	global $DBTABLEPREFIX;
	
	$extraSQL = "";
	$extraSQL = ($startDate == "" || $stopDate == "") ? "" : " AND o.orders_datetime >= '" . $startDatetimestamp . "' AND o.orders_datetime < '" . $stopDatetimestamp . "'";
	$sql = "SELECT COUNT(op.orders_products_id) AS numSold FROM `" . $DBTABLEPREFIX . "orders_products` op LEFT JOIN `" . $DBTABLEPREFIX . "orders` o ON o.orders_id = op.orders_products_order_id WHERE o.orders_status = '" . STATUS_ORDER_SHIPPED . "'" . $extraSQL;
	$result = mysql_query($sql);
	//echo $sql . "<br />";
					
	if ($result && mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_array($result)) {
			return $row['numSold'];
		}
	
		mysql_free_result($result);
	}
	else {
		return "0";
	}
}

//=========================================================
// Gets the total dollar amount for orders containing 
// systesm sold based on a date range and id
//=========================================================
function getTotalDollarAmountOfOrders($startDatetimestamp, $stopDatetimestamp) {
	global $DBTABLEPREFIX;
	
	$extraSQL = "";
	$extraSQL = ($startDate == "" || $stopDate == "") ? "" : " AND o.orders_datetime >= '" . $startDatetimestamp . "' AND o.orders_datetime < '" . $stopDatetimestamp . "'";
	$sql = "SELECT SUM(o.orders_price) AS totalSold FROM `" . $DBTABLEPREFIX . "orders_products` op LEFT JOIN `" . $DBTABLEPREFIX . "orders` o ON o.orders_id = op.orders_products_order_id WHERE o.orders_status = '" . STATUS_ORDER_SHIPPED . "'" . $extraSQL;
	$result = mysql_query($sql);
	//echo $sql . "<br />";
					
	if ($result && mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_array($result)) {
			return $row['totalSold'];
		}
	
		mysql_free_result($result);
	}
	else {
		return "0";
	}
}

//=========================================================
// Gets the profit vs loss for orders containing 
// systesm sold based on a date range and id
//=========================================================
function getProfitVsLossOfOrders($startDatetimestamp, $stopDatetimestamp) {
	global $DBTABLEPREFIX;
	
	$extraSQL = "";
	$extraSQL = ($startDate == "" || $stopDate == "") ? "" : " AND o.orders_datetime >= '" . $startDatetimestamp . "' AND o.orders_datetime < '" . $stopDatetimestamp . "'";
	$sql = "SELECT SUM(o.orders_items_total) AS totalSold, SUM(po.purchaseorders_price) AS totalCost FROM `" . $DBTABLEPREFIX . "orders_products` op LEFT JOIN `" . $DBTABLEPREFIX . "orders` o ON o.orders_id = op.orders_products_order_id LEFT JOIN `" . $DBTABLEPREFIX . "purchaseorders` po ON po.purchaseorders_order_id = o.orders_id WHERE o.orders_status = '" . STATUS_ORDER_SHIPPED . "'" . $extraSQL;
	$result = mysql_query($sql);
	//echo $sql . "<br />";
					
	if ($result && mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_array($result)) {
			return ($row['totalSold'] - $row['totalCost']);
		}
	
		mysql_free_result($result);
	}
	else {
		return "0";
	}
}

//=========================================================
// Gets the shipping cost a customer is paying us for 
// orders containing systesm sold based on a date range and id
//=========================================================
function getShippingCostForCustomerOfOrders($startDatetimestamp, $stopDatetimestamp) {
	global $DBTABLEPREFIX;
	
	$extraSQL = "";
	$extraSQL = ($startDate == "" || $stopDate == "") ? "" : " AND o.orders_datetime >= '" . $startDatetimestamp . "' AND o.orders_datetime < '" . $stopDatetimestamp . "'";
	$sql = "SELECT SUM(o.orders_shipping_price) AS totalCost FROM `" . $DBTABLEPREFIX . "orders_products` op LEFT JOIN `" . $DBTABLEPREFIX . "orders` o ON o.orders_id = op.orders_products_order_id LEFT JOIN `" . $DBTABLEPREFIX . "purchaseorders` po ON po.purchaseorders_order_id = o.orders_id WHERE o.orders_status = '" . STATUS_ORDER_SHIPPED . "'" . $extraSQL;
	$result = mysql_query($sql);
	//echo $sql . "<br />";
					
	if ($result && mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_array($result)) {
			return $row['totalCost'];
		}
	
		mysql_free_result($result);
	}
	else {
		return "0";
	}
}

?>