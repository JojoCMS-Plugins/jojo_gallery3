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
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 * @package jojo_gallery3
 */
class Jojo_Plugin_Jojo_gallery3 extends Jojo_Plugin
{

    public static function getGalleries($categoryid=false, $language=false, $sortby=false, $include=false) {
        global $page;
        if ($categoryid == 'all' && $include != 'alllanguages') {
            $categoryid = array();
            $sectionpages = self::getPluginPages('', $page->page['root']);
            foreach ($sectionpages as $s) {
                $categoryid[] = $s['gallerycategoryid'];
            }
        }
        if (is_array($categoryid)) {
             $categoryquery = " AND category IN ('" . implode("','", $categoryid) . "')";
        } else {
            $categoryquery = is_numeric($categoryid) ? " AND category = '$categoryid'" : '';
        }
        $query  = "SELECT i.*, c.*, p.pageid, pg_menutitle, pg_title, pg_url, pg_status, pg_language, pg_livedate, pg_expirydate";
        $query .= " FROM {gallery3} i";
        $query .= " LEFT JOIN {gallerycategory} c ON (i.category=c.gallerycategoryid) LEFT JOIN {page} p ON (c.pageid=p.pageid)";
        $query .= " WHERE 1" . $categoryquery;
        $galleries = Jojo::selectQuery($query);
        $sortby = !$sortby && isset($galleries[0]['sortby']) ? $galleries[0]['sortby'] : $sortby;
        $galleries = self::cleanItems($galleries, '', $include);
        $galleries = self::formatItems($galleries);
        $galleries =  self::sortItems($galleries, $sortby);
        return $galleries;
    }

    static function getItemsById($ids = false, $sortby=false, $clean=true) {
        $query  = "SELECT i.*, c.*, p.pageid, pg_menutitle, pg_title, pg_url, pg_status, pg_language, pg_livedate, pg_expirydate";
        $query .= " FROM {gallery3} i";
        $query .= " LEFT JOIN {gallerycategory} c ON (i.category=c.gallerycategoryid) LEFT JOIN {page} p ON (c.pageid=p.pageid)";
        $query .=  is_array($ids) ? " WHERE gallery3id IN ('". implode("',' ", $ids) . "')" : " WHERE gallery3id=$ids";
        $items = Jojo::selectQuery($query);
        $sortby = !$sortby && isset($items[0]['sortby']) ? $items[0]['sortby'] : $sortby;
        $items = $clean ? self::cleanItems($items) : $items;
        $items = self::formatItems($items);
        $items = is_array($ids) ? self::sortItems($items, $sortby) : $items[0];
        return $items;
    }

    /* clean items for output */
    static function cleanItems($items, $exclude=false, $include=false) {
        $now    = time();
        foreach ($items as $k=>&$i){
            $pagedata = Jojo_Plugin_Core::cleanItems(array($i), $include);
            if (!$pagedata) {
                unset($items[$k]);
                continue;
            }
            $i['pagetitle'] = $pagedata[0]['title'];
            $i['pageurl']   = $pagedata[0]['url'];
        }
        return $items;
    }

    static function formatItems($items) {
         foreach ($items as $k=>&$i){
            $i['id']           = $i['gallery3id'];
            $i['title']        = htmlspecialchars($i['name'], ENT_COMPAT, 'UTF-8', false);
            $i['menutitle']  = isset($i['menutitle']) && !empty($i['menutitle']) ? htmlspecialchars($i['menutitle'], ENT_COMPAT, 'UTF-8', false) : $i['title'];
            // Snip for the index description
            $i['bodyplain'] = array_shift(Jojo::iExplode('[[snip]]', $i['body']));
            /* Strip all tags and template include code ie [[ ]] */
            $i['bodyplain'] = preg_replace('/\[\[.*?\]\]/', '',  trim(strip_tags($i['bodyplain'])));
            $i['date']       = $i['g_date'];
            $i['datefriendly'] = Jojo::formatTimestamp($i['g_date'], "medium");
            $i['files'] = array();
            if (empty($i['sortby'])) {
                $i['files'] = self::getImages($i['id'], 0);
            } else {
                $i['files'] = self::getImages($i['id'], 0, $i['sortby']);
            }
            $i['keyimages'] = array();
            if (isset($i['indeximage']) && !empty($i['indeximage']) ) {
                $i['keyimages'][] =  'gallery3s/' . $i['indeximage'];
            }
            if (count($i['files']) && isset($i['files'][0]['keyimage'])) {
                foreach ($i['files'] as $f) {
                    if ($f['keyimage']) {
                        $i['keyimages'][] =  'gallery3/' .  $i['id'] . '/' . $f['filename'];
                    }
                }
            }
            if (!$i['keyimages'] && count($i['files'])) $i['keyimages'][] =  'gallery3/' .  $i['id'] . '/' . $i['files'][0]['filename'];
            $i['image'] = count($i['keyimages']) ? $i['keyimages'][0] : (count($i['files']) ? 'gallery3/' .  $i['id'] . '/' . $i['files'][0]['filename'] :'');
            $i['numimages']     = isset($i['files']) ? count($i['files']) : 0;
            // gallery settings override category settings
            $i['thumbsize'] =  isset($i['thumbnailsize']) && !empty($i['thumbnailsize']) && empty($i['thumbsize']) ? $i['thumbnailsize'] : $i['thumbsize'];
            $i['previewsize'] =  isset($i['imagesize']) && !empty($i['imagesize']) && empty($i['previewsize']) ? $i['imagesize'] : $i['previewsize'];
            $i['indeximagesize'] =  isset($i['indeximagesize']) && !empty($i['indeximagesize']) ? $i['indeximagesize'] : $i['thumbsize'];
            $i['baseurl']          = $i['url'];
            $i['url']          = self::getUrl($i['id'], $i['url'], $i['title'], $i['language'], $i['category']);
            $i['plugin']     = 'jojo_gallery3';
            unset($items[$k]['bodycode']);
        }
        return $items;
    }

