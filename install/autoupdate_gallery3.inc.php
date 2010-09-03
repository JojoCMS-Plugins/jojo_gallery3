<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2008 Jojo CMS
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Michael Cochrane <mikec@jojocms.org>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */

if (!defined('_MULTILANGUAGE')) {
    define('_MULTILANGUAGE', Jojo::getOption('multilanguage', 'no') == 'yes');
}

$default_td['gallery3'] = array(
        'td_name' => "gallery3",
        'td_primarykey' => "gallery3id",
        'td_displayfield' => "name",
        'td_categorytable' => "gallerycategory",
        'td_categoryfield' => "category",
        'td_orderbyfields' => "displayorder, name",
        'td_topsubmit' => "yes",
        'td_deleteoption' => "yes",
        'td_menutype' => "tree",
        'td_plugin' => "Jojo_gallery3",
    );

$o = 1;

/* Content Tab */

// Gallery3id Field
$default_fd['gallery3']['gallery3id'] = array(
        'fd_name' => "Gallery3id",
        'fd_type' => "readonly",
        'fd_help' => "A unique ID, automatically assigned by the system",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// Category Field
$default_fd['gallery3']['category'] = array(
        'fd_name' => "Page",
        'fd_type' => "dblist",
        'fd_options' => "gallerycategory",
        'fd_default' => "0",
        'fd_size' => "20",
        'fd_help' => "The page on the site the Gallery belongs to",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// Name Field
$default_fd['gallery3']['name'] = array(
        'fd_name' => "Name",
        'fd_type' => "text",
        'fd_required' => "yes",
        'fd_size' => "50",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// SEO Title Field
$default_fd['gallery3']['seotitle'] = array(
        'fd_name' => "SEO Title",
        'fd_type' => "text",
        'fd_options' => "70",
        'fd_size' => "50",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// Bodycode Field
$default_fd['gallery3']['bodycode'] = array(
        'fd_name' => "Bodycode",
        'fd_type' => "texteditor",
        'fd_options' => "body",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// Body Field
$default_fd['gallery3']['body'] = array(
        'fd_name' => "Body",
        'fd_type' => "hidden",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// URL Field
$default_fd['gallery3']['url'] = array(
        'fd_name' => "URL",
        'fd_type' => "internalurl",
        'fd_size' => "30",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

//Timestamp
$default_fd['gallery3']['g_date'] = array(
        'fd_order' => $o++,
        'fd_required' => 'no',
        'fd_type' => "unixdate",
        'fd_default' => 'now',
        'fd_help' => '',
        'fd_tabname' => "Content",
    );

// Layout Field
$default_fd['gallery3']['layout'] = array(
        'fd_name' => "Layout Style",
        'fd_type' => "radio",
        'fd_options' => "square\nmagazine\njgallery",
        'fd_default' => 'square',
        'fd_help' => "Layout format for the gallery: Square = squared thumbs to full-size via lightbox, Magazine = fullsize formatted into a single layout, JGallery = thumbs clickable to update inline fullsize image",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// Layout Field
$default_fd['gallery3']['show'] = array(
        'fd_name' => "Show In",
        'fd_type' => "hidden",
        'fd_options' => "index\nfilter",
        'fd_default' => 'index',
        'fd_help' => "Show this gallery in the index or make it only show when called by a filter (can still be called by filter if index is selected)",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// Displayorder Field
$default_fd['gallery3']['displayorder'] = array(
        'fd_name' => "Displayorder",
        'fd_type' => "integer",
        'fd_default' => "0",
        'fd_help' => "Order in which the gallery appears on the main listing",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// Thumbnail size Field
$default_fd['gallery3']['thumbsize'] = array(
        'fd_name' => "Thumbnail size",
        'fd_type' => "text",
        'fd_size' => "10",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
        'fd_units' => "pixels",
    );

// Preview size Field
$default_fd['gallery3']['previewsize'] = array(
        'fd_name' => "Preview size",
        'fd_type' => "text",
        'fd_size' => "10",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
        'fd_units' => "pixels",
    );

// Index Image Field
$default_fd['gallery3']['indeximage'] = array(
        'fd_name' => "Index Image",
        'fd_type' => "fileupload",
        'fd_help' => "A separate image for the index, if  available",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
        'fd_mode' => "standard",
    );

// Sortby Field
$default_fd['gallery3']['sortby'] = array(
        'fd_name' => "Sort Images by",
        'fd_type' => "radio",
        'fd_options' => "date:Date\nname:Name\nfilename:Filename\nimageid:ID\norder:Display order",
        'fd_default' => "imageid",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// Meta Description Field
$default_fd['gallery3']['metadescription'] = array(
        'fd_name' => "Meta Description",
        'fd_type' => "textarea",
        'fd_options' => "metadescription",
        'fd_rows' => "3",
        'fd_cols' => "60",
        'fd_help' => "A good sales oriented description of the page for the Search Engine snippet. Try to keep this within 155 characters, as anything larger will be chopped from the snippet.",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// Language Field
$default_fd['gallery3']['language'] = array(
        'fd_name' => "Language/Country",
        'fd_type' => "dblist",
        'fd_options' => "lang_country",
        'fd_default' => "en",
        'fd_size' => "20",
        'fd_help' => "The language/country section this gallery will be in",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// Language Field
$default_fd['gallery3']['html_lang'] = array(
        'fd_name' => "HTML Language",
        'fd_type' => "dblist",
        'fd_options' => "language",
        'fd_default' => "en",
        'fd_size' => "20",
        'fd_help' => "The language this gallery will be in (if not the default language for the language/country chosen above)",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );


/* Images Tab */

// Images Field
$default_fd['gallery3']['images'] = array(
        'fd_name' => "Images",
        'fd_type' => "gallery3images",
        'fd_showlabel' => "no",
        'fd_order' => "1",
        'fd_tabname' => "Images",
    );


/* Tags Tab */

// Tags Field
$default_fd['gallery3']['tags'] = array(
        'fd_name' => "Tags",
        'fd_type' => "tag",
        'fd_options' => "jojo_gallery3",
        'fd_showlabel' => "no",
        'fd_help' => "A list of words describing the gallery",
        'fd_order' => "1",
        'fd_tabname' => "Tags",
    );


/* Gallery Images page */

$default_td['gallery3_image'] = array(
        'td_name' => "gallery3_image",
        'td_primarykey' => "gallery3_imageid",
        'td_displayfield' => "if(CHAR_LENGTH(gi_name) > 0, gi_name, filename)",
        'td_orderbyfields' => "gi_order,gallery3_imageid",
        'td_categorytable' => "gallery3",
        'td_categoryfield' => "gallery3id",
        'td_filter' => "yes",
        'td_topsubmit' => "yes",
        'td_deleteoption' => "yes",
        'td_menutype' => "tree",
        'td_help' => "Gallery image captions credits etc are managed from here.",
        'td_defaultpermissions' => "everyone.show=1\neveryone.view=1\neveryone.edit=1\neveryone.add=1\neveryone.delete=1\nadmin.show=1\nadmin.view=1\nadmin.edit=1\nadmin.add=1\nadmin.delete=1\nnotloggedin.show=1\nnotloggedin.view=1\nnotloggedin.edit=1\nnotloggedin.add=1\nnotloggedin.delete=1\nregistered.show=1\nregistered.view=1\nregistered.edit=1\nregistered.add=1\nregistered.delete=1\nsysinstall.show=1\nsysinstall.view=1\nsysinstall.edit=1\nsysinstall.add=1\nsysinstall.delete=1\n",
    );


/* Content Tab */
$o = 1;

// Gallery3_imageid Field
$default_fd['gallery3_image']['gallery3_imageid'] = array(
        'fd_name' => "Gallery3_imageid",
        'fd_type' => "readonly",
        'fd_help' => "A unique ID, automatically assigned by the system",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// In Gallery Field
$default_fd['gallery3_image']['gallery3id'] = array(
        'fd_name' => "In Gallery",
        'fd_type' => "dblist_g3",
        'fd_options' => "gallery3",
        'fd_required' => "yes",
        'fd_help' => "The gallery this image belongs in",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// Name Field
$default_fd['gallery3_image']['gi_name'] = array(
        'fd_name' => "Image Name",
        'fd_type' => "text",
        'fd_size' => "50",
        'td_orderbyfields' => "gi_order,gallery3_imageid",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// Filename Field
$default_fd['gallery3_image']['filename'] = array(
        'fd_name' => "Filename",
        'fd_type' => "gallery3image",
        'fd_help' => "Upload new or modified or delete gallery images",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// Caption Field
$default_fd['gallery3_image']['caption'] = array(
        'fd_name' => "Caption",
        'fd_type' => "text",
        'fd_size' => "50",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// Credit Field
$default_fd['gallery3_image']['credit'] = array(
        'fd_name' => "Credit",
        'fd_type' => "text",
        'fd_size' => "50",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// Weighted Index Field
$default_fd['gallery3_image']['keyimage'] = array(
        'fd_name' => "Key Image",
        'fd_type' => "yesno",
        'fd_readonly' => "0",
        'fd_default' => "0",
        'fd_help' => "Use this image on the index page (if not overridden by Index Image)",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// Date Field
$default_fd['gallery3_image']['gi_date'] = array(
        'fd_name' => "Date",
        'fd_type' => "unixdate",
        'fd_default' => "now",
        'fd_help' => "Date the photo was taken (defaults to Today)",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// Image Order by Field
$default_fd['gallery3_image']['gi_order'] = array(
        'fd_name' => "Order by",
        'fd_type' => "order",
        'fd_default' => "0",
        'fd_help' => "Order the image is displayed",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
);

/* Gallery Categories page */

$table = 'gallerycategory';
$default_td[$table] = array(
        'td_name' => $table,
        'td_primarykey' => "gallerycategoryid",
        'td_displayfield' => 'pageid',
        'td_categorytable' => "",
        'td_categoryfield' => "",
        'td_filter' => "yes",
        'td_topsubmit' => "yes",
        'td_deleteoption' => "yes",
        'td_menutype' => "list",
        'td_help' => "Gallery page options are managed from here.",
        'td_defaultpermissions' => "everyone.show=1\neveryone.view=1\neveryone.edit=1\neveryone.add=1\neveryone.delete=1\nadmin.show=1\nadmin.view=1\nadmin.edit=1\nadmin.add=1\nadmin.delete=1\nnotloggedin.show=1\nnotloggedin.view=1\nnotloggedin.edit=1\nnotloggedin.add=1\nnotloggedin.delete=1\nregistered.show=1\nregistered.view=1\nregistered.edit=1\nregistered.add=1\nregistered.delete=1\nsysinstall.show=1\nsysinstall.view=1\nsysinstall.edit=1\nsysinstall.add=1\nsysinstall.delete=1\n",
        'td_plugin' => "Jojo_gallery3",
    );

/* Content Tab */
$o = 0;

// Cat Id
$default_fd[$table]['gallerycategoryid'] = array(
        'fd_name' => "ID",
        'fd_type' => "integer",
        'fd_readonly' => "1",
        'fd_help' => "A unique ID, automatically assigned by the system",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// Page Field
$default_fd[$table]['pageid'] = array(
        'fd_name' => "Page",
        'fd_type' => "dbpluginpagelist",
        'fd_options' => "jojo_plugin_jojo_gallery3",
        'fd_readonly' => "1",
        'fd_default' => "0",
        'fd_help' => "The page on the site used for this category.",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );

// Name Field
$default_fd[$table]['addtonav'] = array(
        'fd_name' => "Show Galleries in Nav",
        'fd_type' => "yesno",
        'fd_help' => "Add galleries to navigation as child pages of this one.",
        'fd_default' => "0",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );
    
// Url Field (deprecated)
$default_fd[$table]['gc_url'] = array(
        'fd_name' => "Category URL",
        'fd_type' => "hidden",
        'fd_size' => "50",
        'fd_order' => $o++,
        'fd_tabname' => "Content",
    );