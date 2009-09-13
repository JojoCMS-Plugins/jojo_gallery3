<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2007-2008 Harvey Kane <code@ragepank.com>
 * Copyright 2007-2008 Michael Holt <code@gardyneholt.co.nz>
 * Copyright 2007 Melanie Schulz <mel@gardyneholt.co.nz>
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @author  Michael Cochrane <mikec@jojocms.org>
 * @author  Melanie Schulz <mel@gardyneholt.co.nz>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 * @package jojo_article
 */

if (Jojo::getOption('gallery_randomimage', 'no') == 'yes') {
    /* get a list of all the galleries */
    $allgalleries = Jojo_Plugin_Jojo_gallery3::getGalleries('all');
    shuffle($allgalleries);

    /* Select 2 random images */
    $randomgalleryimages = array();
    foreach ($allgalleries as $k => $g) {
        $id = $g['id'];
        $gurl = $g['url'];
        $gname = $g['name'];
        $images = Jojo_Plugin_Jojo_gallery3::getImages($id);
        if ($images) {
            shuffle($images);
            $image = array_pop($images);
            $image['gname'] = $gname;
            $image['gurl'] = $gurl;
            $image['gmetadescription'] = $g['metadescription'];
            $randomgalleryimages[] = $image;
        }

        if (count($randomgalleryimages) > 1) {
            /* We found 2 images, get out of here */
            break;
        }
    }

    if (isset($randomgalleryimages[0])) {
        $smarty->assign('randomgalleryimage', array($randomgalleryimages[0]));
    }
    if (isset($randomgalleryimages[1])) {
        $smarty->assign('randomgalleryimage2', array($randomgalleryimages[1]));
    }
}
