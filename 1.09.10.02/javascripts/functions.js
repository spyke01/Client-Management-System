/*-------------------------------------------------------------------------*/
// General Functions
/*-------------------------------------------------------------------------*/	
function confirmDelete(text) {
    return confirm("Are you sure you want to delete this "+ text +"?");
}

function sqr_show_hide(id) {
	var item = document.getElementById(id)

	if (item && item.style) {
		if (item.style.display == "none") {
			item.style.display = "";
		}
		else {
			item.style.display = "none";
		}
	}
	else if (item) {
		item.visibility = "show";
	}
}

function sqr_show(id) {
	var item = document.getElementById(id)

	if (item && item.style) {
		item.style.display = "";
	}
	else if (item) {
		item.visibility = "show";
	}
}

function sqr_hide(id) {
	var item = document.getElementById(id)

	item.style.display = "none";
}

function sqr_show_hide_with_img(itemID) {
	obj = document.getElementById('slideDiv' + itemID);
	img = document.getElementById('slideImg' + itemID);

	if (!obj) {
		// nothing to collapse!
		if (img) {
			// hide the clicky image if there is one
			img.style.display = 'none';
		}
		return false;
	}
	else {
		if (obj.style.display == 'none') {
			obj.style.display = '';
			if (img) {
				img_re = new RegExp("_collapsed\\.jpg$");
				img.src = img.src.replace(img_re, '.jpg');
			}
		}
		else {
			obj.style.display = 'none';
			if (img) {
				img_re = new RegExp("\\.jpg$");
				img.src = img.src.replace(img_re, '_collapsed.jpg');
			}
		}
	}
	return false;
}

/*-------------------------------------------------------------------------*/
// Ajax Functions
/*-------------------------------------------------------------------------*/	
function ajaxDeleteNotifier(spinDivID, action, text, row) {
    if (confirm("Are you sure you want to delete this "+ text +"?")) {
		sqr_show_hide(spinDivID);
		new Ajax.Request(action, {asynchronous:true, onSuccess:function(){ new Effect.SlideUp(row);}});
	}
}