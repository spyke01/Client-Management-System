<?
/***************************************************************************
 *                               login.php
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
define('IN_LOGIN', 1); //let the header file know were here to stay Hey! Hey! Hey! 

$current_time = time();

//========================================
// Login Function for registering session
//========================================
if (isset($_POST['password'])) {
	// Convert to simple variables
	$username = $_POST['username'];
	$password = $_POST['password'];
	
	if((!$username) || (!$password)){
		echo "Please enter ALL of the information! <br />";
		exit();
	}	
	
	// strip away any dangerous tags
	$username = keepsafe($username);
	$password = keepsafe($password);
	
	// Convert password to md5 hash
	$password = md5($password);

	// check if the user info validates the db
	$sql = mysql_query("SELECT * FROM `" . $DBTABLEPREFIX . "users` WHERE users_username='" . $username . "' AND users_password='" . $password . "' AND users_active='1' ");
	$login_check = mysql_num_rows($sql);
	//echo $sql;
	
	if($login_check > 0){
		while($row = mysql_fetch_array($sql)){
		foreach( $row AS $key => $val ){
			$$key = stripslashes( $val );
		}
			
			if (isset($_POST['autologin'])) {
				$cookiename = $clms_config['ftsclms_cookie_name'];
				setcookie($cookiename, $users_id . "-" . $users_password, time()+2592000 ); //set cookie for 1 month
			}
									
			// Register some session variables!
			$_SESSION['STATUS'] = "true";
			$_SESSION['userid'] = $users_id;
			$_SESSION['username'] = $users_username;
			$_SESSION['epassword'] = $users_password;
			$_SESSION['email_address'] = $users_email_address;
			$_SESSION['full_name'] = $users_full_name;
			$_SESSION['website'] = $users_website;
			$_SESSION['user_level'] = $users_user_level;
			$_SESSION['script_locale'] = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

			header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/" . $menuvar['LOGIN']);
			
		 	$content = "You are now logged in as " . $_SESSION['username'] . ". <br /><div class=\"center\"><a href=\"" . $menuvar['LOGOUT'] . "\">Logout</a></div>"; 
		}

	} 
	else {
		$content = "You could not be logged in! Either the username and password do not match or you have not validated your membership!<br />
		Please try again!<br /><a href=\"" . $menuvar['HOME'] . "\">Home</a>.";
	}
}

//========================================
// If we got here check and see if they 
// are logged in, if not print login page
//========================================
else{

	if (isset($_SESSION['username'])) {
		$content = "
			You are logged in as " . $_SESSION['username'] . ", and are being redirected to the main page. 
			<br /><div class=\"center\"><a href=\"" . $menuvar['LOGOUT'] . "\">Logout</a></div>
			<meta http-equiv=\"refresh\" content=\"1;url=" . $menuvar['ADMIN'] . "\">"; 
	}
	else { 
		$content = "
				<form name=\"loginForm\" id=\"loginForm\" action=\"" . $menuvar['LOGIN'] . "\" method=\"post\">
				<table border=\"0\" cellpadding=\"0\" cellspacing=\"1\" class=\"contentBox half\">
						<tr><td class=\"title1\" colspan=\"2\">User Login</td></tr>
						<tr>
							<td width=\"32%\" class=\"title2\">Username: </td>
							<td width=\"68%\" class=\"row1\"><input type=\"text\" name=\"username\" class=\"login2\" size=\"20\" maxlength=\"40\" value=\"\" /></td>
						</tr>
						<tr>
							<td width=\"32%\" class=\"title2\">Password: </td>
							<td width=\"68%\" class=\"row1\"><input type=\"password\" name=\"password\" class=\"login2\" size=\"20\" maxlength=\"25\" /></td>
						</tr>
						<tr>
							<td width=\"100%\" colspan=\"2\" class=\"row1\"><input type=\"checkbox\" class=\"check\" name=\"autologin\" border=\"0\" value=\"ON\" checked /> Stay logged in</td>
						</tr>
					</table>
					<br /><br />
					<input type=\"submit\" class=\"button\" name=\"login\" value=\"Login\" />
				</form>
				<script type=\"text/javascript\">
					var valid = new Validation('loginForm', {immediate : true, useTitles:true});
				</script>";
	}
}
unset($_POST['password']); //weve finished registering the session variables le them pass so they dont get reregistered

$page->setTemplateVar('PageContent', $content);	

?>