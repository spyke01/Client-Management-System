<?php 
/***************************************************************************
 *                               notes.php
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
	// Print the Client Notes Table
	//=================================================
	function printClientNotesTable($clientID = "") {
		global $DBTABLEPREFIX, $menuvar, $clms_config;
		
		$extraSQL = ($clientID != "") ? " WHERE notes_client_id = '" . $clientID . "'" : "";
		
		$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "notes`" . $extraSQL . " ORDER BY notes_datetimestamp DESC";
		$result = mysql_query($sql);
		
		//echo $sql;
			
		$content = "
						<table class=\"contentBox\" cellpadding=\"1\" cellspacing=\"1\" width=\"100%\">
							<tr>
								<td class=\"title1\" colspan=\"6\">
									Notes
								</td>
							</tr>						
							<tr class=\"title2\">
								<td><strong>Note</strong></td><td><strong>Noted On</strong></td><td></td>
							</tr>";
							
		$notesids = array();
		if (!$result || mysql_num_rows($result) == 0) { // No appointments yet!
			$content .= "					
							<tr class=\"greenRow\">
								<td colspan=\"6\">There are no notes for this client.</td>
							</tr>";	
		}
		else {	 // Print all our appointments								
			while ($row = mysql_fetch_array($result)) {							
				$rowColor = ($row['notes_urgency'] != LOW) ? "redRow" : "greenRow";
				$rowColor = ($row['notes_urgency'] != HIGH && $rowColor == "redRow") ? "yellowRow" : $rowColor;
					
				$content .= "							
								<tr id=\"" . $row['notes_id'] . "_row\" class=\"" . $rowColor . "\">
									<td><div id=\"" . $row['notes_id'] . "_text\">" . bbcode($row['notes_note']) . "</div></td>
									<td>" . makeDateTime($row['notes_datetimestamp']) . "</td>
									<td><span class=\"center\"><a style=\"cursor: pointer; cursor: hand;\" onclick=\"ajaxDeleteNotifier('" . $row['notes_id'] . "notesSpinner', 'ajax.php?action=deleteitem&table=notes&id=" . $row['notes_id'] . "', 'note', '" . $row['notes_id'] . "_row');\"><img src=\"themes/" . $clms_config['ftsclms_theme'] . "/icons/delete.png\" alt=\"Delete Note\" /></a><span id=\"" . $row['notes_id'] . "notesSpinner\" style=\"display: none;\"><img src=\"themes/" . $clms_config['ftsclms_theme'] . "/icons/indicator.gif\" alt=\"spinner\" /></span></span></td>
								</tr>";		
						
				$notesids[$row['notes_id']] = "";
			}
		}
		mysql_free_result($result);
			
		
		$content .=	"					
						</table>
						<script type=\"text/javascript\">";
		
		$x = 1; //reset the variable we use for our highlight colors
		foreach($notesids as $key => $value) {
			$highlightColors = ($x == 1) ? "highlightcolor:'#CBD5DC',highlightendcolor:'#5194B6'" : "highlightcolor:'#5194B6',highlightendcolor:'#CBD5DC'";
		
			$content .= "						
							new Ajax.InPlaceEditor('" . $key . "_text', 'ajax.php?action=updateitem&table=notes&item=note&id=" . $key . "', {rows:10,cols:50," . $highlightColors . ",loadTextURL:'ajax.php?action=getitem&table=notes&item=note&id=" . $key . "',onComplete:function(){ new Ajax.Updater('" . $key . "_text', 'ajax.php?action=getitem&table=notes&item=note&id=" . $key . "')}});";
			
			$x = ($x==2) ? 1 : 2;
		}
		
		$content .= "
						</script>";
		
		return $content;
	}
?>