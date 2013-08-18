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
 *
 * This program is licensed under the Fast Track Sites Program license 
 * located inside the license.txt file included with this program. This is a 
 * legally binding license, and is protected by all applicable laws, by 
 * editing this page you fall subject to these licensing terms.
 *
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
	$str = preg_replace("#\[url\](.*?)?(.*?)\[/url\]#si", "<A HREF=\"\\1\\2\" TARGET=\"_blank\">\\1\\2</A>", $str);
	
	// Link inside first tag new window
	$str = preg_replace("#\[url=(.*?)?(.*?)\](.*?)\[/url\]#si", "<A HREF=\"\\2\" TARGET=\"_blank\">\\3</A>", $str);
	
	// Link inside tags
	$str = preg_replace("#\[url2\](.*?)?(.*?)\[/url2\]#si", "<A HREF=\"\\1\\2\">\\1\\2</A>", $str);
	
	// Link inside first tag
	$str = preg_replace("#\[url2=(.*?)?(.*?)\](.*?)\[/url2\]#si", "<A HREF=\"\\2\">\\3</A>", $str);
	
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


//==================================================
// Prints out a lovely little bbcode button box
// Keeps me from having to redo several pages
//==================================================
function bbcode_box() {
	$returnstring = "\n	<tr class='row1'>";
	$returnstring .= "\n			<td colspan='2'><center>";
	$returnstring .= "\n				<table border='0' cellspacing='0' cellpadding='0'>";
	$returnstring .= "\n					<tr class='row1'>";
	$returnstring .= "\n						<td style='padding: 1px; margin: 0px;'><img src=\"images/bbcode/bold.gif\" alt=\"Bold\" title=\"Bold\" onclick=\"bbstyle(0)\" onmouseover=\"helpline('b')\" height=\"24\" width=\"25\" /></td>";
	$returnstring .= "\n						<td style='padding: 1px; margin: 0px;'><img src=\"images/bbcode/italic.gif\" alt=\"Italic\" title=\"Italic\" onclick=\"bbstyle(2)\" onmouseover=\"helpline('i')\" height=\"24\" width=\"25\" /></td>";
	$returnstring .= "\n						<td style='padding: 1px; margin: 0px;'><img src=\"images/bbcode/underline.gif\" alt=\"Underline\" title=\"Underline\" onclick=\"bbstyle(4)\" onmouseover=\"helpline('u')\" height=\"24\" width=\"25\" /></td>";
	$returnstring .= "\n						<td style='padding: 1px; margin: 0px;'><img src=\"images/bbcode/image.gif\" alt=\"Insert Image\" title=\"Insert Image\" onclick=\"bbstyle(14)\" onmouseover=\"helpline('p')\" height=\"24\" width=\"25\" /></td>";
	$returnstring .= "\n						<td style='padding: 1px; margin: 0px;'><img src=\"images/bbcode/email.gif\" alt=\"Insert Email\" title=\"Insert Email\" onclick=\"bbstyle(18)\" onmouseover=\"helpline('email')\" height=\"24\" width=\"25\" /></td>";
	$returnstring .= "\n						<td style='padding: 1px; margin: 0px;'><img src=\"images/bbcode/hyperlink.gif\" alt=\"Insert Link\" title=\"Insert Link\" onclick=\"bbstyle(16)\" onmouseover=\"helpline('w')\" height=\"24\" width=\"25\" /></td>";
	$returnstring .= "\n						<td style='padding: 1px; margin: 0px;'><img src=\"images/bbcode/left_just.gif\" alt=\"Align Text To The Left\" title=\"Align Text To The Left\" onclick=\"bbstyle(24)\" onmouseover=\"helpline('left')\" height=\"24\" width=\"25\" /></td>";
	$returnstring .= "\n						<td style='padding: 1px; margin: 0px;'><img src=\"images/bbcode/center.gif\" alt=\"Align Text To The Center\" title=\"Align Text To The Center\" onclick=\"bbstyle(22)\" onmouseover=\"helpline('center')\" height=\"24\" width=\"25\" /></td>";
	$returnstring .= "\n						<td style='padding: 1px; margin: 0px;'><img src=\"images/bbcode/right_just.gif\" alt=\"Align Text To The Right\" title=\"Align Text To The Right\" onclick=\"bbstyle(26)\" onmouseover=\"helpline('right')\" height=\"24\" width=\"25\" /></td>";
	$returnstring .= "\n						<td style='padding: 1px; margin: 0px;'><img src=\"images/bbcode/numbered_list.gif\" alt=\"Insert List\" title=\"Insert List\" onclick=\"bbstyle(10)\" onmouseover=\"helpline('l')\" height=\"24\" width=\"25\" /></td>";
	$returnstring .= "\n						<td style='padding: 1px; margin: 0px;'><img src=\"images/bbcode/list.gif\" alt=\"Insert List\" title=\"Insert List\" onclick=\"bbstyle(12)\" onmouseover=\"helpline('o')\" height=\"24\" width=\"25\" /></td>";
	$returnstring .= "\n						<td style='padding: 1px; margin: 0px;'><img src=\"images/bbcode/quote.gif\" alt=\"Wrap in a Quote\" title=\"Wrap in a Quote\" onclick=\"bbstyle(6)\" onmouseover=\"helpline('q')\" height=\"24\" width=\"25\" /></td>";
	$returnstring .= "\n						<td style='padding: 1px; margin: 0px;'><img src=\"images/bbcode/code.gif\" code=\"\" title=\"Code\" onclick=\"bbstyle(8)\" onmouseover=\"helpline('c')\" height=\"24\" width=\"25\" /></td>";
	$returnstring .= "\n						<td style='padding: 1px; margin: 0px;'><img src=\"images/bbcode/php.gif\" code=\"\" title=\"PHP\" onclick=\"bbstyle(20)\" onmouseover=\"helpline('php')\" height=\"24\" width=\"25\" /></td>";
	$returnstring .= "\n						<td style='padding: 1px; margin: 0px;'><img src=\"images/bbcode/youtube.gif\" code=\"\" title=\"YouTube\" onclick=\"bbstyle(28)\" onmouseover=\"helpline('youtube')\" height=\"20\" width=\"20\" /></td>";
	$returnstring .= "\n						<td style='padding: 1px; margin: 0px;'><img src=\"images/bbcode/googlevid.gif\" code=\"\" title=\"Google Video\" onclick=\"bbstyle(30)\" onmouseover=\"helpline('gvideo')\" height=\"20\" width=\"20\" /></td>";
	$returnstring .= "\n					</tr>";
	$returnstring .= "\n					<tr class='row1'>";
	$returnstring .= "\n						<td colspan='14'>";
	$returnstring .= "\n 							&nbsp;Font row: ";
	$returnstring .= "\n							<select name=\"fontcolor\" onchange=\"bbfontstyle('[color=' + this.form.fontcolor.options[this.form.fontcolor.selectedIndex].value + ']', '[/color]');this.selectedIndex=0;\" onmouseover=\"helpline('s')\">";
	$returnstring .= "\n								<option style=\"color:black; background-color: #FAFAFA\" value=\"#444444\">Default</option>";
	$returnstring .= "\n								<option style=\"color:darkred; background-color: #FAFAFA\" value=\"darkred\">Dark Red</option>";
	$returnstring .= "\n								<option style=\"color:red; background-color: #FAFAFA\" value=\"red\">Red</option>";
	$returnstring .= "\n								<option style=\"color:orange; background-color: #FAFAFA\" value=\"orange\">Orange</option>";
	$returnstring .= "\n								<option style=\"color:brown; background-color: #FAFAFA\" value=\"brown\">Brown</option>";
	$returnstring .= "\n								<option style=\"color:yellow; background-color: #FAFAFA\" value=\"yellow\">Yellow</option>";
	$returnstring .= "\n								<option style=\"color:green; background-color: #FAFAFA\" value=\"green\">Green</option>";
	$returnstring .= "\n								<option style=\"color:olive; background-color: #FAFAFA\" value=\"olive\">Olive</option>";
	$returnstring .= "\n								<option style=\"color:cyan; background-color: #FAFAFA\" value=\"cyan\">Cyan</option>";
	$returnstring .= "\n								<option style=\"color:blue; background-color: #FAFAFA\" value=\"blue\">Blue</option>";
	$returnstring .= "\n								<option style=\"color:darkblue; background-color: #FAFAFA\" value=\"darkblue\">Dark Blue</option>";
	$returnstring .= "\n								<option style=\"color:indigo; background-color: #FAFAFA\" value=\"indigo\">Indigo</option>";
	$returnstring .= "\n								<option style=\"color:violet; background-color: #FAFAFA\" value=\"violet\">Violet</option>";
	$returnstring .= "\n								<option style=\"color:white; background-color: #FAFAFA\" value=\"white\">White</option>";
	$returnstring .= "\n								<option style=\"color:black; background-color: #FAFAFA\" value=\"black\">Black</option>";
	$returnstring .= "\n							</select> "; 
	$returnstring .= "\n 							&nbsp;Font size: ";
	$returnstring .= "\n							<select name=\"fontsize\" onchange=\"bbfontstyle('[size=' + this.form.fontsize.options[this.form.fontsize.selectedIndex].value + ']', '[/size]')\" onmouseover=\"helpline('f')\">";
	$returnstring .= "\n								<option value=\"7\">Tiny</option>";
	$returnstring .= "\n								<option value=\"9\">Small</option>";
	$returnstring .= "\n								<option value=\"12\" selected>Normal</option>";
	$returnstring .= "\n								<option value=\"18\">Large</option>";
	$returnstring .= "\n								<option  value=\"24\">Huge</option>";
	$returnstring .= "\n							</select>";
	$returnstring .= "\n						</td>";
	$returnstring .= "\n					</tr>";
	$returnstring .= "\n					<tr class='row1'>";
	$returnstring .= "\n						<td colspan='14'>";
	$returnstring .= "\n							<input name=\"helpbox\" size='45' maxlength='100' style='width: 380px; font-size: 10px;' class='helpline' value=\"Tip: Styles can be applied quickly to selected text.\" type=\"text\">";
	$returnstring .= "\n					</td>";
	$returnstring .= "\n				</tr>";
	$returnstring .= "\n			</table>";
	$returnstring .= "\n		</center></td>";
	$returnstring .= "\n	</tr>";

	return $returnstring;					
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
	if (is_numeric($value)) { return returnCurrencySymbol() . number_format($value, 2, '.', ','); }
	else { return $value; }
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
// Gets a list of models based on a type string
//=========================================================
function createDropdown($type, $inputName, $currentSelection, $onChange) {
	global $DBTABLEPREFIX, $clms_config;
	
	$onChangeVar = ($onChange == "") ? "" : " onChange=\"" . $onChange . "\"";
	$dropdown = "<select name=\"" . $inputName . "\" id=\"" . $inputName . "\"" . $onChangeVar . ">
					<option value=\"\">--Select One--</option>";
	if ($type == "categories") {
		$sql = "SELECT cat_id, cat_name FROM `" . $DBTABLEPREFIX . "categories` ORDER BY cat_name";
		$result = mysql_query($sql);
		
		if ($result && mysql_num_rows($result) > 0) {
			while ($row = mysql_fetch_array($result)) {
				$dropdown .= "<option value=\"" . $row['cat_id'] . "\"" . testSelected($row['cat_id'], $currentSelection) . ">" . $row['cat_name'] . "</option>";
			}
			mysql_free_result($result);
		}
	}
	if ($type == "clients") {
		$sql = "SELECT clients_id, clients_first_name, clients_last_name FROM `" . $DBTABLEPREFIX . "clients` ORDER BY clients_last_name";
		$result = mysql_query($sql);
		
		if ($result && mysql_num_rows($result) > 0) {
			while ($row = mysql_fetch_array($result)) {
				$dropdown .= "<option value=\"" . $row['clients_id'] . "\"" . testSelected($row['clients_id'], $currentSelection) . ">" . $row['clients_last_name'] . ", " . $row['clients_first_name'] . "</option>";
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
	if ($type == "urgency") {
		$dropdown .= "
					<option value=\"" . LOW . "\"" . testSelected($currentSelection, LOW) . ">Low</option>
					<option value=\"" . MEDIUM . "\"" . testSelected($currentSelection, MEDIUM) . ">Medium</option>
					<option value=\"" . HIGH . "\"" . testSelected($currentSelection, HIGH) . ">High</option>";
	}
	if ($type == "users") {
		$sql = "SELECT users_id, users_email_address, users_first_name, users_last_name FROM `" . $DBTABLEPREFIX . "users` ORDER BY users_last_name";
		$result = mysql_query($sql);
		
		if ($result && mysql_num_rows($result) > 0) {
			while ($row = mysql_fetch_array($result)) {
				$dropdown .= "<option value=\"" . $row['users_id'] . "\"" . testSelected($row['users_id'], $currentSelection) . ">" . $row['users_last_name'] . ", " . $row['users_first_name'] . " (" . $row['users_email_address'] . ")</option>";
			}
			mysql_free_result($result);
		}
	}
	if ($type == "userlevel") {
		$dropdown .= "
					<option value=\"" . BANNED . "\"" . testSelected($currentSelection, BANNED) . ">Banned</option>
					<option value=\"" . USER . "\"" . testSelected($currentSelection, USER) . ">User</option>
					<option value=\"" . MOD . "\"" . testSelected($currentSelection, MOD) . ">Moderator</option>
					<option value=\"" . ADMIN . "\"" . testSelected($currentSelection, ADMIN) . ">Administrator</option>";
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
	$headers .= "From: " . $clms_config['ftsss_sales_email'] . "\n";
	
	// To send HTML mail, the Content-type header must be set
	$headers .= "MIME-Version: 1.0" . "\n";
	$headers .= "Content-type: text/html; charset=iso-8859-1";
	
	// Mail it
	mail($emailAddress, $subject, returnEmailHeader() . $message, $headers);
	
	if ($emailResult) {
		return 1;
	}
	else {
		return 0;
	}
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