<?php 
/***************************************************************************
 *                               products.php
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
	// Print the Products Table
	//=================================================
	function printProductsTable() {
		global $menuvar, $clms_config;
		
		$sql = "SELECT * FROM `" . DBTABLEPREFIX . "products` ORDER BY name ASC";
		$result = mysql_query($sql);
		
		// Create our new table
		$table = new tableClass(1, 1, 1, "contentBox tablesorter", "productsTable");
		
		// Create table title
		$table->addNewRow(array(array("data" => "Products", "colspan" => "6")), "", "title1", "thead");
		
		// Create column headers
		$table->addNewRow(
			array(
				array("type" => "th", "data" => "Name"),
				array("type" => "th", "data" => "Price"),
				array("type" => "th", "data" => "Profit"),
				array("type" => "th", "data" => "Shipping Cost"),
				array("type" => "th", "data" => "Total Cost"),
				array("type" => "th", "data" => "")
			), "", "title2", "thead"
		);
							
		// Add our data
		if (!$result || mysql_num_rows($result) == 0) {
			$table->addNewRow(array(array("data" => "There are no products in the system.", "colspan" => "6")), "productsTableDefaultRow", "greenRow");
		}
		else {
			while ($row = mysql_fetch_array($result)) {				
				$table->addNewRow(
					array(
						array("data" => "<div id=\"" . $row['id'] . "_name\">" . $row['name'] . "</div>"),
						array("data" => "<div id=\"" . $row['id'] . "_price\">" . formatCurrency($row['price']) . "</div>"),
						array("data" => "<div id=\"" . $row['id'] . "_profit\">" . formatCurrency($row['profit']) . "</div>"),
						array("data" => "<div id=\"" . $row['id'] . "_shipping\">" . formatCurrency($row['shipping']) . "</div>"),
						array("data" => formatCurrency($row['price'] + $row['profit'] + $row['shipping'])),
						array("data" => createDeleteLinkWithImage($row['id'], $row['id'] . "_row", "products", "product"), "class" => "center")
					), $row['id'] . "_row", ""
				);
			}
			mysql_free_result($result);
		}
		
		// Return the table's HTML
		return $table->returnTableHTML() . "
				<div id=\"productsTableUpdateNotice\"></div>";
	}
	
	//=================================================
	// Returns the JQuery functions used to allow 
	// in-place editing and table sorting
	//=================================================
	function returnProductsTableJQuery() {
		global $menuvar, $clms_config;		
					
		$JQueryReadyScripts = "
				$('#productsTable').tablesorter({ widgets: ['zebra'], headers: { 5: { sorter: false } } });";
		
		$sql = "SELECT id FROM `" . DBTABLEPREFIX . "products`";
		$result = mysql_query($sql);

		if ($result && mysql_num_rows($result) > 0) {
			while ($row = mysql_fetch_array($result)) {	
				$JQueryReadyScripts .= returnEditInPlaceJQuery($row['id'], "name", "products");
				$JQueryReadyScripts .= returnEditInPlaceJQuery($row['id'], "price", "products");
				$JQueryReadyScripts .= returnEditInPlaceJQuery($row['id'], "profit", "products");
				$JQueryReadyScripts .= returnEditInPlaceJQuery($row['id'], "shipping", "products");
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
	function printNewProductForm() {
		global $menuvar, $clms_config;

		$content .= "
				<div id=\"newProductResponse\">
				</div>
				<form name=\"newProductForm\" id=\"newProductForm\" action=\"" . $menuvar['PRODUCTS'] . "\" method=\"post\" class=\"inputForm\" onsubmit=\"return false;\">
					<fieldset>
						<legend>New Product</legend>
						<div><label for=\"name\">Product Name <span>- Required</span></label> <input name=\"name\" id=\"name\" type=\"text\" size=\"60\" class=\"required\" /></div>
						<div><label for=\"price\">Price </label> <input name=\"price\" id=\"price\" type=\"text\" size=\"60\" /></div>
						<div><label for=\"profit\">Profit </label> <input name=\"profit\" id=\"profit\" type=\"text\" size=\"60\" /></div>
						<div><label for=\"shipping\">Shipping Cost </label> <input name=\"shipping\" id=\"shipping\" type=\"text\" size=\"60\" /></div>
						<div class=\"center\"><input type=\"submit\" class=\"button\" value=\"Create Product\" /></div>
					</fieldset>
				</form>";
			
		return $content;
	}
	
	//=================================================
	// Returns the JQuery functions used to run the 
	// new order form
	//=================================================
	function returnNewProductFormJQuery($reprintTable = 0, $allowModification = 1) {		
		$extraJQuery = ($reprintTable == 0) ? "
  						// Update the proper div with the returned data
						$('#newProductResponse').html('" . progressSpinnerHTML() . "');
						$('#newProductResponse').html(data);
						$('#newProductResponse').effect('highlight',{},500);" 
						: "
						// Clear the default row
						$('#productsTableDefaultRow').remove();
  						// Update the table with the new row
						$('#productsTable > tbody:last').append(data);
						$('#productsTableUpdateNotice').html('" . tableUpdateNoticeHTML() . "');
						// Show a success message
						$('#newProductResponse').html('" . progressSpinnerHTML() . "');
						$('#newProductResponse').html(returnSuccessMessage('product'));";
							
		$JQueryReadyScripts = "
			var v = jQuery(\"#newProductForm\").validate({
				errorElement: \"div\",
				errorClass: \"validation-advice\",
				submitHandler: function(form) {			
					jQuery.get('ajax.php?action=createProduct&reprinttable=" . $reprintTable . "&showButtons=" . $allowModification . "', $('#newProductForm').serialize(), function(data) {
						" . $extraJQuery . "
						// Clear the form
						$('#name').val = '';
						$('#price').val = '';
						$('#profit').val = '';
						$('#shipping').val = '';
					});
				}
			});";
		
		return $JQueryReadyScripts;
	}

?>