    /* sort items for output */
    static function sortItems($items, $sortby=false) {
        if ($sortby) {
            $order = "date";
            $reverse = false;
            switch ($sortby) {
              case "date":
                $order="date";
                $reverse = true;
                break;
              case "name":
                $order="name";
                break;
              case "order":
                $order="order";
                break;
            }
            usort($items, array('Jojo_Plugin_Jojo_gallery3', $order . 'sort'));
            $items = $reverse ? array_reverse($items) : $items;
        }
        return array_values($items);
    }

    private static function filenamesort($a, $b) {
        return strcmp($a['filename'],$b['filename']);
    }

    private static function namesort($a, $b) {
         if (isset($a['name'])) {
            return strcmp($a['name'],$b['name']);
        } else {
            return strcmp($a['filename'],$b['filename']);
        }
    }

    private static function datesort($a, $b) {
         if (isset($a['datetime'])) {
            return strnatcasecmp($a['datetime'],$b['datetime']);
        } else {
            return strnatcasecmp($a['g_date'],$b['g_date']);
        }
    }

    private static function imageidsort($a, $b) {
        if(isset($a['imageid']) and isset($b['imageid'])) return strcmp($a['imageid'],$b['imageid']);
        if(isset($a['imageid'])) return 1;
        return -1;
    }

    private static function ordersort($a, $b) {
         if (isset($a['gi_order'])) {
            return strnatcasecmp($a['gi_order'],$b['gi_order']);
        } else {
            return strnatcasecmp($a['displayorder'],$b['displayorder']);
        }
    }

