<?php

class magazineLayout2
{
    var $width;
    var $images = array();
    var $selected = false; //the id of the default selected image
    var $debug = true;
    var $template = '<img src="image.php?w=[width]&amp;h=[height]&amp;file=[image]" alt="[alt]" title="[title]" width="[width]" height="[height]" />';
    var $padding;
    var $orientation; //right / left - determines which side the main image sits on
    var $ordered = true; //is the order the images are added important?
    var $square = false; //crop all images square for a consistent layout
    var $prefix;

    var $_floatCleared = true;
    var $_widthRounding = 0;
    var $_heightRounding = 0;
    var $_counter = 0;

    function magazineLayout2($width=500, $padding=1)
    {
        $this->width = $width;
        $this->padding = $padding;
        $this->orientation = mt_rand(0,1) ? 'left' : 'right';
    }

    function add($filename, $url='', $title='', $alt='', $selected=false)
    {

        //$url = $filename;

        $image = array('filename'=>$filename, 'url'=>$url, 'title'=>$title, 'alt'=>$alt, 'height'=>0, 'width'=>0, 'ratio'=>1);

        if ($url == '') $url = $filename;

        /* Ensure the file is an image */
        if (
            file_exists($filename) &&
            ($this->_getFileExt($filename) != "jpg") &&
            ($this->_getFileExt($filename) != "jpeg") &&
            ($this->_getFileExt($filename) != "gif") &&
            ($this->_getFileExt($filename) != "png")
             ) {
            if ($this->debug) echo $filename.' is not an image file';
            return false;
        }



        /* Read the dimensions of the image */
        if (!file_exists($filename)) return false;
        $imagesize = getimagesize($filename);
        $w = $imagesize[0];
        $h = $imagesize[1];

        /* don't include zero sized images */
        if (!$h || !$w) {
            if ($this->debug) echo $filename.' - cannot read image size<br />';
            return false;
        }

        /* Find the ration of width:height */
        $ratio = $w / $h;

        /* Set format based on the dimensions */
        $format = ($w > $h) ? 'landscape' : 'portrait';

        $image['width'] = $w;
        $image['height'] = $h;
        $image['ratio'] = $ratio;

        $this->images[] = $image;

        /* set the selected image */
        if (($this->selected=='false') || $selected) $this->setSelected($image['filename']);


        return true;
    }

    function setSelected($filename)
    {
        $n = $this->numImages();
        for ($i=0;$i<$n;$i++) {
            if ($this->images[$i]['filename'] == $filename) $this->selected = $i;
        }
    }

