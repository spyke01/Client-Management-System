<?php 
/***************************************************************************
 *                               users.php
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
// Gets a username from a userid
//=========================================================
function getUsernameFromID($userID) {
	global $DBTABLEPREFIX;
	
	$sql = "SELECT users_username FROM `" . $DBTABLEPREFIX . "users` WHERE users_id='" . $userID . "'";
	$result = mysql_query($sql);
	
	if ($result && mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_array($result)) {
			return $row['users_username'];
		}	
		mysql_free_result($result);
	}
}

//=========================================================
// Gets a username from a userid
//=========================================================
function getUsersNameByOrderID($orderID) {
	global $DBTABLEPREFIX;
	
	$sql = "SELECT orders_user_id FROM `" . $DBTABLEPREFIX . "orders` WHERE orders_id='" . $orderID . "'";
	$result = mysql_query($sql);
	
	if ($result && mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_array($result)) {
			$sql2 = "SELECT users_first_name, users_last_name FROM `" . $DBTABLEPREFIX . "users` WHERE users_id='" . $row['orders_user_id'] . "'";
			$result2 = mysql_query($sql2);
	
			if ($result2 && mysql_num_rows($result2) > 0) {
				while ($row2 = mysql_fetch_array($result2)) {
					if ($row2['users_last_name'] == "" && $row2['users_first_name'] == "") {
						// Pull from billing info
						$sql3 = "SELECT addresses_first_name, addresses_last_name FROM `" . $DBTABLEPREFIX . "addresses` WHERE addresses_order_id='" . $orderID . "' AND addresses_type = '0'";
						$result3 = mysql_query($sql3);
	
						if ($result3 && mysql_num_rows($result3) > 0) {
							while ($row3 = mysql_fetch_array($result3)) {
								return $row3['addresses_last_name'] . ", " . $row3['addresses_first_name'];
							}
							mysql_free_result($result3);
						}						
					}
					else {
						return $row2['users_last_name'] . ", " . $row2['users_first_name'];
					}					
				}
				mysql_free_result($result2);
			}
		}
		mysql_free_result($result);
	}
}

//=========================================================
// Gets a userid from a userid
//=========================================================
function getUserIDByOrderID($orderID) {
	global $DBTABLEPREFIX;
	
	$sql = "SELECT orders_user_id FROM `" . $DBTABLEPREFIX . "orders` WHERE orders_id='" . $orderID . "'";
	$result = mysql_query($sql);
	
	if ($result && mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_array($result)) {
			return $row['orders_user_id'];
		}	
		mysql_free_result($result);
	}
}

//=========================================================
// Gets a user's userlevel from a userid
//=========================================================
function getUserlevelFromID($userID) {
	global $DBTABLEPREFIX;
	$level = "";
	
	$sql = "SELECT users_user_level FROM `" . $DBTABLEPREFIX . "users` WHERE users_id='" . $userID . "' LIMIT 1";
	$result = mysql_query($sql);
	
	if ($result && mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_array($result)) {
			$level = ($row['users_user_level'] == ADMIN) ? "Administrator" : "Moderator";
			$level = ($row['users_user_level'] == USER) ? "User" : $level;
			$level = ($row['users_user_level'] == BANNED) ? "Banned" : $level;
		}	
		mysql_free_result($result);
	}
	
	return $level;
}

//=========================================================
// Gets an email address from a userid
//=========================================================
function getEmailAddressFromID($userID) {
	global $DBTABLEPREFIX;
	
	$sql = "SELECT users_email_address FROM `" . $DBTABLEPREFIX . "users` WHERE users_id='" . $userID . "'";
	$result = mysql_query($sql);
	
	if ($result && mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_array($result)) {
			return $row['users_email_address'];
		}	
		mysql_free_result($result);
	}
}

//=========================================================
// Gets an email address from a orderid
//=========================================================
function getEmailAddressFromOrderID($orderID) {
	global $DBTABLEPREFIX;
	
	$sql = "SELECT orders_user_id FROM `" . $DBTABLEPREFIX . "orders` WHERE orders_id='" . $orderID . "'";
	$result = mysql_query($sql);
	
	if ($result && mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_array($result)) {
			return getEmailAddressFromID($row['orders_user_id']);
		}	
		mysql_free_result($result);
	}
}

//=========================================================
// Returns basic info on a tech(username, name, phone # & ext)
//=========================================================
function getTechUserInfoFromID($userID) {
	global $DBTABLEPREFIX;
	$block = "";
	
	$sql = "SELECT u.users_email_address, u.users_first_name, u.users_last_name, ua.useraddresses_day_phone, ua.useraddresses_day_phone_ext FROM `" . $DBTABLEPREFIX . "users` u, `" . $DBTABLEPREFIX . "useraddresses` ua WHERE u.users_id='$userID' AND u.users_id = ua.useraddresses_user_id AND useraddresses_type = '0'";
	$result = mysql_query($sql);
	
	if ($result && mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_array($result)) {
			$block = "<strong>Sales Rep:</strong> " . " " . $row['users_first_name'] . " " . $row['users_last_name'] . " - ext. " . $row['useraddresses_day_phone_ext'] . "<br /><strong>Sales Email:</strong> " . $row['users_email_address'];
		}
		mysql_free_result($result);
	}
	
	return $block;
}

//=========================================================
// Gets an address from an userid
//=========================================================
function getUserAddress($type, $userID) {
	global $DBTABLEPREFIX;
	$sql = "SELECT u.users_first_name, u.users_last_name, ua.* FROM `" . $DBTABLEPREFIX . "useraddresses` ua, `" . $DBTABLEPREFIX . "users` u WHERE ua.useraddresses_user_id='$userID' AND ua.useraddresses_user_id=u.users_id AND ua.useraddresses_type='$type'";
	$result = mysql_query($sql);
	
	while ($row = mysql_fetch_array($result)) {
		$addressLine2 = (trim($row['useraddresses_street_2']) != "") ? $row['useraddresses_street_2'] . "<br />" : "";
		$companyName = (trim($row['useraddresses_company']) != "") ? $row['useraddresses_company'] . "<br />" : "";
		$adressVar = $companyName . 
					$row['useraddresses_first_name'] . " " . $row['useraddresses_last_name'] . "<br />" .
					$row['useraddresses_street_1'] . "<br />" .
					$addressLine2 .
					$row['useraddresses_city'] . ", " . $row['useraddresses_state'] . " " . $row['useraddresses_country'] . " " . $row['useraddresses_zip'] . "<br /><br />" .
					//"<strong>Email Address: </strong>" . $row['useraddresses_email_address'] . "<br />" .
					"<strong>Primary Phone Number: </strong>" . $row['useraddresses_day_phone'] . "<br />" .
					"<strong>Secondary Phone Number: </strong>" . $row['useraddresses_night_phone'] . "<br />" .
					"<strong>Fax: </strong>" . $row['useraddresses_fax'] . "<br />";
		return $adressVar;
	}
	
	mysql_free_result($result);
}

//=========================================================
// Gets an address from the DB
//=========================================================
function getUserPanelUserAddress($userID, $type) {
	global $DBTABLEPREFIX, $_SESSION, $FTS_COUNTRIES, $FTS_STATES;
	$userID = ($_SESSION['user_level'] == MOD || $_SESSION['user_level'] == ADMIN) ? $userID : $_SESSION['userid'];
	
	$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "useraddresses` WHERE useraddresses_user_id='" . $userID . "' AND useraddresses_type='" . keepsafe($type) . "' LIMIT 1";
	$result = mysql_query($sql);
	
	$row = mysql_fetch_array($result);
	
	$tableTitle = ($type == BILL_ADDRESS) ? "Billing Information" : "Shipping Information";
	$adressVar = "
						<table class=\"contentBox\" cellpadding=\"1\" cellspacing=\"1\" width=\"100%\">
							<tr>
								<td class=\"title1\" colspan=\"2\">
									<span class=\"floatRight\"><a href=\"ajax.php?action=showUserPanelUserAddressEdit&id=" . $userID . "\" rel=\"lyteframe\" title=\"Edit Bill/Ship Information\" rev=\"width: 700px; height: 500px; scrolling: yes;\">Edit Bill/Ship Information</a></span>
									" . $tableTitle . "
								</td>
							</tr>		
							<tr>
								<td class=\"title2\" style=\"width: 30%\"><strong>First Name</strong></td>
								<td class=\"row1\">" . $row['useraddresses_first_name'] . "</td>
							</tr>	
							<tr>
								<td class=\"title2\" style=\"width: 30%\"><strong>Last Name</strong></td>
								<td class=\"row1\">" . $row['useraddresses_last_name'] . "</td>
							</tr>	
							<tr>
								<td class=\"title2\" style=\"width: 30%\"><strong>Company Name</strong></td>
								<td class=\"row1\">" . $row['useraddresses_company'] . "</td>
							</tr>
							<tr>
								<td class=\"title2\"><strong>Address</strong></td>
								<td class=\"row1\">
									" . $row['useraddresses_street_1'] . "<br />
									" . $row['useraddresses_street_2'] . "
								</td>
							</tr>	
							<tr>
								<td class=\"title2\"><strong>City</strong></td>
								<td class=\"row1\">" . $row['useraddresses_city'] . "</td>
							</tr>	
							<tr>
								<td class=\"title2\"><strong>Country</strong></td>
								<td class=\"row1\">" . $FTS_COUNTRIES[$row['useraddresses_country']] . "</td>
							</tr>	
							<tr id=\"billStateRow\"" . $billShowStates . ">
								<td class=\"title2\"><strong>State / Province</strong></td>
								<td class=\"row1\">" . $FTS_STATES[$row['useraddresses_state']] . "</td>
							</tr>	
							<tr>
								<td class=\"title2\"><strong>Postal Code</strong></td>
								<td class=\"row1\">" . $row['useraddresses_zip'] . "</td>
							</tr>	
							<tr>
								<td class=\"title2\"><strong>Primary Phone Number</strong></td>
								<td class=\"row1\">" . $row['useraddresses_day_phone'] . " ext. " . $row['useraddresses_day_phone_ext'] . "</td>
							</tr>	
							<tr>
								<td class=\"title2\"><strong>Secondary Phone Number</strong></td>
								<td class=\"row1\">" . $row['useraddresses_night_phone'] . " ext. " . $row['useraddresses_night_phone_ext'] . "</td>
							</tr>
							<tr>
								<td class=\"title2\"><strong>Fax</strong></td>
								<td class=\"row1\">" . $row['useraddresses_fax'] . "</td>
							</tr>	
						</table>";
		return $adressVar;
	
	mysql_free_result($result);
}

//=========================================================
// Gets an user's CC info from the DB
//=========================================================
function getUserPanelUserCreditCard($userID) {
	global $DBTABLEPREFIX, $_SESSION;
	$userID = ($_SESSION['user_level'] == MOD || $_SESSION['user_level'] == ADMIN) ? $userID : $_SESSION['userid'];
	
	$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "creditcards` WHERE creditcards_user_id='" . $userID . "' LIMIT 1";
	$result = mysql_query($sql);
	
	$row = mysql_fetch_array($result);
	
	$adressVar = "
						<table class=\"contentBox\" cellpadding=\"1\" cellspacing=\"1\" width=\"100%\">
							<tr>
								<td class=\"title1\" colspan=\"2\">
									<span class=\"floatRight\">
										<a href=\"ajax.php?action=showCreditCardEdit&id=" . $userID . "\" rel=\"lyteframe\" title=\"Edit Credit Card Information\" rev=\"width: 700px; height: 500px; scrolling: yes;\">Edit Credit Card Information</a> &nbsp;|&nbsp;
										<a href=\"\" title=\"Delete Credit Card Information\" onClick=\"deleteCreditCardInfo(); return false;\">Delete Credit Card Information</a>
									</span>
									Credit Card Information
								</td>
							</tr>		
							<tr>
								<td class=\"title2\" style=\"width: 200px;\"><strong>Name as it Appears on Card</strong></td>
								<td class=\"row1\">" . $row['creditcards_name_on_card'] . "</td>
							</tr>				
							<tr>
								<td class=\"title2\"><strong>Card Type</strong></td>
								<td class=\"row1\">" . printCreditCardType($row['creditcards_card_type']) . "</td>
							</tr>			
							<tr>
								<td class=\"title2\"><strong>Card Number</strong></td>
								<td class=\"row1\">" . maskCCNumber($row['creditcards_card_number']) . "</td>
							</tr>	
							<tr>
								<td class=\"title2\"><strong>Security ID on the Back of the Card</strong></td>
								<td class=\"row1\">" . maskCCSID($row['creditcards_card_sid']) . "</td>
							</tr>	
							<tr>
								<td class=\"title2\"><strong>Expiration Date</strong></td>
								<td class=\"row1\">" . printCreditCardMonth($row['creditcards_exp_month']) . "/" . printCreditCardYear($row['creditcards_exp_year']) . "</td>
							</tr>	
							<tr>
								<td class=\"title2\"><strong>Bank Name</strong></td>
								<td class=\"row1\">" . $row['creditcards_bank_name'] . "</td>
							</tr>
							<tr>
								<td class=\"title2\"><strong>Bank phone Number</strong></td>
								<td class=\"row1\">" . $row['creditcards_bank_number'] . "</td>
							</tr>
						</table>";
		return $adressVar;
	
	mysql_free_result($result);
}

?>