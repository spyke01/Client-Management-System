<? 
/***************************************************************************
 *                               categories.php
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
	// Print out our categories table
	//==================================================
	$content = "
				<div id=\"updateMe\">
					" . printCategoriesTable() . "
				</div>
		<script language = \"Javascript\">
		
		function ValidateForm(theForm){
			var name=document.newCatForm.newcatname
			
			if ((name.value==null)||(name.value==\"\")){
				alert(\"Please enter the new categories name.\")
				name.focus()
				return false
			}
			new Ajax.Updater('updateMe', 'ajax.php?action=postcat', {onComplete:function(){ new Effect.Highlight('newCat');},asynchronous:true, parameters:Form.serialize(theForm), evalScripts:true}); 
			name.value = '';
			return false;
		 }
		</script>";	

	$page->setTemplateVar("PageContent", $content);
}
else {
	$page->setTemplateVar("PageContent", "\nYou Are Not Authorized To Access This Area. Please Refrain From Trying To Do So Again.");
}
?>