<? 
/***************************************************************************
 *                               reports.php
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
if ($_SESSION['user_level'] == SYSTEM_ADMIN) {
	//==================================================
	// Handle editing, adding, and deleting of users
	//==================================================	
	if ($actual_action == "viewreport" && isset($actual_report)) {
		// Add breadcrumb
		$page->addBreadCrumb("View Report", "");
		$reportData = $reportJQuery = "";
		
		// Depending on the report we request lets build the page
		switch ($actual_report) {
			case 'invoiceAccountsAging':
				$reportData = printInvoiceAccountsAgingReport();
				$reportJQuery = returnInvoiceAccountsAgingReportJQuery();
				break;
			case 'orderAccountsAging':
				$reportData = printOrderAccountsAgingReport();
				$reportJQuery = returnOrderAccountsAgingReportJQuery();
				break;
			case 'clientDetails':
				$reportData = printClientDetailsReport();
				$reportJQuery = returnClientDetailsReportJQuery();
				break;
			case 'invoicePayments':
				$reportData = printInvoicePaymentsReport();
				$reportJQuery = returnInvoicePaymentsReportJQuery();
				break;
			case 'invoices':
				$reportData = printInvoicesReport();
				$reportJQuery = returnInvoicesReportJQuery();
				break;
			case 'serialNumbers':
				$reportData = printSerialNumbersReport();
				$reportJQuery = returnSerialNumbersReportJQuery();
				break;
			case 'userDetails':
				$reportData = printUserDetailsReport();
				$reportJQuery = returnUserDetailsReportJQuery();
				break;
			default:
				$reportData = "You did not specify a proper report, please try again.";
				break;
		}
		
		// Take and send the actual data to the page
		$otherVersionLink = ($actual_style == "printerFriendly") ? "<a href=\"" . $menuvar['VIEWREPORT'] . "&report=" . $actual_report . "\">Normal Version</a>" : "<a href=\"" . $menuvar['VIEWREPORT'] . "&report=" . $actual_report . "&style=printerFriendly\">Printer Friendly Version</a>";
		
		$page_content .= "
			<div class=\"roundedBox\">
				<span class=\"versionLinkContainer\"> " . $otherVersionLink . "</span>
				" . $reportData . "
			</div>";
		
		// Handle our JQuery needs
		$JQueryReadyScripts = $reportJQuery;
	}
	else {		
		//==================================================
		// Print out our reports table
		//==================================================
		
		$page_content .= "
						<div id=\"tabs\">
							<ul>
								<li><a href=\"#builtinReports\"><span>Built-in Reports</span></a></li>
								<li><a href=\"#runACustomReport\"><span>Run a Custom Report</span></a></li>
							</ul>
							<div id=\"builtinReports\">
								<ul>
									<li><a href=\"" . $menuvar['VIEWREPORT'] . "&report=invoiceAccountsAging\">Invoice Accounts Aging</a></li>
									<li><a href=\"" . $menuvar['VIEWREPORT'] . "&report=orderAccountsAging\">Order Accounts Aging</a></li>
									<li><a href=\"" . $menuvar['VIEWREPORT'] . "&report=serialNumbers\">Serial Numbers</a></li>
									<li><a href=\"" . $menuvar['VIEWREPORT'] . "&report=clientDetails\">Client Details</a></li>
									<li><a href=\"" . $menuvar['VIEWREPORT'] . "&report=userDetails\">User Details</a></li>
									<li><a href=\"" . $menuvar['VIEWREPORT'] . "&report=invoices\">Invoices</a></li>
									<li><a href=\"" . $menuvar['VIEWREPORT'] . "&report=invoicePayments\">Invoice Payments</a></li>
								</ul>
							</div>
							<div id=\"runACustomReport\">
								
							</div>
						</div>";
				
		// Handle our JQuery needs
		$JQueryReadyScripts = "$(\"#tabs\").tabs();";
	}
	
	$page->setTemplateVar("PageContent", $page_content);
	$page->setTemplateVar("JQueryReadyScript", $JQueryReadyScripts);
}
else {
	$page->setTemplateVar("PageContent", "\nYou Are Not Authorized To Access This Area. Please Refrain From Trying To Do So Again.");
}
?>