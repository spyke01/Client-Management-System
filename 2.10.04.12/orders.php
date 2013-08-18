<? 
/***************************************************************************
 *                               orders.php
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
if ($_SESSION['userid']) {
	$allowEditing = ($_SESSION['user_level'] == SYSTEM_ADMIN || $_SESSION['user_level'] == CLIENT_ADMIN) ? 1 : 0;
	
	//==================================================
	// Handle editing, adding, and deleting of clients
	//==================================================	
	if (($actual_action == "vieworder" || $actual_action == "paymenthistory" || $actual_action == "emailorder") && isset($actual_id)) {
		// Add breadcrumb
		$page->addBreadCrumb("Order Details", "");
		$otherVersionLink = ($actual_style == "printerFriendly") ? "<a href=\"" . $menuvar['VIEWORDER'] . "&id=" . $actual_id . "\">Normal Version</a>" : "<a href=\"" . $menuvar['VIEWORDER'] . "&id=" . $actual_id . "&style=printerFriendly\">Printer Friendly Version</a>";
		
		$page_content .= "
						<div id=\"tabs\">
							<ul>
								<li><a href=\"#viewOrder\"><span>View Order</span></a></li>
								<li><a href=\"#paymentHistory\"><span>Payment History</span></a></li>
								<li><a href=\"#emailOrder\"><span>Email Order</span></a></li>
							</ul>
							<div id=\"viewOrder\">
								<span class=\"versionLinkContainer\"> " . $otherVersionLink . "</span>
								<div id=\"updateMeViewOrder\">
								" . printOrder($actual_id) . "
								</div>
							</div>
							<div id=\"paymentHistory\">
								<div id=\"updateMeOrderPayments\">
									" . printOrderPaymentsTable($actual_id) . "
								</div>";
		$page_content .= ($allowEditing) ? "
								<br /><br />
								" . printMakeOrderPaymentForm($actual_id) : "";
		$page_content .= "
							</div>
							<div id=\"emailOrder\">
								" . printEmailOrderForm($actual_id) . "
							</div>
						</div>";
					
		$JQueryReadyScripts = returnOrderJQuery($actual_id, $allowEditing) . returnOrderPaymentsTableJQuery() . returnMakeOrderPaymentFormJQuery($actual_id, 1) . returnEmailOrderFormJQuery() . "var \$tabs = $(\"#tabs\").tabs();";
		$JQueryReadyScripts .= ($allowEditing) ? returnMakeOrderPaymentFormJQuery($actual_id, 1) : "";
		
		// Select proper tab if needed
		$JQueryReadyScripts .= ($actual_action == "paymenthistory") ? "\$tabs.tabs('select', 1);" : "";
		$JQueryReadyScripts .= ($actual_action == "emailorder") ? "\$tabs.tabs('select', 2);" : "";
	}
	else {

	}
	
	$page->setTemplateVar("PageContent", $page_content);
	$page->setTemplateVar("JQueryReadyScript", $JQueryReadyScripts);
}
else {
	$page->setTemplateVar("PageContent", "\nYou Are Not Authorized To Access This Area. Please Refrain From Trying To Do So Again.");
}
?>