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

	// See if we have a user account
	$sql = "SELECT * FROM `" . USERSDBTABLEPREFIX . "users` WHERE username='" . $username . "' AND password='" . $password . "' AND active='1';";
	$result = mysql_query($sql);
	//echo $sql;
	
	if($result && mysql_num_rows($result) > 0){
		while($row = mysql_fetch_array($result)){			
			// Create a login cookie
			if (isset($_POST['autologin'])) {
				$cookiename = $clms_config['ftsclms_cookie_name'];
				setcookie($cookiename, $row['id'] . "-" . $row['password'], time()+2592000 ); //set cookie for 1 month
			}
									
			// Register some session variables!
			$_SESSION['STATUS'] = "true";
			$_SESSION['userid'] = $row['id'];
			$_SESSION['username'] = $row['username'];
			$_SESSION['epassword'] = $row['password'];
			$_SESSION['email_address'] = $row['email_address'];
			$_SESSION['first_name'] = $row['first_name'];
			$_SESSION['last_name'] = $row['last_name'];
			$_SESSION['website'] = $row['website'];
			$_SESSION['user_level'] = $row['user_level'];
			$_SESSION['script_locale'] = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

			header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/" . $menuvar['LOGIN']);
			
		 	$page_content .= "You are now logged in as " . $_SESSION['username'] . ". <br /><div class=\"center\"><a href=\"" . $menuvar['LOGOUT'] . "\">Logout</a></div>"; 
		}
	} 
	else {	
		// See if we have a client account
		$sql = "SELECT * FROM `" . DBTABLEPREFIX . "clients` WHERE username='" . $username . "' AND password='" . $password . "';";
		$result = mysql_query($sql);
		//echo $sql;
		
		if($result && mysql_num_rows($result) > 0){
			while($row = mysql_fetch_array($result)){
				// Create a login cookie
				if (isset($_POST['autologin'])) {
					$cookiename = $clms_config['ftsclms_cookie_name'];
					setcookie($cookiename, $row['id'] . "-" . $row['username'], time()+2592000 ); //set cookie for 1 month
				}
										
				// Register some session variables!
				$_SESSION['STATUS'] = "true";
				$_SESSION['userid'] = $row['id'];
				$_SESSION['username'] = $row['username'];
				$_SESSION['epassword'] = $row['password'];
				$_SESSION['email_address'] = $row['email_address'];
				$_SESSION['first_name'] = $row['first_name'];
				$_SESSION['last_name'] = $row['last_name'];
				$_SESSION['website'] = $row['website'];
				$_SESSION['user_level'] = USER;
				$_SESSION['script_locale'] = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	
				header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/" . $menuvar['LOGIN']);

			 	$page_content .= "You are now logged in as " . $_SESSION['username'] . ". <br /><div class=\"center\"><a href=\"" . $menuvar['LOGOUT'] . "\">Logout</a></div>"; 
			}	
		} 
		else {
			$page_content .= "You could not be logged in! Either the username and password do not match or you have not validated your membership!<br />
			Please try again!<br /><a href=\"" . $menuvar['HOME'] . "\">Home</a>.";
		}
	}
	
	unset($_POST['password']);
}

//========================================
// If we got here check and see if they 
// are logged in, if not print login page
//========================================
else{
	if (isset($_SESSION['username'])) {
		$page_content .= "
			You are logged in as " . $_SESSION['username'] . ", and are being redirected to the main page. 
			<br /><div class=\"center\"><a href=\"" . $menuvar['LOGOUT'] . "\">Logout</a></div>
			<meta http-equiv=\"refresh\" content=\"1;url=" . $menuvar['HOME'] . "\">"; 
	}
	else { 
		$page_content .= "
				<form name=\"loginForm\" id=\"loginForm\" action=\"" . $menuvar['LOGIN'] . "\" method=\"post\" class=\"inputForm\">
					<fieldset>
						<legend><span>Login</span></legend>
						<div><label for=\"username\">Username </label> <input type=\"text\" name=\"username\" id=\"username\" size=\"20\"  maxlength=\"40\"class=\"required\" /></div>
						<div><label for=\"password\">Password </label> <input type=\"password\" name=\"password\" id=\"password\" size=\"20\" maxlength=\"25\" class=\"required\" /></div>
						<div><label for=\"autologin\">Stay logged in</label> <input type=\"checkbox\" name=\"autologin\" value=\"1\" checked /></div>
						<div class=\"center\"><input type=\"submit\" class=\"button\" value=\"Login\" /></div>
					</fieldset>
				</form>";
	
		// Handle our JQuery needs
		$JQueryReadyScripts = "
			var v = jQuery(\"#loginForm\").validate({
				errorElement: \"div\",
				errorClass: \"validation-advice\"
			});";
	}
}

$page->setTemplateVar("PageContent", $page_content);	
	$page->setTemplateVar("JQueryReadyScript", $JQueryReadyScripts);

?>