    /*  */
    function output()
    {
        $html = '';
        $js = '';
        $i = 0;

        //echo $this->getAverageRatio().'<br>';

        switch ($this->numImages()) {
        case 0:

            break;
        case 1:
            $html .= $this->get1(0);
            break;
        case 2:
            $html .= $this->get2b(0,1);
            break;
        case 3:
            if ($this->getAverageRatio() > 1.4) { //predominantly landscape
                $html .= $this->get1(0);
                $html .= $this->get3a($i++,$i++,$i++);
            } else {
                $html .= $this->get3b($i++,$i++,$i++);
            }
           break;
        case 4:
            if (false) { //testing new layout
                $html .= $this->get4c($i++,$i++,$i++,$i++);
            } elseif ($this->getAverageRatio() > 1.4) { //predominantly landscape
                $html .= $this->get1(0);
                $html .= $this->get4a($i++,$i++,$i++,$i++);
            } elseif ($this->getAverageRatio(array(2,3)) > 1.5) { //last 2 images are panoramic
                $html .= $this->get2b($i++,$i++);
                $html .= $this->get2a($i++,$i++);
            } else {
                $html .= $this->get4b($i++,$i++,$i++,$i++);
            }
            break;
        case 5:
            if ($this->getAverageRatio(array(3,4)) > 1.6) { //last 2 images are panoramic
                $html .= $this->get3b($i++,$i++,$i++);
                $html .= $this->get2a($i++,$i++);
            } else {
                $html .= $this->get2b($i++,$i++);
                $html .= $this->get3a($i++,$i++,$i++);
            }
            break;
        case 6:
            if ($this->getAverageRatio() > 1.5) { //mostly landscape
                $html .= $this->get3a($i++,$i++,$i++);
                $html .= $this->get1(0);
                $html .= $this->get3a($i++,$i++,$i++);
            } elseif (($this->getAverageRatio(array(0,1)) > 1.5) && ($this->getAverageRatio(array(4,5)) > 1.5)) { //the first 2 and last 2 are panoramic
                $html .= $this->get2a($i++,$i++);
                $html .= $this->get2b($i++,$i++);
                $html .= $this->get2a($i++,$i++);
            } else {
                $html .= $this->get3b($i++,$i++,$i++);
                $html .= $this->get3a($i++,$i++,$i++);
            }
            break;
        case 7:
            if ($this->getAverageRatio() > 1.5) { //mostly landscape
                $html .= $this->get3a($i++,$i++,$i++);
                $html .= $this->get1(0);
                $html .= $this->get4a($i++,$i++,$i++,$i++);
            } elseif (($this->getAverageRatio(array(0,1,2)) > 1.5) && ($this->getAverageRatio(array(5,6)) > 1.5)) { //the first 3 and last 2 are panoramic
                $html .= $this->get3a($i++,$i++,$i++);
                $html .= $this->get2b($i++,$i++);
                $html .= $this->get2a($i++,$i++);
            } elseif (($this->getAverageRatio(array(0,1)) > 1.5) && ($this->getAverageRatio(array(4,5,6)) > 1.5)) { //the first 2 and last 3 are panoramic
                $html .= $this->get2a($i++,$i++);
                $html .= $this->get2b($i++,$i++);
                $html .= $this->get3a($i++,$i++,$i++);
            } elseif (($this->getAverageRatio(array(0,1)) > 1.5) && ($this->getAverageRatio(array(5,6)) > 1.5)) { //the first 2 and last 2 are panoramic
                $html .= $this->get2a($i++,$i++);
                $html .= $this->get3b($i++,$i++,$i++);
                $html .= $this->get2a($i++,$i++);
            } else {
                $html .= $this->get3b($i++,$i++,$i++);
                $html .= $this->get4a($i++,$i++,$i++,$i++);
            }
            break;
        case 8:
            if ($this->getAverageRatio() < 0.8) { //mostly portrait
               $html .= $this->get4a($i++,$i++,$i++,$i++);
                $html .= $this->get2b($i++,$i++);
                $html .= $this->get3a($i++,$i++,$i++);
            } elseif ($this->getAverageRatio() < 1.5) { //not panoramic
               $html .= $this->get3a($i++,$i++,$i++);
               $html .= $this->get2b($i++,$i++);
               $html .= $this->get3a($i++,$i++,$i++);
            } else {
                $html .= $this->get4a($i++,$i++,$i++,$i++);
                $html .= $this->get1(0);
                $html .= $this->get4a($i++,$i++,$i++,$i++);
            }
            break;
        case 9:
            $html .= $this->get3a($i++,$i++,$i++);
            $html .= $this->get3b($i++,$i++,$i++);
            $html .= $this->get3a($i++,$i++,$i++);
            break;
        case 10:
            $html .= $this->get3a($i++,$i++,$i++);
            $html .= $this->get3b($i++,$i++,$i++);
            $html .= $this->get4a($i++,$i++,$i++,$i++);
            break;
        default:
            $html = '';
        }

        $html = '<div class="mag" style="width: '.$this->width.'px;">'."\n".$html.'<div style="clear: both;"></div>'."\n".'</div>'."\n";
        $js = '<script type="text/javascript">'.$js.'</script>';
        return $html.$js;
    }

    function get1($i1=0)
    {
        /*
        111 or 1
               1
        */

        $s = floor($this->width - ($this->padding * 2));

        $html = '';
        $this->newRow();
        $html .= $this->getLink($this->images[$this->selected]['url'], true, 'a', $s);
        return $html;
    }

