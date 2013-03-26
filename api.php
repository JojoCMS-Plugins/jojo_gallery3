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

$_provides['pluginClasses'] = array(
        'Jojo_Plugin_Jojo_gallery3'       => 'Gallery - Gallery Listing and View',
        );

/* Sitemap filter */
Jojo::addFilter('jojo_sitemap', 'sitemap', 'jojo_gallery3');

/* XML Sitemap filter */
Jojo::addFilter('jojo_xml_sitemap', 'xmlsitemap', 'jojo_gallery3');

/* capture the button press in the admin section */
Jojo::addHook('admin_action_start', 'admin_action_start', 'jojo_gallery3');
Jojo::addHook('admin_action_after_save_page', 'admin_action_after_save_page', 'jojo_gallery3');
Jojo::addHook('admin_action_after_save_gallerycategory', 'admin_action_after_save_gallerycategory', 'jojo_gallery3');
Jojo::addHook('admin_action_after_save_gallery3_image', 'admin_action_after_save_gallery3_image', 'jojo_gallery3');
Jojo::addHook('admin_action_after_save_gallery3', 'admin_action_after_save_gallery3', 'jojo_gallery3');

/* add lightbox css to head */
Jojo::addHook('customhead', 'customhead', 'jojo_gallery3');

/* add lightbox javascript to foot */
Jojo::addHook('foot', 'foot', 'jojo_gallery3');

/* add a new field type */
$_provides['fieldTypes'] = array('gallery3images' => 'Gallery3 images', 'gallery3image' => 'Gallery3 display image', 'dblist_g3' => 'Gallery3 images gallery list');

/* Gallery filter for grabbing [[gallery3: my-gallery]] */
Jojo::addFilter('output', 'contentFilter', 'jojo_gallery3');

/* Search Filter */
Jojo::addFilter('jojo_search', 'search', 'jojo_gallery3');

/* Register URI handlers */
Jojo::registerURI(null, 'jojo_plugin_jojo_gallery3', 'isUrl');

/* Get the names of the galleries from the database to serve as examples in the UI - easier than remembering */
$galleries = Jojo_Plugin_Jojo_gallery3::getGalleries();
$names = array();
foreach ($galleries as $g) {
    $names[] = $g['name'];
}
$examples = (count($galleries) < 10) && count($galleries) ? ' (eg. \"'.implode('\", \"', $names).'\")' : '';

/* add an icon onto the editors for inserting galleries */
$vars = array('galleryname' => array(
                'name'        => 'name/id',
                'description' => 'Gallery name or ID' . $examples)
                );
$gallery3 = array(
                'name'        => 'Image gallery',
                'format'      => '[[gallery3:[galleryname]]]',
                'description' => '',
                'vars'        => $vars,
                'icon'        => 'images/gallery3.gif'
                );
Jojo::addContentVar($gallery3);

$_options[] = array(
    'id'          => 'gallery3_single_page',
    'category'    => 'Gallery',
    'label'       => 'Single page gallery',
    'description' => 'When this option is enabled, if there is only one gallery in the system, no need to show the gallery index - display gallery content instead.',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_gallery3'
);

$_options[] = array(
    'id'          => 'gallery_inplacesitemap',
    'category'    => 'Gallery',
    'label'       => 'Gallery sitemap location',
    'description' => 'Show galleries as a separate list on the site map, or in-place on the page list',
    'type'        => 'radio',
    'default'     => 'separate',
    'options'     => 'separate,inplace',
    'plugin'      => 'jojo_gallery3'
);

$_options[] = array(
    'id'          => 'gallery_orderby',
    'category'    => 'Gallery',
    'label'       => 'Sort Order - Galleries',
    'description' => 'Order galleries in the index by name or date added (newest first) - can be overridden by "display order" field',
    'type'        => 'radio',
    'default'     => 'name',
    'options'     => 'name,date',
    'plugin'      => 'jojo_gallery3'
);


$_options[] = array(
    'id'          => 'gallery_next_prev',
    'category'    => 'Gallery',
    'label'       => 'Show Next / Previous links',
    'description' => 'Show a link to the next and previous gallery at the bottom of each gallery page',
    'type'        => 'radio',
    'default'     => 'yes',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_gallery3'
);

$_options[] = array(
    'id'          => 'gallery_shownumimages',
    'category'    => 'Gallery',
    'label'       => 'Show gallery image count',
    'description' => 'Show the number of images in each gallery in the index',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_gallery3'
);

$_options[] = array(
    'id'          => 'gallery_randomimage',
    'category'    => 'Gallery',
    'label'       => 'Random image for sidebar',
    'description' => 'Generate a random teaser image for use elsewhere in the site template',
    'type'        => 'radio',
    'default'     => 'no',
    'options'     => 'yes,no',
    'plugin'      => 'jojo_gallery3'
);

$_options[] = array(
    'id'          => 'gallery_magazinelayoutwidth',
    'category'    => 'Gallery',
    'label'       => 'Width for magazinelayout',
    'description' => 'Set the width of the magazine layouts in pixels',
    'type'        => 'integer',
    'default'     => '400',
    'options'     => '',
    'plugin'      => 'jojo_gallery3'
);

$_options[] = array(
    'id'          => 'gallery_jgallerythumbs',
    'category'    => 'Gallery',
    'label'       => 'jGallery Thumbs',
    'description' => 'Display thumbnails above or below the main image?',
    'type'        => 'radio',
    'default'     => 'above',
    'options'     => 'above,below',
    'plugin'      => 'jojo_gallery3'
);
