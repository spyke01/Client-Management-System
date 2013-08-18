/*-------------------------------------------------------------------------*/
// Ajax Functions
/*-------------------------------------------------------------------------*/	
invoicesProductRowNumber = 0;
ordersProductRowNumber = 0;

function ajaxDeleteNotifier(spinDivID, action, text, row) {
    if (confirm("Are you sure you want to delete this " + text + "?")) {
		$('#' + spinDivID).toggle();	
		jQuery.get(action, function(data) { $('#' + row).hide('drop',{},500); });
	}
}

function ajaxDeleteInvoicePaymentNotifier(spinDivID, action, text, row, invoiceID) {
    if (confirm("Are you sure you want to delete this " + text + "?")) {
		$('#' + spinDivID).toggle();	
		jQuery.get(action, function(data) { $('#' + row).hide('drop',{},500); });
		// Update the invoice to show the payment removal
		jQuery.get('ajax.php?action=reprintInvoice&id=' + invoiceID, function(data) {
			$('#updateMeViewInvoice').html(data);
		});
	}
}

function ajaxQuickDivUpdate(action, divID, spinnerHTML) {
	jQuery.get(action, function(data) {
		// Clear the current graph and show the new one
		$('#' + divID).html(spinnerHTML);
		$('#' + divID).html(data);
	});
}

$.fn.clearForm = function() {
	return this.each(function() {
		var type = this.type, tag = this.tagName.toLowerCase();
		if (tag == 'form')
			return $(':input',this).clearForm();
		if (type == 'text' || type == 'password' || tag == 'textarea')
			this.value = '';
		else if (type == 'checkbox' || type == 'radio')
			this.checked = false;
		else if (tag == 'select')
			this.selectedIndex = -1;
	});
};

function returnSuccessMessage(itemName) { 
    return "<span class=\"greenText bold\">Successfully created " + itemName + "!</span>";
}

// Invoice Functions
function invoicesAddProductRow(linkObj) {
	if (linkObj) {
		spinnerObj = $(linkObj).parent().parent().find('span.spinner');
		spinnerObj.toggle(); 	
	}
	jQuery.get('ajax.php?action=returnInvoiceProductTableRowHTML&id=' + invoicesProductRowNumber, function(data) {
		$('#addInvoiceProductsTable > tbody:last').append(data);
		invoicesProductRowNumber++;
		if (linkObj) spinnerObj.toggle();
	});
}

function invoicesRemoveProductRow(linkObj) { 
    if (confirm("Are you sure you want to delete this invoice line?")) {
		$(linkObj).parent().parent().remove(); 
		$(linkObj).parent().parent().find('span.spinner').toggle(); 
	}
}

function updateInvoiceLineTotalAmount(invoiceProductID, spinnerHTML) { 
    $('#' + invoiceProductID + '_totalDue').html(spinnerHTML);
	jQuery.get('ajax.php?action=getInvoiceLineTotal&id=' + invoiceProductID, function(data) {
		$('#' + invoiceProductID + '_lineTotal').html(data);
	});
}

function updateInvoiceSubtotalAmount(invoiceID, spinnerHTML) { 
    $('#' + invoiceID + '_totalDue').html(spinnerHTML);
	jQuery.get('ajax.php?action=getInvoiceSubtotal&id=' + invoiceID, function(data) {
		$('#' + invoiceID + '_subtotal').html(data);
	});
}

function updateInvoiceTotalDueAmount(invoiceID, spinnerHTML) { 
    $('.' + invoiceID + '_totalDue').html(spinnerHTML);
	jQuery.get('ajax.php?action=getInvoiceTotalDue&id=' + invoiceID, function(data) {
		$('.' + invoiceID + '_totalDue').html(data);
	});
}

function updateInvoiceTotals(invoiceID, spinnerHTML) { 
    updateInvoiceSubtotalAmount(invoiceID, spinnerHTML);
    updateInvoiceTotalDueAmount(invoiceID, spinnerHTML);
}

// Order Functions
function ordersAddProductRow(linkObj) {
	if (linkObj) {
		spinnerObj = $(linkObj).parent().parent().find('span.spinner');
		spinnerObj.toggle(); 	
	}
	jQuery.get('ajax.php?action=returnOrderProductTableRowHTML&id=' + ordersProductRowNumber, function(data) {
		$('#addOrderProductsTable > tbody:last').append(data);
		ordersProductRowNumber++;
		if (linkObj) spinnerObj.toggle();
	});
}

function ordersRemoveProductRow(linkObj) { 
    if (confirm("Are you sure you want to delete this order line?")) {
		$(linkObj).parent().parent().remove(); 
		$(linkObj).parent().parent().find('span.spinner').toggle(); 
	}
}

function updateOrderLineTotalAmount(orderProductID, spinnerHTML) { 
    $('#' + orderProductID + '_totalDue').html(spinnerHTML);
	jQuery.get('ajax.php?action=getOrderLineTotal&id=' + orderProductID, function(data) {
		$('#' + orderProductID + '_lineTotal').html(data);
	});
}

function updateOrderSubtotalAmount(orderID, spinnerHTML) { 
    $('#' + orderID + '_totalDue').html(spinnerHTML);
	jQuery.get('ajax.php?action=getOrderSubtotal&id=' + orderID, function(data) {
		$('#' + orderID + '_subtotal').html(data);
	});
}

function updateOrderTotalDueAmount(orderID, spinnerHTML) { 
    $('.' + orderID + '_totalDue').html(spinnerHTML);
	jQuery.get('ajax.php?action=getOrderTotalDue&id=' + orderID, function(data) {
		$('.' + orderID + '_totalDue').html(data);
	});
}

function updateOrderTotals(orderID, spinnerHTML) { 
    updateOrderSubtotalAmount(orderID, spinnerHTML);
    updateOrderTotalDueAmount(orderID, spinnerHTML);
}