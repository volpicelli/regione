<?php require_once "phpgallery/include_phpgallery.php" ?>
<?php

//must declare the gallery and set property here! for ajax request
$gallery=new PhpGallery();

$gallery->GalleryFolder="defaultalbum"; //the path to the image directory
$gallery->AllowEdit=true;			//default is false
$gallery->AllowPostComment=true;	//default is false
$gallery->AllowShowComment=true;	//default is true;

//set the user info for comment poster, if not set, the poster will save as guest
$gallery->LogonUserID="username@server.com";
$gallery->LogonUserName="UserName";

//set the resource component option.
$gallery->Layout="SlideShow";
$gallery->Editor="Explorer";
$gallery->Slider="NewWin";
$gallery->Viewer="LightBox";
$gallery->Popup="Default";
$gallery->Theme="Classic";

//handle the ajax request , if any
$gallery->ProcessAjaxPostback();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>PHP Gallery</title>
	<link href="Sample.css" type="text/css" rel="stylesheet" />
</head>
<body>

<div id="Common">
<div > Titolo gallery </div>
<!--
<div id="CommonHeader">
	<table align="center" width="100%" border="0" cellspacing="0" cellpadding="2">
		<tr>
		    <td style="text-align:right;font-size:11px;padding-right:20px;">
	            <a href="http://cutesoft.net/forums/18/ShowForum.aspx" target="_blank" title="Image Gallery Forums">Forums</a> |
	            <a href="http://cutesoft.net/Support/" target="_blank">Support</a> | 
	            <a href="http://cutesoft.net/About+CuteSoft/" target="_blank" title="About Us">Company</a>
		    </td>
	    </tr>
		<tr>
			<td>
				<a href="index.php">
				  <img src="logo.png" />
				</a>
			</td>
		</tr>
	</table>
    <div class="nav">
	    <a href="index.php" class="current">Home</a><span class="Accent">|</span>	
	    <a href="Demo.htm">Demo</a><span class="Accent">|</span>
		<a href="Deployment.htm">Deployment</a><span class="Accent">|</span>
		<a href="http://phpimagegallery.com/Order.php" title="Purchase">Order</a>
    </div> 
</div>	
-->
	<div id="CommonBody" align="center">
	<?php
		$gallery->Render();
	?>
	</div>	
</div>

</body>
</html>


