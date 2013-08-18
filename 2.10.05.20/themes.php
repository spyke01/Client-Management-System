<? 
/***************************************************************************
 *                               themes.php
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
	// Handle editing, adding, and deleting of pages
	//==================================================	
	if (isset($_POST['submit'])) {
		$sql = "UPDATE `" . DBTABLEPREFIX . "config` SET value ='" . keepsafe($_POST['theme']) . "' WHERE name ='ftsclms_theme' LIMIT 1";
		$result = mysql_query($sql);
		
		if ($result) {
			$page_content .= "
						<div class=\"roundedBox\">
							Your theme has been successfully changed.
							<meta http-equiv=\"refresh\" content=\"5;url=" . $menuvar['THEMES'] . "\">
						</div>";	
		}
		else {
			$page_content .= "
						<div class=\"roundedBox\">
							There was an error while attempting to change your theme.
							<meta http-equiv=\"refresh\" content=\"5;url=" . $menuvar['THEMES'] . "\">
						</div>";
		}		
	}		
	else {	
		//==================================================
		// Get the current theme
		//==================================================
		$sql = "SELECT value FROM `" . DBTABLEPREFIX . "config` WHERE name ='ftsclms_theme' LIMIT 1";
		$result = mysql_query($sql);
		
		if ($result && mysql_num_rows($result) != 0) {
			$row = mysql_fetch_array($result);
			$currenttheme = $row['value'];
			mysql_free_result($result);
		}
				
		//==================================================
		// Get and store our available themes
		//==================================================		
		$stylepath = "themes";
		if($dir = opendir($stylepath)){					
			$sub_dir_names = array();
			while (false !== ($file = readdir($dir))) {				
				if ($file != "." && $file != ".." && $file != "installer" && $file != "jquery" && is_dir($stylepath . '/' . $file)) {
					$sub_dir_names[$file] .= '';	
				}
			}			
		}		
		ksort($sub_dir_names); //sort by name			
		
		//==================================================
		// Build our table
		//==================================================
		// Create our new table
		$table = new tableClass(1, 1, 1, "contentBox tablesorter", "themesTable");
		
		// Create table title
		$table->addNewRow(array(array("data" => "Available Themes", "colspan" => "4")), "", "title1", "thead");
		
		// Create column headers
		$table->addNewRow(
			array(
				array("type" => "th", "data" => "Preview"),
				array("type" => "th", "data" => "Name"),
				array("type" => "th", "data" => "Author"),
				array("type" => "th", "data" => "Active")
			), "", "title2", "thead"
		);
		
		// Add our data
		if (empty($sub_dir_names)) {
			$table->addNewRow(array(array("data" => "There are no themes in the system.", "colspan" => "4")), "themesTableTableDefaultRow", "greenRow");
		}
		else {
			$x = 1; //reset the variable we use for our row colors
			
			foreach($sub_dir_names as $name => $nothing) {
				$preview = (is_file($stylepath . '/' . $name . '/preview.png')) ? $stylepath . "/" . $name . "/preview.png" : "images/nopreview.png"; // Thanks Joe!		
				$THEME_NAME = "N/A"; // Reset variable
				$THEME_AUTHOR = "N/A"; // Reset variable
			
				if (file_exists($stylepath . '/' . $name . '/themedetails.php')) { include ($stylepath . '/' . $name . '/themedetails.php'); }
			
				$table->addNewRow(array(
					array("data" => "<img src=\"" . $preview . "\" alt=\"Preview\" />", "class" => "center"),
					array("data" => $THEME_NAME),
					array("data" => $THEME_AUTHOR),
					array("data" => "<input name=\"theme\" type=\"radio\" value=\"" . $name . "\"" . testChecked($name, $currenttheme) . " />", "class" => "center")
				), "", "row" . $x);
				
				$x = ($x == 2) ? 1 : 2;
			}
		}
		
		//==================================================
		// Print our table
		//==================================================
		$page_content .= "
				<div class=\"roundedBox\">
					<form name=\"themechanger\" action=\"" . $menuvar['THEMES'] . "\" method=\"post\">
						" . $table->returnTableHTML() . "
					<br />
					<center><input name=\"submit\" class=\"button\" type=\"submit\" value=\"Change It!\" /></center>
				</form>
			</div>";
	}
	$page->setTemplateVar("PageContent", $page_content);
	$page->setTemplateVar("JQueryReadyScript", $JQueryReadyScripts);
}
else {
	$page->setTemplateVar('PageContent', "\nYou Are Not Authorized To Access This Area. Please Refrain From Trying To Do So Again.");
}
?>