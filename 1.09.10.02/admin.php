<? 
/***************************************************************************
 *                               admin.php
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

if ($_SESSION['user_level'] == ADMIN || $_SESSION['user_level'] == MOD) {
	$page->setTemplateVar('PageContent', "Welcome back " . $_SESSION['username'] . ". <br />The Fast Track Sites Client Management System (FTSCLMS) Admin Panel, please choose which section you want to access using the menu at the left..<br /><br />" .  version_functions(yes));
}
else {
	$page->setTemplateVar('PageContent', "\nYou Are Not Authorized To Access This Area. Please Refrain From Trying To Do So Again.");
}
?>