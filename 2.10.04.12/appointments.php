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
if ($_SESSION['user_level'] == SYSTEM_ADMIN || $_SESSION['user_level'] == CLIENT_ADMIN) {

	$currentDate = (isset($_GET['date'])) ? keeptasafe($_GET['date']) : time();
	
	//=================================================
	// Edit an Appointment
	//=================================================
	if ($actual_action == "editAppointment") {
	
	}
	
	//=================================================
	// Show Appointments for Chosen Date
	//=================================================
	if ($actual_action == "viewdate") {			
		// Add breadcrumb
		$page->addBreadCrumb("View Appointments for " . makeDate($currentDate), "");
		
		$page_content .= "
				<div class=\"roundedBox\">
					<div id=\"updateMeAppointments\">
						" . printViewDateTable($currentDate) . "
					</div>
					<br /><br />
					" . printNewAppointmentForm($currentDate) . "
				</div>";
					
		$JQueryReadyScripts = returnNewAppointmentFormJQuery(2);
	}
	
	//=================================================
	// Print out the calendar
	//=================================================	
	else {
		$page_content .= "
						<div id=\"tabs\">
							<ul>
								<li><a href=\"#currentAppointments\"><span>Current Appointments</span></a></li>
								<li><a href=\"#createANewAppointment\"><span>Create a New Appointment</span></a></li>
							</ul>
							<div id=\"currentAppointments\">
								<div id=\"updateMeAppointments\">
									" . printAppointmentCalendar() . "
								</div>
							</div>
							<div id=\"createANewAppointment\">
								" . printNewAppointmentForm($currentDate) . "
							</div>
						</div>";
					
		$JQueryReadyScripts = returnNewAppointmentFormJQuery(3) . "$(\"#tabs\").tabs();";
	}
	
	$page->setTemplateVar("PageContent", $page_content);
	$page->setTemplateVar("JQueryReadyScript", $JQueryReadyScripts);
}
else {
	$page->setTemplateVar("PageContent", "\nYou Are Not Authorized To Access This Area. Please Refrain From Trying To Do So Again.");
}
?>