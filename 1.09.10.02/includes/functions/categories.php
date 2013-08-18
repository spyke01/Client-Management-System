<?php 
/***************************************************************************
 *                               categories.php
 *                            -------------------
 *   begin                : Saturday, Sept 24, 2005
 *   copyright            : (C) 2005 Paden Clayton - Fast Track Sites
 *   email                : sales@fasttacksites.com
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
 	//=================================================
	// Returns Category Name from the ID
	//=================================================
	function getCatNameByID($catID) {
		global $DBTABLEPREFIX, $menuvar, $clms_config;
		$catName = "";
		
		$sql = "SELECT cat_name FROM `" . $DBTABLEPREFIX . "categories` WHERE cat_id='" . $catID . "' LIMIT 1";
		$result = mysql_query($sql);
				
		if ($result && mysql_num_rows($result) > 0) {
			while ($row = mysql_fetch_array($result)) {	
				$catName = $row['cat_name'];
			}
			mysql_free_result($result);
		}
		
		return $catName;
	}
 
	//=================================================
	// Print the Categories Table
	//=================================================
	function printCategoriesTable() {
		global $DBTABLEPREFIX, $menuvar, $clms_config;
		
		$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "categories` ORDER BY cat_name ASC";
		$result = mysql_query($sql);
			
		$x = 1; //reset the variable we use for our row colors	
			
		$content = "
						<table class=\"contentBox\" cellpadding=\"1\" cellspacing=\"1\" width=\"100%\">
							<tr>
								<td class=\"title1\" colspan=\"2\">
									<div style=\"float: right;\">
										<form name=\"newCatForm\" action=\"" . $menuvar['CATEGORIES'] . "\" method=\"post\" onSubmit=\"ValidateForm(this); return false;\">
											<input type=\"text\" name=\"newcatname\" />
											<input type=\"image\" src=\"themes/" . $clms_config['ftsclms_theme'] . "/icons/add.png\" />
										</form>
									</div>
									Categories
								</td>
							</tr>							
							<tr class=\"title2\">
								<td><strong>Name</strong></td><td></td>
							</tr>";
		$catids = array();
		if (!$result || mysql_num_rows($result) == 0) { // No cats yet!
			$content .= "
							<tr class=\"greenRow\">
								<td colspan=\"2\">There are no categories in the database.</td>
							</tr>";	
		}
		else {	 // Print all our cats								
			while ($row = mysql_fetch_array($result)) {
				
				$content .=	"					
									<tr id=\"" . $row['cat_id'] . "\" class=\"row" . $x . "\">
										<td><div id=\"" . $row['cat_id'] . "_text\">" . $row['cat_name'] . "</div></td>
										<td>
											<center><a style=\"cursor: pointer; cursor: hand;\" onclick=\"new Ajax.Request('ajax.php?action=deleteitem&table=categories&id=" . $row['cat_id'] . "', {asynchronous:true, onSuccess:function(){ new Effect.SlideUp('" . $row['cat_id'] . "');}});\"><img src=\"themes/" . $clms_config['ftsclms_theme'] . "/icons/delete.png\" alt=\"Delete Category\" /></a></center>
										</td>
									</tr>";
									
				$catids[$row['cat_id']] = $row['cat_name'];					
				$x = ($x==2) ? 1 : 2;
			}
		}
		mysql_free_result($result);
			
		
		$content .=	"					
						</table>
						<script type=\"text/javascript\">";
		
		$x = 1; //reset the variable we use for our highlight colors
		foreach($catids as $key => $value) {
			$highlightColors = ($x == 1) ? "highlightcolor:'#CBD5DC',highlightendcolor:'#5194B6'" : "highlightcolor:'#5194B6',highlightendcolor:'#CBD5DC'";
			
			$content .= "\n							new Ajax.InPlaceEditor('" . $key . "_text', 'ajax.php?action=updateitem&table=categories&item=name&id=" . $key . "', {rows:1,cols:50," . $highlightColors . ",loadTextURL:'ajax.php?action=getitem&table=categories&item=name&id=" . $key . "'});";
			$x = ($x==2) ? 1 : 2;
		}
		
		$content .= "
						</script>";
		
		return $content;
	}

?>