    function get2a($i1,$i2)
    {
        /*
        1122

        Equation: t = 4p + ha + hb Variable: h

        */

        $a = $this->images[$i1]['ratio'];
        $b = $this->images[$i2]['ratio'];
        $t = $this->width;
        $p = $this->padding;

        if ($this->square) $a = $b = 1;

        $h1 = floor( (4*$p - $t) / (-$a - $b) );

        $html = '';
        $this->newRow();
        $html .= $this->getLink($this->images[$i1]['url'], false, 'b', false, $h1);
        $html .= $this->getLink($this->images[$i2]['url'], false, 'b', false, $h1);

        return $html;
    }

    function get2b($i1,$i2)
    {
        /*
        1133
        2233
        */

        /* To save space in the equation */
        $a = $this->getAverageRatio();
        $b = $this->images[$i1]['ratio'];
        $c = $this->images[$i2]['ratio'];
        $t = $this->width;
        $p = $this->padding;

        if ($this->square) $a = $b = $c = 1;

        /*
        Enter the following data at http://www.hostsrv.com/webmab/app1/MSP/quickmath/02/pageGenerate?site=quickmath&s1=equations&s2=solve&s3=advanced#reply
        EQUATIONS
        x/a = w/b + w/c + 2p
        w+x+4p = t
        VARIABLES
        w
        x
        */

        /* width of left column with 2 small images */
        $w1 = floor(
        -(
        (2 * $a * $b * $c * $p + 4 * $b * $c * $p - $b * $c * $t)
        /
        ($a * $b + $c * $b + $a * $c)
        )
        );

        /* width of right column with 1 large image */
        $w2 = floor(
        ($a * (-4 * $b * $p + 2 * $b * $c * $p - 4 * $c * $p + $b * $t + $c * $t))
        /
        ($a * $b + $c * $b + $a * $c)
        );

        $html = '';
        $this->newRow();
        $html .= $this->getLink($this->images[$this->selected]['url'], true, 'a', $w2);
        $html .= $this->getLink($this->images[$i1]['url'], false, 'b', $w1);
        $html .= $this->getLink($this->images[$i2]['url'], false, 'b', $w1);

        return $html;
    }

    function get3a($i1,$i2,$i3)
    {
        /*
        1223
        */

        /* To save space in the equation */
        $a = $this->images[$i3]['ratio'];
        $b = $this->images[$i1]['ratio'];
        $c = $this->images[$i2]['ratio'];
        $t = $this->width;
        $p = $this->padding;

        if ($this->square) $a = $b = $c = 1;

        /*
        Enter the following data at http://www.hostsrv.com/webmab/app1/MSP/quickmath/02/pageGenerate?site=quickmath&s1=equations&s2=solve&s3=advanced#reply
        EQUATIONS
        t = 6p + ah + bh + ch
        VARIABLES
        h
        */

        $h1 = floor(
        (6 * $p - $t)
        /
        (-$a -$b -$c)
        );

        $html = '';
        $this->newRow();
        $html .= $this->getLink($this->images[$i1]['url'], false, 'b', false, $h1);
        $html .= $this->getLink($this->images[$i2]['url'], false, 'b', false, $h1);
        $html .= $this->getLink($this->images[$i3]['url'], false, 'a', false, $h1);

        return $html;
    }

