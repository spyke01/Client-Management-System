<? 
/***************************************************************************
 *                               config.php
 *                            -------------------
 *   begin                : Tuseday, March 14, 2006
 *   copyright            : (C) 2006 Fast Track Sites
 *   email                : sales@fasttracksites.com
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
$clms_config = array();

$sql = "SELECT * FROM `" . $DBTABLEPREFIX . "config`";
$result = mysql_query($sql);

if (mysql_num_rows($result) > 0) {
	while ($row = mysql_fetch_array($result)) {
		$clms_config[$row['config_name']] = $row['config_value'];
	}
	mysql_free_result($result);
}

?>