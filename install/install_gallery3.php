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

$table = 'gallery3';
$query = "
    CREATE TABLE {gallery3} (
        `gallery3id` int(11) NOT NULL auto_increment,
        `name` varchar(255) NOT NULL default '',
        `seotitle` varchar(255) NOT NULL default '',
        `bodycode` text NOT NULL,
        `body` text NOT NULL,
        `url` varchar(255) NOT NULL default '',
        `g_date` timestamp NOT NULL default CURRENT_TIMESTAMP,
        `displayorder` int(11) NOT NULL default '0',
        `layout` enum('square','magazine','jgallery') default 'square',
        `show` enum('index','filter') default 'index',
        `thumbsize` varchar(255) NOT NULL default '',
        `previewsize` varchar(255) NOT NULL default '',
        `metadescription` varchar(255) NOT NULL default '',
        `language` varchar(100) NOT NULL default 'en',
        `html_lang` varchar(100) NOT NULL default 'en',
        `category` int(11) NOT NULL default '0',
        `tags` varchar(255) NOT NULL default '',
        `images` varchar(255) NOT NULL default '',
        `sortby` enum('date','name','imageid','filename','order') NOT NULL default 'imageid',
         PRIMARY KEY  (`gallery3id`),
         FULLTEXT KEY `title` (`name`),
         FULLTEXT KEY `body` (`name`,`body`)
    ) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci  AUTO_INCREMENT=1000;";

/* Check table structure */
$result = Jojo::checkTable($table, $query);

/* Output result */
if (isset($result['created'])) {
    echo sprintf("jojo_gallery3: Table <b>%s</b> Does not exist - created empty table.<br />", $table);
}

if (isset($result['added'])) {
    foreach ($result['added'] as $col => $v) {
        echo sprintf("jojo_gallery3: Table <b>%s</b> column <b>%s</b> Does not exist - added.<br />", $table, $col);
    }
}

if (isset($result['different'])) Jojo::printTableDifference($table,$result['different']);

/* Check for Gallery Category table */


$table = 'gallerycategory';
$query = "
    CREATE TABLE {gallerycategory} (
      `gallerycategoryid` int(11) NOT NULL auto_increment,
      `gc_url` varchar(255) NOT NULL default '',
      PRIMARY KEY  (`gallerycategoryid`)
    ) TYPE=MyISAM ;";

/* Check table structure */
$result = Jojo::checkTable($table, $query);

/* Output result */
if (isset($result['created'])) {
    echo sprintf("jojo_gallery3: Table <b>%s</b> Does not exist - created empty table.<br />", $table);
}

if (isset($result['added'])) {
    foreach ($result['added'] as $col => $v) {
        echo sprintf("jojo_gallery3: Table <b>%s</b> column <b>%s</b> Does not exist - added.<br />", $table, $col);
    }
}

if (isset($result['different'])) Jojo::printTableDifference($table, $result['different']);

/* Check for Gallery Image table */

$table = 'gallery3_image';
$query = "
    CREATE TABLE {gallery3_image} (
      `gallery3_imageid` int(11) NOT NULL auto_increment,
      `gi_name` varchar(255) NOT NULL default '',
      `gi_date` datetime NOT NULL,
      `gi_order` int(11) NOT NULL default '0',
      `filename` varchar(255) NOT NULL default '',
      `caption` varchar(255) NOT NULL default '',
      `credit` varchar(255) NOT NULL default '',
      `gallery3id` int(11) NOT NULL default '0',
      PRIMARY KEY  (`gallery3_imageid`),
      FULLTEXT KEY `title` (`caption`),
      FULLTEXT KEY `body` (`caption`,`gi_name`)
    ) TYPE=MyISAM ;";

/* Check table structure */
$result = Jojo::checkTable($table, $query);

/* Output result */
if (isset($result['created'])) {
    echo sprintf("jojo_gallery3: Table <b>%s</b> Does not exist - created empty table.<br />", $table);
}

if (isset($result['added'])) {
    foreach ($result['added'] as $col => $v) {
        echo sprintf("jojo_gallery3: Table <b>%s</b> column <b>%s</b> Does not exist - added.<br />", $table, $col);
    }
}

if (isset($result['different'])) Jojo::printTableDifference($table, $result['different']);