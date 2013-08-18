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
 *
 * This program is licensed under the Fast Track Sites Program license 
 * located inside the license.txt file included with this program. This is a 
 * legally binding license, and is protected by all applicable laws, by 
 * editing this page you fall subject to these licensing terms.
 *
 ***************************************************************************/
	include 'includes/header.php';
	
	$actual_id = keepsafe($_GET['id']);
	$actual_action = parseurl($_GET['action']);
	$actual_value = parseurl($_GET['value']);
	$actual_type = parseurl($_GET['type']);
	
	//================================================
	// Main updater and get functions
	//================================================
	// Update an item in a DB table
	if ($actual_action == "updateitem") {
		$item = parseurl($_GET['item']);
		$table = parseurl($_GET['table']);
		$tableabrev = ($table == "categories") ? "cat" : $table;
		$updateto = ($item == "datetimestamp" || $item == "date_ordered" || $item == "date_shipped") ? strtotime(keeptasafe($_REQUEST['value'])) : keeptasafe($_REQUEST['value']);
		
		$sql = "UPDATE `" . $DBTABLEPREFIX . $table . "` SET " . $tableabrev . "_" . $item ." = '" . $updateto . "' WHERE " . $tableabrev . "_id = '" . $actual_id . "'";
		$result = mysql_query($sql);		
		
		if ($item == "datetimestamp" || $item == "date_ordered" || $item == "date_shipped") { 
			$result =  (trim($updateto) != "") ? makeDateTime($updateto) : "";
			echo $result;
		}
		else { echo stripslashes($updateto); }
	}
	// Get an item from a DB table
	elseif ($actual_action == "getitem") {
		$item = parseurl($_GET['item']);
		$table = parseurl($_GET['table']);
		$tableabrev = ($table == "categories") ? "cat" : $table;
		$sqlrow = $tableabrev . "_" . $item;
		
		$sql = "SELECT $sqlrow FROM `" . $DBTABLEPREFIX . $table . "` WHERE " . $tableabrev . "_id = '" . $actual_id . "'";
		$result = mysql_query($sql);
		
		$row = mysql_fetch_array($result);
		mysql_free_result($result);
		
		if ($item == "datetimestamp" || $item == "date_ordered" || $item == "date_shipped") { 
			$result =  (trim($row[$sqlrow]) != "") ? makeShortDateTime($row[$sqlrow]) : ""; 
			echo $result;
		}
		else { echo bbcode($row[$sqlrow]); }
	}	
	// Delete a row from a DB table
	elseif ($actual_action == "deleteitem") {
		$table = parseurl($_GET['table']);
		$sql = "DELETE FROM `" . $DBTABLEPREFIX . $table . "` WHERE " . $table . "_id = '$actual_id'";
		$result = mysql_query($sql);
	}
	
	//================================================
	// Update our cats in the database
	//================================================
	// Post a cat
	elseif ($actual_action == "postcat") {
		$name = keeptasafe($_POST['newcatname']);	
		
		$sql = "INSERT INTO `" . $DBTABLEPREFIX . "categories` (`cat_name`) VALUES ('" . $name . "')";
		$result = mysql_query($sql);
		
		$newCommentId = mysql_insert_id();
		
		echo printCategoriesTable();
	}
		
	//================================================
	// Update our notes in the database
	//================================================
	// Post a note
	elseif ($actual_action == "postnote") {
		$datetimestamp = time();
		$client_id = keeptasafe($_POST['client_id']);
		$note = keeptasafe($_POST['note']);
		$urgency = keeptasafe($_POST['urgency']);	
		
		$sql = "INSERT INTO `" . $DBTABLEPREFIX . "notes` (`notes_datetimestamp`, `notes_client_id`, `notes_note`, `notes_urgency`) VALUES ('" . $datetimestamp . "', '" . $client_id . "', '" . $note . "', '" . $urgency . "')";
		$result = mysql_query($sql);
		
		$content = ($result) ? "	<span style=\"color: green; font-weight: bold;\">Successfully created note!</span>" : "	<span style=\"color: red; font-weight: bold;\">Failed to create note!!!</span>";
		
		echo $content;
	}
		
	//================================================
	// Update our appointments in the database
	//================================================
	// Post an appointment
	elseif ($actual_action == "postappointment") {
		$datetimestamp = strtotime($_POST['datetimestamp']);
		$client_id = keeptasafe($_POST['client_id']);
		$place = keeptasafe($_POST['place']);
		$attire = keeptasafe($_POST['attire']);
		$description = keeptasafe($_POST['description']);
		$urgency = keeptasafe($_POST['urgency']);	
		
		$sql = "INSERT INTO `" . $DBTABLEPREFIX . "appointments` (`appointments_datetimestamp`, `appointments_client_id`, `appointments_place`, `appointments_attire`, `appointments_description`, `appointments_urgency`) VALUES ('" . $datetimestamp . "', '" . $client_id . "', '" . $place . "', '" . $attire . "', '" . $description . "', '" . $urgency . "')";
		$result = mysql_query($sql);
		
		$content = ($result) ? "	<span style=\"color: green; font-weight: bold;\">Successfully created appointment!</span>" : "	<span style=\"color: red; font-weight: bold;\">Failed to create appointment!!!<br />$sql</span>";
		
		if (keepsafe($_GET['reprinttable']) != "true") { echo $content;}
		else {	echo printViewDateTable($datetimestamp); }		
	}
		
	//================================================
	// Update our calendar
	//================================================
	elseif ($actual_action == "postappointmentcalendar") {
		echo printAppointmentCalendar();
	}
		
	//================================================
	// Update our orders in the database
	//================================================
	// Post an order
	elseif ($actual_action == "postorder") {
		$number = keeptasafe($_POST['number']);
		$client_id = keeptasafe($_POST['client_id']);
		$date_ordered = strtotime($_POST['date_ordered']);
		$date_shipped = strtotime($_POST['date_shipped']);
		$total = keeptasafe($_POST['total']);
		$tracking_no = keeptasafe($_POST['tracking_no']);
		$shipped_by = keeptasafe($_POST['shipped_by']);	
		
		$sql = "INSERT INTO `" . $DBTABLEPREFIX . "orders` (`orders_number`, `orders_client_id`, `orders_date_ordered`, `orders_date_shipped`, `orders_total`, `orders_tracking_no`, `orders_shipped_by`) VALUES ('" . $number . "', '" . $client_id . "', '" . $date_ordered . "', '" . $date_shipped . "', '" . $total . "', '" . $tracking_no . "', '" . $shipped_by . "')";
		$result = mysql_query($sql);
					
		echo printOrdersTable();
	}
	
	else {
		// Do Nothing
	}

?>