    public function _getContent() {
        global $smarty;
        $content = array();
        $pageid = $this->page['pageid'];
        $pageprefix = Jojo::getPageUrlPrefix($pageid);
        $smarty->assign('multilangstring', $pageprefix);
        $id = Jojo::getFormData('id',        0);
        $url       = Jojo::getFormData('url',      '');
        $categorydata =  Jojo::selectRow("SELECT * FROM {gallerycategory} WHERE pageid = ?", $pageid);
        $categorydata['type'] = isset($categorydata['type']) ? $categorydata['type'] : 'normal';
        if ($categorydata['type']=='index') {
            $categoryid = 'all';
        } elseif ($categorydata['type']=='parent') {
            $childcategories = Jojo::selectQuery("SELECT gallerycategoryid FROM {page} p  LEFT JOIN {gallerycategory} c ON (c.pageid=p.pageid) WHERE pg_parent = ? AND pg_link = 'jojo_plugin_jojo_gallery3'", $pageid);
            foreach ($childcategories as $c) {
                $categoryid[] = $c['gallerycategoryid'];
            }
            $categoryid[] = $categorydata['gallerycategoryid'];
        } else {
            $categoryid = $categorydata['gallerycategoryid'];
        }
        $sortby = $categorydata ? $categorydata['sortby'] : 'order';

        $galleries = self::getGalleries($categoryid, '', $sortby, $include='showhidden');

        /* if there is only one gallery and singlepage option is set, display the gallery data instead */
        $single = false;
        if (Jojo::getOption('gallery3_single_page') == 'yes' && count($galleries) ==1) {
                $single = true;
                $id = $galleries[0]['gallery3id'];
                $smarty->assign('single', $single);
        }
        /* use the URL to find the ID */
        if (!empty($url) && empty($id)) {
            foreach ($galleries as $k => $g ) {
                if ($g['baseurl'] == $url) {
                    $id = $g['id'];
                }
            }
        }
        if (!empty($id)) {
            foreach ($galleries as $k => $g ) {
                if ($g['gallery3id'] == $id) {
                    $gallery = $g;
                    $currentkey = $k;
                }
            }
            /* get gallery data */
            if (!isset($currentkey)) {
                $content['content'] = 'Unable to find this gallery.';
                return $content;
            }
           /* calculate the next and previous galleries */
            if (Jojo::getOption('gallery_next_prev') == 'yes') {
                $nextkey = $currentkey + 1;
                if (isset($galleries[$nextkey])) {
                      $smarty->assign('nextgallery', $galleries[$nextkey]);
                }
                $prevkey = $currentkey - 1;
                if (isset($galleries[$prevkey])) {
                      $smarty->assign('prevgallery', $galleries[$prevkey]);
                }
             }

            /* Ensure the tags class is available */
            if (class_exists('Jojo_Plugin_Jojo_Tags')) {
                /* Split up tags for display */
                $tags = array();
                $tags = Jojo_Plugin_Jojo_Tags::getTags('jojo_gallery3', $id);
                if (count($tags)) {
                    $smarty->assign('tags', $tags);
                }
            }

            /* no need for breadcrumb if single option set */
            $breadcrumbs = $this->_getBreadCrumbs();
            if (!$single) {
                /* Add breadcrumb */
                $breadcrumb = array();
                $breadcrumb['name']               = $gallery['title'];
                $breadcrumb['rollover']           = $gallery['title'];
                $breadcrumb['url']                = $gallery['url'];
                $breadcrumbs[count($breadcrumbs)] = $breadcrumb;
            }

            /* Get the HTML */
            $smarty->assign('gallery', $gallery);
            $smarty->assign('galleryhtml', self::getGalleryHtml($id, $gallery));

            $content['content']         = $smarty->fetch('jojo_gallery3_detail.tpl');
            $content['breadcrumbs']     = $breadcrumbs;
            $content['title']           = $gallery['title'];
            $content['seotitle']        = Jojo::either($gallery['seotitle'], $gallery['title']);
            $content['metadescription'] = $gallery['metadescription'];

        } else {
            $smarty->assign('galleries', $galleries);
            $smarty->assign('pagecontent', $this->page['pg_body']);
            $content['content'] = $smarty->fetch('jojo_gallery3_index.tpl');
        }
        return $content;
    }

    static function _getPrefix($categoryid=false) {
        $cacheKey = 'gallery3';
        $cacheKey .= ($categoryid) ? $categoryid : 'false';

        /* Have we got a cached result? */
        static $_cache;
        if (isset($_cache[$cacheKey])) {
            return $_cache[$cacheKey];
        }

        /* Cache some stuff */
        $res = Jojo::selectRow("SELECT p.pageid, pg_title, pg_url FROM {page} p LEFT JOIN {gallerycategory} c ON (c.pageid=p.pageid) WHERE `gallerycategoryid` = '$categoryid'");
        if ($res) {
            $_cache[$cacheKey] = !empty($res['pg_url']) ? $res['pg_url'] : $res['pageid'] . '/' . Jojo::cleanURL($res['pg_title']);
        } else {
            $_cache[$cacheKey] = '';
        }
        return $_cache[$cacheKey];
    }

    static function getPrefixById($id=false) {
        if ($id) {
            $data = Jojo::selectRow("SELECT category FROM {gallery3} WHERE gallery3id = ?", array($id));
            $prefix = $data ? self::_getPrefix($data['category']) : '';
            return $prefix;
        }
        return false;
    }

    /**
     * Check if url prefix belongs to this plugin
     */
    static public function checkPrefix($prefix) {
        static $_prefixes, $categories;
        if (!isset($categories)) {
            /* Initialise cache */
            $categories = array(false);
            $categories = array_merge($categories, Jojo::selectAssoc("SELECT gallerycategoryid, gallerycategoryid as gallerycategoryid2 FROM {gallerycategory}"));
            $_prefixes = array();
        }
        /* Check if it's in the cache */
        if (isset($_prefixes[$prefix])) {
            return $_prefixes[$prefix];
        }
        /* Check everything */
        foreach($categories as $category) {
            $testPrefix = self::_getPrefix($category);
            $_prefixes[$testPrefix] = true;
            if ($testPrefix == $prefix) {
                /* The prefix is good */
                return true;
            }
        }
        /* Didn't match */
        $_prefixes[$testPrefix] = false;
        return false;
    }

