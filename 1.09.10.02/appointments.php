<? 
/***************************************************************************
 *                               appointments.php
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
if ($_SESSION['user_level'] == ADMIN) {

	$currentDate = keeptasafe($_GET['date']);
	
	//=================================================
	// Edit an Appointment
	//=================================================
	if ($actual_action == "editAppointment") {
	
	}
	
	//=================================================
	// Show Appointments for Chosen Date
	//=================================================
	if ($actual_action == "viewdate") {			
		$content = "
					<div id=\"updateMe\">
						" . printViewDateTable($currentDate) . "
					</div>
					" . printNewAppointmentForm($currentDate, 1);
	}
	
	//=================================================
	// Print out the calendar
	//=================================================	
	else {
		$content = "					
					<div id=\"updateMe\">
					" . printAppointmentCalendar() . "
					</div>
					" . printNewAppointmentForm($currentDate, 0);
	}
	
	$page->setTemplateVar("PageContent", $content);
}
else {
	$page->setTemplateVar("PageContent", "\nYou Are Not Authorized To Access This Area. Please Refrain From Trying To Do So Again.");
}
?>