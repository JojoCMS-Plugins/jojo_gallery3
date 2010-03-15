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

/* ensure users of this function have access to the admin page */
/*
$page = Jojo_Plugin::getPage(Jojo::parsepage('admin'));
if (!$page->perms->hasPerm($_USERGROUPS, 'view')) {
    echo "You do not have permission to use this function";
    exit();
}
*/



$frajax = new frajax();
$frajax->title = 'Upload Image - ' . _SITETITLE;
$frajax->sendHeader();

$galleryid = Jojo::getFormData('id', '');
if (empty($galleryid)) {
    $frajax->alert('an error occured uploading this file');
    exit();
}

if (isset($_FILES['uploadimage'])) {

    $filename = $_FILES['uploadimage']['name'];

    /* We must not allow PHP files to be uploaded to the server - dangerous */
    $ext = Jojo::getfileextension($filename);
    if ( ($ext == 'php') || ($ext == 'php3') || ($ext == 'php4') || ($ext == 'inc') || ($ext == 'phtml')) {
        $frajax->alert('You cannot upload PHP files into this system for security reasons. If you really need to, please Zip them first and upload the Zip file.');
        $frajax->sendFooter();
        exit();
    }

    //Check error codes
    switch ($_FILES['uploadimage']['error']) {
        case UPLOAD_ERR_INI_SIZE: //1
               $error = "The uploaded file exceeds the maximum size allowed (1Mb)";
            break;
        case UPLOAD_ERR_FORM_SIZE: //2
               $error = "The uploaded file exceeds the maximum size allowed in PHP.INI";
            break;
        case UPLOAD_ERR_PARTIAL: //3
               $error = "The file has only been partially uploaded. There may have been an error in transfer, or the server may be having technical problems . ";
            break;
        case UPLOAD_ERR_NO_FILE: //4 - this is only a problem if it's a required field
            $error = "File missing";
            break;
        case 6: // UPLOAD_ERR_NO_TMP_DIR - for some odd reason the constant wont work
            $error = "There is no temporary folder on the server";
            //log for administrator
            break;
        case UPLOAD_ERR_OK: //0
            //check for empty file
            if($_FILES['uploadimage']["size"] == 0) {
                $error = "The uploaded file is empty . ";
            }
            if (!is_uploaded_file($_FILES['uploadimage']['tmp_name'])) { //improve this code when you have time - will work, but needs fleshing out
                $frajax->alert('The write permissions may not be set correctly on this folder. Please contact the administrator.');
                exit();
            }

            if ($error != '') $frajax->alert($error);


               /* Rename files on the way up to be search engine friendly - no spaces, no caps, no special chars */
              $pieces = explode('.',$filename);
              if (count($pieces) > 1) {
                $newfilename = '';
                $n = count($pieces)-1;
                for ($i = 0;$i<$n;$i++) {
                  $newfilename .=  Jojo::cleanURL($pieces[$i]);
                }
                $newfilename .= '.'.strtolower($pieces[count($pieces)-1]);
                $filename = $newfilename;
              }


               /* All appears good, so attempt to move to final resting place */


            $destination = _DOWNLOADDIR.'/gallery3/'.$galleryid;
            $destination = rtrim($destination,'/').'/'.basename($filename);

            /* ensure the destination is within _DOWNLOADDIR */
            if (!preg_match('%^'._DOWNLOADDIR.'(.*)\\z%im', $destination)) {
                $frajax->alert('Destination folder ('.$destination.') out of bounds');
                exit();
           }
            Jojo::RecursiveMkdir(dirname($destination));
            //Ensure file does not already exist on server, rename if it does

            if (move_uploaded_file($_FILES['uploadimage']['tmp_name'], $destination)) {
              //$frajax->alert('File uploaded');

              /* reload parts of the UI */
              foreach (Jojo::listPlugins('jojo_gallery3.php') as $pluginfile) {
                  require_once($pluginfile);
                  break;
              }
              $thumbs = Jojo_Plugin_Jojo_gallery3::getAdminHtml($galleryid);
              $frajax->assign("files", "innerHTML", $thumbs);
              $frajax->assign("uploadimage", "value", '');
              /* file list */
              /*
              require_once(_BASEDIR . '/includes/insert-image-functions.inc.php');
              $resources = getImageList($folder);
              $smarty->assign('resources',$resources);
              $output = $smarty->fetch('insert-image-files.tpl');
              $frajax->assign("files", "innerHTML",$output);
              */
            } else {
                $frajax->alert('The file upload failed. Please contact the webmaster . ');
                exit();
            }
            break;
        default:
            //this code shouldn't execute - 0 should be the default
    }

} else {
    echo "";
}

$frajax->sendFooter();
