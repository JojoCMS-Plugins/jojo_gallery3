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


class Jojo_Field_gallery3Image extends Jojo_Field
{
    var $fd_size;
    var $error;
    var $index;

    function __construct($fielddata = array())
    {
        parent::__construct($fielddata);
        $this->fd_maxvalue   = 1000 * Jojo::getOption('max_fileupload_size','5000');
        $this->thumbsize     = 200; //pixels - this should be defined in the DB rather than hard-coded here
        $this->viewthumbsize = 350;
    }

    function checkvalue()
    {
        $recordid = $this->table->getFieldValue('gallery3_imageid');
        $galleryid = $this->table->getFieldValue('gallery3id');
        $oldgalleryid = Jojo::selectRow("SELECT `gallery3id` FROM {gallery3_image} WHERE `gallery3_imageid` = ?", array($recordid));
        $oldgalleryid = isset($oldgalleryid['gallery3id']) ? $oldgalleryid['gallery3id'] : '';

        if (($this->fd_required == 'yes') && ($this->isblank())) {
            $this->error = 'Required field';
        }

        if ( (!$this->isblank()) && !file_exists(_DOWNLOADDIR . '/gallery3/' . $galleryid . '/' . $this->value ) && !file_exists(_DOWNLOADDIR . '/gallery3/' . $oldgalleryid . '/' . $this->value )  ) {
            $this->error = 'The image file is missing from the upload directory'. $galleryid . $oldgalleryid;
        }

        return empty($this->error) ? true : false;
    }

