<?php 
/***************************************************************************
 *                               downloads.php
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
	// Print the Downloads Table
	//=================================================
	function printDownloadsTable($clientID = "", $allowModification = 1) {
		global $menuvar, $clms_config;
		
		$extraSQL = ($clientID != "") ? " AND c.id ='" . $clientID . "'" : "";
		
		$sql = "SELECT d.* FROM `" . DBTABLEPREFIX . "downloads` d, `" . DBTABLEPREFIX . "clients` c WHERE c.id = d.client_id" . $extraSQL . " ORDER BY c.last_name, d.name ASC";
		$result = mysql_query($sql);
			
		// Create our new table
		$table = new tableClass(1, 1, 1, "contentBox tablesorter", "downloadsTable");
		
		// Create table title
		$table->addNewRow(array(array("data" => "Downloads", "colspan" => "4")), "", "title1", "thead");
		
		// Create column headers
		$headerNameArray = array(
			array("type" => "th", "data" => "File"),
			array("type" => "th", "data" => "Serial Number"),
			array("type" => "th", "data" => "Uploaded On")
		);
		
		if ($allowModification == 1) array_push($headerNameArray, array("type" => "th", "data" => ""));
		
		$table->addNewRow($headerNameArray, "", "title2", "thead");
							
		// Add our data
		if (!$result || mysql_num_rows($result) == 0) {
			$table->addNewRow(array(array("data" => "There are no downloads for this client.", "colspan" => "4")), "downloadsTableDefaultRow", "greenRow");
		}
		else {
			while ($row = mysql_fetch_array($result)) {
				// Build our final column array
				$rowDataArray = array(
					array("data" => "<a href=\"" . $row['url'] . "\">" . $row['name'] . "</a>"),
					array("data" => "<div id=\"" . $row['id'] . "_serial_number\">" . $row['serial_number'] . "</div>"),
					array("data" => makeDateTime($row['datetimestamp']))
				);
				
				if ($allowModification == 1) array_push($rowDataArray, array("data" => createDeleteLinkWithImage($row['id'], $row['id'] . "_row", "downloads", "download"), "class" => "center"));
						
				$table->addNewRow($rowDataArray, $row['id'] . "_row", "");
			}
			mysql_free_result($result);
		}
		
		// Return the table's HTML
		return $table->returnTableHTML() . "
				<div id=\"downloadsTableUpdateNotice\"></div>";
	}
	
	//=================================================
	// Returns the JQuery functions used to allow 
	// in-place editing and table sorting
	//=================================================
	function returnDownloadsTableJQuery($clientID = "", $allowModification = 1) {
		global $menuvar, $clms_config;
		
		$extraJQueryReadyScripts = ($allowModification == 1) ? ", headers: { 3: { sorter: false } }" : "";
		
		$JQueryReadyScripts = "
				$('#downloadsTable').tablesorter({ widgets: ['zebra']" . $extraJQueryReadyScripts . " });";
				
		// Only allow modification of rows if we have permission
		if ($allowModification == 1) {
			$extraSQL = ($clientID != "") ? " AND c.id ='" . $clientID . "'" : "";
			
			$sql = "SELECT id FROM `" . DBTABLEPREFIX . "downloads` d, `" . DBTABLEPREFIX . "clients` c WHERE c.id = d.client_id" . $extraSQL;
			$result = mysql_query($sql);
	
			if ($result && mysql_num_rows($result) > 0) {
				while ($row = mysql_fetch_array($result)) {	
					$JQueryReadyScripts .= returnEditInPlaceJQuery($row['id'], "serial_number", "downloads");
				}
				mysql_free_result($result);
			}
		}
		
		return $JQueryReadyScripts;
	}
	
	//=================================================
	// Create a form to add new orders
	//
	// Used so that we can display it in many places
	//=================================================
	function printNewDownloadForm($clientID = "") {
		global $menuvar, $clms_config;

		$clientIDSelect = ($clientID != "") ? "<input type=\"hidden\" name=\"client_id\" value=\"" . $clientID . "\" />" : "<div><label for=\"client_id\">Client <span>- Required</span></label> " . createDropdown("clients", "client_id", "", "") . "</div>";
		
		$content .= "
			<div id=\"newDownloadResponse\">
			</div>
			<form name=\"newDownloadForm\" id=\"newDownloadForm\" action=\"" . $menuvar['DOWNLOADS'] . "\" method=\"post\" class=\"inputForm\" onsubmit=\"return false;\">
					<input type=\"hidden\" name=\"uplodedFilesName\" id=\"uplodedFilesName\" />
					<fieldset>
						<legend>New Download</legend>
						" . $clientIDSelect . "
						<div><label for=\"name\">Download Name <span>- Required</span></label> <input type=\"text\" name=\"name\" id=\"name\" size=\"60\" class=\"required\" /></div>
						<div><label for=\"file\">Upload File </label> <input type=\"file\" name=\"file\" id=\"file\" size=\"60\" /></div>
						<div>-or-</div>
						<div><label for=\"url\">File URL </label> <input type=\"text\" name=\"url\" id=\"url\" size=\"60\" /></div>
						<div><label for=\"serial_number\">Serial Number </label> <input type=\"text\" name=\"serial_number\" id=\"serial_number\" size=\"60\" /></div>
						<div class=\"center\"><input type=\"submit\" class=\"button\" value=\"Create Download\" /></div>
					</fieldset>
				</form>";
			
		return $content;
	}
	
	//=================================================
	// Returns the JQuery functions used to run the 
	// new download form
	//=================================================
	function returnNewDownloadFormJQuery($reprintTable = 0, $allowModification = 1) {
		$extraJQuery = ($reprintTable == 0) ? "
  						// Update the proper div with the returned data
						$('#newDownloadResponse').html('" . progressSpinnerHTML() . "');
						$('#newDownloadResponse').html(data);
						$('#newDownloadResponse').effect('highlight',{},500);" 
						: "
						// Clear the default row
						$('#downloadsTableDefaultRow').remove();
  						// Update the table with the new row
						$('#downloadsTable > tbody:last').append(data);
						$('#downloadsTableUpdateNotice').html('" . tableUpdateNoticeHTML() . "');
						// Show a success message
						$('#newDownloadResponse').html('" . progressSpinnerHTML() . "');
						$('#newDownloadResponse').html(returnSuccessMessage('download'));";
						
		$JQueryReadyScripts = "
			$(\"#file\").uploadify({
				'uploader'       : 'themes/jquery/uploadify/uploadify.swf',
				'script'         : 'uploadify.php',
				'cancelImg'      : 'themes/jquery/uploadify/cancel.png',
				'auto'           : true,
				'onComplete': function(event,queueID,fileObj,response,data) {
				 	$('#uplodedFilesName').val(response);
				}
			});
			var v = jQuery(\"#newDownloadForm\").validate({
				errorElement: \"div\",
				errorClass: \"validation-advice\",
				submitHandler: function(form) {			
					jQuery.get('ajax.php?action=createDownload&reprinttable=" . $reprintTable . "&showButtons=" . $allowModification . "', $('#newDownloadForm').serialize(), function(data) {
  						" . $extraJQuery . "
						// Clear the form
						$('#name').val = '';
						$('#url').val = '';
						$('#serial_number').val = '';
					});
				}
			});";
		
		return $JQueryReadyScripts;
	}

?>