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
        `menutitle` varchar(255) NOT NULL default '',
       `bodycode` text NOT NULL,
        `body` text NOT NULL,
        `url` varchar(255) NOT NULL default '',
        `g_date` int(11) NOT NULL default '0',
        `displayorder` int(11) NOT NULL default '0',
        `layout` enum('square','magazine','jgallery','custom') default 'square',
        `show` enum('index','filter') default 'index',
        `thumbsize` varchar(255) NOT NULL default '',
        `previewsize` varchar(255) NOT NULL default '',
        `indeximage` varchar(255) NOT NULL default '',
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

/* Convert mysql date format to unix timestamps */
if (Jojo::tableExists($table) && Jojo::getMySQLType($table, 'g_date') == 'timestamp') {
    date_default_timezone_set(Jojo::getOption('sitetimezone', 'Pacific/Auckland'));
    $items = Jojo::selectQuery("SELECT gallery3id, g_date FROM {gallery3}");
    Jojo::structureQuery("ALTER TABLE  {gallery3} CHANGE  `g_date`  `g_date` INT(11) NOT NULL DEFAULT '0'");
    foreach ($items as $k => $a) {
        if ($a['g_date']!='0000-00-00') {
            $timestamp = Jojo::strToTimeUK($a['g_date']);
        } else {
            $timestamp = 0;
        }
       Jojo::updateQuery("UPDATE {gallery3} SET g_date=? WHERE gallery3id=?", array($timestamp, $a['gallery3id']));
    }
}

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
      `pageid` int(11) NOT NULL default '0',
      `type` enum('normal','parent','index') NOT NULL default 'normal',
      `gc_url` varchar(255) NOT NULL default '',
      `sortby` enum('date','name','order') NOT NULL default 'order',
      `addtonav` tinyint(1) NOT NULL default '0',
      `showcaptions` tinyint(1) NOT NULL default '0',
      `showdate` tinyint(1) NOT NULL default '0',
      `thumbnailsize` varchar(255) NOT NULL default '',
      `imagesize` varchar(255) NOT NULL default '',
      `indeximagesize` varchar(255) NOT NULL default '',
      PRIMARY KEY  (`gallerycategoryid`),
      KEY `id` (`pageid`)
    ) TYPE=MyISAM ;";

//check for conversion of categories to use pageid
$updategcat= (boolean)(Jojo::tableExists($table) && !Jojo::fieldExists($table, 'pageid'));

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

//convert existing categories to use pageid if they aren't already
if ($updategcat) {
    $gpages = Jojo::selectAssoc("SELECT pg_url AS url, pg_url, pageid FROM {page} WHERE pg_link = 'Jojo_Plugin_Jojo_Gallery3' OR pg_link= 'jojo_plugin_jojo_gallery3'");
    $gcats = Jojo::selectQuery("SELECT * FROM {gallerycategory}");
    foreach ($gcats as $g) {
         $pageid = $gpages[$g['gc_url']]['pageid'];
        Jojo::updateQuery("UPDATE {gallerycategory} SET pageid=? WHERE gallerycategoryid = ?", array($pageid, $g['gallerycategoryid']));
    }
}

/* Check for Gallery Image table */

$table = 'gallery3_image';
$query = "
    CREATE TABLE {gallery3_image} (
      `gallery3_imageid` int(11) NOT NULL auto_increment,
      `gi_name` varchar(255) NOT NULL default '',
      `gi_date` int(11) NOT NULL default '0',
      `gi_order` int(11) NOT NULL default '0',
      `filename` varchar(255) NOT NULL default '',
      `caption` varchar(255) NOT NULL default '',
      `credit` varchar(255) NOT NULL default '',
      `keyimage` tinyint(1) NOT NULL default '0',
      `gallery3id` int(11) NOT NULL default '0',
      PRIMARY KEY  (`gallery3_imageid`),
      FULLTEXT KEY `title` (`caption`),
      FULLTEXT KEY `body` (`caption`,`gi_name`)
    ) TYPE=MyISAM ;";

/* Convert mysql date format to unix timestamps */
if (Jojo::tableExists($table) && Jojo::getMySQLType($table, 'gi_date') == 'datetime') {
    date_default_timezone_set(Jojo::getOption('sitetimezone', 'Pacific/Auckland'));
    $items = Jojo::selectQuery("SELECT gallery3_imageid, gi_date FROM {gallery3_image}");
    Jojo::structureQuery("ALTER TABLE  {gallery3_image} CHANGE  `gi_date`  `gi_date` INT(11) NOT NULL DEFAULT '0'");
    foreach ($items as $k => $a) {
        if ($a['gi_date']!='0000-00-00') {
            $timestamp = Jojo::strToTimeUK($a['gi_date']);
        } else {
            $timestamp = 0;
        }
       Jojo::updateQuery("UPDATE {gallery3_image} SET gi_date=? WHERE gallery3_imageid=?", array($timestamp, $a['gallery3_imageid']));
    }
}
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