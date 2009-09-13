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

class Jojo_Field_gallery3images extends Jojo_Field
{

    /*
     * Check the value of this field
     */
    function checkvalue()
    {
        return true;
    }

    /*
     * Return the html for editing this field
     */
    function displayedit()
    {
        global $smarty;
        $this->texttype = $this->fd_options;

        foreach (Jojo::listPlugins('jojo_gallery3.php') as $pluginfile) {
            require_once($pluginfile);
            break;
        }

        /* Get an array of all images in /downloads/gallery3/[galleryid]/ */
        $galleryid = $this->table->getRecordID();
        if ($galleryid) {
            $thumbs = Jojo_Plugin_Jojo_Gallery3::getAdminHtml($galleryid);
            $smarty->assign('thumbs',    $thumbs);
            $smarty->assign('currentid', $galleryid);
        }
        return  $smarty->fetch('admin/fields/gallery3images.tpl');
    }
}