    public static function isUrl($uri) {
        $prefix = false;
        $getvars = array();
        /* Check the suffix matches and extract the prefix */
        if ($uribits = Jojo_Plugin::isPluginUrl($uri)) {
            $prefix = $uribits['prefix'];
            $getvars = $uribits['getvars'];
        } else {
            return false;
        }
        /* Check the prefix matches */
        if ($res = self::checkPrefix($prefix)) {
            /* If full uri matches a prefix it's an index page so ignore it and let the page plugin handle it */
            if (self::checkPrefix(trim($uri, '/'))) return false;
            /* The prefix is good, pass through uri parts */
            foreach($getvars as $k => $v) {
                $_GET[$k] = $v;
            }
            return true;
        }
        return false;
    }

     public static function customhead() {
        return '<link rel="stylesheet" type="text/css" href="'._SITEURL.'/external/jquery-lightbox/css/jquery.lightbox-0.4.css" media="screen" />'."\n";
    }

     public static function foot() {
        return '<script type="text/javascript" src="'._SITEURL.'/external/jquery-lightbox/js/jquery.lightbox-0.4.pack.js"></script>'."\n";

    }

    public static function getImages($galleryid, $refresh=false, $sort="imageid") {
        if (empty($galleryid)) {
            return array();
        }

        switch ($sort) {
          case "name":
            $order="gi_name,filename";
            break;
          case "filename":
            $order="filename,gi_name";
            break;
          case "date":
            $order="gi_date DESC";
            break;
          case "imageid":
            $order="gallery3_imageid";
            break;
          case "order":
            $order="gi_order";
        }

        if (Jojo::ctrlF5() || $refresh) {
            $files = Jojo_Plugin_Jojo_gallery3::scandir(_DOWNLOADDIR . '/gallery3/' . $galleryid . '/');
            if (empty($files)) {
                return array();
            }

            /* check list of files in the gallery directory against the database records */
            $dbrows = Jojo::selectAssoc("SELECT `filename`, gi_date as datetime, gi_name as name, gi.*, g.sortby FROM {gallery3_image} as gi LEFT JOIN {gallery3} g ON (g.gallery3id=gi.gallery3id ) WHERE gi.gallery3id = ? order by $order", $galleryid);

            foreach($files as $key => $filename) {
                /* Image in the directory - but no record in the database */
                if (!isset($dbrows[$filename])) {
                    $exif = (function_exists('exif_read_data') && Jojo::getFileExtension($filename)=='jpg') ? exif_read_data(_DOWNLOADDIR . '/gallery3/' . $galleryid . '/' . $filename): array();
                    $timestamp = time();
                    if (isset($exif['DateTime'])) {
                        $datetime = explode(' ', $exif['DateTime']);
                        $date = explode(':', $datetime[0]);
                        $time = explode(':', $datetime[1]);
                        $timestamp = mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);
                    }
                    $id = Jojo::insertQuery("INSERT INTO {gallery3_image} SET `filename` = ?, `gallery3id` = ?, `gi_date` = ? ", array($filename, $galleryid, $timestamp));
                    $files[$key] = array(
                                    'filename'   => $filename,
                                    'gallery3id' => $galleryid,
                                    'imageid' => $id,
                                    'date'           => Jojo::formatTimestamp($timestamp, "medium"),
                                    'datetime'     => $timestamp
                                   );
                } else {
                    /* Image in the directory and in the database - return the record */
                    $fileloc = _DOWNLOADDIR . '/gallery3/' . $galleryid . '/' . $filename;
                    $files[$key] = array(
                                    'filename'      => $filename,
                                    'gallery3id'    => $galleryid,
                                    'caption'       => htmlspecialchars($dbrows[$filename]['caption'],ENT_COMPAT,'UTF-8',false),
                                    'date'           => Jojo::formatTimestamp($dbrows[$filename]['gi_date'], "medium"),
                                    'datetime'     => $dbrows[$filename]['gi_date'],
                                    'keyimage'     => $dbrows[$filename]['keyimage'],
                                    'sortby'     => $dbrows[$filename]['sortby']
                                   );
                    $files[$key] = array_merge($files[$key], $dbrows[$filename]);
                    unset($dbrows[$filename]);
               }
           }

           foreach($dbrows as $file => $value) {
               /* Delete database row for files that are missing (have been deleted or moved)  */
               Jojo::deleteQuery("DELETE FROM {gallery3_image} WHERE `filename` = ? AND `gallery3id` = ? LIMIT 1", array($file, $galleryid));
           }
        } else {
            $files = Jojo::selectQuery("SELECT `filename` as `key`, `filename` as filename, gi_date as datetime, gi_name as name, gi.*, g.sortby FROM {gallery3_image} as gi LEFT JOIN {gallery3} g ON (g.gallery3id=gi.gallery3id ) WHERE gi.gallery3id = ? order by $order", $galleryid);
            foreach($files as $key => &$filename) {
                    $filename['caption'] = htmlspecialchars($filename['caption'],ENT_COMPAT,'UTF-8',false);
             }
        }

