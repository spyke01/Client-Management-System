<?php 
/***************************************************************************
 *                               general.php
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
 
	//==================================================
	// Strips Dangerous tags out of input boxes 
	//==================================================
	function keepsafe($makesafe) {
		$makesafe=strip_tags($makesafe); // strip away any dangerous tags
		$makesafe=str_replace(" ","",$makesafe); // remove spaces from variables
		$makesafe=str_replace("%20","",$makesafe); // remove escaped spaces
		$makesafe = trim(preg_replace('/[^\x09\x0A\x0D\x20-\x7F]/e', '"&#".ord($0).";"', $makesafe)); //encodes all ascii items above #127
	
	    // Stripslashes
	    if (get_magic_quotes_gpc()) {
	        $makesafe = stripslashes($makesafe);
	    }
	    // Quote if not integer
	    if (!is_numeric($makesafe)) {
	        $makesafe = mysql_real_escape_string($makesafe);
	    }
	    return $makesafe;
	}
	
	//==================================================
	// Strips Dangerous tags out of textareas 
	//==================================================
	function keeptasafe($makesafe) {
		$makesafe = trim(preg_replace('/[^\x09\x0A\x0D\x20-\x7F]/e', '"&#".ord($0).";"', $makesafe)); //encodes all ascii items above #127
		
	    // Stripslashes
	    if (get_magic_quotes_gpc()) {
	        $makesafe = stripslashes($makesafe);
	    }
	    // Quote if not integer
	    if (!is_numeric($makesafe)) {
	        $makesafe = mysql_real_escape_string($makesafe);
	    }
	    return $makesafe;
	}
	
	//==================================================
	// Strips Dangerous tags out of get and post values
	//==================================================
	function parseurl($makesafe) {
		$makesafe=strip_tags($makesafe); // strip away any dangerous tags
		$makesafe=str_replace(" ","",$makesafe); // remove spaces from variables
		$makesafe=str_replace("%20","",$makesafe); // remove escaped spaces
		$makesafe = trim(preg_replace('/[^\x09\x0A\x0D\x20-\x7F]/e', '"&#".ord($0).";"', $makesafe)); //encodes all ascii items above #127
	
	    // Stripslashes
	    if (get_magic_quotes_gpc()) {
	        $makesafe = stripslashes($makesafe);
	    }
	    // Quote if not integer
	    if (!is_numeric($makesafe)) {
	        $makesafe = mysql_real_escape_string($makesafe);
	    }
	    return $makesafe;
	}
	
	//==================================================
	// Creates a date from a timestamp
	//==================================================
	function makeDate($time) {
		global $clms_config;
		
		$date = @gmdate('l F d, Y', $time + (3600 * $clms_config['ftsclms_time_zone'])); // Makes date in the format of: Thursday July 05, 2006
		return $date;
	}
	
	function makeTime($time) {
		global $clms_config;
		
		$date = @gmdate('g:i A', $time + (3600 * $clms_config['ftsclms_time_zone'])); // Makes date in the format of: 3:30 PM
		return $date;
	}
	
	function makeDateTime($time) {
		global $clms_config;
		
		$date = @gmdate('l F d, Y - g:i A', $time + (3600 * $clms_config['ftsclms_time_zone'])); // Makes date in the format of: Thursday July 5, 2006 - 3:30 pm
		return $date;
	}
	
	function makeOrderDateTime($time) {
		global $clms_config;
		
		$date = @gmdate('M d, Y - g:i A', $time + (3600 * $clms_config['ftsclms_time_zone'])); // Makes date in the format of: Jul 5, 2006 - 3:30 pm
		return $date;
	}
	
	function makeShortDate($time) {
		global $clms_config;
		
		$date = ($time == "") ? "" : @gmdate('m/d/Y', $time + (3600 * $clms_config['ftsclms_time_zone'])); // Makes date in the format of: 07/05/2006
		return $date;
	}
	
	function makeShortDateTime($time) {
		global $clms_config;
		
		$date = ($time == "") ? "" : @gmdate('m/d/Y g:i A', $time + (3600 * $clms_config['ftsclms_time_zone'])); // Makes date in the format of: 07/05/2006 - 3:30 pm
		return $date;
	}
	
	function makeCurrentYear($time) {
		global $clms_config;
		
		$date = ($time == "") ? "" : @gmdate('Y', $time + (3600 * $clms_config['ftsclms_time_zone'])); // Makes date in the format of: 2006
		return $date;
	}
	
	function makeXYearsFromCurrentYear($time, $numOfYears) {
		global $clms_config;
		
		$date = ($time == "") ? "" : @gmdate('Y', $time + (3600 * $clms_config['ftsclms_time_zone'])) + $numOfYears; // Makes date in the format of: 2026
		return $date;
	}
	
	function makeYear($time) {
		global $clms_config;
		
		$date = @gmdate('Y', $time + (3600 * $clms_config['ftsclms_time_zone'])); // Makes date in the format of: 2006
		return $date;
	}
	
	function makeMonth($time) {
		global $clms_config;
		
		$date = @gmdate('M', $time + (3600 * $clms_config['ftsclms_time_zone'])); // Makes date in the format of: Jul
		return $date;
	}
	
	function makeShortMonth($time) {
		global $clms_config;
		
		$date = @gmdate('m', $time + (3600 * $clms_config['ftsclms_time_zone'])); // Makes date in the format of: 05
		return $date;
	}
	
	function makeXMonthsFromCurrentMonthAsTimestamp($numOfMonths) {
		$currentTime = time();
		$currentMonth = makeShortMonth($currentTime);
		$currentYear = makeYear($currentTime);
		
		// Increase month count
		for ($i = 0; $i < $numOfMonths; $i++) {
			// Handle Dec
			$currentMonth = ($currentMonth == "12") ? 1 : ($currentMonth + 1);
			$currentYear = ($currentMonth == "12") ? ($currentYear + 1) : $currentYear;
		}
		
		$timestamp = strtotime($currentMonth . "/01/" . $currentYear);
		return $timestamp;
	}
	
	//=================================================
	// BBCode Functions Generated from: 
	// http://bbcode.strefaphp.net/bbcode.php
	// A gigantic thanks goes out to the 
	// programmers there!!
	// 
	// Use the function like so: echo bbcode($string);
	//=================================================
	Function bbcode($str){
		// Makes < and > page friendly
		//$str=str_replace("&","&amp;",$str);
		$str=str_replace("<","&lt;",$str);
		$str=str_replace(">","&gt;",$str);
		
		// Link inside tags new window
		$str = preg_replace("#\[url\](.*?)?(.*?)\[/url\]#si", "<a href=\"\\1\\2\" target=\"_blank\">\\1\\2</a>", $str);
		
		// Link inside first tag new window
		$str = preg_replace("#\[url=(.*?)?(.*?)\](.*?)\[/url\]#si", "<a href=\"\\2\" target=\"_blank\">\\3</a>", $str);
		
		// Link inside tags
		$str = preg_replace("#\[url2\](.*?)?(.*?)\[/url2\]#si", "<a href=\"\\1\\2\">\\1\\2</a>", $str);
		
		// Link inside first tag
		$str = preg_replace("#\[url2=(.*?)?(.*?)\](.*?)\[/url2\]#si", "<a href=\"\\2\">\\3</a>", $str);
		
		// Automatic links if no url tags used
		$str = preg_replace_callback("#([\n ])([a-z]+?)://([a-z0-9\-\.,\?!%\*_\#:;~\\&$@\/=\+]+)#si", "bbcode_autolink", $str);
		$str = preg_replace("#([\n ])www\.([a-z0-9\-]+)\.([a-z0-9\-.\~]+)((?:/[a-z0-9\-\.,\?!%\*_\#:;~\\&$@\/=\+]*)?)#i", " <a href=\"http://www.\\2.\\3\\4\" target=\"_blank\">www.\\2.\\3\\4</a>", $str);
		$str = preg_replace("#([\n ])([a-z0-9\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)?[\w]+)#i", "\\1<a href=\"mailto: \\2@\\3\">\\2_(at)_\\3</a>", $str);
		
		// PHP Code
		$str = preg_replace("#\[php\](.*?)\[/php]#si", "<div class=\"codetop\"><u><strong>&lt?PHP:</strong></u></div><div class=\"codemain\">\\1</div>", $str);
		
		// Bold
		$str = preg_replace("#\[b\](.*?)\[/b\]#si", "<strong>\\1</strong>", $str);
		
		// Italics
		$str = preg_replace("#\[i\](.*?)\[/i\]#si", "<em>\\1</em>", $str);
		
		// Underline
		$str = preg_replace("#\[u\](.*?)\[/u\]#si", "<u>\\1</u>", $str);
		
		// Alig text
		$str = preg_replace("#\[align=(left|center|right)\](.*?)\[/align\]#si", "<div align=\"\\1\">\\2</div>", $str); 
		
		// Font Color
		$str = preg_replace("#\[color=(.*?)\](.*?)\[/color\]#si", "<span style=\"color:\\1\">\\2</span>", $str);
		
		// Font Size
		$str = preg_replace("#\[size=(.*?)\](.*?)\[/size\]#si", "<span style=\"font-size:\\1\">\\2</span>", $str);
		
		// Image
		$str = preg_replace("#\[img\](.*?)\[/img\]#si", "<img src=\"\\1\" border=\"0\" alt=\"\" />", $str);
		
		// Uploaded image
		$str = preg_replace("#\[ftp_img\](.*?)\[/ftp_img\]#si", "<img src=\"img/\\1\" border=\"0\" alt=\"\" />", $str);
		
		// HR Line
		$str = preg_replace("#\[hr=(\d{1,2}|100)\]#si", "<hr class=\"linia\" width=\"\\1%\" />", $str);
		
		// Code
		$str = preg_replace("#\[code\](.*?)\[/code]#si", "<div class=\"codetop\"><u><strong>Code:</strong></u></div><div class=\"codemain\">\\1</div>", $str);
		
		// Code, Provide Author
		$str = preg_replace("#\[code=(.*?)\](.*?)\[/code]#si", "<div class=\"codetop\"><u><strong>Code \\1:</strong></u></div><div class=\"codemain\">\\2</div>", $str);
		
		// Quote
		$str = preg_replace("#\[quote\](.*?)\[/quote]#si", "<div class=\"quotetop\"><u><strong>Quote:</strong></u></div><div class=\"quotemain\">\\1</div>", $str);
		
		// Quote, Provide Author
		$str = preg_replace("#\[quote=(.*?)\](.*?)\[/quote]#si", "<div class=\"quotetop\"><u><strong>Quote \\1:</strong></u></div><div class=\"quotemain\">\\2</div>", $str);
		
		// Email
		$str = preg_replace("#\[email\]([a-z0-9\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)?[\w]+)\[/email\]#i", "<a href=\"mailto:\\1@\\2\">\\1@\\2</a>", $str);
		
		// Email, Provide Author
		$str = preg_replace("#\[email=([a-z0-9\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)?[\w]+)?(.*?)\](.*?)\[/email\]#i", "<a href=\"mailto:\\1@\\2\">\\5</a>", $str);
		
		// YouTube
		$str = preg_replace("#\[youtube\]http://(?:www\.)?youtube.com/v/([0-9A-Za-z-_]{11})[^[]*\[/youtube\]#si", "<object width=\"425\" height=\"350\"><param name=\"movie\" value=\"http://www.youtube.com/v/\\1\"></param><param name=\"wmode\" value=\"transparent\"></param><embed src=\"http://www.youtube.com/v/\\1\" type=\"application/x-shockwave-flash\" wmode=\"transparent\" width=\"425\" height=\"350\"></embed></object>", $str);
		$str = preg_replace("#\[youtube\]http://(?:www\.)?youtube.com/watch\?v=([0-9A-Za-z-_]{11})[^[]*\[/youtube\]#si", "<object width=\"425\" height=\"350\"><param name=\"movie\" value=\"http://www.youtube.com/v/\\1\"></param><param name=\"wmode\" value=\"transparent\"></param><embed src=\"http://www.youtube.com/v/\\1\" type=\"application/x-shockwave-flash\" wmode=\"transparent\" width=\"425\" height=\"350\"></embed></object>", $str);
		
		// Google Video
		$str = preg_replace("#\[gvideo\]http://video.google.[A-Za-z0-9.]{2,5}/videoplay\?docid=([0-9A-Za-z-_]*)[^[]*\[/gvideo\]#si", "<object width=\"425\" height=\"350\"><param name=\"movie\" value=\"http://video.google.com/googleplayer.swf\?docId=\\1\"></param><param name=\"wmode\" value=\"transparent\"></param><embed src=\"http://video.google.com/googleplayer.swf\?docId=\\1\" type=\"application/x-shockwave-flash\" allowScriptAccess=\"sameDomain\" quality=\"best\" bgcolor=\"#ffffff\" scale=\"noScale\" salign=\"TL\"  FlashVars=\"playerMode=embedded\" wmode=\"transparent\" width=\"425\" height=\"350\"></embed></object>", $str);
		
		// change \n to <br />
		$str = nl2br($str);
		
		// return bbdecoded string
		return $str;
	}
	
	
	function bbcode_autolink($str) {
	$lnk=$str[3];
	if(strlen($lnk)>30){
	if(substr($lnk,0,3)=='www'){$l=9;}else{$l=5;}
	$lnk=substr($lnk,0,$l).'(...)'.substr($lnk,strlen($lnk)-8);}
	return ' <a href="'.$str[2].'://'.$str[3].'" target="_blank">'.$str[2].'://'.$lnk.'</a>';
	}
	
	//==================================================
	// Replacement for die()
	// Used to display msgs without displaying the board
	//==================================================
	function message_die($msg_text = '', $msg_title = '') {
		echo "<html>\n<body>\n" . $msg_title . "\n<br /><br />\n" . $msg_text . "</body>\n</html>";
		include('includes/footer.php');
		exit;
	}
	
	//=========================================================
	// nl2br replacement for ajax calls since newlines are escaped
	//=========================================================
	function ajaxnl2br($string) {
		return str_replace(array("\\r\\n", "\\r", "\\n"), "<br />", $string);
	}
	
	//=========================================================
	// Check if this item should be selected
	//=========================================================
	function testSelected($testFor, $testAgainst) {
		if ($testFor == $testAgainst) { return " selected=\"selected\""; }
	}
	
	//=========================================================
	// Check if this item should be checked
	//=========================================================
	function testChecked($testFor, $testAgainst) {
		if ($testFor == $testAgainst) { return " checked=\"checked\""; }
	}
	
	//=========================================================
	// Outputs Yes or No
	//=========================================================
	function returnYesNo($value) {
		if ($value == 1 || $value == true) { return "Yes"; }
		else { return "No"; }
	}
	
	//=========================================================
	// Returns the system's selected currency symbol
	//=========================================================
	function returnCurrencySymbol() {
		global $clms_config;
		
		return $clms_config['ftsclms_currency_type'];
	}
	
	//=========================================================
	// Returns the proper http or https depending on the system setting
	//=========================================================
	function returnHttpLinks($input) {
		global $clms_config;
		
		$output = ($clms_config['ftsss_use_https'] == 1) ? str_replace("http://", "https://", $input) : str_replace("https://", "http://", $input);
		
		return $output;
	}
	
	//=========================================================
	// Padds a string to a certain length
	//=========================================================
	function paddString($var, $desiredLength, $paddingValue, $sideToPadd) {
		$padding = "";
			
		if (strlen($var) == $desiredLength) {
			return $var;
		}
		elseif (strlen($var) > $desiredLength) {
			// If we are padding the left then we will grab the right most pieces
			if ($sideToPadd == "L") { return substr($var, 0, -$desiredLength); }
			// If we are padding the right then we will grab the left most pieces
			else { return substr($var, 0, $desiredLength); }		
		}
		else {
			$spacesToPadd = $desiredLength - strlen($var);
			
			for ($i = 0; $i < $spacesToPadd; $i++) {
				$padding .= $paddingValue;
			}
			
			if ($sideToPadd == "L") { return $padding . $var; }
			else { return $var . $padding; }
		}
	}
	
	//=========================================================
	// Puts items into money format
	//=========================================================
	function formatCurrency($value) {
		// All non numeric values should be turned into 0
		if (!is_numeric($value)) $value = 0;
		
		return returnCurrencySymbol() . number_format($value, 2, '.', ',');
	}
	
	//=========================================================
	// Takes the change off of a number without rounding it up
	//=========================================================
	function stripChange($value) {
		$returnVar = "";
				
		if (is_numeric($value)) { 
			// If we have multiple periods then we will output all but the last one
			$explodedValue = explode(".", $value);
			
			if (count($explodedValue) > 1) {
				for ($x = 0; $x < count($explodedValue) - 1; $x++) {
					$returnVar = ($returnVar == "") ? $explodedValue[$x] :  "." . $explodedValue[$x];
				}
			}
			else { $returnVar = $explodedValue[0]; }
		}
		else { $returnVar = $value; }
		
		return $returnVar;
	}
	
	//=========================================================
	// Returns the HTML code for our delete links
	//=========================================================
	function createDeleteLinkWithImage($DBTableRowID, $rowName, $DBTableName, $typeName) {
		global $clms_config;
	
		return "<a style=\"cursor: pointer; cursor: hand;\" onclick=\"ajaxDeleteNotifier('" . $DBTableRowID . $DBTableName . "Spinner', 'ajax.php?action=deleteitem&table=" . $DBTableName . "&id=" . $DBTableRowID . "', '" . $typeName . "', '" . $rowName . "');\"><img src=\"themes/" . $clms_config['ftsclms_theme'] . "/icons/delete.png\" alt=\"Delete " . ucfirst($typeName) . "\" /></a><span id=\"" . $DBTableRowID . $DBTableName . "Spinner\" style=\"display: none;\">" . progressSpinnerHTML() . "</span>";
	}
	
	//=========================================================
	// Returns the HTML code for our delete links
	//=========================================================
	function createInvoicePaymentDeleteLinkWithImage($DBTableRowID, $rowName, $DBTableName, $typeName, $invoiceID) {
		global $clms_config;
	
		return "<a style=\"cursor: pointer; cursor: hand;\" onclick=\"ajaxDeleteInvoicePaymentNotifier('" . $DBTableRowID . $DBTableName . "Spinner', 'ajax.php?action=deleteitem&table=" . $DBTableName . "&id=" . $DBTableRowID . "', '" . $typeName . "', '" . $rowName . "', '" . $invoiceID . "');\"><img src=\"themes/" . $clms_config['ftsclms_theme'] . "/icons/delete.png\" alt=\"Delete " . ucfirst($typeName) . "\" /></a><span id=\"" . $DBTableRowID . $DBTableName . "Spinner\" style=\"display: none;\">" . progressSpinnerHTML() . "</span>";
	}
	
	//=========================================================
	// Returns the HTML code for our spinner
	//=========================================================
	function progressSpinnerHTML() {
		global $clms_config;
	
		return "<img src=\"themes/" . $clms_config['ftsclms_theme'] . "/icons/indicator.gif\" alt=\"spinner\" />";
	}
	
	//=========================================================
	// Returns the HTML code for a table update notice
	//=========================================================
	function tableUpdateNoticeHTML() {	
		return "<div class=\"updateNotice\">A new row has been added to this table, inline editing for this new row will be disabled until the next page refresh.</div>";
	}
	
	//=========================================================
	// Returns the JQUERY code for our edit in-place
	//=========================================================
	function returnEditInPlaceJQuery($DBTableRowID, $DBTableFieldName, $DBTableName, $inputtype = "", $extraOptions = "") {
		$inputtypeJQuery = ($inputtype != "") ? "type      : '" . $inputtype . "'," : "";
		$extraOptions = ($extraOptions != "") ? $extraOptions . "," : "";
		
		return "
					     $('#" . $DBTableRowID . "_" . $DBTableFieldName . "').addClass('editableItemHolder').editable('ajax.php?action=updateitem&table=" . $DBTableName . "&item=" . $DBTableFieldName . "&id=" . $DBTableRowID . "', { 
					         " . $inputtypeJQuery . "
							 " . $extraOptions . "
							 cancel    : 'Cancel',
					         submit    : 'OK',
					         indicator : '" . progressSpinnerHTML() . "',
					         tooltip   : 'Click to edit...'
					     });";
	}
	
	//=========================================================
	// Create a dropdown without the need for repeating code
	//=========================================================
	function createDropdown($type, $inputName, $currentSelection = "", $onChange = "", $class = "") {
		global $clms_config;
		
		$onChangeVar = ($onChange == "") ? "" : " onChange=\"" . $onChange . "\"";
		$classVar = ($class == "") ? "" : " class=\"" . $class . "\"";
		
		$dropdown = "<select name=\"" . $inputName . "\" id=\"" . $inputName . "\"" . $classVar . "" . $onChangeVar . ">
						<option value=\"\">--Select One--</option>";
		if ($type == "categories") {
			$sql = "SELECT id, name FROM `" . DBTABLEPREFIX . "categories` ORDER BY name";
			$result = mysql_query($sql);
			
			if ($result && mysql_num_rows($result) > 0) {
				while ($row = mysql_fetch_array($result)) {
					$dropdown .= "<option value=\"" . $row['id'] . "\"" . testSelected($row['id'], $currentSelection) . ">" . $row['name'] . "</option>";
				}
				mysql_free_result($result);
			}
		}
		if ($type == "clients") {
			$sql = "SELECT id, first_name, last_name FROM `" . DBTABLEPREFIX . "clients` ORDER BY last_name";
			$result = mysql_query($sql);
			
			if ($result && mysql_num_rows($result) > 0) {
				while ($row = mysql_fetch_array($result)) {
					$dropdown .= "<option value=\"" . $row['id'] . "\"" . testSelected($row['id'], $currentSelection) . ">" . $row['last_name'] . ", " . $row['first_name'] . "</option>";
				}
				mysql_free_result($result);
			}
		}
		if ($type == "currencies") {
			global $FTS_CURRENCIES;
			
			foreach($FTS_CURRENCIES as $key => $value) {
				$dropdown .= "<option value=\"" . $key . "\"" . testSelected($key, $currentSelection) . ">" . $value . "</option>";
			}
		}
		if ($type == "countries") {
			global $FTS_COUNTRIES;
		
			foreach($FTS_COUNTRIES as $key => $value) {
				$dropdown .= "<option value=\"" . $key . "\"" . testSelected($key, $currentSelection) . ">" . $value . "</option>";
			}
		}
		if ($type == "daterange") {
			$itemArray = array('today' => "Today", 'thisWeek' => "This Week", 'thisMonth' => "This Month", 'thisYear' => "This Year", 'allTime' => "Alltime", 'custom' => "Custom Date Range");
			
			foreach ($itemArray as $key => $value) {
				$dropdown .= "
						<option value=\"" . $key . "\"" . testSelected($currentSelection, $key) . ">" . $value . "</option>";
			}
		}
		if ($type == "graphs") {
			$itemArray = array('invoicedVsPaid' => "Invoiced vs Paid", 'totalPayments' => "Total Payments", 'totalProfit' => "Total Profit");
			
			foreach ($itemArray as $key => $value) {
				$dropdown .= "
						<option value=\"" . $key . "\"" . testSelected($currentSelection, $key) . ">" . $value . "</option>";
			}
		}
		if ($type == "graphtypes") {
			$itemArray = array('area2d' => "Area (2D)", 'bar2d' => "Bar (2D)", 'column' => "Column", 'column2d' => "Column (2D)", 'doughnut2d' => "Doughnut (2D)", 'funnel' => "Funnel", 'line' => "Line", 'pie' => "Pie", 'pie2d' => "Pie (2D)");
			
			foreach ($itemArray as $key => $value) {
				$dropdown .= "
						<option value=\"" . $key . "\"" . testSelected($currentSelection, $key) . ">" . $value . "</option>";
			}
		}
		if ($type == "paymenttypes") {
			global $FTS_PAYMENTTYPES;
		
			foreach($FTS_PAYMENTTYPES as $key => $value) {
				$dropdown .= "<option value=\"" . $key . "\"" . testSelected($key, $currentSelection) . ">" . $value . "</option>";
			}
		}
		if ($type == "products") {
			$sql = "SELECT id, name FROM `" . DBTABLEPREFIX . "products` ORDER BY name ASC";
			$result = mysql_query($sql);
			
			if ($result && mysql_num_rows($result) > 0) {
				while ($row = mysql_fetch_array($result)) {
					$dropdown .= "<option value=\"" . $row['id'] . "\"" . testSelected($row['id'], $currentSelection) . ">" . $row['name'] . "</option>";
				}
				mysql_free_result($result);
			}
		}
		if ($type == "productswithprice") {
			$sql = "SELECT id, name, price, profit, shipping FROM `" . DBTABLEPREFIX . "products` ORDER BY name ASC";
			$result = mysql_query($sql);
			
			if ($result && mysql_num_rows($result) > 0) {
				while ($row = mysql_fetch_array($result)) {
					$dropdown .= "<option value=\"" . $row['id'] . "\"" . testSelected($row['id'], $currentSelection) . ">" . $row['name'] . " - " . formatCurrency($row['price'] + $row['profit'] + $row['shipping']) . "</option>";
				}
				mysql_free_result($result);
			}
		}
		if ($type == "timezone") {
			global $FTS_TIMEZONES;
		
			foreach($FTS_TIMEZONES as $key => $value) {
				$dropdown .= "<option value=\"" . $key . "\"" . testSelected($key, $currentSelection) . ">" . $value . "</option>";
			}
		}
		if ($type == "urgency") {
			$dropdown .= "
						<option value=\"" . LOW . "\"" . testSelected($currentSelection, LOW) . ">Low</option>
						<option value=\"" . MEDIUM . "\"" . testSelected($currentSelection, MEDIUM) . ">Medium</option>
						<option value=\"" . HIGH . "\"" . testSelected($currentSelection, HIGH) . ">High</option>";
		}
		if ($type == "users") {
			$sql = "SELECT id, email_address, first_name, last_name FROM `" . USERSDBTABLEPREFIX . "users` ORDER BY last_name";
			$result = mysql_query($sql);
			
			if ($result && mysql_num_rows($result) > 0) {
				while ($row = mysql_fetch_array($result)) {
					$dropdown .= "<option value=\"" . $row['id'] . "\"" . testSelected($row['id'], $currentSelection) . ">" . $row['last_name'] . ", " . $row['first_name'] . " (" . $row['email_address'] . ")</option>";
				}
				mysql_free_result($result);
			}
		}
		if ($type == "userlevel") {
			$dropdown .= "
						<option value=\"" . BANNED . "\"" . testSelected($currentSelection, BANNED) . ">Banned</option>
						<option value=\"" . USER . "\"" . testSelected($currentSelection, USER) . ">User</option>
						<option value=\"" . CLIENT_ADMIN . "\"" . testSelected($currentSelection, CLIENT_ADMIN) . ">Client Administrator</option>
						<option value=\"" . SYSTEM_ADMIN . "\"" . testSelected($currentSelection, SYSTEM_ADMIN) . ">System Administrator</option>";
		}
		$dropdown .= "</select>";	
		
		return $dropdown;	
	}
	
	//=========================================================
	// Case insensitive str_replace
	//=========================================================
	if(!function_exists('str_ireplace')){
	   function str_ireplace($search, $replace, $subject){
	       if(is_array($search)){
	           array_walk($search, 'make_pattern');
	       }
	       else{
	           $search = '/'.preg_quote($search, '/').'/i';
	       }
	       return preg_replace($search, $replace, $subject);
	   }
	} 
	
	//=========================================================
	// Returns company info block
	//=========================================================
	function returnCompanyInfoBlock() {
		global $clms_config;
		
		$companyInfoBlock = "";
		$companyInfoBlock .= ($clms_config['ftsclms_invoice_company_name'] != "") ? $clms_config['ftsclms_invoice_company_name'] . "<br />" : "";
		$companyInfoBlock .= ($clms_config['ftsclms_invoice_address'] != "") ? nl2br($clms_config['ftsclms_invoice_address']) . "<br />" : "";
		$companyInfoBlock .= ($clms_config['ftsclms_invoice_city'] != "") ? $clms_config['ftsclms_invoice_city'] . ", " . $clms_config['ftsclms_invoice_state'] . " " . $clms_config['ftsclms_invoice_zip'] . "<br />" : "";
		$companyInfoBlock .= ($clms_config['ftsclms_invoice_phone_number'] != "") ? "Phone: " . $clms_config['ftsclms_invoice_phone_number'] . "<br />" : "";
		$companyInfoBlock .= ($clms_config['ftsclms_invoice_fax'] != "") ? "Fax: " . $clms_config['ftsclms_invoice_fax'] . "<br />" : "";
		$companyInfoBlock .= ($clms_config['ftsclms_invoice_email_address'] != "") ? "Email: " . $clms_config['ftsclms_invoice_email_address'] . "<br />" : "";
		$companyInfoBlock .= ($clms_config['ftsclms_invoice_website'] != "") ? "Website: " . $clms_config['ftsclms_invoice_website'] . "<br />" : "";
			
		return $companyInfoBlock;
	}
	
	//=========================================================
	// Returns a header for emails
	//=========================================================
	function returnEmailHeader() {
		global $clms_config;
		
		return "<img src=\"" . returnHttpLinks($clms_config['ftsss_store_url']) . "/images/logo.png\" alt=\"" . $clms_config['ftsss_store_name'] . "\" /><br />
			Phone: " . $clms_config['ftsss_phone_number'] . "<br />
			Fax: " . $clms_config['ftsss_fax'] . "<br />
			Website: " . returnHttpLinks($clms_config['ftsss_store_url']) . "<br /><br />";
	}	
	
	//=========================================================
	// Sends an email message using the supplied values
	//=========================================================
	function emailMessage($emailAddress, $subject, $message) {
		global $clms_config;
		
		$headers = "";
		
		// Additional headers
		//$headers .= "To: " . $emailAddress . "\n";
		$headers .= "From: " . $clms_config['ftsclms_invoice_email_address'] . "\n";
		
		// To send HTML mail, the content-type header must be set
		$headers .= "MIME-Version: 1.0" . "\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
		
		// Mail it
		$emailResult = mail($emailAddress, $subject, $message, $headers);
		
		if ($emailResult) {
			return 1;
		}
		else {
			return 0;
		}
	}

	//=========================================================
	// Allows us to get any remote file we need with post vars
	//=========================================================	
	function returnRemoteFilePost($host, $directory, $filename, $urlVariablesArray = array()) {
		$result = "";
	
		$urlVariables = array();    
		foreach($urlVariablesArray as $key=>$value) {
	        $urlVariables[] = $key . "=" . urlencode($value);
	    }  
		$urlVariables = implode('&', $urlVariables);

		//open connection
		$ch = curl_init();
		
		//set the url, number of POST vars, POST data
		curl_setopt($ch, CURLOPT_URL, "http://" . $host . "/" . $directory . "/" . $filename);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $urlVariables);
		
		//execute post
		$result = curl_exec($ch);
		$info = curl_getinfo($ch);
		
		//close connection
		curl_close($ch);		
		
		return $result;
	}
	
	//==================================================
	// This function will notify user of updates and
	// other important information
	//
	// USAGE:
	// version_functions();
	// 
	// Removal or hinderance is a direct violation of 
	// the program license and is constituted as a 
	// breach of contract as is punishable by law.
	//
	// MODIFIED TO REMOVE CALLHOME AND VERSION CHECK
	//==================================================
	function version_functions($print_update_info) {
		include('_license.php');
		
		//=========================================================
		// Get all of the variables we need to pass to the 
		// call home script ready
		//=========================================================
		
			
		//=========================================================
		// Should we display advanced option?
		// Connection to the FTS server has to be made or the 
		// options will not be shown
		//=========================================================
		if ($print_update_info == "advancedOptions" || $print_update_info == "advancedOptionsText") {
			return true;
		}
			
		//=========================================================
		// Should we print out wether or not to update?
		//=========================================================
		if ($print_update_info == "yes") {
			//return "<div class=\"errorMessage\">Version check connection failed.</div>";
		}
	}

?>