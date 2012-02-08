<?php

/*
EXAMPLE 5
Define any width
*/

//include the class file
require_once('magazinelayout.class.php');



//200px

//Define the width for the output area (pixels)
$width = 200;
 
//Define padding around each image - this *must* be included in your stylesheet (pixels)
$padding = 1;
 
//Define your template for outputting images
$template = "<img src=\"image.php?size=[size]&amp;file=[image]\" alt=\"\" />";
 
//create a new instance of the class
$mag = new magazinelayout($width,$padding,$template);
 
//Add the images in any order - uncomment next lines to experiment with different combinations
$mag->addImage('s1.jpg');
$mag->addImage('l1.jpg');
$mag->addImage('l5.jpg');
$mag->addImage('p5.jpg');

//display the output
$content200 = $mag->getHtml();



//400px

//Define the width for the output area (pixels)
$width = 400;
 
//Define padding around each image - this *must* be included in your stylesheet (pixels)
$padding = 1;
 
//Define your template for outputting images
$template = "<img src=\"image.php?size=[size]&amp;file=[image]\" alt=\"\" />";
 
//create a new instance of the class
$mag = new magazinelayout($width,$padding,$template);
 
//Add the images in any order - uncomment next lines to experiment with different combinations
$mag->addImage('s1.jpg');
$mag->addImage('l1.jpg');
$mag->addImage('l5.jpg');
$mag->addImage('p5.jpg');

//display the output
$content400 = $mag->getHtml();





//600px

//Define the width for the output area (pixels)
$width = 600;
 
//Define padding around each image - this *must* be included in your stylesheet (pixels)
$padding = 1;
 
//Define your template for outputting images
$template = "<img src=\"image.php?size=[size]&amp;file=[image]\" alt=\"\" />";
 
//create a new instance of the class
$mag = new magazinelayout($width,$padding,$template);
 
//Add the images in any order - uncomment next lines to experiment with different combinations
$mag->addImage('s1.jpg');
$mag->addImage('l1.jpg');
$mag->addImage('l5.jpg');
$mag->addImage('p5.jpg');

//display the output
$content600 = $mag->getHtml();

echo "
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
   \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html>
<head>
<title>Magazine Layout Example</title>
<link type=\"text/css\" rel=\"StyleSheet\" href=\"style.css\" />
<style type=\"text/css\">
.magazine-image img {
	margin: 1px;
	border: 0px;
}
</style>
</head>
<body>
<h1>Example 5</h1>
<p>You choose the width of the container, based on how much space you have available. Height will vary depending on the images and the layout chosen.</p>
<h3>200px</h3>
".$content200."
<h3>400px</h3>
".$content400."
<h3>600px</h3>
".$content600."
</body>
</html>
";
