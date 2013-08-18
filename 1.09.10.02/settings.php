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
 *
 * This program is licensed under the Fast Track Sites Program license 
 * located inside the license.txt file included with this program. This is a 
 * legally binding license, and is protected by all applicable laws, by 
 * editing this page you fall subject to these licensing terms.
 *
 ***************************************************************************/

if ($_SESSION['user_level'] == ADMIN) {
	
	if (isset($_POST['submit'])) {		
		foreach($_POST as $name => $value) {
			if ($name != 'submit'){			
				if ($name == "ftsclms_active") {
					if ($value == "") { $value = 0; }
					else { $value = 1; }	
				}
				$sql = "UPDATE `" . $DBTABLEPREFIX . "config` SET config_value = '" . $value . "' WHERE config_name = '" . $name . "'";
				$result = mysql_query($sql);
			}
		}		
		
		// Handle checkboxes, unchecked boxes are not posted so we check for this and mark them in the DB as such
		if (!isset($_POST['ftsclms_active'])) {
			$sql = "UPDATE `" . $DBTABLEPREFIX . "config` SET config_value = '0' WHERE config_name = 'ftsclms_active'";
			$result = mysql_query($sql);
		}
		
		unset($_POST['submit']);
	}
	
	$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "config`";
	$result = mysql_query($sql);
	
	// This is used to let us get the actual items and not just config_name and config_value
	while ($row = mysql_fetch_array($result)) {
		extract($row);
		$config_name = $row['config_name'];
		$config_value = $row['config_value'];
		$current_config[$config_name] = $config_value;
	}	
	extract($current_config);
		
	// Give our template the values
	$content = "<form action=\"" . $menuvar['SETTINGS'] . "\" method=\"post\" target=\"_top\">
					<table class=\"contentBox\" cellpadding=\"1\" cellspacing=\"1\" width=\"100%\">
						<tr><td class=\"title1\" colspan=\"2\">Client Management System Settings</td></tr>
						<tr class=\"row1\">
							<td><strong>Active: </strong></td>
							<td>
								<input name=\"ftsclms_active\" type=\"checkbox\" value=\"1\"". testChecked($ftsclms_active, ACTIVE) . " />
							</td>
						</tr>
						<tr class=\"row2\">
							<td><strong>Inactive Message:</strong></td>
							<td>
								<textarea name=\"ftsclms_inactive_msg\" cols=\"45\" rows=\"10\">" . $current_config['ftsclms_inactive_msg'] . "</textarea>
							</td>
						</tr>
						<tr class=\"row1\">
							<td><strong>System Time Zone: </strong></td>
							<td>
								<input type=\"text\" name=\"ftsclms_time_zone\" size=\"60\" value=\"" . $ftsclms_time_zone . "\" />
							</td>
						</tr>
						<tr class=\"row2\">
							<td><strong>System Currency: </strong></td>
							<td>
								" . createDropdown("currencies", "ftsclms_currency_type", $ftsclms_currency_type, "") . "
							</td>
						</tr>
					</table>
					<br /><br />
					<span class=\"center\"><input type=\"submit\" name=\"submit\" class=\"button\" value=\"Update Settings\" /></span>
				</form>";

	$page->setTemplateVar('PageContent', $content);
}
else {
	$page->setTemplateVar('PageContent', "\nYou Are Not Authorized To Access This Area. Please Refrain From Trying To Do So Again.");
}
?>