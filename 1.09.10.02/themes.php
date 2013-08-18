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
 *
 * This program is licensed under the Fast Track Sites Program license 
 * located inside the license.txt file included with this program. This is a 
 * legally binding license, and is protected by all applicable laws, by 
 * editing this page you fall subject to these licensing terms.
 *
 ***************************************************************************/

	//==================================================
	// Handle editing, adding, and deleting of pages
	//==================================================	
	if (isset($_POST['submit'])) {
		$sql = "UPDATE `" . $DBTABLEPREFIX . "config` SET config_value ='" . $_POST['theme'] . "' WHERE config_name ='ftsclms_theme' LIMIT 1";
		$result = mysql_query($sql);
		
		if ($result) {
			$content = "
						<center>Your theme has been successfully changed.</center>
						<meta http-equiv=\"refresh\" content=\"5;url=" . $menuvar['THEMES'] . "\">";	
		}
		else {
			$content = "
						<center>There was an error while attempting to change your theme.</center>
						<meta http-equiv=\"refresh\" content=\"5;url=" . $menuvar['THEMES'] . "\">";	
		}		
	}		
	else {	
		//==================================================
		// Get the current theme
		//==================================================
		$sql = "SELECT config_value FROM `" . $DBTABLEPREFIX . "config` WHERE config_name ='ftsclms_theme' LIMIT 1";
		$result = mysql_query($sql);
		
		if ($result && mysql_num_rows($result) != 0) {
			$row = mysql_fetch_array($result);
			$currenttheme = $row['config_value'];
		}
		
		$x = 1; //reset the variable we use for our row colors	
		
		//==================================================
		// Get and store our available themes
		//==================================================		
		$stylepath = "themes";
		if($dir = opendir($stylepath)){					
			$sub_dir_names = array();
			while (false !== ($file = readdir($dir))) {				
				if ($file != "." && $file != ".." && $file != "installer" && is_dir($stylepath . '/' . $file)) {
					$sub_dir_names[$file] .= '';	
				}
			}			
		}		
		ksort($sub_dir_names); //sort by name			
			
		//==================================================
		// Print our table
		//==================================================
		$content = "
					<form name=\"themechanger\" action=\"" . $menuvar['THEMES'] . "\" method=\"post\">
						<table class=\"contentBox\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\">
							<tr>
								<td class=\"title1\" colspan=\"4\">Available Themes</td>
							</tr>							
							<tr class=\"title2\">
								<td><strong>Preview</strong></td><td><strong>Name</strong></td><td><strong>Author</strong></td><td><strong>Active</strong></td>
							</tr>";			
			
		foreach($sub_dir_names as $name => $nothing) { 		
			$selected = ( $name == $currenttheme ) ? ' checked="checked"' : '';			
			$preview = (is_file($stylepath . '/' . $name . '/preview.jpg')) ? $stylepath . "/" . $name . "/preview.jpg" : "images/nopreview.jpg"; // Thanks Joe!		
			$THEME_NAME = "N/A"; // Reset variable
			$THEME_AUTHOR = "N/A"; // Reset variable
			
			if (file_exists($stylepath . '/' . $name . '/themedetails.php')) { include ($stylepath . '/' . $name . '/themedetails.php'); }
			
			$content .=			"<tr class=\"row" . $x . "\">
									<td width=\"20%\"><center><img src=\"" . $preview . "\" alt=\"Preview\" /></center></td>
									<td width=\"40%\">" . $THEME_NAME . "</td>
									<td width=\"30%\">" . $THEME_AUTHOR . "</td>
									<td width=\"10%\"><center><input name=\"theme\" type=\"radio\" value=\"" . $name . "\"" . $selected . " /></center></td>
								</tr>";
								
			$x = ($x==2) ? 1 : 2;					
		}		
		$content .=	"	</table>
					<br />
					<center><input name=\"submit\" class=\"button\" type=\"submit\" value=\"Change It!\" /></center>
				</form>";
	}
	$page->setTemplateVar('PageContent', $content);
?>