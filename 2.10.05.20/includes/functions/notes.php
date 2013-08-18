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

	//=================================================
	// Print the Client Notes Table
	//=================================================
	function printNotesTable($clientID = "", $allowModification = 1) {
		global $menuvar, $clms_config;
		
		$extraSQL = ($clientID != "") ? " WHERE client_id = '" . $clientID . "'" : "";
		
		$sql = "SELECT * FROM `" . DBTABLEPREFIX . "notes`" . $extraSQL . " ORDER BY datetimestamp DESC";
		$result = mysql_query($sql);
		
		//echo $sql;
		
		// Create our new table
		$table = new tableClass(1, 1, 1, "contentBox tablesorter", "notesTable");
		
		// Create table title
		$table->addNewRow(array(array("data" => "Notes", "colspan" => "3")), "", "title1", "thead");
		
		// Create column headers
		$headerNameArray = array(
				array("type" => "th", "data" => "Note"),
				array("type" => "th", "data" => "Noted On")
		);
		
		if ($allowModification == 1) array_push($headerNameArray, array("type" => "th", "data" => ""));
		
		$table->addNewRow($headerNameArray, "", "title2", "thead");
							
		// Add our data
		if (!$result || mysql_num_rows($result) == 0) {
			$table->addNewRow(array(array("data" => "There are no notes for this client.", "colspan" => "3")), "notesTableDefaultRow", "greenRow");
		}
		else {
			while ($row = mysql_fetch_array($result)) {
				$rowColor = ($row['urgency'] != LOW) ? "redRow" : "greenRow";
				$rowColor = ($row['urgency'] != HIGH && $rowColor == "redRow") ? "yellowRow" : $rowColor;
				
				// Build our final column array
				$rowDataArray = array(
					array("data" => "<div id=\"" . $row['id'] . "_note\">" . bbcode($row['note']) . "</div>"),
					array("data" => makeDateTime($row['datetimestamp']))
				);
				
				if ($allowModification == 1) array_push($rowDataArray, array("data" => createDeleteLinkWithImage($row['id'], $row['id'] . "_row", "notes", "note"), "class" => "center"));
						
				$table->addNewRow($rowDataArray, $row['id'] . "_row", $rowColor);
			}
			mysql_free_result($result);
		}
		
		// Return the table's HTML
		return $table->returnTableHTML() . "
				<div id=\"notesTableUpdateNotice\"></div>";
	}
	
	//=================================================
	// Returns the JQuery functions used to allow 
	// in-place editing and table sorting
	//=================================================
	function returnNotesTableJQuery($clientID = "", $allowModification = 1) {
		global $menuvar, $clms_config;
		
		$extraJQueryReadyScripts = ($allowModification == 1) ? " headers: { 2: { sorter: false } }" : "";
		
		$JQueryReadyScripts = "
				$('#notesTable').tablesorter({
					" . $extraJQueryReadyScripts . "
				});";
				
		// Only allow modification of rows if we have permission
		if ($allowModification == 1) {		
			$extraSQL = ($clientID != "") ? " WHERE client_id = '" . $clientID . "'" : "";
			
			$sql = "SELECT id FROM `" . DBTABLEPREFIX . "notes`" . $extraSQL;
			$result = mysql_query($sql);
	
			if ($result && mysql_num_rows($result) > 0) {
				while ($row = mysql_fetch_array($result)) {	
					$JQueryReadyScripts .= returnEditInPlaceJQuery($row['id'], "note", "notes", "textarea", "loadurl: 'ajax.php?action=getitem&table=notes&item=note&id=" . $row['id'] . "'");
				}
				mysql_free_result($result);
			}
		}
		
		return $JQueryReadyScripts;
	}
	
	//=================================================
	// Create a form to add new notes
	//
	// Used so that we can display it in many places
	//=================================================
	function printNewNoteForm($clientID = "") {
		global $menuvar, $clms_config;

		$clientIDSelect = ($clientID != "") ? "<input type=\"hidden\" name=\"client_id\" value=\"" . $clientID . "\" />" : "<div><label for=\"client_id\">Client <span>- Required</span></label> " . createDropdown("clients", "client_id", "", "") . "</div>";
		
		$content .= "
			<div id=\"newNoteResponse\">
			</div>
			<form name=\"newNoteForm\" id=\"newNoteForm\" action=\"" . $menuvar['NOTES'] . "\" method=\"post\" class=\"inputForm\" onsubmit=\"return false;\">
				<fieldset>
					<legend>Add a Note</legend>
					" . $clientIDSelect . "
					<div><label for=\"note\">Note <span>- Required</span></label> <textarea name=\"note\" id=\"note\" rows=\"10\" cols=\"50\" class=\"required\"></textarea></div>
					<div><label for=\"urgency\">Urgency <span>- Required</span></label> " . createDropdown("urgency", "urgency", "", "") . "</div>
					<div class=\"center\"><input type=\"submit\" class=\"button\" value=\"Make the Note!\" /></div>
				</fieldset>
			</form>";
			
		return $content;
	}
	
	//=================================================
	// Returns the JQuery functions used to run the 
	// new note form
	//=================================================
	function returnNewNoteFormJQuery($reprintTable = 0, $allowModification = 1) {
		$extraJQuery = ($reprintTable == 0) ? "
  						// Update the proper div with the returned data
						$('#newNoteResponse').html('" . progressSpinnerHTML() . "');
						$('#newNoteResponse').html(data);
						$('#newNoteResponse').effect('highlight',{},500);" 
						: "
						// Clear the default row
						$('#notesTableDefaultRow').remove();
  						// Update the table with the new row
						$('#notesTable > tbody:last').append(data);
						$('#notesTableUpdateNotice').html('" . tableUpdateNoticeHTML() . "');
						// Show a success message
						$('#newNoteResponse').html('" . progressSpinnerHTML() . "');
						$('#newNoteResponse').html(returnSuccessMessage('note'));";
						
		$JQueryReadyScripts = "
			var v = jQuery(\"#newNoteForm\").validate({
				errorElement: \"div\",
				errorClass: \"validation-advice\",
				submitHandler: function(form) {			
					jQuery.get('ajax.php?action=createNote&reprinttable=" . $reprintTable . "&showButtons=" . $allowModification . "', $('#newNoteForm').serialize(), function(data) {
  						" . $extraJQuery . "
						// Clear the form
						$('#note').val = '';
					});
				}
			});";
		
		return $JQueryReadyScripts;
	}
?>