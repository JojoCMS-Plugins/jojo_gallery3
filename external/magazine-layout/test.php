<?php

/*
test.php
Select a combination of images to create a Magazine layout
*/

//include the class file
require_once('magazinelayout.class.php');

//Define the width for the output area (pixels)
$width = 600;
 
//Define padding around each image - this *must* be included in your stylesheet (pixels)
$padding = 3;
 
//Define your template for outputting images
$template = "<img src=\"image.php?size=[size]&amp;file=[image]\" alt=\"\" />";
 
//create a new instance of the class
$mag = new magazinelayout($width,$padding,$template);
 
//Add the images in any order - uncomment next lines to experiment with different combinations
$i = 0;
if (isset($_POST['l1'])) {$mag->addImage('l1.jpg'); $i++;}
if (isset($_POST['l2'])) {$mag->addImage('l2.jpg'); $i++;}
if (isset($_POST['l3'])) {$mag->addImage('l3.jpg'); $i++;}
if (isset($_POST['l4'])) {$mag->addImage('l4.jpg'); $i++;}
if (isset($_POST['l5'])) {$mag->addImage('l5.jpg'); $i++;}
if (isset($_POST['p1'])) {$mag->addImage('p1.jpg'); $i++;}
if (isset($_POST['p2'])) {$mag->addImage('p2.jpg'); $i++;}
if (isset($_POST['p3'])) {$mag->addImage('p3.jpg'); $i++;}
if (isset($_POST['p4'])) {$mag->addImage('p4.jpg'); $i++;}
if (isset($_POST['p5'])) {$mag->addImage('p5.jpg'); $i++;}
if (isset($_POST['s1'])) {$mag->addImage('s1.jpg'); $i++;}

//display the output
$content = ($i > 0) ? $mag->getHtml() : '';

echo "
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
   \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html>
<head>
<title>Magazine Layout Example</title>
<link type=\"text/css\" rel=\"StyleSheet\" href=\"style.css\" />
</head>
<body>
<h1>Magazine Layout test script</h1>


".$content."

<p>Select 2 or more images to view as a magazine layout, then press the button below.</p>

<form action=\"".$_SERVER['REQUEST_URI']."\" method=\"post\">
<h3>Landscape Images</h3>
<div class=\"thumb\"><label for=\"l1\"><img src=\"image.php?file=l1.jpg&size=100\" alt=\"\" /><br /><input type=\"checkbox\" name=\"l1\" id=\"l1\" value=\"l1\" /></label></div>
<div class=\"thumb\"><label for=\"l2\"><img src=\"image.php?file=l2.jpg&size=100\" alt=\"\" /><br /><input type=\"checkbox\" name=\"l2\" id=\"l2\" value=\"l2\" /></label></div>
<div class=\"thumb\"><label for=\"l3\"><img src=\"image.php?file=l3.jpg&size=100\" alt=\"\" /><br /><input type=\"checkbox\" name=\"l3\" id=\"l3\" value=\"l3\" /></label></div>
<div class=\"thumb\"><label for=\"l4\"><img src=\"image.php?file=l4.jpg&size=100\" alt=\"\" /><br /><input type=\"checkbox\" name=\"l4\" id=\"l4\" value=\"l4\" /></label></div>
<div class=\"thumb\"><label for=\"l5\"><img src=\"image.php?file=l5.jpg&size=100\" alt=\"\" /><br /><input type=\"checkbox\" name=\"l5\" id=\"l5\" value=\"l5\" /></label></div>
<h3 style=\"clear: both;\">Portrait Images</h3>
<div class=\"thumb\"><label for=\"p1\"><img src=\"image.php?file=p1.jpg&size=100\" alt=\"\" /><br /><input type=\"checkbox\" name=\"p1\" id=\"p1\" value=\"p1\" /></label></div>
<div class=\"thumb\"><label for=\"p2\"><img src=\"image.php?file=p2.jpg&size=100\" alt=\"\" /><br /><input type=\"checkbox\" name=\"p2\" id=\"p2\" value=\"p2\" /></label></div>
<div class=\"thumb\"><label for=\"p3\"><img src=\"image.php?file=p3.jpg&size=100\" alt=\"\" /><br /><input type=\"checkbox\" name=\"p3\" id=\"p3\" value=\"p3\" /></label></div>
<div class=\"thumb\"><label for=\"p4\"><img src=\"image.php?file=p4.jpg&size=100\" alt=\"\" /><br /><input type=\"checkbox\" name=\"p4\" id=\"p4\" value=\"p4\" /></label></div>
<div class=\"thumb\"><label for=\"p5\"><img src=\"image.php?file=p5.jpg&size=100\" alt=\"\" /><br /><input type=\"checkbox\" name=\"p5\" id=\"p5\" value=\"p5\" /></label></div>
<h3 style=\"clear: both;\">Square Images</h3>
<div class=\"thumb\"><label for=\"s1\"><img src=\"image.php?file=s1.jpg&size=100\" alt=\"\" /><br /><input type=\"checkbox\" name=\"s1\" id=\"s1\" value=\"s1\" /></label></div>

<div style=\"clear: both;\"><input type=\"submit\" name=\"submit\" value=\"View as Magazine Layout\" /></div>
These images are all stored at 800px across the longest edge. Calculations for resizing images are done on the fly by the Magazine Layout script.
</form>


</body>
</html>
";
