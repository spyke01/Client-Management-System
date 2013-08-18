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
 *
 * This program is licensed under the Fast Track Sites Program license 
 * located inside the license.txt file included with this program. This is a 
 * legally binding license, and is protected by all applicable laws, by 
 * editing this page you fall subject to these licensing terms.
 *
 ***************************************************************************/

if ($_SESSION['user_level'] == ADMIN) {
	//==================================================
	// Print out our orders table
	//==================================================
	$content = "
				<div id=\"updateMe\">
					" . printOrdersTable() . "
				</div>";
	
	//==================================================
	// Print out our new order form
	//==================================================
	$content .= "
				<br /><br />
				<form name=\"newOrderForm\" id=\"newOrderForm\" action=\"" . $PHP_SELF . "\" method=\"post\" onSubmit=\"ValidateForm(this); return false;\">
					<table class=\"contentBox\" cellpadding=\"1\" cellspacing=\"1\" width=\"100%\">
						<tr>
							<td class=\"title1\" colspan=\"2\">New Order</td>
						</tr>						
						<tr class=\"row1\">
							<td><strong>Order Number: </strong></td>
							<td><input type=\"text\" name=\"number\" size=\"60\" class=\"required\" /></td>
						</tr>					
						<tr class=\"row2\">
							<td><strong>Client: </strong></td>
							<td>
								" . createDropdown("clients", "client_id", "", "") . "
							</td>
						</tr>						
						<tr class=\"row1\">
							<td><strong>Date Ordered: </strong></td>
							<td><input type=\"text\" name=\"date_ordered\" id=\"date_ordered\" size=\"60\" class=\"required\" /><img src=\"themes/" . $clms_config['ftsclms_theme'] . "/icons/calendar.gif\" onclick=\"showChooser(this, 'date_ordered', 'date_orderedChooser', " . makeCurrentYear(time()) . ", " . makeXYearsFromCurrentYear(time(), 20) . ", Date.patterns.ShortDatePattern, false);\" /><div id=\"date_orderedChooser\" class=\"dateChooser select-free\" style=\"display: none; visibility: hidden; width: 160px;\"></div></td>
						</tr>						
						<tr class=\"row2\">
							<td><strong>Date Shipped: </strong></td>
							<td><input type=\"text\" name=\"date_shipped\" id=\"date_shipped\" size=\"60\" /><img src=\"themes/" . $clms_config['ftsclms_theme'] . "/icons/calendar.gif\" onclick=\"showChooser(this, 'date_shipped', 'date_shippedChooser', " . makeCurrentYear(time()) . ", " . makeXYearsFromCurrentYear(time(), 20) . ", Date.patterns.ShortDatePattern, false);\" /><div id=\"date_shippedChooser\" class=\"dateChooser select-free\" style=\"display: none; visibility: hidden; width: 160px;\"></div></td>
						</tr>						
						<tr class=\"row1\">
							<td><strong>Total: </strong></td>
							<td><input type=\"text\" name=\"total\" size=\"60\" class=\"required\" /></td>
						</tr>						
						<tr class=\"row2\">
							<td><strong>Tracking Number: </strong></td>
							<td><input type=\"text\" name=\"tracking_no\" size=\"60\" /></td>
						</tr>						
						<tr class=\"row1\">
							<td><strong>Shipped by: </strong></td>
							<td><input type=\"text\" name=\"shipped_by\" size=\"60\" /></td>
						</tr>							
					</table>									
					<br />
					<input type=\"submit\" class=\"button\" value=\"Make the Order!\" />
				</form>
			<script type=\"text/javascript\">
				var valid = new Validation('newOrderForm', {immediate : true, useTitles:true, onFormValidate : ValidateForm});
			
				function ValidateForm(result, formRef){		
					if (result == true) {			
						new Ajax.Updater('updateMe', 'ajax.php?action=postorder', {asynchronous:true, parameters:Form.serialize(document.newOrderForm), evalScripts:true}); 
			
						var id=document.newOrderForm.id
						var date_ordered=document.newOrderForm.date_ordered
						var date_shipped=document.newOrderForm.date_shipped
						var total=document.newOrderForm.total
						var tracking_no=document.newOrderForm.tracking_no
						var shipped_by=document.newOrderForm.shipped_by
						id.value = '';
						date_ordered.value = '';
						date_shipped.value = '';
						total.value = '';
						tracking_no.value = '';
						shipped_by.value = '';
					}
					return false;
				}
			</script>";			

	$page->setTemplateVar('PageContent', $content);
}
else {
	$page->setTemplateVar('PageContent', "\nYou Are Not Authorized To Access This Area. Please Refrain From Trying To Do So Again.");
}
?>