    function get3b($i1,$i2,$i3)
    {
        /*
        11444
        22444
        33444
        */

        /* To save space in the equation */
        $a = $this->getAverageRatio();
        $b = $this->images[$i1]['ratio'];
        $c = $this->images[$i2]['ratio'];
        $d = $this->images[$i3]['ratio'];
        $t = $this->width;
        $p = $this->padding;

        if ($this->square) $a = $b = $c = $d = 1;

        /*
        Enter the following data at http://www.hostsrv.com/webmab/app1/MSP/quickmath/02/pageGenerate?site=quickmath&s1=equations&s2=solve&s3=advanced#reply
        EQUATIONS
        x/a = w/b + w/c + 2p
        w+x+4p = t
        VARIABLES
        w
        x
        */

        /* width of left column with 2 small images */
        $w1 = floor(
        -(
        (4 * $a * $b * $c * $d * $p + 4 * $b * $c * $d * $p - $b * $c * $d * $t)
        /
        ($a * $b * $c + $a * $d * $c + $b * $d * $c + $a * $b * $d)
        )
        );

        /* width of right column with 1 large image */
        $w2 = floor(
        -(
        (-4 * $p - (-(1/$c) -(1/$d) -(1/$b)) * (4 * $p - $t) )
        /
        ( (1/$b) + (1/$c) + (1/$d) + (1/$a) )
        )
        );

        $html = '';
        $this->newRow();
        $html .= $this->getLink($this->images[$this->selected]['url'], true, 'a', $w2);
        $html .= $this->getLink($this->images[$i1]['url'], false, 'b', $w1);
        $html .= $this->getLink($this->images[$i2]['url'], false, 'b', $w1);
        $html .= $this->getLink($this->images[$i3]['url'], false, 'b', $w1);

        return $html;
    }

    function get4a($i1,$i2,$i3,$i4)
    {
        /*
        1234
        */

        /* To save space in the equation */
        $a = $this->images[$i1]['ratio'];
        $b = $this->images[$i2]['ratio'];
        $c = $this->images[$i3]['ratio'];
        $d = $this->images[$i4]['ratio'];
        $t = $this->width;
        $p = $this->padding;

        if ($this->square) $a = $b = $c = $d = 1;

        /*
        Enter the following data at http://www.hostsrv.com/webmab/app1/MSP/quickmath/02/pageGenerate?site=quickmath&s1=equations&s2=solve&s3=advanced#reply
        EQUATIONS
        t = 6p + ah + bh + ch + dh
        VARIABLES
        h
        */

        $h1 = floor(
        (8 * $p - $t)
        /
        (-$a -$b -$c -$d)
        );

        //$h1 = floor($this->_fullwidth / ($this->images[$p1]['ratio'] + $this->images[$p2]['ratio'] + $this->images[$p3]['ratio'] + $this->images[$p4]['ratio']));
        $html = '';
        $this->newRow();
        $html .= $this->getLink($this->images[$i1]['url'], false, 'b', false, $h1);
        $html .= $this->getLink($this->images[$i2]['url'], false, 'b', false, $h1);
        $html .= $this->getLink($this->images[$i3]['url'], false, 'b', false, $h1);
        $html .= $this->getLink($this->images[$i4]['url'], false, 'a', false, $h1);
        return $html;
    }

    function get4b($i1,$i2,$i3,$i4)
    {
        /*
        11444
        22444
        33444
        */

        /* To save space in the equation */
        $a = $this->getAverageRatio();
        $b = $this->images[$i1]['ratio'];
        $c = $this->images[$i2]['ratio'];
        $d = $this->images[$i3]['ratio'];
        $e = $this->images[$i4]['ratio'];
        $t = $this->width;
        $p = $this->padding;

        if ($this->square) $a = $b = $c = $d = $e = 1;

        /*
        Enter the following data at http://www.hostsrv.com/webmab/app1/MSP/quickmath/02/pageGenerate?site=quickmath&s1=equations&s2=solve&s3=advanced#reply
        EQUATIONS
        x/a = w/b + w/c + w/d + w/e + 2p
        w+x+4p = t
        VARIABLES
        w
        x
        */

        /* width of left column with 2 small images */
        $w1 = floor(
        -(
        ( 2 * $a * $b * $c * $d * $e * $p + 4 * $b * $c * $d * $e * $p - $b * $c * $d * $e * $t )
        /
        ($a * $b * $d * $c + $a * $b * $e * $c + $a * $d * $e * $c + $b * $d * $e * $c + $a * $b * $d * $e )
        )
        );

        /* width of right column with 1 large image */
        $w2 = floor(
        -(
        (-2 * $p - (-(1/$c) -(1/$d) -(1/$e) -(1/$b)) * (4 * $p - $t) )
        /
        ( (1/$b) + (1/$c) + (1/$d) + (1/$e) + (1/$a) )
        )
        );

        $html = '';
        $this->newRow();
        $html .= $this->getLink($this->images[$this->selected]['url'], true, 'a', $w2);
        $html .= $this->getLink($this->images[$i1]['url'], false, 'b', $w1);
        $html .= $this->getLink($this->images[$i2]['url'], false, 'b', $w1);
        $html .= $this->getLink($this->images[$i3]['url'], false, 'b', $w1);
        $html .= $this->getLink($this->images[$i4]['url'], false, 'b', $w1);

        return $html;
    }

