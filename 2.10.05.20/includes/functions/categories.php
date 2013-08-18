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
	// Returns Category Name from the ID
	//=================================================
	function getCatNameByID($catID) {
		global $menuvar, $clms_config;
		$catName = "";
		
		$sql = "SELECT name FROM `" . DBTABLEPREFIX . "categories` WHERE id='" . $catID . "' LIMIT 1";
		$result = mysql_query($sql);
				
		if ($result && mysql_num_rows($result) > 0) {
			while ($row = mysql_fetch_array($result)) {	
				$catName = $row['name'];
			}
			mysql_free_result($result);
		}
		
		return $catName;
	}

 	//=================================================
	// Print the Categories Table
	//=================================================
	function printCategoriesTable() {
		global $menuvar, $clms_config;
		
		$sql = "SELECT * FROM `" . DBTABLEPREFIX . "categories` ORDER BY name ASC";
		$result = mysql_query($sql);
		
		// Create our new table
		$table = new tableClass(1, 1, 1, "contentBox tablesorter", "categoriesTable");
		
		// Create table title
		$table->addNewRow(array(array("data" => "Client Categories", "colspan" => "2")), "", "title1", "thead");
		
		// Create column headers
		$table->addNewRow(
			array(
				array("type" => "th", "data" => "Name"),
				array("type" => "th", "data" => "")
			), "", "title2", "thead"
		);
							
		// Add our data
		if (!$result || mysql_num_rows($result) == 0) {
			$table->addNewRow(array(array("data" => "There are no client categories in the system.", "colspan" => "2")), "clientCategoriesTableDefaultRow", "greenRow");
		}
		else {
			while ($row = mysql_fetch_array($result)) {				
				$table->addNewRow(
					array(
						array("data" => "<div id=\"" . $row['id'] . "_name\">" . $row['name'] . "</div>"),
						array("data" => createDeleteLinkWithImage($row['id'], $row['id'] . "_row", "categories", "category"), "class" => "center")
					), $row['id'] . "_row", ""
				);
			}
			mysql_free_result($result);
		}
		
		// Return the table's HTML
		return $table->returnTableHTML() . "
				<div id=\"categoriesTableUpdateNotice\"></div>";
	}
	
	//=================================================
	// Returns the JQuery functions used to allow 
	// in-place editing and table sorting
	//=================================================
	function returnCategoriesTableJQuery() {
		global $menuvar, $clms_config;		
					
		$JQueryReadyScripts = "
				$('#categoriesTable').tablesorter({ widgets: ['zebra'], headers: { 1: { sorter: false } } });";
		
		$sql = "SELECT id FROM `" . DBTABLEPREFIX . "categories`";
		$result = mysql_query($sql);

		if ($result && mysql_num_rows($result) > 0) {
			while ($row = mysql_fetch_array($result)) {	
				$JQueryReadyScripts .= returnEditInPlaceJQuery($row['id'], "name", "categories");
			}
			mysql_free_result($result);
		}
		
		return $JQueryReadyScripts;
	}
	
	//=================================================
	// Create a form to add new category
	//
	// Used so that we can display it in many places
	//=================================================
	function printNewCategoryForm() {
		global $menuvar, $clms_config;

		$content .= "
				<div id=\"newCategoryResponse\">
				</div>
				<form name=\"newCategoryForm\" id=\"newCategoryForm\" action=\"" . $menuvar['CATEGORIES'] . "\" method=\"post\" class=\"inputForm\" onsubmit=\"return false;\">
					<fieldset>
						<legend>New Client Category</legend>
						<div><label for=\"catname\">Category Name <span>- Required</span></label> <input name=\"catname\" id=\"catname\" type=\"text\" size=\"60\" class=\"required\" /></div>
						<div class=\"center\"><input type=\"submit\" class=\"button\" value=\"Create Category\" /></div>
					</fieldset>
				</form>";
			
		return $content;
	}
	
	//=================================================
	// Returns the JQuery functions used to run the 
	// new order form
	//=================================================
	function returnNewCategoryFormJQuery($reprintTable = 0, $allowModification = 1) {
		$extraJQuery = ($reprintTable == 0) ? "
  						// Update the proper div with the returned data
						$('#newCategoryResponse').html('" . progressSpinnerHTML() . "');
						$('#newCategoryResponse').html(data);
						$('#newCategoryResponse').effect('highlight',{},500);" 
						: "
						// Clear the default row
						$('#clientCategoriesTableDefaultRow').remove();
  						// Update the table with the new row
						$('#categoriesTable > tbody:last').append(data);
						$('#categoriesTableUpdateNotice').html('" . tableUpdateNoticeHTML() . "');
						// Show a success message
						$('#newCategoryResponse').html('" . progressSpinnerHTML() . "');
						$('#newCategoryResponse').html(returnSuccessMessage('client category'));";
							
		$JQueryReadyScripts = "
			var v = jQuery(\"#newCategoryForm\").validate({
				errorElement: \"div\",
				errorClass: \"validation-advice\",
				submitHandler: function(form) {			
					jQuery.get('ajax.php?action=createCategory&reprinttable=" . $reprintTable . "&showButtons=" . $allowModification . "', $('#newCategoryForm').serialize(), function(data) {
						" . $extraJQuery . "
						// Clear the form
						$('#catname').val = '';
					});
				}
			});";
		
		return $JQueryReadyScripts;
	}

?>