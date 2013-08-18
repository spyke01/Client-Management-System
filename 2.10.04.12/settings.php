<? 
/***************************************************************************
 *                               settings.php
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
	// Handle updating system variables in the database
	if (isset($_POST['submit'])) {		
		foreach($_POST as $name => $value) {
			if ($name != "submit"){			
				if ($name == "ftsclms_active") {
					if ($value == "") { $value = 0; }
					else { $value = 1; }	
				}
				$sql = "UPDATE `" . DBTABLEPREFIX . "config` SET value = '" . keeptasafe($value) . "' WHERE name = '" . keeptasafe($name) . "'";
				$result = mysql_query($sql);
			}
		}		
		
		// Handle checkboxes, unchecked boxes are not posted so we check for this and mark them in the DB as such
		if (!isset($_POST['ftsclms_active'])) {
			$sql = "UPDATE `" . DBTABLEPREFIX . "config` SET value = '0' WHERE name = 'ftsclms_active'";
			$result = mysql_query($sql);
		}
		
		unset($_POST['submit']);
	}
	
	// Pull the curent variables since we can't trust oir clms_config to carry the latest
	$current_config = array();
	
	$sql = "SELECT * FROM `" . DBTABLEPREFIX . "config`";
	$result = mysql_query($sql);
	
	// This is used to let us get the actual items and not just name and value
	if ($result && mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_array($result)) {
			$name = $row['name'];
			$value = $row['value'];
			$current_config[$name] = $value;
		}
		mysql_free_result($result);
	}
		
	// Give our template the values
	$page_content .= "
				<form action=\"" . $menuvar['SETTINGS'] . "\" method=\"post\" class=\"inputForm\">
					<div id=\"tabs\">
						<ul>
							<li><a href=\"#systemSettings\"><span>System Settings</span></a></li>
							<li><a href=\"#invoiceSettings\"><span>Invoice Settings</span></a></li>
						</ul>
						<div id=\"systemSettings\">
							<fieldset>
								<legend>System Settings</legend>
								<div><label for=\"ftsclms_active\">Active </label> <input name=\"ftsclms_active\" type=\"checkbox\" value=\"1\"". testChecked($current_config['ftsclms_active'], ACTIVE) . " /></div>
								<div><label for=\"ftsclms_inactive_msg\">Inactive Message </label> <textarea name=\"ftsclms_inactive_msg\" cols=\"45\" rows=\"10\">" . $current_config['ftsclms_inactive_msg'] . "</textarea></div>
								<div><label for=\"ftsclms_time_zone\">System Time Zone </label> " . createDropdown("timezone", "ftsclms_time_zone", $current_config['ftsclms_time_zone'], "") . "</div>
								<div><label for=\"ftsclms_currency_type\">System Currency </label> " . createDropdown("currencies", "ftsclms_currency_type", $current_config['ftsclms_currency_type'], "") . "</div>
								<div><label for=\"ftsclms_sales_tax\">Sales Tax </label> <input type=\"text\" name=\"ftsclms_sales_tax\" id=\"ftsclms_sales_tax\" size=\"60\" value=\"" . $current_config['ftsclms_sales_tax'] . "\" /></div>
							</fieldset>
						</div>
						<div id=\"invoiceSettings\">
							<fieldset>
								<legend>Invoice Settings</legend>
								<div><label for=\"ftsclms_invoice_company_name\">Company Name </label> <input type=\"text\" name=\"ftsclms_invoice_company_name\" id=\"ftsclms_invoice_company_name\" size=\"60\" value=\"" . $current_config['ftsclms_invoice_company_name'] . "\" /></div>
								<div><label for=\"ftsclms_invoice_address\">Address </label> <textarea name=\"ftsclms_invoice_address\" cols=\"45\" rows=\"10\">" . $current_config['ftsclms_invoice_address'] . "</textarea></div>
								<div><label for=\"ftsclms_invoice_city\">City </label> <input type=\"text\" name=\"ftsclms_invoice_city\" id=\"ftsclms_invoice_city\" size=\"60\" value=\"" . $current_config['ftsclms_invoice_city'] . "\" /></div>
								<div><label for=\"ftsclms_invoice_state\">State </label> <input type=\"text\" name=\"ftsclms_invoice_state\" id=\"ftsclms_invoice_state\" size=\"60\" value=\"" . $current_config['ftsclms_invoice_state'] . "\" /></div>
								<div><label for=\"ftsclms_invoice_zip\">Zip </label> <input type=\"text\" name=\"ftsclms_invoice_zip\" id=\"ftsclms_invoice_zip\" size=\"60\" value=\"" . $current_config['ftsclms_invoice_zip'] . "\" /></div>
								<div><label for=\"ftsclms_invoice_phone_number\">Phone Number </label> <input type=\"text\" name=\"ftsclms_invoice_phone_number\" id=\"ftsclms_invoice_phone_number\" size=\"60\" value=\"" . $current_config['ftsclms_invoice_phone_number'] . "\" /></div>
								<div><label for=\"ftsclms_invoice_fax\">Fax </label> <input type=\"text\" name=\"ftsclms_invoice_fax\" id=\"ftsclms_invoice_fax\" size=\"60\" value=\"" . $current_config['ftsclms_invoice_fax'] . "\" /></div>
								<div><label for=\"ftsclms_invoice_email_address\">Email Address </label> <input type=\"text\" name=\"ftsclms_invoice_email_address\" id=\"ftsclms_invoice_email_address\" size=\"60\" value=\"" . $current_config['ftsclms_invoice_email_address'] . "\" /></div>
								<div><label for=\"ftsclms_invoice_website\">Website </label> <input type=\"text\" name=\"ftsclms_invoice_website\" id=\"ftsclms_invoice_website\" size=\"60\" value=\"" . $current_config['ftsclms_invoice_website'] . "\" /></div>
							</fieldset>
						</div>
					</div>
					<div class=\"clear center\"><input type=\"submit\" name=\"submit\" class=\"button\" value=\"Update Settings\" /></div>
				</form>";
				
	$JQueryReadyScripts .= "$(\"#tabs\").tabs();";

	$page->setTemplateVar("PageContent", $page_content);
	$page->setTemplateVar("JQueryReadyScript", $JQueryReadyScripts);
}
else {
	$page->setTemplateVar('PageContent', "\nYou Are Not Authorized To Access This Area. Please Refrain From Trying To Do So Again.");
}
?>