<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"> 
	<head>
		<title>Fast Track Sites Updater - <? $page->printTemplateVar("PageTitle");  ?></title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta http-equiv="content-language" content="en-us" />
		<!--Stylesheets Begin-->
			<link rel="stylesheet" type="text/css" href="themes/installer/main.css" />
			<!--[if lt IE 7]>
				<style>
				</style>
			<![endif]-->			
		<!--Stylesheets End-->
	<!--Javascripts Begin-->
		<script type="text/javascript" src="javascripts/scriptaculous1.8.2.js"></script>	
		<script type="text/javascript" src="javascripts/validation.js"></script>	
	<!--Javascripts End-->
	</head>
	<body>
		<div id="container">
			<div id="page">
				<div id="contentHeader"></div>		
				<div id="header">
					<img src="images/ftsLogo.png" alt="Fast Track Sites" />
				</div>		
				<div id="content">
					<div id="leftCol">
						<ul id="steps">
							<li<? if ($actual_step == 1) { echo " class=\"current\""; } ?>>1. Update Databse Tables</li>
							<li<? if ($actual_step == 2) { echo " class=\"current\""; } ?>>2. Finish</li>
						</ul>
					</div>
					<div id="rightCol">
						<? $page->printTemplateVar('PageContent'); ?>	
					</div>
				</div>				
				<div id="footer">
					<div style="float: right; padding-right: 5px;">
						Powered By: <a href="http://www.fasttracksites.com">Fast Track Sites Updater</a>
					</div>
					Copyright &copy; 2009 Fast Track Sites
					<br />
					<em><a href="http://www.wefunction.com/function-free-icon-set">Function Icons</a></em>
				</div>
				<div id="contentFooter"></div>	
			</div>
		</div>
	</body>
</html>
