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
 * @package jojo_core
 */

Jojo_Field::includeFieldType('list');
Jojo_Field::includeFieldType('dblist');

//////////////////////DBLISTFIELD//////////////////////
class Jojo_Field_dblist_g3 extends Jojo_Field_dblist
{

    function checkValue() {

        $recordid = $this->table->getFieldValue('gallery3_imageid');

        /* no id, must be a new file, stop */
        if ( !$recordid ) {
            return true;
        }

        $oldgalleryid = Jojo::selectRow("SELECT `gallery3id` FROM {gallery3_image} WHERE `gallery3_imageid` = ?", array($recordid));
        $oldgalleryid = $oldgalleryid['gallery3id'];
        $newgalleryid = $this->value;

        /* gallery unchanged, stop */
        if ( $oldgalleryid == $newgalleryid) {
            return true;
        }

        $filename = $this->table->getFieldValue('filename');

        /* ensure we have data to work with */
        if ( (!$this->isblank()) && (!file_exists(_DOWNLOADDIR . '/gallery3/' . $oldgalleryid . '/' . $filename )) ) {
            $this->error = 'The file is missing from the old gallery directory ';
            return false;
        }

        /* check filename clash */
        if ( (!$this->isblank()) && (file_exists(_DOWNLOADDIR . '/gallery3/' . $newgalleryid . '/' . $filename )) ) {
            $this->error = "An image with this file name already exists in this gallery";
            return false;
         }

        /* Move the file */
        $oldfile = _DOWNLOADDIR . '/gallery3/' . $oldgalleryid . '/' . $filename ;
        $newfile = _DOWNLOADDIR . '/gallery3/' . $newgalleryid . '/' . $filename ;

        /* create the folder if it does not already exist */
        Jojo::RecursiveMkdir(dirname($newfile));

        rename($oldfile,$newfile);

        if ( !file_exists(_DOWNLOADDIR . '/gallery3/' . $newgalleryid . '/' . $filename ) ) {
            $this->error = "The image didn't move";
            return false;
         }
        /* update the database so the save function knows where to find the file */
        Jojo::updateQuery("UPDATE {gallery3_image} SET `gallery3id`  =  ? WHERE `gallery3_imageid` = ?", array($newgalleryid, $recordid));

        return empty($this->error) ? true : false;
    }

}
