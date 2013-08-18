<?php 
/***************************************************************************
 *                               appointments.php
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
	// Print the Appointment calendar
	//
	// Used so that we can display it in many places
	//=================================================
	function printAppointmentCalendar() {
		global $DBTABLEPREFIX, $menuvar;
		// Gather variables from
		// user input and break them
		// down for usage in our script
		
		$date = (!isset($_REQUEST['date'])) ? mktime(0,0,0,date('m'), date('d'), date('Y')) : $_REQUEST['date'];
		
		$day = date('d', $date);
		$month = date('m', $date);
		$year = date('Y', $date);
		
		// Get the first day of the month
		$month_start = mktime(0,0,0,$month, 1, $year);
		
		// Get friendly month name
		$month_name = date('M', $month_start);
		
		// Figure out which day of the week
		// the month starts on.
		$month_start_day = date('D', $month_start);
		
		switch($month_start_day){
		    case "Sun": $offset = 0; break;
		    case "Mon": $offset = 1; break;
		    case "Tue": $offset = 2; break;
		    case "Wed": $offset = 3; break;
		    case "Thu": $offset = 4; break;
		    case "Fri": $offset = 5; break;
		    case "Sat": $offset = 6; break;
		}
		
		// determine how many days are in the last month.
		$num_days_last = ($month == 1 || $month == 01) ? cal_days_in_month(0, 12, ($year -1)) : cal_days_in_month(0, ($month -1), $year);
		
		// determine how many days are in the current month.
		$num_days_current = cal_days_in_month(0, $month, $year);
		
		// Build an array for the current days
		// in the month
		for($i = 1; $i <= $num_days_current; $i++){
		    $num_days_array[] = $i;
		}
		
		// Build an array for the number of days
		// in last month
		for($i = 1; $i <= $num_days_last; $i++){
		    $num_days_last_array[] = $i;
		}
		
		// If the $offset from the starting day of the
		// week happens to be Sunday, $offset would be 0,
		// so don't need an offset correction.
		
		if($offset > 0){
		    $offset_correction = array_slice($num_days_last_array, -$offset, $offset);
		    $new_count = array_merge($offset_correction, $num_days_array);
		    $offset_count = count($offset_correction);
		}
		
		// The else statement is to prevent building the $offset array.
		else {
		    $offset_count = 0;
		    $new_count = $num_days_array;
		}
		
		// count how many days we have with the two
		// previous arrays merged together
		$current_num = count($new_count);
		
		// Since we will have 5 HTML table rows (TR)
		// with 7 table data entries (TD)
		// we need to fill in 35 TDs
		// so, we will have to figure out
		// how many days to appened to the end
		// of the final array to make it 35 days.
		
		
		if($current_num > 35){
		   $num_weeks = 6;
		   $outset = (42 - $current_num);
		} elseif($current_num < 35){
		   $num_weeks = 5;
		   $outset = (35 - $current_num);
		}
		if($current_num == 35){
		   $num_weeks = 5;
		   $outset = 0;
		}
		// Outset Correction
		for($i = 1; $i <= $outset; $i++){
		   $new_count[] = $i;
		}
		
		// Now let's "chunk" the $all_days array
		// into weeks. Each week has 7 days
		// so we will array_chunk it into 7 days.
		$weeks = array_chunk($new_count, 7);
		
		
		// Build Previous and Next Links
		$previous_link = "<a href=\"" . $menuvar['APPOINTMENTS'] . "&date=";
		$previous_link .= ($month == 1 || $month == 01) ? mktime(0,0,0,12,$day,($year -1)) : mktime(0,0,0,($month -1),$day,$year);
		$previous_link .= "\"><< Prev</a>";
		
		$next_link = "<a href=\"" . $menuvar['APPOINTMENTS'] . "&date=";
		$next_link .= ($month == 12) ? mktime(0,0,0,1,$day,($year + 1)) : mktime(0,0,0,($month +1),$day,$year);
		$next_link .= "\">Next >></a>";
		
		// Build the heading portion of the calendar table
		$content = "
			<table class=\"contentBox calendar\" cellpadding=\"1\" cellspacing=\"1\">
		    	<tr>
		    		<td colspan=\"7\" class=\"title1\" width=\"100%\">
						<div id=\"calendarNext\" class=\"right\" style=\"float: right;\">" . $next_link . "</div>
						<div id=\"calendarMonth\" class=\"center\" style=\"float: right;\">" . $month_name . " " . $year . "</div>
		   				<div id=\"calendarPrev\" class=\"left\">" . $previous_link . "</div>
		    		 </td>
		    	 <tr class=\"title2\">
		    		 <td>Sunday</td><td>Monday</td><td>Tuesday</td><td>Wednesday</td><td>Thursday</td><td>Friday</td><td>Saturday</td>
				</tr>";
		
		// Now we break each key of the array 
		// into a week and create a new table row for each
		// week with the days of that week in the table data
		
		$i = 0;
		foreach($weeks AS $week){
			$content .= "\n		<tr>";
			
			foreach($week as $d){
				// Get our appointment for this day
				$sql = ($i < $offset_count) ? "SELECT c.clients_first_name, c.clients_last_name, a.appointments_datetimestamp, a.appointments_urgency FROM `" . $DBTABLEPREFIX . "appointments` a, `" . $DBTABLEPREFIX . "clients` c WHERE a.appointments_client_id = c.clients_id AND SUBSTRING(FROM_UNIXTIME(`appointments_datetimestamp`) FROM 1 FOR 10) = SUBSTRING(FROM_UNIXTIME(" . mktime(0,0,0,($month - 1),$d,$year) . ") FROM 1 FOR 10)" : "SELECT c.clients_first_name, c.clients_last_name, a.appointments_datetimestamp, a.appointments_urgency FROM `" . $DBTABLEPREFIX . "appointments` a, `" . $DBTABLEPREFIX . "clients` c WHERE a.appointments_client_id = c.clients_id AND SUBSTRING(FROM_UNIXTIME(`appointments_datetimestamp`) FROM 1 FOR 10) = SUBSTRING(FROM_UNIXTIME(" . mktime(0,0,0,$month,$d,$year) . ") FROM 1 FOR 10)";
				$sql = (($i > $offset_count || $outset > 0) && ($i >= ($num_weeks * 7) - $outset)) ? "SELECT c.clients_first_name, c.clients_last_name, a.appointments_datetimestamp, a.appointments_urgency FROM `" . $DBTABLEPREFIX . "appointments` a, `" . $DBTABLEPREFIX . "clients` c WHERE a.appointments_client_id = c.clients_id AND SUBSTRING(FROM_UNIXTIME(`appointments_datetimestamp`) FROM 1 FOR 10) = SUBSTRING(FROM_UNIXTIME(" . mktime(0,0,0,($month + 1),$d,$year) . ") FROM 1 FOR 10)" : $sql;
				$result = mysql_query($sql);
				
				$appointments = ""; // Reset our appointments variable
				
				while ($row = mysql_fetch_array($result)) {
					$rowColor = ($row['appointments_urgency'] != LOW) ? "redRow" : "greenRow";
					$rowColor = ($row['appointments_urgency'] != HIGH && $rowColor == "redRow") ? "yellowRow" : $rowColor;			
					
					$appointments .= "<div class=\"" . $rowColor . "\">" . makeTime($row['appointments_datetimestamp']) . " : " . $row['clients_first_name'] . " " . $row['clients_last_name'] . "</div>";
				}
				
				// Print the actual table item
				if($i < $offset_count){
					$day_link = "<a href=\"" . $menuvar['APPOINTMENTS'] . "&action=viewdate&date=" . mktime(0,0,0,($month -1),$d,$year) . "\">" . $d . "</a>";
					$content .= "\n			<td class=\"nonmonthdays\"><div class=\"date\">" . $day_link . "</div><div class=\"appointments\">" . $appointments . "</div></td>";
				}
				if(($i >= $offset_count) && ($i < ($num_weeks * 7) - $outset)){
					$day_link = "<a href=\"" . $menuvar['APPOINTMENTS'] . "&action=viewdate&date=" . mktime(0,0,0,$month,$d,$year) . "\">" . $d . "</a>";
					$content .= ($date == mktime(0,0,0,$month,$d,$year)) ? "\n			<td class=\"today\"><div class=\"date\">" . $day_link . "</div><div class=\"appointments\">" . $appointments . "</div></td>" : "\n			<td class=\"monthdays\"><div class=\"date\">$day_link</div><div class=\"appointments\">$appointments</div></td>";
				} 
				elseif(($outset > 0)) {
					if(($i >= ($num_weeks * 7) - $outset)){
						$day_link = "<a href=\"" . $menuvar['APPOINTMENTS'] . "&action=viewdate&date=" . mktime(0,0,0,($month +1),$d,$year) . "\">" . $d . "</a>";
						$content .= "\n			<td class=\"nonmonthdays\"><div class=\"date\">" . $day_link . "</div><div class=\"appointments\">" . $appointments . "</div></td>";
					}
				}
				
				$i++;
			}
			$content .= "\n		</tr>";   
		}
		
		// Close out your table and that's it!
		$content .= "\n		</table>";
		
		return $content;
	}

	//=================================================
	// Print the View Date Table
	//=================================================
	function printViewDateTable($datetimestamp) {
		global $DBTABLEPREFIX, $menuvar, $clms_config;
		
		$sql = "SELECT c.clients_first_name, c.clients_last_name, a.* FROM `" . $DBTABLEPREFIX . "appointments` a, `" . $DBTABLEPREFIX . "clients` c WHERE a.appointments_client_id = c.clients_id AND SUBSTRING(FROM_UNIXTIME(`appointments_datetimestamp`) FROM 1 FOR 10) = SUBSTRING(FROM_UNIXTIME(" . $datetimestamp . ") FROM 1 FOR 10)";
		$result = mysql_query($sql);
		
		//echo $sql;
			
		$content = "
						<table class=\"contentBox\" cellpadding=\"1\" cellspacing=\"1\" width=\"100%\">
							<tr>
								<td class=\"title1\" colspan=\"6\">
									Appointments for " . makeDate($datetimestamp) . "
								</td>
							</tr>							
							<tr class=\"title2\">
								<td class=\"title2\">Time</td><td class=\"title2\">Place</td><td class=\"title2\">Attire</td><td class=\"title2\">Client</td><td class=\"title2\">Description</td><td></td>
							</tr>";
							
		$appointmentids = array();
		if (!$result || mysql_num_rows($result) == 0) { // No appointments yet!
			$content .= "					
							<tr class=\"greenRow\">
								<td colspan=\"6\">There are no appointments scheduled for today.</td>
							</tr>";	
		}
		else {	 // Print all our appointments								
			while ($row = mysql_fetch_array($result)) {
					
				$rowColor = ($row['appointments_urgency'] != LOW) ? "redRow" : "greenRow";
				$rowColor = ($row['appointments_urgency'] != HIGH && $rowColor == "redRow") ? "yellowRow" : $rowColor;
				
				$content .=	"					
							<tr id=\"" . $row['appointments_id'] . "_row\" class=\"" . $rowColor . "\">
								<td><div id=\"" . $row['appointments_id'] . "_datetimestamp\">" . makeTime($row['appointments_datetimestamp']) . "</div></td>
								<td><div id=\"" . $row['appointments_id'] . "_place\">" . $row['appointments_place'] . "</div></td>
								<td><div id=\"" . $row['appointments_id'] . "_attire\">" . $row['appointments_attire'] . "</div></td>
								<td>" . $row['clients_first_name'] . " " . $row['clients_last_name'] . "</td>
								<td><div id=\"" . $row['appointments_id'] . "_description\">" . bbcode($row['appointments_description']) . "</div></td>
								<td><span class=\"center\"><a style=\"cursor: pointer; cursor: hand;\" onclick=\"ajaxDeleteNotifier('" . $row['appointments_id'] . "appointmentsSpinner', 'ajax.php?action=deleteitem&table=appointments&id=" . $row['appointments_id'] . "', 'appointment', '" . $row['appointments_id'] . "_row');\"><img src=\"themes/" . $clms_config['ftsclms_theme'] . "/icons/delete.png\" alt=\"Delete Appointment\" /></a><span id=\"" . $row['appointments_id'] . "appointmentsSpinner\" style=\"display: none;\"><img src=\"themes/" . $clms_config['ftsclms_theme'] . "/icons/indicator.gif\" alt=\"spinner\" /></span></span></td>
							</tr>";
									
				$appointmentids[$row['appointments_id']] = "";
			}
		}
		mysql_free_result($result);
			
		
		$content .=	"					
						</table>
						<script type=\"text/javascript\">";
		
		$x = 1; //reset the variable we use for our highlight colors
		foreach($appointmentids as $key => $value) {
			$highlightColors = ($x == 1) ? "highlightcolor:'#CBD5DC',highlightendcolor:'#5194B6'" : "highlightcolor:'#5194B6',highlightendcolor:'#CBD5DC'";
		
			$content .= "						
							new Ajax.InPlaceEditor('" . $key . "_datetimestamp', 'ajax.php?action=updateitem&table=appointments&item=datetimestamp&id=" . $key . "', {rows:1,cols:30," . $highlightColors . ",loadTextURL:'ajax.php?action=getitem&table=appointments&item=datetimestamp&id=" . $key . "'});
							new Ajax.InPlaceEditor('" . $key . "_place', 'ajax.php?action=updateitem&table=appointments&item=place&id=" . $key . "', {rows:1,cols:30," . $highlightColors . ",loadTextURL:'ajax.php?action=getitem&table=appointments&item=place&id=" . $key . "'});
							new Ajax.InPlaceEditor('" . $key . "_attire', 'ajax.php?action=updateitem&table=appointments&item=attire&id=" . $key . "', {rows:1,cols:30," . $highlightColors . ",loadTextURL:'ajax.php?action=getitem&table=appointments&item=attire&id=" . $key . "'});
							new Ajax.InPlaceEditor('" . $key . "_description', 'ajax.php?action=updateitem&table=appointments&item=description&id=" . $key . "', {rows:10,cols:30," . $highlightColors . ",loadTextURL:'ajax.php?action=getitem&table=appointments&item=description&id=" . $key . "'});";
			
			$x = ($x==2) ? 1 : 2;
		}
		
		$content .= "
						</script>";
		
		return $content;
	}
	
	//=================================================
	// Create a form to add new appointments
	//
	// Used so that we can display it in many places
	//=================================================
	function printNewAppointmentForm($date, $ajaxIt = 0) {
		global $DBTABLEPREFIX, $menuvar, $clms_config;
		
		$currentDate = (trim($date) != "") ? @gmdate('m/d/Y', $date + (3600 * '-5.00')) : $date;	
	
		$content = "
					<br />";
					
		if ($ajaxIt == 0) {		
			$content .= "	
						<div id=\"response\">
						</div>";
			$updaterCode = "new Ajax.Updater('response', 'ajax.php?action=postappointment', {onComplete:function(){ new Effect.Highlight('response'); new Ajax.Updater('updateMe', 'ajax.php?action=postappointmentcalendar', {asynchronous:true});},asynchronous:true, parameters:Form.serialize(document.newAppointmentForm), evalScripts:true});";
		}
		else {
			$updaterCode = "new Ajax.Updater('updateMe', 'ajax.php?action=postappointment&reprinttable=true', {onComplete:function(){ new Ajax.Updater('updateMe', 'ajax.php?action=postappointmentcalendar', {asynchronous:true}); },asynchronous:true, parameters:Form.serialize(document.newAppointmentForm), evalScripts:true});";
		}
		
		$content .= "
					<form name=\"newAppointmentForm\" id=\"newAppointmentForm\" action=\"" . $menuvar['APPOINTMENTS'] . "\" method=\"post\" onSubmit=\"return false;\">
						<table class=\"contentBox\" cellpadding=\"1\" cellspacing=\"1\" width=\"100%\">
							<tr>
								<td class=\"title1\" colspan=\"2\">New Appointment</td>
							</tr>							
							<tr class=\"row1\">
								<td><strong>Client: </strong></td>
								<td>
									" . createDropdown("clients", "client_id", "", "") . "
								</td>
							</tr>
							<tr class=\"row2\">
								<td><strong>Date and Time: </strong></td>
								<td><input type=\"text\" name=\"datetimestamp\" id=\"datetimestamp\" size=\"20\" value=\"" . $currentDate . "\" /><img src=\"themes/" . $clms_config['ftsclms_theme'] . "/icons/calendar.gif\" onclick=\"showChooser(this, 'datetimestamp', 'datetimestampChooser', " . makeCurrentYear(time()) . ", " . makeXYearsFromCurrentYear(time(), 20) . ", Date.patterns.ShortDatePattern, false);\" /><div id=\"datetimestampChooser\" class=\"dateChooser select-free\" style=\"display: none; visibility: hidden; width: 160px;\"></div></td>
							</tr>
							<tr class=\"row1\">
								<td><strong>Place: </strong></td>
								<td><input type=\"text\" name=\"place\" size=\"60\" /></td>
							</tr>
							<tr class=\"row2\">
								<td><strong>Attire: </strong></td>
								<td><input type=\"text\" name=\"attire\" size=\"60\" /></td>
							</tr>						
							<tr class=\"row1\">
								<td><strong>Description: </strong></td>
								<td>
									<textarea name=\"description\" rows=\"10\" cols=\"50\"></textarea>
								</td>
							</tr>	
							<tr class=\"row2\">
								<td><strong>Urgency: </strong></td>
								<td>
									" . createDropdown("urgency", "urgency", "", "") . "
								</td>
							</tr>							
						</table>									
						<br />
						<input type=\"submit\" class=\"button\" value=\"Make the Appointment!\" />
					</form>
					
			<script type=\"text/javascript\">
				var valid = new Validation('newAppointmentForm', {immediate : true, useTitles:true, onFormValidate : ValidateForm});
			
				function ValidateForm(result, formRef){
					if (result == true) {			
						" . $updaterCode . "	
						var date=document.newAppointmentForm.datetimestamp
						var place=document.newAppointmentForm.place
						var attire=document.newAppointmentForm.attire
						var description=document.newAppointmentForm.description
						date.value = '';
						place.value = '';
						attire.value = '';
						description.value = '';
					}
					return false;
				}
			</script>";
			
		return $content;
	}

	//=================================================
	// Print the Client Appointments Table
	//=================================================
	function printClientAppointmentsTable($clientID = "") {
		global $DBTABLEPREFIX, $menuvar, $clms_config;
		
		$extraSQL = ($clientID != "") ? " WHERE appointments_client_id = '" . $clientID . "'" : "";
		
		$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "appointments`" . $extraSQL . " ORDER BY appointments_datetimestamp ASC";
		$result = mysql_query($sql);
		
		//echo $sql;
			
		$content = "
						<table class=\"contentBox\" cellpadding=\"1\" cellspacing=\"1\" width=\"100%\">
							<tr>
								<td class=\"title1\" colspan=\"6\">
									Appointments
								</td>
							</tr>							
							<tr class=\"title2\">
								<td class=\"title2\">Time</td><td class=\"title2\">Place</td><td class=\"title2\">Attire</td><td class=\"title2\">Description</td><td></td>
							</tr>";
							
		$appointmentsids = array();
		if (!$result || mysql_num_rows($result) == 0) { // No appointments yet!
			$content .= "					
							<tr class=\"greenRow\">
								<td colspan=\"6\">There are no apointments scheduled for this client.</td>
							</tr>";	
		}
		else {	 // Print all our appointments								
			while ($row = mysql_fetch_array($result)) {							
				$rowColor = ($row['appointments_urgency'] != LOW) ? "redRow" : "greenRow";
				$rowColor = ($row['appointments_urgency'] != HIGH && $rowColor == "redRow") ? "yellowRow" : $rowColor;
					
				$content .= "					
							<tr id=\"" . $row['appointments_id'] . "\" class=\"" . $rowColor . "\">
								<td><div id=\"" . $row['appointments_id'] . "_datetimestamp\">" . makeDateTime($row['appointments_datetimestamp']) . "</div></td>
								<td><div id=\"" . $row['appointments_id'] . "_place\">" . $row['appointments_place'] . "</div></td>
								<td><div id=\"" . $row['appointments_id'] . "_attire\">" . $row['appointments_attire'] . "</div></td>
								<td><div id=\"" . $row['appointments_id'] . "_description\">" . bbcode($row['appointments_description']) . "</div></td>
								<td><span class=\"center\"><a style=\"cursor: pointer; cursor: hand;\" onclick=\"ajaxDeleteNotifier('" . $row['appointments_id'] . "appointmentsSpinner', 'ajax.php?action=deleteitem&table=appointments&id=" . $row['appointments_id'] . "', 'appointment', '" . $row['appointments_id'] . "_row');\"><img src=\"themes/" . $clms_config['ftsclms_theme'] . "/icons/delete.png\" alt=\"Delete Appointment\" /></a><span id=\"" . $row['appointments_id'] . "appointmentsSpinner\" style=\"display: none;\"><img src=\"themes/" . $clms_config['ftsclms_theme'] . "/icons/indicator.gif\" alt=\"spinner\" /></span></span></td>
							</tr>";		
						
				$appointmentsids[$row['appointments_id']] = "";
			}
		}
		mysql_free_result($result);
			
		
		$content .=	"					
						</table>
						<script type=\"text/javascript\">";
		
		$x = 1; //reset the variable we use for our highlight colors
		foreach($appointmentsids as $key => $value) {
			$highlightColors = ($x == 1) ? "highlightcolor:'#CBD5DC',highlightendcolor:'#5194B6'" : "highlightcolor:'#5194B6',highlightendcolor:'#CBD5DC'";
		
			$content .= "						
							new Ajax.InPlaceEditor('" . $key . "_datetimestamp', 'ajax.php?action=updateitem&table=appointments&item=datetimestamp&id=" . $key . "', {rows:1,cols:30," . $highlightColors . ",loadTextURL:'ajax.php?action=getitem&table=appointments&item=datetimestamp&id=" . $key . "'});
							new Ajax.InPlaceEditor('" . $key . "_place', 'ajax.php?action=updateitem&table=appointments&item=place&id=" . $key . "', {rows:1,cols:30," . $highlightColors . ",loadTextURL:'ajax.php?action=getitem&table=appointments&item=place&id=" . $key . "'});
							new Ajax.InPlaceEditor('" . $key . "_attire', 'ajax.php?action=updateitem&table=appointments&item=attire&id=" . $key . "', {rows:1,cols:30," . $highlightColors . ",loadTextURL:'ajax.php?action=getitem&table=appointments&item=attire&id=" . $key . "'});
							new Ajax.InPlaceEditor('" . $key . "_description', 'ajax.php?action=updateitem&table=appointments&item=description&id=" . $key . "', {rows:10,cols:30," . $highlightColors . ",loadTextURL:'ajax.php?action=getitem&table=appointments&item=description&id=" . $key . "'});";
			
			$x = ($x==2) ? 1 : 2;
		}
		
		$content .= "
						</script>";
		
		return $content;
	}
?>