        $sort =  (isset($files[0]['sortby']) && $files[0]['sortby']) ? $files[0]['sortby'] : 'date';
        usort($files, array('Jojo_Plugin_Jojo_gallery3', $sort . 'sort'));
        return $files;
    }

    public static function getAdminHtml($galleryid)
    {
        global $smarty;
        $files = self::getImages($galleryid, 1);
        $smarty->assign('images',    $files);
        $smarty->assign('galleryid', $galleryid);
        return $smarty->fetch('jojo_gallery3_admin_images.tpl');
    }

    public function getCorrectUrl()
    {
        global $page;
        $language  = $page->page['pg_language'];
        $id = Jojo::getFormData('id',     0);
        $url       = Jojo::getFormData('url',    '');
        $data = Jojo::selectRow("SELECT gallerycategoryid FROM {gallerycategory} WHERE pageid=?", $page->page['pageid']);
        $categoryid = !empty($data['gallerycategoryid']) ? $data['gallerycategoryid'] : '';

        $correcturl = self::getUrl($id, $url, null, $language, $categoryid);
        if ($correcturl) {
            return _SITEURL . '/' . $correcturl;
        }
        return parent::getCorrectUrl();
    }

    public static function admin_action_start()
    {
        if (isset($_POST['gallery3submit']) && isset($_POST['gallery3id'])) {
            $galleryid = $_POST['gallery3id'];
            foreach (Jojo::listPlugins('actions/gallery3-upload-image.php') as $pluginfile) {
                require_once($pluginfile);
                exit();
            }
        }
        return true;
    }

    static function getPluginPages($for='', $section=0)
    {
        global $sectiondata;
        $items =  Jojo::selectAssoc("SELECT p.pageid AS id, c.*, p.*  FROM {gallerycategory} c LEFT JOIN {page} p ON (c.pageid=p.pageid) ORDER BY pg_parent, pg_order");
        // use core function to clean out any pages based on permission, status, expiry etc
        $items =  Jojo_Plugin_Core::cleanItems($items, $for);
        foreach ($items as $k=>$i){
            if ($section && $section != $i['root']) {
                unset($items[$k]);
                continue;
            }
        }
        return $items;
    }

    static function getNavItems($pageid, $selected=false)
    {
        $nav = array();
        $section = Jojo::getSectionRoot($pageid);
        $gallerypages = self::getPluginPages('', $section);
        if (!$gallerypages) return $nav;
        $categoryid = $gallerypages[$pageid]['gallerycategoryid'];
        $galleries = isset($gallerypages[$pageid]['addtonav']) && $gallerypages[$pageid]['addtonav'] ? self::getGalleries($categoryid) : '';
        if (!$galleries) return $nav;
        //if the gallery index is currently selected, check to see if a gallery has been called
        if ($selected) {
            $id = Jojo::getFormData('id', 0);
            $url = Jojo::getFormData('url', '');
        }
        foreach ($galleries as $g) {
            $nav[$g['id']]['url'] = $g['url'];
            $nav[$g['id']]['title'] = $g['title'];
            $nav[$g['id']]['label'] = $g['menutitle'];
            $nav[$g['id']]['selected'] = (boolean)($selected && (($id && $id== $g['id']) ||(!empty($url) && $g['baseurl'] == $url)));
        }
        return $nav;
    }

    public static function admin_action_after_save_gallery3_image()
    {
         if ( (isset($_FILES['fm_FILE_filename']) || isset($_POST['fm_filename'])) && isset($_POST['fm_gallery3id'])) {
            $galleryid = $_POST['fm_gallery3id'];
            $filename = (isset($_FILES['fm_FILE_filename']['name']) && !empty($_FILES['fm_FILE_filename']['name'])) ? $_FILES['fm_FILE_filename']['name'] : $_POST['fm_filename'];
            $timestamp = (isset($_POST['fm_gi_date']) && !empty($_POST['fm_gi_date'])) ? $_POST['fm_gi_date'] : '';
            if (empty($timestamp) || $timestamp=='n/a') {
            // no timestamp submitted in the form - use the exif data if available, otherwise set to now
                $exif = (function_exists('exif_read_data')) ? exif_read_data(_DOWNLOADDIR . '/gallery3/' . $galleryid . '/' . $filename) : array();
                $etimestamp = 0;
                if (isset($exif['DateTime'])) {
                    $datetime = explode(' ', $exif['DateTime']);
                    $date = explode(':', $datetime[0]);
                    $time = explode(':', $datetime[1]);
                    $etimestamp = mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);
                } elseif (isset($exif['FileDateTime'])) {
                    $etimestamp = $exif['FileDateTime'];
                }
                $timestamp = $etimestamp ? $etimestamp : time();
                if ( isset($_POST['fm_gallery3_imageid']) && !empty($_POST['fm_gallery3_imageid']) ) {
                    $id = $_POST['fm_gallery3_imageid'];
                    Jojo::updateQuery("UPDATE {gallery3_image} SET `gi_date`=? WHERE `gallery3_imageid`= ?", array($timestamp, $id));
                } else {
                    Jojo::updateQuery("UPDATE {gallery3_image} SET `gi_date`=? WHERE `filename`= ? AND `gallery3id` = ?", array($timestamp, $filename, $galleryid));
                }
            }

        }
        return true;
    }
    
    public static function admin_action_after_save_gallery3($galleryid)
    {
   

        if (isset($_FILES['uploadimage'])) {
            $frajax = new frajax();
            $frajax->title = 'Upload Image - ' . _SITETITLE;
            $frajax->sendHeader();     
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
        
        }       

        
        return true;
    }
    

    // Sync the category to the page table
    static function admin_action_after_save_gallerycategory($id) {
        if (!Jojo::getFormData('fm_pageid', 0)) {
            // no pageid set for this category (either it's a new category or maybe the original page was deleted)
            self::sync_category_to_page($id);
       }
    }

    // Sync the category from the page table
    static function admin_action_after_save_page($id) {
        if (strtolower(Jojo::getFormData('fm_pg_link',    ''))=='jojo_plugin_jojo_gallery3') {
           self::sync_page_to_category($id);
       }
    }

    static function sync_category_to_page($catid) {
        // add a new hidden page for this category and make up a title
            $newpageid = Jojo::insertQuery(
            "INSERT INTO {page} SET pg_title = ?, pg_link = ?, pg_url = ?, pg_parent = ?, pg_status = ?",
            array(
                'Orphaned galleries',  // Title
                'jojo_plugin_jojo_gallery3',  // Link
                'orphaned-galleries',  // URL
                0,  // Parent - don't do anything smart, just put it at the top level for now
                'hidden' // hide new page so it doesn't show up on the live site until it's been given a proper title and url
            )
        );
        // If we successfully added the page, update the category with the new pageid
        if ($newpageid) {
            jojo::updateQuery(
                "UPDATE {gallerycategory} SET pageid = ? WHERE gallerycategoryid = ?",
                array(
                    $newpageid,
                    $catid
                )
            );
       }
    return true;
    }

    static function sync_page_to_category($pageid) {
        // Get the list of categories by page id
        $categories = jojo::selectAssoc("SELECT pageid AS id, pageid FROM {gallerycategory}");
        // no category for this page id
        if (!count($categories) || !isset($categories[$pageid])) {
            jojo::insertQuery("INSERT INTO {gallerycategory} (pageid) VALUES ('$pageid')");
        }
        return true;
    }


    public static function getGalleryHtml($galleryid, $gallery=false, $clean=true)
    {
        if (!$galleryid) return '';
        global $smarty;
        $gallery = !$gallery ? self::getItemsById($galleryid, '', $clean) : $gallery;
        if (!isset($gallery)) return false;
        $layout = $gallery['layout'] ? $gallery['layout'] : 'square';
        $smarty->assign('galleryid', $galleryid);
        $smarty->assign('gallery', $gallery);
        $smarty->assign('images', $gallery['files']);

        if ($layout == 'jgallery') {
            return $smarty->fetch('jojo_gallery3_jgallery.tpl');

        } elseif ($layout == 'magazine2' ) {
            foreach (Jojo::listPlugins('external/magazine2/magazinelayout2.class.php') as $pluginfile) {
                require_once($pluginfile);
                break;
            }
            $mag = new magazineLayout2(Jojo::getOption('gallery_magazinelayoutwidth'),1);
            $mag->template    = '<img src="images/[width]x[height]/[image]" alt="[alt]" title="[title]" width="[width]" height="[height]" />';
            $mag->square      = true;
            $mag->orientation = 'right';
            $mag->prefix      = 'images/default/';
            $max              = min(10, count($gallery['files'])); //the magazine2 script only supports 10 images at present
            for ($i=0;$i<$max;$i++) {
                $mag->add(_DOWNLOADDIR.'/gallery3/' . $galleryid . '/' . $gallery['files'][$i]['filename'], 'gallery3/' . $galleryid . '/' . $gallery['files'][$i]['filename']);
            }
            $smarty->assign('mag', $mag->output());
            return $smarty->fetch('jojo_gallery3_magazine2.tpl');
        } elseif ($layout == 'magazine') {
            foreach (Jojo::listPlugins('external/magazine-layout/magazinelayout.class.php') as $pluginfile) {
                require_once($pluginfile);
                break;
            }
            $mag = new magazinelayout(Jojo::getOption('gallery_magazinelayoutwidth'),1,'<img src="images/[size]/[image]" alt="[alt]" title="[title]" width="[width]" height="[height]" />');
            $max = min(8, count($gallery['files'])); //the magazine script only supports 8 images at present
            for ($i=0;$i<$max;$i++) {
                $mag->addImage(_DOWNLOADDIR.'/gallery3/' . $galleryid . '/' . $gallery['files'][$i]['filename'], 'gallery3/' . $galleryid . '/' . $gallery['files'][$i]['filename']);
            }
            $smarty->assign('mag', $mag->getHtml());
            return $smarty->fetch('jojo_gallery3_magazine.tpl');    
        } elseif ($layout == 'custom') {
            $html = $smarty->fetch('jojo_gallery3_custom.tpl');
            $html = Jojo::applyFilter('jojo_gallery3_custom', $html, array($galleryid, $gallery));
            return $html;
        } else {
            return $smarty->fetch('jojo_gallery3_square.tpl');
        }
    }

    public static function contentFilter($content)
    {
        if (strpos($content, '[[gallery3:') === false) {
            return $content;
        }
        preg_match_all('/\[\[gallery3: ?([^\]]*)\]\]/', $content, $matches);
        foreach($matches[1] as $k => $search) {
            /* convert name into ID */
            if (is_numeric($search)) {
                $id = $search;
            } else {
                $gallery = Jojo::selectRow("SELECT gallery3id FROM {gallery3} WHERE name = ?", array($search));
                $id = $gallery['gallery3id'];
            }
            if (isset($id)) {
                $html      = self::getGalleryHtml($id, '', $clean=false);
                $content   = str_replace($matches[0][$k], $html, $content);
            }
        }
        return $content;
    }

    /* data is an array of all fields from the database - saves an extra query */
    public function getUrl($id, $url=false, $title=false, $language=false, $categoryid=false)
    {
        if (_MULTILANGUAGE) {
            $language = !empty($language) ? $language : Jojo::getOption('multilanguage-default', 'en');
            $multilangstring = Jojo::getMultiLanguageString($language);
        }
        /* URL specified */
        if (!empty($url)) {
            $fullurl = (_MULTILANGUAGE ? $multilangstring : '') . self::_getPrefix($categoryid) . '/' . $url . '/';
            return $fullurl;
         }
        /* ID + title specified */
        if ($id && !empty($title)) {
            $fullurl = (_MULTILANGUAGE ? $multilangstring : '') . self::_getPrefix($categoryid) . '/' . $id . '/' .  Jojo::cleanURL($title) . '/';
          return $fullurl;
        }
        /* use the ID to find either the URL or title */
        if ($id) {
            $item = Jojo::selectRow("SELECT url, name, language, category FROM {gallery3} WHERE gallery3id = ?", array($id));
             if ($item) {
                return self::getUrl($id, $item['url'], $item['name'], $item['language'], $item['category']);
            }
         }
        /* No matching ID or no ID supplied */
        return false;
    }


    private static function scandir($dir = './', $sort = 0)
    {
        $files = array();
        if (!is_dir($dir)) return false;
        $dir_open = @ opendir($dir);
        if (! $dir_open) return false;

        while (($dir_content = readdir($dir_open)) !== false) {
            if ( ($dir_content != '.') && ($dir_content != '..') && ($dir_content != '.svn') ) {
                $files[] = $dir_content;
            }
        }
        if ($sort == 1) rsort($files, SORT_STRING);
        else sort($files, SORT_STRING);
        return $files;
    }

    /**
     * Sitemap filter
     *
     * Receives existing sitemap and adds galleries
     */
    public static function sitemap($sitemap)
    {
        global $page;
        /* See if we have any plugin pages to display and find all of them */
        $indexes =  self::getPluginPages('sitemap');
        if (!count($indexes)) {
            return $sitemap;
        }
        if (Jojo::getOption('gallery_inplacesitemap', 'separate') == 'separate') {
            /* Remove any existing links to the galleries section from the page listing on the sitemap */
            foreach($sitemap as $j => $section) {
                $sitemap[$j]['tree'] = self::_sitemapRemoveSelf($section['tree']);
            }
            $_INPLACE = false;
        } else {
            $_INPLACE = true;
        }

         /* Make sitemap trees for each page found */
        $limit = 15;
        foreach($indexes as $k => $i){
            $categoryid = $i['gallerycategoryid'];
            /* Create tree and add index and feed links at the top */
            $tree = new hktree();
            $indexurl = $i['url'];
            if ($_INPLACE) {
                $parent = 0;
            } else {
               $tree->addNode('index', 0, $i['title'], $indexurl);
               $parent = 'index';
            }
            $galleries = self::getGalleries($categoryid);
            $n = count($galleries);

          /* do not add anything else to sitemap if there is only one gallery in this index and singlepage option is set */
            if (($n == 1) && (Jojo::getOption('gallery3_single_page') == 'yes')) {
                continue;
            }
             foreach ($galleries as $g) {
                $tree->addNode($g['id'], $parent, $g['title'], $g['url']);
            }
            /* Add to the sitemap array */
            if ($_INPLACE) {
                /* Add inplace */
                $url = $i['url'];
                $sitemap['pages']['tree'] = self::_sitemapAddInplace($sitemap['pages']['tree'], $tree->asArray(), $url);
            } else {
                $mldata = Jojo::getMultiLanguageData();
                /* Add to the end */
                $sitemap["galleries$k"] = array(
                    'title' => $i['title'] . (count($mldata['sectiondata'])>1 ? ' (' . ucfirst($mldata['sectiondata'][$i['root']]['name']) . ')' : ''),
                    'tree' => $tree->asArray(),
                    'order' => 3 + $k,
                    'header' => '',
                    'footer' => '',
                    );
            }
        }
        return $sitemap;
    }

    static function _sitemapAddInplace($sitemap, $toadd, $url)
    {
        foreach ($sitemap as $k => $t) {
            if ($t['url'] == $url) {
                $sitemap[$k]['children'] = isset($sitemap[$k]['children']) ? array_merge($toadd, $sitemap[$k]['children']): $toadd;
            } elseif (isset($sitemap[$k]['children'])) {
                $sitemap[$k]['children'] = self::_sitemapAddInplace($t['children'], $toadd, $url);
            }
        }
        return $sitemap;
    }

    static function _sitemapRemoveSelf($tree)
    {
        static $urls;

        if (!is_array($urls)) {
            $urls = array();
            $indexes =  self::getPluginPages('sitemap');
            if (count($indexes)==0) {
               return $tree;
            }
            foreach($indexes as $key => $i){
                $urls[] = $i['url'];
            }
        }
        foreach ($tree as $k =>$t) {
            if (in_array($t['url'], $urls)) {
                unset($tree[$k]);
            } else {
                $tree[$k]['children'] = self::_sitemapRemoveSelf($t['children']);
            }
        }
        return $tree;
    }

    public static function numGalleries()
    {
        static $num;
        if (isset($num)) return $num;
        $data = Jojo::selectQuery("SELECT COUNT(*) AS numgalleries FROM {gallery3} WHERE 1 GROUP BY gallery3id");
        if (!count($data)) return 0;
        $num = $data[0]['numgalleries'];
        return $num;
    }

    /**
     * XML Sitemap filter
     *
     * Receives existing sitemap and adds gallery pages
     */
    static function xmlsitemap($sitemap)
    {
        /* Get articles from database */
        $items = self::getGalleries('all', 'alllanguages');
        $now = time();
        $indexes =  self::getPluginPages('xmlsitemap');
        $ids=array();
        foreach ($indexes as $i) {
            $ids[$i['gallerycategoryid']] = true;
        }
        /* Add items to sitemap */
        foreach($items as $k => $a) {
            // strip out items from expired pages
            if (!isset($ids[$a['category']])) {
                unset($items[$k]);
                continue;
            }
            $url = _SITEURL . '/'. $a['url'];
            $lastmod = $a['date'];
            $priority = 0.6;
            $changefreq = '';
            $sitemap[$url] = array($url, $lastmod, $changefreq, $priority);
        }
        /* Return sitemap */
        return $sitemap;
    }

    /**
     * Site Search
     */
    static function search($results, $keywords, $language, $booleankeyword_str=false)
    {
        $searchfields = array(
            'plugin' => 'jojo_gallery3',
            'table' => 'gallery3',
            'idfield' => 'gallery3id',
            'languagefield' => 'html_lang',
            'primaryfields' => 'name',
            'secondaryfields' => 'name, body',
        );
        $rawresults =  Jojo_Plugin_Jojo_search::searchPlugin($searchfields, $keywords, $language, $booleankeyword_str);
        $data = $rawresults ? self::getItemsById(array_keys($rawresults), '', $clean=true) : '';
        if ($data) {
            $data= self::cleanItems($data);
            foreach ($data as $result) {
                $result['relevance'] = $rawresults[$result['id']]['relevance'];
                $result['type'] = $result['pagetitle'];
                $result['tags'] = isset($rawresults[$result['id']]['tags']) ? $rawresults[$result['id']]['tags'] : '';
                $results[] = $result;
            }
        }
        /* Return results */
        return $results;
    }

    /*
    * Tags
    */
    static function getTagSnippets($ids)
    {
        $snippets = self::getItemsById($ids, '', $clean=true);
        return $snippets;
    }

}