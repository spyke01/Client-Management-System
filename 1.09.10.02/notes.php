<? 
/***************************************************************************
 *                               notes.php
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
	// Print out our new notes form
	//==================================================
	$content = "
				<div id=\"response\">
				</div>
				<form name=\"newNoteForm\" id=\"newNoteForm\" action=\"" . $menuvar['NOTES'] . "\" method=\"post\" onSubmit=\"ValidateForm(this); return false;\">
					<table class=\"contentBox\" cellpadding=\"1\" cellspacing=\"1\" width=\"100%\">
						<tr>
							<td class=\"title1\" colspan=\"2\">New Note</td>
						</tr>							
						<tr class=\"row1\">
							<td><strong>Client: </strong></td>
								<td>
									" . createDropdown("clients", "client_id", "", "") . "
								</td>
						</tr>						
						<tr class=\"row2\">
							<td><strong>Note: </strong></td>
								<td>
									<textarea name=\"note\" rows=\"10\" cols=\"50\" class=\"required\"></textarea>
								</td>
						</tr>	
						<tr class=\"row1\">
							<td><strong>Urgency: </strong></td>
								<td>
									" . createDropdown("urgency", "urgency", "", "") . "
								</td>
						</tr>							
					</table>									
					<br />
					<input type=\"submit\" class=\"button\" value=\"Make the Note!\" />
				</form>
			<script type=\"text/javascript\">
				var valid = new Validation('newNoteForm', {immediate : true, useTitles:true, onFormValidate : ValidateForm});
			
				function ValidateForm(result, formRef){		
					if (result == true) {						
						new Ajax.Updater('response', 'ajax.php?action=postnote', {onComplete:function(){ new Effect.Highlight('response');},asynchronous:true, parameters:Form.serialize(document.newNoteForm), evalScripts:true}); 
			
						var note=document.newNoteForm.note
						note.value = '';
					}
					return false;
				}
			</script>";	

	$page->setTemplateVar("PageContent", $content);
}
else {
	$page->setTemplateVar("PageContent", "\nYou Are Not Authorized To Access This Area. Please Refrain From Trying To Do So Again.");
}
?>