    function displayedit()
    {
        $galleryid = $this->table->getFieldValue('gallery3id');

        $retval = '<div class="col-md-12">';
        $crop_x = '';
        $crop_y = '';
        $thumb_w = 0;
        $thumb_h = 0;
        $readonly = ($this->fd_readonly) ? ' readonly="readonly"' : '';
        $suffix = ( ($this->index == '0') || ($this->index != '') ) ? '_' . $this->index : '';
        if (!$this->isblank()) {
            /* Make sure the file exists */
            if (file_exists(_DOWNLOADDIR . '/gallery3/' . $galleryid . '/' . $this->value)) {
                $filesize = filesize(_DOWNLOADDIR.'/gallery3/' . $galleryid . '/' . $this->value);
                $filetype = Jojo::getFileExtension($this->value);

                /* If an image, then display a thumbnail image */
                if ( (strtolower(Jojo::getFileExtension($this->value)) == "jpg") or (strtolower(Jojo::getFileExtension($this->value)) == "jpeg") or (strtolower(Jojo::getFileExtension($this->value)) == "gif") or (strtolower(Jojo::getFileExtension($this->value)) == "png") ) {
                    /* read cropdata */
                    $cropdata = Jojo_Plugin_Core_Image::getCropData(_DOWNLOADDIR.'/gallery3/' . $galleryid . '/' . $this->value);
                    $crop_x = $cropdata[0];
                    $crop_y = $cropdata[1];

                    //Find out the dimensions of the image (actual size)
                    $imagesize = ( Jojo::fileExists(_DOWNLOADDIR . '/gallery3/' . $galleryid . '/' . $this->value) && ($this->value != '') ) ? getimagesize(_DOWNLOADDIR. '/gallery3/' . $galleryid . '/' . $this->value) : false;
                    if (!$imagesize) { //this would happen for a file that is labelled as an image, but isn't a valid format
                        $this->error = "The image does not appear to be a valid format";
                    } else {
                        if ($imagesize[0] > $imagesize[1]) {
                            $thumb_w = $this->thumbsize;
                            $thumb_h = round($this->thumbsize * ($imagesize[1] / $imagesize[0]));
                        } else {
                            $thumb_w = round($this->thumbsize * ($imagesize[0] / $imagesize[1]));
                            $thumb_h = $this->thumbsize;
                        }
                        $retval .= '<div id="crop_canvas_' . $this->fd_field . '" class="crop_canvas" style="width:' . $thumb_w . 'px;height:' . $thumb_h . 'px;"></div><img src="images/' . $this->thumbsize . '/gallery3/' . $galleryid . '/' . $this->value . '" border="0" width="' . $thumb_w . '" height="' . $thumb_h . '" alt="" /><br>
                        <p><span class="note">click image to ' . ( $cropdata ? 'move' : 'set' ) . ' focal point for auto-cropping</span></p>';
                    }
                }
            } else { //the database says there should be a file, but there isn't
                $this->error = "The image is missing from the upload directory"; //this should already be set by now
            }
        }

        $class = ($this->error != "") ? 'error' : '';
        $retval .= '<input type="hidden" name="fm_'.$this->fd_field."\" value=\"".$this->value."\" /><input type=\"hidden\" name=\"fm_".$this->fd_field."_delete\" value=\"\" />";
        $retval .=  $this->value ? '<p>'.$this->value.'&nbsp; <span class="note">('. ( $imagesize ? $imagesize[0] . 'x' . $imagesize[1] . 'px - ' : '') . Jojo::roundBytes($filesize) . ')</span>&nbsp; <a class="btn btn-default btn-xs" href="'._SITEURL.'/downloads/gallery3/' . $galleryid . '/' . $this->value.'" target="_BLANK">view file</a></p>
        <div>replace (on save) with:</div>' : '';
        $retval .= '<input type="hidden" name="MAX_FILE_SIZE" value="'.$this->fd_maxvalue.'" />'."\n".'<input class="' . $class . '" type="file" name="fm_FILE_'.$this->fd_field.'" id="fm_FILE_'.$this->fd_field.'"  size="'.$this->fd_size.'" value=""'.$readonly.' onchange="fullsave=true;" title="'.htmlentities($this->fd_help).'" />';
        $cropval = ($crop_x && $crop_y) ? $crop_x .','. $crop_y : '';
        $retval .= '<input type="hidden" name="fm_crop_'.$this->fd_field.'" id="fm_crop_'.$this->fd_field.'" value="'.$cropval.'" />';
        $retval .= '<script type="text/javascript">var crop=$(\'#fm_crop_'.$this->fd_field.'\').val().split(\',\'); if (crop.length==2){$(\'#crop_canvas_'.$this->fd_field.'\').append(\'<div class="crop_point" style="margin:\'+(Math.round(crop[1]*'.($thumb_h/100).') - 25)+\'px 0 0 \'+(Math.round(crop[0]*'.($thumb_w/100).') - 25)+\'px;"></div>\');} $(\'#crop_canvas_'.$this->fd_field.'\').mousedown(function(event){$(\'#crop_canvas_'.$this->fd_field.'\').children(\'.crop_point\').remove();$(\'#crop_canvas_'.$this->fd_field.'\').append(\'<div class="crop_point" style="margin:\'+(event.pageY - $(this).offset().top - 25)+\'px 0 0 \'+(event.pageX - $(this).offset().left - 25)+\'px;"></div>\');$(\'#fm_crop_'.$this->fd_field.'\').val( Math.round((event.pageX - $(this).offset().left)/'.($thumb_w/100).')+\',\'+Math.round((event.pageY - $(this).offset().top)/'.($thumb_h/100).'));return false;});</script>' . "\n";
        $retval .= '</div>';


        return $retval;
    }


    function displayview()
    {
        $retval = '';
        if (!$this->isblank()) {
            $galleryid = $this->table->getFieldValue('gallery3id');

            /* Make sure the file exists */
            if (file_exists(_DOWNLOADDIR.'/'.$this->fd_table.'/'.$this->value)) {
                $filesize = filesize(_DOWNLOADDIR.'/'.$this->fd_table.'/'.$this->value);
                $filetype = strtolower(Jojo::getFileExtension($this->value));
                $retval .= '<div class="col-md-12">';
                $retval .= '<span title="' . Jojo::roundBytes($filesize) . "\"><a href=\"" . _SITEURL . '/downloads/gallery3/' . $galleryid . '/' .  $this->value . "\" target=\"_BLANK\">" . $this->value . "</a></span><br>";

                //If an image, then display a thumbnail image
                if ( $filetype == "jpg" || $filetype == "jpeg" ) {
                    /* Find out the dimensions of the image (actual size) */
                    $imagesize = getimagesize(_DOWNLOADDIR.'/'.$this->fd_table.'s/'.$this->value);
                    /* this would happen for a file that is labelled as an image, but isn't a valid format */
                    if (!$imagesize) {
                        $this->error = 'The image does not appear to be a valid format';
                    } else {
                        $retval .= '<span title="Actual size ' . $imagesize[0] . 'x' . $imagesize[1] . 'px '. Jojo::roundBytes($filesize).'"><img src="images/'.$this->viewthumbsize . '/gallery3/' . $galleryid . '/' . $this->value . '" border="0" align="absmiddle" alt="' . $this->value . '"></span><br>';
                    }
                }
                $retval .= '</div>';
            } else { //the database says there should be a file, but there isn't
                $this->error = 'The image is missing from the upload directory'; //this should already be set by now
            }
        }

        if ($this->error != '') {$class = ' error';}

        return $retval;
    }

