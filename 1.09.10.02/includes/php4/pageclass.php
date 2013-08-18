<?php
/***************************************************************************
 *                               pageclass.php
 *                            -------------------
 *   begin                : Tuesday, August 15, 2006
 *   copyright            : (C) 2006 Paden Clayton
 *   website              : http://www.fasttracksites.com
 *   email                : sales@fasttracksites.com
 *
 *
 ***************************************************************************/

/***************************************************************************
 *
 * This program is licensed under the FTS program license that has been 
 * included with this program. It is located inside the license.txt file 
 * that is found in the base directory. This license is legally binding and
 * in the event of a breach of contract, legal action may be taken. 
 *
 ***************************************************************************/
 
class pageClass { 
	var $email;  
	var $templateVars = array();
	var $topmenu = array();
	var $userOptionsLeftMenu = array();
	var $adminOptionsLeftMenu = array();

	//===============================================================
	// This function will me used for setting our template variables 
	//===============================================================
	function setTemplateVar($varname, $varvalue) {
		$this->templateVars[$varname] = $varvalue;
	}
	
	//===============================================================
	// This function will me used for printing our template variables 
	//===============================================================
	function printTemplateVar($varname) {
		echo $this->templateVars[$varname];
	}
	
	//===============================================================
	// This function determines wether the link is internal or 
	// external. An internal link should be the id in the DB. 
	//===============================================================
	function makeMenuItem($menu, $label, $page, $class = "") {
		if ($menu == "top") {
			$this->topmenu[$label] = array();
			$this->topmenu[$label]['value'] = $this->makeNavURL($page);
			if ($class != "") { $this->topmenu[$label]['class'] = $class; }
		}
		
		if ($menu == "userOptionsLeft") {
			$this->userOptionsLeftMenu[$label] = array();
			$this->userOptionsLeftMenu[$label]['value'] = $this->makeNavURL($page);
			if ($class != "") { $this->userOptionsLeftMenu[$label]['class'] = $class; }
		}
		
		if ($menu == "adminOptionsLeft") {
			$this->adminOptionsLeftMenu[$label] = array();
			$this->adminOptionsLeftMenu[$label]['value'] = $this->makeNavURL($page);
			if ($class != "") { $this->adminOptionsLeftMenu[$label]['class'] = $class; }
		}
	}
	
	//===============================================================
	// This function determines wether the link is internal or 
	// external. An internal link should be the id in the DB. 
	//===============================================================
	function makeNavURL($id) {
		if (is_numeric($id)) {
			return "index.php?p=viewentry&amp;id=$id";
		}
		else {
			return "$id";
		}
	}

	//===============================================================
	// This function prints our menus on the page, it also allows 
	// for customization of what type of what type of tag to use 
	//
	// $menu = top, left, bottom
	// $tag = a, ul, ol
	// $seperator = text that goes between links ie <br />
	// $tagClass = name of a class that is added to each tag
	// $tagBodyClass = name of class that is added to UL or OL
	// $headeritem = text or other item that will be at top of menu
	//===============================================================
	function printMenu($menu, $tag, $seperator = "", $tagClass = "", $tagBodyID = "", $tagBodyClass = "", $headerItem = "") {
		global $bts_config;
		
		if ($menu == "top") { $actualMenu = &$this->topmenu; }
		
		$doneonce = 0;
	
		if ($tag != "a") { 
			$classTag = ($tagBodyClass != "") ? " class=\"$tagBodyClass\"" : "";
			$idTag = ($tagBodyID != "") ? " id=\"$tagBodyID\"" : "";
			echo "<$tag" . $idTag . $classTag . ">"; 
		}
		
		if ($headerItem != "") { echo "\n						$headerItem"; }
		
		foreach ($actualMenu as $text => $page) {
			if ($doneonce == "1" && $seperator != "" && $tag == "a") { echo $seperator; } // do seperators only for a's
			
			$classTag = ($tagClass != "") ? " class=\"$tagClass\"" : "";
			$classTag = (trim($actualMenu[$text]['class']) != "") ? " class=\"" . $actualMenu[$text]['class'] . "\"" : $classTag;
			
			if ($tag != "a") {
				if ($actualMenu[$text]['value'] != "") { echo "\n						<li" . $classTag . "><a href=\"" . $actualMenu[$text]['value'] . "\">$text</a></li>"; }
				else { echo "\n						<li" . $classTag . ">$text</li>"; }
				
			}
			else {
				if ($actualMenu[$text]['value'] != "") { echo "\n						<a href=\"" . $actualMenu[$text]['value'] . "\"" . $classTag . ">$text</a>"; }
				else { echo "\n						$text"; }
			}
			
			$doneonce = "1";
		}
		
		if ($tag != "a") { echo "\n					</$tag>\n"; }
	}	

	//===============================================================
	// This function prints our sidebar 
	//
	// $tagClass = name of a class that is added to the sidebar ul
	//===============================================================
	function printSidebar($tagId, $tagClass = "") {
		global $bts_config;
		
		$doneonce = 0;
	
		$classTag = ($tagClass != "") ? " class=\"$tagClass\"" : "";
		$idTag = ($tagId != "") ? " id=\"$tagId\"" : "";
		echo "<ul" . $classTag . $idTag . ">"; 
		
		// Print userOptionsLeft menu if its active
		if ($this->templateVars['uOLm_active'] == ACTIVE) {
			echo "\n						<li class=\"title\">User Menu</li>";
			
			foreach ($this->userOptionsLeftMenu as $text => $page) {
				echo "\n						<li><a href=\"" . $this->userOptionsLeftMenu[$text]['value'] . "\">$text</a></li>";
			}

		}
		
		// Print adminOptionsLeft menu if its active
		if ($this->templateVars['aOLm_active'] == ACTIVE && ($_SESSION['user_level'] == MOD || $_SESSION['user_level'] == ADMIN)) {
			echo "\n						<li class=\"title\">Admin Menu</li>";
			
			foreach ($this->adminOptionsLeftMenu as $text => $page) {
				echo "\n						<li><a href=\"" . $this->adminOptionsLeftMenu[$text]['value'] . "\">$text</a></li>";
				//print_r($this->adminOptionsLeftMenu);
				//print_r($this->adminOptionsLeftMenu[$text]);
			}

		}
	
		echo "\n						<li class=\"titleFooter\"></li>";
		echo "\n					</ul>\n";
	}	
} 

?>