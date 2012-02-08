<?php

/*
EXAMPLE 3
No Padding
*/

//include the class file
require_once('magazinelayout.class.php');

//Define the width for the output area (pixels)
$width = 600;
 
//Define padding around each image - this *must* be included in your stylesheet (pixels)
$padding = 0;
 
//Define your template for outputting images
$template = "<img src=\"image.php?size=[size]&amp;file=[image]\" alt=\"\" />";
 
//create a new instance of the class
$mag = new magazinelayout($width,$padding,$template);
 
//Add the images in any order - uncomment next lines to experiment with different combinations
$mag->addImage('l4.jpg');
$mag->addImage('l5.jpg');
$mag->addImage('l2.jpg');



//display the output
$content = $mag->getHtml();

echo "
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
   \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html>
<head>
<title>Magazine Layout Example</title>
</head>
<body>
<h1>Example 3</h1>
<p>Padding is optional, or can be specified to any size</p>
".$content."
</body>
</html>
";