    function getfilesize()
    {
        $galleryid = $this->table->getFieldValue('gallery3id');

        $filesize = 0;
        if (file_exists(_DOWNLOADDIR . '/gallery3/' . $galleryid . '/' . $this->value )) {
            $filesize = filesize(_DOWNLOADDIR . '/gallery3/' . $galleryid . '/' . $this->value );
        }
        return Jojo::roundBytes($filesize);
    }

    function getImageDimensions()
    {
        $galleryid = $this->table->getFieldValue('gallery3id');

        $retval = "";
        if (file_exists(_DOWNLOADDIR . '/gallery3/' . $galleryid . '/' . $this->value )) {
            $filetype = strtolower(Jojo::getFileExtension($this->value));
            if ($filetype == "jpg" || $filetype == "jpeg") {
                /* Find out the dimensions of the image (actual size) */
                $imagesize = getimagesize(_DOWNLOADDIR . '/gallery3/' . $galleryid . '/' . $this->value );
                /* this would happen for a file that is labelled as an image, but isn't a valid format */
                if (!$imagesize) {
                    $this->error = 'The image does not appear to be a valid format';
                } else {
                    $retval .= $imagesize[0].' x '.$imagesize[1].'px';
                }
            }
        }
        return $retval;
    }

    //TODO: Add error checking to this..
    function getrelativeurl()
    {
        $galleryid = $this->table->getFieldValue('gallery3id');

        return 'downloads/gallery3/' . $galleryid . '/' . $this->value;
    }

    //TODO: Add error checking to this..
    function getabsoluteurl()
    {
        $galleryid = $this->table->getFieldValue('gallery3id');

        return _SITEURL.'/downloads/gallery3/' . $galleryid . '/' . $this->value;
    }