    function get4c($i1,$i2,$i3,$i4)
    {

        /* UNDER CONSTRUCTION */

        /*
        1155533
        2255544
        */

        /* To save space in the equation */
        $a = $this->getAverageRatio();
        $b = $this->images[$i1]['ratio'];
        $c = $this->images[$i2]['ratio'];
        $d = $this->images[$i3]['ratio'];
        $e = $this->images[$i4]['ratio'];
        $t = $this->width;
        $p = $this->padding;

        if ($this->square) $a = $b = $c = $d = $e = 1;

        /*
        Enter the following data at http://www.hostsrv.com/webmab/app1/MSP/quickmath/02/pageGenerate?site=quickmath&s1=equations&s2=solve&s3=advanced#reply
        EQUATIONS
        x/a = w/b + w/c + 3p
        y/a = w/d + w/e + 3p
        w+x+y+6p = t
        VARIABLES
        w
        x
        y
        */

        /* Hold on tight Dorothy, Kansas is f**king long gone */

        /* width of left column with 2 small images */
        $w1 = floor(
        -(
         (
          3 * $a * $b * $c * $d * $p + 6 * $b * $c * $d * $p+3 * $a * $b * $c * $e * $p + 6 * $b * $c * $e * $p - $b * $c * $d * $t - $b * $c * $e * $t
         )
        /
        ($a * $d * $b+c * $d * $b + $a * $e * $b + $c * $e * $b + $d * $e * $b + $a * $c * $d + $a * $c * $e + $c * $d * $e)
        )
        );

        /* width of column with 1 large image */
        $w2 = floor(
        3 * $a * $p +
        (
        ($a * $d * (-(1/$e)-(1/$d)) * $e * (3 * $a * $b * $p + 6  * $b * $p + 3 * $a * $c * $p + 6 * $c * $p - $b * $t - $c * $t))
        /
        ($a * $d * $b + $c * $d * $b + $a  * $e * $b + $c  * $e * $b + $d * $e * $b + $a * $c * $d + $a * $c * $e + $c * $d * $e)
        )
        );

        /* width of right column with 2 small images */
        $w3 = floor(
        -(
        ($d * $e * (3 * $a * $b * $p + 6 * $b * $p + 3 * $a * $c * $p + 6 * $c * $p - $b * $t - $c * $t))
        /
        ($a * $d * $b + $c * $d * $b + $a * $e * $b + $c * $e * $b + $d * $e * $b + $a * $c * $d + $a * $c * $e + $c * $d * $e)
        )
        );
echo 'w1 = '.$w1.'<br>';
echo 'w2 = '.$w2.'<br>';
echo 'w3 = '.$w3.'<br>';
echo 't = '.($w1+$w2+$w3).'<br>';
        $html = '';
        $this->newRow();
        $html .= '<div style="float:left; clear:both; width:'.$w1.'px">';
        $this->_floatCleared = true;
        $html .= $this->getLink($this->images[$i1]['url'], false, 'b', $w1);
        $html .= $this->getLink($this->images[$i2]['url'], false, 'b', $w1);
        $html .= '</div>';
        $html .= $this->getLink($this->images[$this->selected]['url'], true, 'b', $w2);
        $html .= $this->getLink($this->images[$i3]['url'], false, 'a', $w3);
        $html .= $this->getLink($this->images[$i4]['url'], false, 'a', $w3);

        return $html;
    }

    function newRow()
    {
        $this->_floatCleared = false;
        $this->_widthRounding = 0;
        $this->_heightRounding = 0;
    }

