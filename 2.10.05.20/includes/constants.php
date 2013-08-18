<?php 
/***************************************************************************
 *                               constants.php
 *                            -------------------
 *   begin                : Tuseday, March 14, 2006
 *   copyright            : (C) 2006 Fast Track Sites
 *   email                : sales@fasttracksites.com
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

//=====================================================
// Application
//=====================================================
define('A_NAME', 'fts_clms');
define('A_VERSION', '2.10.05.20');

//=====================================================
// Debug Level
//=====================================================
//define('DEBUG', 1); // Debugging on
define('DEBUG', 0); // Debugging off

//=====================================================
// Global state
//=====================================================
define('ACTIVE', 1);
define('INACTIVE', 0);

//=====================================================
// Urgency
//=====================================================
define('LOW', 0);
define('MEDIUM', 1);
define('HIGH', 2);

//=====================================================
// User Levels <- Do not change these values!!
//=====================================================
define('USER', 0);
define('SYSTEM_ADMIN', 1);
define('CLIENT_ADMIN', 2);
define('BANNED', 3);

//=====================================================
// Status
//=====================================================
define('STATUS_INVOICE_AWAITING_PAYMENT', 0);
define('STATUS_INVOICE_PAID', 1);
define('STATUS_INVOICE_VOID', 2);

define('STATUS_INVOICE_AWAITING_PAYMENT_STATUS_TXT', 'Awaiting Payment');
define('STATUS_INVOICE_PAID_STATUS_TXT', 'Paid');
define('STATUS_INVOICE_VOID_STATUS_TXT', 'Void');

define('STATUS_ORDER_AWAITING_PAYMENT', 0);
define('STATUS_ORDER_PAID', 1);
define('STATUS_ORDER_VOID', 2);

define('STATUS_ORDER_AWAITING_PAYMENT_STATUS_TXT', 'Awaiting Payment');
define('STATUS_ORDER_PAID_STATUS_TXT', 'Paid');
define('STATUS_ORDER_VOID_STATUS_TXT', 'Void');

//=====================================================
// Payment Types
//=====================================================
$FTS_PAYMENTTYPES = array("0" => "Cash", "1" => "Check", "2" => "Credit Card", "3" => "Google Checkout", "4" => "PayPal");

//=====================================================
// Currencies
//=====================================================
$FTS_CURRENCIES = array("$" => "Dollar ($)", "&euro;" => "Euro (&euro;)", "&pound;" => "Pound (&pound;)", "&yen;" => "Yen (&yen;)");

//=====================================================
// System Settings
//=====================================================
$FTS_TIMEZONES = array(
	"-12" => "[UTC - 12] Baker Island Time",
	"-11" => "[UTC - 11] Niue Time, Samoa Standard Time",
	"-10" => "[UTC - 10] Hawaii-Aleutian Standard Time, Cook Island Time",
	"-9.5" => "[UTC - 9:30] Marquesas Islands Time",
	"-9" => "[UTC - 9] Alaska Standard Time, Gambier Island Time",
	"-8" => "[UTC - 8] Pacific Standard Time",
	"-7" => "[UTC - 7] Mountain Standard Time",
	"-6" => "[UTC - 6] Central Standard Time",
	"-5" => "[UTC - 5] Eastern Standard Time",
	"-4.5" => "[UTC - 4:30] Venezuelan Standard Time",
	"-4" => "[UTC - 4] Atlantic Standard Time",
	"-3.5" => "[UTC - 3:30] Newfoundland Standard Time",
	"-3" => "[UTC - 3] Amazon Standard Time, Central Greenland Time",
	"-2" => "[UTC - 2] Fernando de Noronha Time, South Georgia &amp; the South Sandwich Islands Time",
	"-1" => "[UTC - 1] Azores Standard Time, Cape Verde Time, Eastern Greenland Time",
	"0" => "[UTC] Western European Time, Greenwich Mean Time",
	"1" => "[UTC + 1] Central European Time, West African Time",
	"2" => "[UTC + 2] Eastern European Time, Central African Time",
	"3" => "[UTC + 3] Moscow Standard Time, Eastern African Time",
	"3.5" => "[UTC + 3:30] Iran Standard Time",
	"4" => "[UTC + 4] Gulf Standard Time, Samara Standard Time",
	"4.5" => "[UTC + 4:30] Afghanistan Time",
	"5" => "[UTC + 5] Pakistan Standard Time, Yekaterinburg Standard Time",
	"5.5" => "[UTC + 5:30] Indian Standard Time, Sri Lanka Time",
	"5.75" => "[UTC + 5:45] Nepal Time",
	"6" => "[UTC + 6] Bangladesh Time, Bhutan Time, Novosibirsk Standard Time",
	"6.5" => "[UTC + 6:30] Cocos Islands Time, Myanmar Time",
	"7" => "[UTC + 7] Indochina Time, Krasnoyarsk Standard Time",
	"8" => "[UTC + 8] Chinese Standard Time, Australian Western Standard Time, Irkutsk Standard Time",
	"8.75" => "[UTC + 8:45] Southeastern Western Australia Standard Time",
	"9" => "[UTC + 9] Japan Standard Time, Korea Standard Time, Chita Standard Time",
	"9.5" => "[UTC + 9:30] Australian Central Standard Time",
	"10" => "[UTC + 10] Australian Eastern Standard Time, Vladivostok Standard Time",
	"10.5" => "[UTC + 10:30] Lord Howe Standard Time",
	"11" => "[UTC + 11] Solomon Island Time, Magadan Standard Time",
	"11.5" => "[UTC + 11:30] Norfolk Island Time",
	"12" => "[UTC + 12] New Zealand Time, Fiji Time, Kamchatka Standard Time",
	"12.75" => "[UTC + 12:45] Chatham Islands Time",
	"13" => "[UTC + 13] Tonga Time, Phoenix Islands Time",
	"14" => "[UTC + 14] Line Island Time"
);

$FTS_COUNTRIES = array("USA" => "United States", "CAN" => "Canada", "MEX" => "Mexico", "AFG" => "Afghanistan", "ALB" => "Albania", "DZA" => "Algeria", "ASM" => "American Samoa", "AND" => "Andorra", "AGO" => "Angola", "AIA" => "Anguilla", "ATA" => "Antarctica", "ATG" => "Antigua and Barbuda", "ARG" => "Argentina", "ARM" => "Armenia", "ABW" => "Aruba", "AUS" => "Australia", "AUT" => "Austria", "AZE" => "Azerbaijan", "BHS" => "Bahamas", "BHR" => "Bahrain", "BGD" => "Bangladesh", "BRB" => "Barbados", "BLR" => "Belarus", "BEL" => "Belgium", "BLZ" => "Belize", "BEN" => "Benin", "BMU" => "Bermuda", "BTN" => "Bhutan", "BOL" => "Bolivia", "BIH" => "Bosnia and Herzegowina", "BWA" => "Botswana", "BVT" => "Bouvet Island", "BRA" => "Brazil", "IOT" => "British Indian Ocean Terr.", "BRN" => "Brunei Darussalam", "BGR" => "Bulgaria", "BFA" => "Burkina Faso", "BDI" => "Burundi", "KHM" => "Cambodia", "CMR" => "Cameroon", "CPV" => "Cape Verde", "CYM" => "Cayman Islands", "CAF" => "Central African Republic", "TCD" => "Chad", "CHL" => "Chile", "CHN" => "China", "CXR" => "Christmas Island", "CCK" => "Cocos (Keeling) Islands", "COL" => "Colombia", "COM" => "Comoros", "COG" => "Congo", "COK" => "Cook Islands", "CRI" => "Costa Rica", "CIV" => "Cote d'Ivoire", "HRV" => "Croatia (Hrvatska)", "CUB" => "Cuba", "CYP" => "Cyprus", "CZE" => "Czech Republic", "DNK" => "Denmark", "DJI" => "Djibouti", "DMA" => "Dominica", "DOM" => "Dominican Republic", "TMP" => "East Timor", "ECU" => "Ecuador", "EGY" => "Egypt", "SLV" => "El Salvador", "GNQ" => "Equatorial Guinea", "ERI" => "Eritrea", "EST" => "Estonia", "ETH" => "Ethiopia", "FLK" => "Falkland Islands/Malvinas", "FRO" => "Faroe Islands", "FJI" => "Fiji", "FIN" => "Finland", "FRA" => "France", "FXX" => "France, Metropolitan", "GUF" => "French Guiana", "PYF" => "French Polynesia", "ATF" => "French Southern Terr.", "GAB" => "Gabon", "GMB" => "Gambia", "GEO" => "Georgia", "DEU" => "Germany", "GHA" => "Ghana", "GIB" => "Gibraltar", "GRC" => "Greece", "GRL" => "Greenland", "GRD" => "Grenada", "GLP" => "Guadeloupe", "GUM" => "Guam", "GTM" => "Guatemala", "GIN" => "Guinea", "GNB" => "Guinea-Bissau", "GUY" => "Guyana", "HTI" => "Haiti", "HMD" => "Heard & McDonald Is.", "HND" => "Honduras", "HKG" => "Hong Kong", "HUN" => "Hungary", "ISL" => "Iceland", "IND" => "India", "IDN" => "Indonesia", "IRN" => "Iran", "IRQ" => "Iraq", "IRL" => "Ireland", "ISR" => "Israel", "ITA" => "Italy", "JAM" => "Jamaica", "JPN" => "Japan", "JOR" => "Jordan", "KAZ" => "Kazakhstan", "KEN" => "Kenya", "KIR" => "Kiribati", "PRK" => "Korea, North", "KOR" => "Korea, South", "KWT" => "Kuwait", "KGZ" => "Kyrgyzstan", "LAO" => "Lao People's Dem. Rep.", "LVA" => "Latvia", "LBN" => "Lebanon", "LSO" => "Lesotho", "LBR" => "Liberia", "LBY" => "Libyan Arab Jamahiriya", "LIE" => "Liechtenstein", "LTU" => "Lithuania", "LUX" => "Luxembourg", "MAC" => "Macau", "MKD" => "Macedonia", "MDG" => "Madagascar", "MWI" => "Malawi", "MYS" => "Malaysia", "MDV" => "Maldives", "MLI" => "Mali", "MLT" => "Malta", "MHL" => "Marshall Islands", "MTQ" => "Martinique", "MRT" => "Mauritania", "MUS" => "Mauritius", "MYT" => "Mayotte", "FSM" => "Micronesia", "MDA" => "Moldova", "MCO" => "Monaco", "MNG" => "Mongolia", "MSR" => "Montserrat", "MAR" => "Morocco", "MOZ" => "Mozambique", "MMR" => "Myanmar", "NAM" => "Namibia", "NRU" => "Nauru", "NPL" => "Nepal", "NLD" => "Netherlands", "ANT" => "Netherlands Antilles", "NCL" => "New Caledonia", "NZL" => "New Zealand", "NIC" => "Nicaragua", "NER" => "Niger", "NGA" => "Nigeria", "NIU" => "Niue", "NFK" => "Norfolk Island", "MNP" => "Northern Mariana Is.", "NOR" => "Norway", "OMN" => "Oman", "PAK" => "Pakistan", "PLW" => "Palau", "PAN" => "Panama", "PNG" => "Papua New Guinea", "PRY" => "Paraguay", "PER" => "Peru", "PHL" => "Philippines", "PCN" => "Pitcairn", "POL" => "Poland", "PRT" => "Portugal", "PRI" => "Puerto Rico", "QAT" => "Qatar", "REU" => "Reunion", "ROM" => "Romania", "RUS" => "Russian Federation", "RWA" => "Rwanda", "KNA" => "Saint Kitts and Nevis", "LCA" => "Saint Lucia", "VCT" => "St. Vincent & Grenadines", "WSM" => "Samoa", "SMR" => "San Marino", "STP" => "Sao Tome & Principe", "SAU" => "Saudi Arabia", "SEN" => "Senegal", "SYC" => "Seychelles", "SLE" => "Sierra Leone", "SGP" => "Singapore", "SVK" => "Slovakia (Slovak Republic)", "SVN" => "Slovenia", "SLB" => "Solomon Islands", "SOM" => "Somalia", "ZAF" => "South Africa", "SGS" => "S.Georgia & S.Sandwich Is.", "ESP" => "Spain", "LKA" => "Sri Lanka", "SHN" => "St. Helena", "SPM" => "St. Pierre & Miquelon", "SDN" => "Sudan", "SUR" => "Suriname", "SJM" => "Svalbard & Jan Mayen Is.", "SWZ" => "Swaziland", "SWE" => "Sweden", "CHE" => "Switzerland", "SYR" => "Syrian Arab Republic", "TWN" => "Taiwan", "TJK" => "Tajikistan", "TZA" => "Tanzania", "THA" => "Thailand", "TGO" => "Togo", "TKL" => "Tokelau", "TON" => "Tonga", "TTO" => "Trinidad and Tobago", "TUN" => "Tunisia", "TUR" => "Turkey", "TKM" => "Turkmenistan", "TCA" => "Turks & Caicos Islands", "TUV" => "Tuvalu", "UGA" => "Uganda", "UKR" => "Ukraine", "ARE" => "United Arab Emirates", "GBR" => "United Kingdom", "UMI" => "U.S. Minor Outlying Is.", "URY" => "Uruguay", "UZB" => "Uzbekistan", "VUT" => "Vanuatu", "VAT" => "Vatican (Holy See)", "VEN" => "Venezuela", "VNM" => "Viet Nam", "VGB" => "Virgin Islands (British)", "VIR" => "Virgin Islands (U.S.)", "WLF" => "Wallis & Futuna Is.", "ESH" => "Western Sahara", "YEM" => "Yemen", "YUG" => "Yugoslavia", "ZAR" => "Zaire", "ZMB" => "Zambia", "ZWE" => "Zimbabwe");

$FTS_STATES = array("AE" => "AE", "AL" => "Alabama", "AK" => "Alaska", "AZ" => "Arizona", "AR" => "Arkansas", "CA" => "California", 
		"CO" => "Colorado", "CT" => "Connecticut", "DE" => "Delaware", "DC" => "District of Columbia", "FL" => "Florida", 
		"GA" => "Georgia", "HI" => "Hawaii", "ID" => "Idaho", "IL" => "Illinois", "IN" => "Indiana", "IA" => "Iowa", 
		"KS" => "Kansas", "KY" => "Kentucky", "LA" => "Louisiana", "ME" => "Maine", "MD" => "Maryland", 
		"MA" => "Massachusetts", "MI" => "Michigan", "MN" => "Minnesota", "MS" => "Mississippi", "MO" => "Missouri", 
		"MT" => "Montana", "NE" => "Nebraska", "NV" => "Nevada", "NH" => "New Hampshire", "NJ" => "New Jersey", 
		"NM" => "New Mexico", "NY" => "New York", "NC" => "North Carolina", "ND" => "North Dakota", "OH" => "Ohio",
		"OK" => "Oklahoma", "OR" => "Oregon", "PA" => "Pennsylvania", "RI" => "Rhode Island", "SC" => "South Carolina", 
		"SD" => "South Dakota", "TN" => "Tennessee", "TX" => "Texas", "UT" => "Utah", "VT" => "Vermont", 
		"VA" => "Virginia", "WA" => "Washington", "WV" => "West Virginia", "WI" => "Wisconsin", "WY" => "Wyoming");
?>