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
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This program is also licensed under a program license located inside 
 * the license.txt file included with this program. This license overrides
 * all license statements made in the previous license reveference. 
 ***************************************************************************/

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
define('ADMIN', 1);
define('MOD', 2);
define('BANNED', 3);

//=====================================================
// Currencies
//=====================================================
$FTS_CURRENCIES = array("$" => "Dollar ($)", "&euro;" => "Euro (&euro;)", "&pound;" => "Pound (&pound;)", "&yen;" => "Yen (&yen;)");

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

//=====================================================
// Application
//=====================================================
$A_Name = "fts_clms";
$A_Version = "1.09.10.02";
?>