    function setvalue($newvalue)
    {
        /* delete the file if the _delete field has been set */
        if (!empty($_POST['fm_'.$this->fd_field.'_delete'])) {
            return $this->deletefile();
        }
        
        $this->value = $newvalue;
        $galleryid = $this->table->getFieldValue('gallery3id');

        /* set cropdata if needed */
        if (!empty($newvalue) && !empty($_POST['fm_crop_' . $this->fd_field])) {
            $crop = explode(',', $_POST['fm_crop_'.$this->fd_field]);
            $data = file_get_contents(_DOWNLOADDIR . '/gallery3/' . $galleryid . '/'. $newvalue);
            Jojo::updateQuery("REPLACE INTO {cropdata} SET hash=?, filename=?, x=?, y=?", array(sha1($data), $newvalue, $crop[0], $crop[1]));
        }

        /* ensure we have data to work with */
        if (!isset($_FILES["fm_FILE_".$this->fd_field])) return false;

        /* set some variables for convenience */
        $filename = str_replace(' ', '_', str_replace(array('?','&',"'",',','[',']'), '', stripslashes($_FILES["fm_FILE_".$this->fd_field]['name'])));
        $tmpfilename = $_FILES["fm_FILE_".$this->fd_field]['tmp_name'];


        /* Check error codes */
        switch ($_FILES['fm_FILE_'.$this->fd_field]['error']) {
            case UPLOAD_ERR_INI_SIZE: //1
                $this->error = 'The uploaded file exceeds the maximum size allowed in PHP.INI';
                break;
            case UPLOAD_ERR_FORM_SIZE: //2
                $this->error = 'The uploaded file exceeds the maximum size allowed ('.$this->fd_maxvalue.')';
                break;
            case UPLOAD_ERR_PARTIAL: //3
                $this->error = 'The file has only been partially uploaded. There may have been an error in transfer, or the server may be having technical problems.';
                break;

            case UPLOAD_ERR_NO_FILE: //4 - this is only a problem if it's a required field
                //remember, a required field only needs to be set the first time, perhaps its better to check this somewhere else
                break;

            case 6: // UPLOAD_ERR_NO_TMP_DIR - for some odd reason the constant wont work
                $this->error = 'There is no temporary folder on the server - please contact the webmaster ('._WEBMASTERADDRESS.')';
                break;

            case UPLOAD_ERR_OK: //0
                /* check for empty file */
                if($_FILES['fm_FILE_'.$this->fd_field]['size'] == 0) {
                    $this->error = 'The uploaded file is empty.';
                    return false;
                }

                /* ensure file is uploaded correctly */
                if (!is_uploaded_file($tmpfilename)) {
                    /* improve this code when you have time - will work, but needs fleshing out */
                    $this->error = 'Possible hacking attempt. Script will now halt.';
                    die($this->error);
                }

                /* All appears good, so prepare to move file to final resting place */
                $destination = _DOWNLOADDIR . '/gallery3/' . $galleryid . '/' . basename($filename);

                /* create the folder if it does not already exist */
                Jojo::RecursiveMkdir(dirname($destination));

                /* Ensure file does not already exist on server, rename if it does */
                $i=1;
                $newname ='';
                while (file_exists($destination)){
                    $i++;
                    $newname = $i."_".$filename;
                    $destination = _DOWNLOADDIR . '/gallery3/' . $galleryid . '/' .$newname;
                }

                /* move to final location */
                if (file_exists($destination) || move_uploaded_file($tmpfilename, $destination)) {
                    $message = "Upload successful";
                    $this->value =  Jojo::either($newname, $filename);
                } else {
                    $this->error = "Possible hacking attempt. Script will now halt.";
                    die($this->error);
                }
                break;
            default:
                /* this code shouldn't execute - 0 should be the default */
                $this->error = 'An unknown error occurred - please contact the webmaster ('._WEBMASTERADDRESS.')';
        }
        return true;
    }

    function deletefile()
    {
        /* Make sure there is a file set (ie cant delete empty variable) */
        if ($this->isblank()) {
            $this->error = "There is no file to delete."; //TODO: come up with a decent error message
            return false;
        }

        $galleryid = $this->table->getFieldValue('gallery3id');

        /* check file exists */
        if (!file_exists(_DOWNLOADDIR.'/gallery3/' . $galleryid . '/' . $this->value )) {
            $this->error = "The file does not exist on the server. It may have already been deleted.";
            return false;
        }

        /* Check it's a file, not a directory (previous check will return true if a directory exists of same name) */
        if (!is_file(_DOWNLOADDIR . '/gallery3/' . $galleryid . '/' . $this->value )) {
            $this->error = "The file specified is not a file (it may be a directory, or may not exist).";
            return false;
        }

        /* delete the file */
        if (!unlink(_DOWNLOADDIR.'/gallery3/' . $galleryid . '/' . $this->value )) {
            $this->error = "Unable to delete the specified file. The file permissions may not be set correctly.";
            return false;
        }

        /* check file exists again (it shouldn't because we just deleted it) */
        if (file_exists(_DOWNLOADDIR.'/gallery3/' . $galleryid . '/' . $this->value )) {
            $this->error = "The file still exists on the server. It may not have been deleted properly.";
            return false;
        }

        /* Clear field in database to reflect deleted file */
        if ($this->table->getRecordID() != 0) {
            $query = sprintf("UPDATE {%s} SET `%s` = '' WHERE `%s` = ? LIMIT 1",
                                $this->fd_table,
                                $this->fd_field,
                                $this->table->getOption('primarykey')
                            );
            Jojo::updateQuery($query, array($this->table->getRecordID()));
        }
        $this->value = '';

        /* File is gone */
        return true;
    }

    /* Delete the file when the database record is deleted */
    function ondelete()
    {
        $this->deletefile();
        return true;
    }
}
