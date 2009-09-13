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
 * @package jojo_gallery3
 */

/* Gallery */
Jojo::updateQuery("UPDATE {page} SET pg_link='Jojo_Plugin_Jojo_Gallery3' WHERE pg_link='jojo_gallery3.php'");
$data = Jojo::selectQuery("SELECT * FROM {page} WHERE pg_link='Jojo_Plugin_Jojo_Gallery3'");
if (!count($data)) {
    echo "jojo_gallery3: Adding <b>Gallery</b> Page to menu<br />";

    /* ensure there are no clashes with /gallery/ for the URL */
    $data = Jojo::selectQuery("SELECT * FROM {page} WHERE pg_url='gallery'");
    $url = count($data) ? 'gallery3' : 'gallery';
    Jojo::insertQuery("INSERT INTO {page} SET pg_title='Gallery', pg_link='Jojo_Plugin_Jojo_Gallery3', pg_url= ?, pg_order=4", array($url));
}

/* Edit Gallery */
$data = Jojo::selectQuery("SELECT * FROM {page} WHERE pg_url='admin/edit/gallery3'");
if (!count($data)) {
    echo "jojo_gallery3: Adding <b>Edit Gallery</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title='Edit Gallery', pg_link='Jojo_Plugin_Admin_Edit', pg_url='admin/edit/gallery3', pg_parent = ?, pg_order=4", array($_ADMIN_CONTENT_ID));
}

$data = Jojo::selectRow("SELECT pageid FROM {page}  WHERE pg_url='admin/edit/gallery3'");
$gallery3pageid = $data['pageid'];

/* Edit Gallery Categories */
$data = Jojo::selectQuery("SELECT * FROM {page}  WHERE pg_url='admin/edit/gallerycategory'");
if (!count($data)) {
    echo "jojo_gallery3: Adding <b>Gallery Categories</b> Page to Edit Content menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title='Gallery Categories', pg_link='Jojo_Plugin_Admin_Edit', pg_url='admin/edit/gallerycategory', pg_parent=?, pg_order=5", array($gallery3pageid));
}

/* Edit Gallery Image Data */
$data = Jojo::selectQuery("SELECT * FROM {page}  WHERE pg_url='admin/edit/gallery3_image'");
if (!count($data)) {
    echo "jojo_gallery3: Adding <b>Edit Gallery Image Data</b> Page to Edit Content menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title='Gallery Image Data', pg_link='Jojo_Plugin_Admin_Edit', pg_url='admin/edit/gallery3_image', pg_parent=?, pg_order=6", array($gallery3pageid));
}

/* Ensure there is a folder for uploading gallery images */
$res = Jojo::RecursiveMkdir(_DOWNLOADDIR . '/gallery3');
if ($res === true) {
    echo "jojo_gallery3: Created folder: " . _DOWNLOADDIR . '/gallery3';
} elseif($res === false) {
    echo 'jojo_gallery3: Could not automatically create ' .  _DOWNLOADDIR . '/gallery3' . 'folder on the server. Please create this folder and assign 777 permissions.';
}