    /* feed an array of image IDs, or leave blank to average all images */
    function getAverageRatio($images=false)
    {
        $total = 0;
        if (is_array($images)) {
            $numimages = count($images);
            foreach ($images as $imageid) {
                $total += $this->images[$imageid]['ratio'];
            }
        } else {
            $numimages = count($this->images);
            foreach ($this->images as $image) {
                $total += $image['ratio'];
            }
        }
        return $numimages ? $total / $numimages : 1;
    }

    function getHeight($filename, $width=false, $average=false)
    {
        if ($this->square) return $width;
        foreach ($this->images as $image) {
            if (basename($image['filename']) == basename($filename)) break;
        }
        if (empty($width)) return $image['height'];
        if ($average) $image['ratio'] = $this->getAverageRatio();
        $height = $width / $image['ratio'];
        $rounded = floor($height);
        $this->_heightRounding += $height - $rounded;
        /* add an extra pixel to the width if it has been lost in rounding previously */
        if ($this->_heightRounding > 1) {
            $extra = floor($this->_heightRounding);
            $rounded += $extra;
            $this->_heightRounding -= $extra;
        }
        return $rounded;
    }

    function getWidth($filename, $height=false, $average=false)
    {
        if ($this->square) return $height;
        foreach ($this->images as $image) {
            if (basename($image['filename']) == basename($filename)) break;
        }
        if (empty($height)) return $image['width'];
        if ($average) $image['ratio'] = $this->getAverageRatio();
        $width = $image['ratio'] * $height;
        $rounded = floor($width);
        $this->_widthRounding += $width - $rounded;
        /* add an extra pixel to the width if it has been lost in rounding previously */
        if ($this->_widthRounding > 1) {
            $extra = floor($this->_widthRounding);
            $rounded += $extra;
            $this->_widthRounding -= $extra;
        }
        return $rounded;
    }

    function getOrientation($code)
    {
        $primary = $this->orientation;
        $secondary = $this->orientation == 'right' ? 'left' : 'right';
        return ($code == 'a') ? $primary : $secondary;
    }

    function getLink($url, $main=false, $float='b', $width=false, $height=false)
    {
        $styles = array();
        if (!empty($float)) $styles[] = 'float:'.$this->getOrientation($float);
        if (!$this->_floatCleared) {
            $styles[] = 'clear:both';
            $this->_floatCleared = true;
        }
        $style = count($styles) ? ' style="'.implode(';',$styles).'"' : '';

        $classes = array();
        if (!empty($main)) $classes[] = 'main';
        if (!$main && $url == $this->images[$this->selected]['url']) $classes[] = 'selected';
        $class = count($classes) ? ' class="'.implode(' ',$classes).'"' : '';

        return '<a href="'.$this->prefix.$url.'"'.$class.$style.' rel="lightbox">' . $this->insertImage($url, $width, $height, $main) . '</a>'."\n";
    }

    /* Replaces variables into the supplied image template */
    function insertImage($filename, $w=false, $h=false, $average=false)
    {
        static $i;
        if (empty($i)) $i = 1;

        /* ensure we know both width and height for image */
        if (empty($w) && empty($h)) return false;
        if (empty($h)) $h = $this->getHeight($filename, $w, $average);
        if (empty($w)) $w = $this->getWidth($filename, $h, $average);

        foreach ($this->images as $image) {
            if ($image['filename'] == $filename) break;
        }
        $alt = !empty($image['alt']) ? $image['alt'] : '';
        $title = !empty($image['title']) ? $image['title'] : '';
        if ($this->ordered) $title = $i.'. '.$title;

        if (!$average) $i++;

        return str_replace('[image]',$filename,str_replace('[width]',$w, str_replace('[height]',$h, str_replace('[alt]',trim($alt), str_replace('[title]',trim($title), $this->template)))));
    }

    /* returns the number of images in the layout */
    function numImages()
    {
        return count($this->images);
    }

    /* Gets the file extension for a given filename */
    function _getFileExt($file)
    {
        $file = strtolower($file);
        $ext = explode(".", $file);
        if (count($ext) == 0) return '';
        return $ext[count($ext)-1];
    }

}