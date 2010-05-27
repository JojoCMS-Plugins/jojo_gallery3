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

    public static function isUrl($uri)
    {
        $prefix = false;
        $getvars = array();
        /* Check the suffix matches and extra the prefix */
        if (preg_match('#^(.+)/([0-9]+)/([a-z0-9-_]+)$#', $uri, $matches)) {
            /* "$prefix/[id:integer]/[string]" eg "section/123/name-of-item/" */
            $prefix = $matches[1];
            $getvars = array(
                        'id' => $matches[2]
                        );
        } elseif (preg_match('#^(.+)/([0-9]+)$#', $uri, $matches)) {
            /* "$prefix/[id:integer]" eg "section/123/" */
            $prefix = $matches[1];
            $getvars = array(
                        'id' => $matches[2]
                        );
        } elseif (preg_match('#^(.+)/([a-z0-9-_]+)$#', $uri, $matches)) {
            /* "$prefix/[url:(string)]" eg "section/name-of-item/" */
            $prefix = $matches[1];
            $getvars = array(
                        'url' => $matches[2]
                        );
        } else {
            /* Didn't match */
            return false;
        }
        /* Check the prefix matches */
        if (self::checkPrefix($prefix)) {
            /* The prefix is good, pass through uri parts */
            foreach($getvars as $k => $v) {
                $_GET[$k] = $v;
            }
            return true;
        }
        return false;
    }

    /**
     * Check if url prefix belongs to this plugin
     */
    public static function checkPrefix($prefix)
    {
        /* Cache some stuff */
        static $_prefixes, $languages, $categories;
        if (!isset($languages)) {
            /* Initialise cache */
            if (Jojo::tableExists('lang_country')) {
                $languages = Jojo::selectAssoc("SELECT lc_code, lc_code as lc_code2 FROM {lang_country}");
            } else {
                $languages = Jojo::selectAssoc("SELECT languageid, languageid as languageid2 FROM {language} WHERE active = 'yes'");
            }
            $categories = array(false);
            if (Jojo::getOption('gallery_enable_categories', 'no') == 'yes') {
                $categories = array_merge($categories, Jojo::selectAssoc("SELECT gallerycategoryid, gallerycategoryid as gallerycategoryid2 FROM {gallerycategory}"));
            }
            $_prefixes = array();
        }

        /* Check if it's in the cache */
        if (isset($_prefixes[$prefix])) {
            return $_prefixes[$prefix];
        }

        /* Check everything */
        foreach ($languages as $language) {
            $language = $language ? $language : Jojo::getOption('multilanguage-default', 'en');
            foreach($categories as $category) {
                $testPrefix = Jojo_Plugin_Jojo_gallery3::_getPrefix($language, $category);
                $_prefixes[$testPrefix] = true;
                if ($testPrefix == $prefix) {
                    /* The prefix is good */
                    return true;
                }
            }
        }

        /* Didn't match */
        $_prefixes[$testPrefix] = false;
        return false;
    }

     public static function saveTags($record, $tags = array())
    {
        /* Ensure the tags class is available */
        if (!class_exists('Jojo_Plugin_Jojo_Tags')) {

            /* Delete existing tags for this item */
            Jojo_Plugin_Jojo_Tags::deleteTags('jojo_gallery3', $record['gallery3id']);

            /* Save all the new tags */
            foreach($tags as $tag) {
                Jojo_Plugin_Jojo_Tags::saveTag($tag, 'jojo_gallery3', $record['gallery3id']);
            }
        }
    }

     public static function getTagSnippets($ids)
    {
        /* Convert array of ids to a string */
        $ids = "'" . implode($ids, "', '") . "'";

        /* Get the galleries */
        $galleries = Jojo::selectQuery("SELECT * FROM {gallery3} WHERE gallery3id IN ($ids) ORDER BY displayorder, name");

        /* Create the snippets */
        $snippets = array();
        foreach ($galleries as $i => $g){
            $snippets[] = array(
                    'id'    => $g['gallery3id'],
                    'title' => htmlspecialchars($g['name'], ENT_COMPAT, 'UTF-8', false),
                    'text'  => strip_tags($g['body']),
                    'url'   => Jojo::urlPrefix(false) . self::getUrl($g['gallery3id'], $g, $g['language'], $g['category'])
                );
        }

        /* Return the snippets */
        return $snippets;
    }

     public static function customhead()
    {
        return '<script type="text/javascript" src="'._PROTOCOL.$_SERVER['HTTP_HOST'].'/external/jquery-lightbox/js/jquery.lightbox-0.4.pack.js"></script>'."\n".'<link rel="stylesheet" type="text/css" href="'._PROTOCOL.$_SERVER['HTTP_HOST'].'/external/jquery-lightbox/css/jquery.lightbox-0.4.css" media="screen" />';
    }

    public function _getContent()
    {
        global $smarty, $_USERGROUPS, $_USERID;
        $content = array();

        $language = !empty($this->page['pg_language']) ? $this->page['pg_language'] : Jojo::getOption('multilanguage-default', 'en');
        $mldata = Jojo::getMultiLanguageData();
        $lclanguage = $mldata['longcodes'][$language];

        $_CATEGORIES = (Jojo::getOption('gallery_enable_categories', 'no') == 'yes') ? true : false ;
        $categorydata =  ($_CATEGORIES) ? Jojo::selectRow("SELECT `gallerycategoryid` FROM {gallerycategory} WHERE `gc_url` = ?", array($this->page['pg_url'])) : '';
        $categoryid = ($_CATEGORIES && isset($categorydata['gallerycategoryid']) ) ? $categorydata['gallerycategoryid'] : '';

        $id  = Jojo::getFormData('id',  0);
        $url = Jojo::getFormData('url', false);
        $galleries = self::getGalleries($categoryid, $language, $index=true);

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
                      $nextgallery = array('id'=>$galleries[$nextkey]['gallery3id'], 'title'=>$galleries[$nextkey]['name'], 'url'=>$galleries[$nextkey]['url']);
                      $smarty->assign('nextgallery', $nextgallery);
                }
                $prevkey = $currentkey - 1;
                if (isset($galleries[$prevkey])) {
                      $prevgallery = array('id'=>$galleries[$prevkey]['gallery3id'], 'title'=>$galleries[$prevkey]['name'], 'url'=>$galleries[$prevkey]['url']);
                      $smarty->assign('prevgallery', $prevgallery);
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
                $breadcrumb['name']               = $gallery['name'];
                $breadcrumb['rollover']           = $gallery['name'];
                $breadcrumb['url']                = $gallery['url'];
                $breadcrumbs[count($breadcrumbs)] = $breadcrumb;
            }

            /* Get the HTML */
            $galleryhtml = self::getGalleryHtml($id);

            $smarty->assign('gallery', $gallery);
            $smarty->assign('galleryhtml', $galleryhtml);
            // The language of the page is generated in the template.
            // Need to generate the language prefix here so we have more control over
            // whether the language prefix is displayed or not.
            // Part of SEO friendly internationalisation.
            if (_MULTILANGUAGE) {
                $pg_lang_prefix = Jojo::getMultiLanguageString( $language, false );
                if ($pg_lang_prefix == '' ) {
                    $pg_lang_prefix = 'null';
                }
            } else {
                $pg_lang_prefix = 'null'; // make sure that pg_lang_prefix has a value to check against so that we don't break existing functionality for a site that has not been upgraded yet.
            }
            $smarty->assign('pg_lang_prefix', $pg_lang_prefix);
            $content['content']         = $smarty->fetch('jojo_gallery3_detail.tpl');
            $content['breadcrumbs']     = $breadcrumbs;
            $content['title']           = $gallery['name'];
            $content['seotitle']        = Jojo::either($gallery['seotitle'], $gallery['name']);
            $content['metadescription'] = $gallery['metadescription'];

        } else {

            $smarty->assign('galleries', $galleries);
            $content['content'] = $smarty->fetch('jojo_gallery3_index.tpl');
        }

        return $content;
    }


    public static function _getPrefix($language=false, $categoryid=false) {
        $cacheKey = 'gallery3';
        $cacheKey .= ($language) ? $language : 'false';
        $cacheKey .= ($categoryid) ? $categoryid : 'false';

        /* Have we got a cached result? */
        static $_cache;
        if (isset($_cache[$cacheKey])) {
            return $_cache[$cacheKey];
        }

        $language = !empty($language) ? $language : Jojo::getOption('multilanguage-default', 'en');
        $_CATEGORIES = (Jojo::getOption('gallery_enable_categories', 'no') == 'yes') ? true : false ;
        $categorydata =  ($_CATEGORIES && !empty($categoryid)) ? Jojo::selectRow("SELECT `gc_url` FROM {gallerycategory} WHERE `gallerycategoryid` = '$categoryid';") : '';
        $category = ($_CATEGORIES && !empty($categoryid)) ? $categorydata['gc_url'] : '';

        $query = "SELECT pageid, pg_title, pg_url FROM {page} WHERE pg_link = ?";
        $query .= (_MULTILANGUAGE) ? " AND pg_language = '$language'" : '';
        $query .= (!empty($category)) ? " AND pg_url LIKE '%$category'": '';

        $res = Jojo::selectRow($query, array('Jojo_Plugin_Jojo_gallery3'));
        if ($res) {
            $_cache[$cacheKey] = !empty($res['pg_url']) ? $res['pg_url'] : $res['pageid'] . '/' . strtolower($res['pg_title']);
        } else {
            $_cache[$cacheKey] = '';
        }

        return $_cache[$cacheKey];
    }

    public static function getImages($galleryid, $refresh=false, $sort="imageid")
    {
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
            $dbrows = Jojo::selectAssoc("SELECT `filename`, gi_date as datetime, gi_name as name, gi.* FROM {gallery3_image} as gi WHERE `gallery3id` = ? order by $order", $galleryid);

            foreach($files as $key => $filename) {
                /* Image in the directory - but no record in the database */
                if (!isset($dbrows[$filename])) {
                    /* Insert into the database if there's no record for this image (anywhere) */
                    Jojo::insertQuery("INSERT INTO {gallery3_image} SET `filename` = ?, `gallery3id` = ? ", array($filename, $galleryid));
                    $files[$key] = array(
                                    'filename'   => $filename,
                                    'gallery3id' => $galleryid,
                                    'datetime'     => ''
                                   );
               } else {
                    /* Image in the directory and in the database - return the record */
                    $fileloc = _DOWNLOADDIR . '/gallery3/' . $galleryid . '/' . $filename;
                    $files[$key] = array(
                                    'filename'      => $filename,
                                    'gallery3id'    => $galleryid,
                                    'caption'       => htmlspecialchars($dbrows[$filename]['caption'],ENT_COMPAT,'UTF-8',false),
                                    'date'           => Jojo::mysql2date($dbrows[$filename]['datetime'], "medium"),
                                    'datetime'     => $dbrows[$filename]['datetime']
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
            $files = Jojo::selectQuery("SELECT `filename` as `key`, `filename` as filename, gi_date as datetime, gi_name as name, gi.* FROM {gallery3_image} as gi WHERE `gallery3id` = ? order by $order", $galleryid);
            foreach($files as $key => &$filename) {
                    $filename['caption'] = htmlspecialchars($filename['caption'],ENT_COMPAT,'UTF-8',false);
             }

        }

        $gallery = Jojo::selectRow('SELECT * FROM {gallery3} WHERE gallery3id = ?', $galleryid);
        if (isset($gallery['sortby']) && $gallery['sortby']) {
            usort($files, array('Jojo_Plugin_Jojo_gallery3', $gallery['sortby'] . 'sort'));
        } else {
            usort($files, array('Jojo_Plugin_Jojo_gallery3', 'datesort'));
        }

        return $files;
    }

    private static function namesort($a, $b)
    {
         if ($a['name']) {
            return strcmp($a['name'],$b['name']);
        } else {
            return strcmp($a['filename'],$b['filename']);
        }
    }

    private static function datesort($a, $b)
    {
        return strcmp($a['datetime'],$b['datetime']);
    }

    private static function imageidsort($a, $b)
    {
        if(isset($a['imageid']) and isset($b['imageid'])) return strcmp($a['imageid'],$b['imageid']);
        if(isset($a['imageid'])) return 1;
        return -1;
    }

    public static function getAdminHtml($galleryid)
    {
        global $smarty;
        $files = Jojo_Plugin_Jojo_gallery3::getImages($galleryid, 1);
        $smarty->assign('images',    $files);
        $smarty->assign('galleryid', $galleryid);
        return $smarty->fetch('jojo_gallery3_admin_images.tpl');
    }

    public function getCorrectUrl()
    {
        //Assume the URL is correct
        return _PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    public static function admin_action_start()
    {
        if (isset($_POST['gallery3submit'])) {
            $galleryid = $_POST['gallery3id'];
            foreach (Jojo::listPlugins('actions/gallery3-upload-image.php') as $pluginfile) {
                require_once($pluginfile);
                exit();
            }
        }
        return true;
    }

    public static function getGalleryHtml($galleryid, $layout=false)
    {
        global $smarty;
        $gallery = Jojo::selectRow("SELECT * FROM {gallery3} WHERE gallery3id = ?", array($galleryid));
        if (!isset($gallery)) return false;
        $files = Jojo_Plugin_Jojo_gallery3::getImages($galleryid,0,$gallery['sortby']);
        $gallery['numimages'] = count($files);
        $gallery['layout'] = isset($gallery['layout']) ? $gallery['layout'] : "square";
        $layout = $layout ? $layout : $gallery['layout'];
        $smarty->assign('gallery', $gallery);
        $smarty->assign('images', $files);

        if ($layout == 'jgallery') {
            $smarty->assign('galleryid', $galleryid);
            return $smarty->fetch('jojo_gallery3_jgallery.tpl');

        } elseif ($layout == 'magazine2' || $layout == 'magazine') {
            foreach (Jojo::listPlugins('external/magazine2/magazinelayout2.class.php') as $pluginfile) {
                require_once($pluginfile);
                break;
            }
            $mag = new magazineLayout2('400',1);
            $mag->template    = '<img src="images/[width]x[height]/[image]" alt="[alt]" title="[title]" width="[width]" height="[height]" />';
            $mag->square      = true;
            $mag->orientation = right;
            $mag->prefix      = 'images/default/';
            $max              = min(10, count($files)); //the magazine2 script only supports 10 images at present
            for ($i=0;$i<$max;$i++) {
                $mag->add(_DOWNLOADDIR.'/gallery3/' . $galleryid . '/' . $files[$i]['filename'], 'gallery3/' . $galleryid . '/' . $files[$i]['filename']);
            }
            //$mag->setSelected('l1.jpg');

            $smarty->assign('mag', $mag->output());
            return $smarty->fetch('jojo_gallery3_magazine2.tpl');

        } else {
            $smarty->assign('images', $files);
            $smarty->assign('galleryid', $galleryid);
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
                $html      = self::getGalleryHtml($id);
                $content   = str_replace($matches[0][$k], $html, $content);
            }
        }
        return $content;
    }

    /* data is an array of all fields from the database - saves an extra query */
    public function getUrl($galleryid, $gallery=false, $language=false, $categoryid=false)
    {

        if (!is_array($gallery)) {
            $gallery = Jojo::selectRow("SELECT * FROM {gallery3} WHERE gallery3id = ?", array($galleryid));
            if (empty($gallery)) return false;
        }

       if (_MULTILANGUAGE) {
            $language = !empty($gallery['language']) ? $gallery['language'] : Jojo::getOption('multilanguage-default', 'en');
            $mldata = Jojo::getMultiLanguageData();
            $lclanguage = $mldata['longcodes'][$language];
        } else { $language = ''; }

        $categoryid = !empty($gallery['category']) ? $gallery['category'] : '';

        /* URL specified */
        if (!empty($gallery['url'])) {
            $fullurl = (_MULTILANGUAGE) ? Jojo::getMultiLanguageString ( $language, false ) : '';
            $fullurl .= self::_getPrefix( $language, $categoryid)  . '/' . $gallery['url'] . '/';
            return $fullurl;
         }

        /* Gallery ID + title specified */
        if ($gallery['gallery3id'] && !empty($gallery['name'])) {
            $fullurl = (_MULTILANGUAGE) ? Jojo::getMultiLanguageString ( $language, false ) : '';
            $fullurl .= (_MULTILANGUAGE && $language != 'en') ? Jojo_Plugin_Jojo_gallery3::_getPrefix( $language, $categoryid ) . '/' . $gallery['gallery3id'] . '/' . urlencode(strtolower($gallery['name'])) : Jojo::rewrite(Jojo_Plugin_Jojo_gallery3::_getPrefix(( _MULTILANGUAGE ? $language : ''), (!empty($categoryid) ? $categoryid : '')), $gallery['gallery3id'], $gallery['name'], '');
            return $fullurl;
        }

       /* No gallery matching the ID supplied or no ID supplied */
        return false;

    }

    public static function getGalleries($categoryid=false, $language=false, $index=false) {

         /* Get category url and id if needed */
        $_CATEGORIES = (Jojo::getOption('gallery_enable_categories', 'no') == 'yes') ? true : false ;
        $gallerysorting = (Jojo::getOption('gallery_orderby', 'name') == 'date') ? 'g_date DESC, name' : 'name';
        $query = "SELECT * FROM {gallery3} WHERE 1";
        $query .= ($_CATEGORIES && $categoryid != 'all') ? " AND (`category` = '$categoryid')" : '';
        $query .= _MULTILANGUAGE ? " AND (`language` = '$language')" : '';
        $query .= $index ? " AND (`show` = 'index')" : '';
        $query .= " ORDER BY `displayorder`, $gallerysorting";

        $galleries = Jojo::selectQuery($query);

        foreach ($galleries as &$g) {
            $id                 = $g['gallery3id'];
            $categoryid         = $_CATEGORIES ? $g['category'] : '';
            $g['id']            = $id;
            $g['name']          = htmlspecialchars($g['name'], ENT_COMPAT, 'UTF-8', false);
            $g['baseurl']       = $g['url'];
            $g['url']           = self::getUrl($id, $g, $language, $categoryid);
            if (empty($g['sortby'])) {
                $files              = self::getImages($id,0);
            } else {
                $files              = self::getImages($id,0,$g['sortby']);
            }
            $g['image']         = !empty($files[0]['filename']) ? $files[0]['filename'] :'';
            $g['bodyplain']     = strip_tags($g['body']);
            $g['numimages']     = !empty($files[0]) ? count($files) : 0;
        }
        return $galleries;
    }

    /**
     * Sitemap filter
     *
     * Receives existing sitemap and adds galleries
     */
    public static function sitemap($sitemap)
    {

        /* See if we have any sections to display and find all of them */
        $_CATEGORIES = (Jojo::getOption('gallery_enable_categories', 'no') == 'yes') ? true : false ;
        $query = "SELECT *";
        $query .= ($_CATEGORIES) ? ", gallerycategoryid" : '';
        $query .= " FROM {page}";
        $query .= ($_CATEGORIES) ? ' LEFT JOIN {gallerycategory} gc ON (gc_url = pg_url)' : '';
        $query .= " WHERE pg_link = 'Jojo_Plugin_Jojo_gallery3' AND pg_sitemapnav = 'yes'";
        $indexes = Jojo::selectQuery($query);
        if (!count($indexes)) {
            return $sitemap;
        }

        if (_MULTILANGUAGE) $mldata = Jojo::getMultiLanguageData();

        if (Jojo::getOption('gallery_inplacesitemap', 'separate') == 'separate') {
            /* Remove any existing links to the galleries section from the page listing on the sitemap */
            foreach($sitemap as $j => $section) {
                $sitemap[$j]['tree'] = self::_sitemapRemoveSelf($section['tree']);
            }
            $_INPLACE = false;
        } else {
            $_INPLACE = true;
        }

         /* Make sitemap trees for each gallery instance found */

        foreach($indexes as $k => $i){
            /* Get language and language longcode if needed */
            $language = '';
            if (_MULTILANGUAGE) {
                $language = !empty($i['pg_language']) ? $i['pg_language'] : Jojo::getOption('multilanguage-default', 'en');
                $lclanguage = $mldata['longcodes'][$language];
            }
            $categoryid = ($_CATEGORIES && !empty($i['gallerycategoryid'])) ? $i['gallerycategoryid'] : '';

            /* Create tree and add index and feed links at the top */
            $tree = new hktree();
            $indexurl = (_MULTILANGUAGE ? Jojo::getMultiLanguageString ( $language, false ) : '') . self::_getPrefix((_MULTILANGUAGE ? $language : ''), $categoryid) . '/';
            if ($_INPLACE) {
                $parent = 0;
            } else {
               $tree->addNode('index', 0, $i['pg_title'] . ' Index', $indexurl);
               $parent = 'index';
            }

             /* Get the gallery content from the database */
            $galleries = self::getGalleries($categoryid, $language);
            $n = count($galleries);

          /* do not add anything else to sitemap if there is only one gallery in this index and singlepage option is set */
            if (($n == 1) && (Jojo::getOption('gallery3_single_page') == 'yes')) {
                continue;
            }

             foreach ($galleries as $g) {
                $tree->addNode($g['id'], $parent, $g['name'], self::getUrl($g['id'], $g['url'], $language, $categoryid));
            }

            /* Add to the sitemap array */
            if ($_INPLACE) {
                /* Add inplace */
                $url = ((_MULTILANGUAGE) ? Jojo::getMultiLanguageString( $language, false ) : '') . self::_getPrefix(( _MULTILANGUAGE ? $language : ''), $categoryid) . '/';
                $sitemap['pages']['tree'] = self::_sitemapAddInplace($sitemap['pages']['tree'], $tree->asArray(), $url);
            } else {
                /* Add to the end */
                $sitemap["galleries$k"] = array(
                    'title' => $i['pg_title'] . ( _MULTILANGUAGE ? ' (' . ucfirst($lclanguage) . ')' : ''),
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
                $sitemap[$k]['children'] = $toadd;
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
            $mldata = Jojo::getMultiLanguageData();
            $urls = array();
            $_CATEGORIES = (Jojo::getOption('gallery_enable_categories', 'no') == 'yes') ? true : false ;
            $query = "SELECT *";
            $query .= ($_CATEGORIES) ? ", gallerycategoryid" : '';
            $query .= " FROM {page}";
            $query .= ($_CATEGORIES) ? ' LEFT JOIN {gallerycategory} gc ON (gc_url = pg_url)' : '';
            $query .= " WHERE pg_link = 'Jojo_Plugin_Jojo_gallery3' AND pg_sitemapnav = 'yes'";
            $indexes = Jojo::selectQuery($query);
            if (count($indexes)==0) {
               return $tree;
            }

            foreach($indexes as $key => $i){
                $language = !empty($i['pg_language']) ? $i['pg_language'] : Jojo::getOption('multilanguage-default', 'en');
                $lclanguage = $mldata['longcodes'][$language];
                $urls[] = ((_MULTILANGUAGE) ? Jojo::getMultiLanguageString ( $language, false ) : '') . self::_getPrefix( ( _MULTILANGUAGE ? $language : ''), (!empty($i['gallerycategoryid']) ? $i['gallerycategoryid'] : '' )) . '/';
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
    public static function xmlsitemap($sitemap)
    {
        /* do not add anything to sitemap if there is only one gallery and singlepage option is set */
        if ((self::numGalleries() == 1) && (Jojo::getOption('gallery3_single_page') == 'yes')) {
            return $sitemap;
        }

        /* Get galleries from database */
        $galleries = Jojo::selectQuery("SELECT * FROM {gallery3} WHERE 1");

        /* Add galleries to sitemap */
        foreach($galleries as $g) {
            $url                = _SITEURL . '/'. self::getUrl($g['gallery3id'], $g, $g['language'], $g['category']);
            $lastmod       = '';
            $priority         = 0.6;
            $changefreq    = '';
            $sitemap[$url] = array($url, $lastmod, $changefreq, $priority);
        }

        /* Return sitemap */
        return $sitemap;
    }


    /**
     * Site Search
     *
     */
    public static function search($results, $keywords, $language, $booleankeyword_str=false)
    {
        $_CATEGORIES = (Jojo::getOption('gallery_enable_categories', 'no') == 'yes') ? true : false ;
        $_TAGS = class_exists('Jojo_Plugin_Jojo_Tags') ? true : false ;
        $tagid = ($_TAGS) ? Jojo_Plugin_Jojo_Tags::_getTagId(implode(' ', $keywords)): '';

        global $_USERGROUPS;
        $pagePermissions = new JOJO_Permissions();
        $boolean = ($booleankeyword_str) ? true : false;
        $keywords_str = ($boolean) ? $booleankeyword_str :  implode(' ', $keywords);
        if ($boolean && stripos($booleankeyword_str, '+') === 0  ) {
            $like = '1';
            foreach ($keywords as $keyword) {
                $like .= sprintf(" AND (gallery3.name LIKE '%%%s%%' OR gallery3.body LIKE '%%%s%%')", JOJO::clean($keyword), JOJO::clean($keyword));
            }
        } elseif ($boolean && stripos($booleankeyword_str, '"') === 0) {
            $like = "gallery3.name LIKE '%%%". implode(' ', $keywords). "%%' OR gallery3.body LIKE '%%%". implode(' ', $keywords) . "%%'";
        } else {
            $like = '0';
            foreach ($keywords as $keyword) {
                $like .= sprintf(" OR gallery3.name LIKE '%%%s%%' OR gallery3.body LIKE '%%%s%%'", JOJO::clean($keyword), JOJO::clean($keyword));
            }
        }

        $query = "SELECT gallery3id, url, gallery3.name, body, language, category, ((MATCH(gallery3.name) AGAINST (?" . ($boolean ? ' IN BOOLEAN MODE' : '') . ") * 0.2) + MATCH(gallery3.name, gallery3.body) AGAINST (?" . ($boolean ? ' IN BOOLEAN MODE' : '') . ")) AS relevance";
        $query .= ", p.pg_url, p.pg_title ";
        $query .= "FROM {gallery3} AS gallery3 ";
        $query .= $_CATEGORIES ? " LEFT JOIN {gallerycategory} gc ON (gallery3.category=gc.gallerycategoryid) LEFT JOIN {page} p ON (gc.gc_url=p.pg_url AND p.pg_language=gallery3.language)" : "LEFT JOIN {page} p ON (p.pg_link='Jojo_Plugin_Jojo_gallery3' AND p.pg_language=gallery3.language)";
        $query .= "LEFT JOIN {language} AS language ON (gallery3.language = languageid) ";
        $query .= $tagid ? " LEFT JOIN {tag_item} AS tag ON (tag.itemid = gallery3.gallery3id AND tag.plugin='jojo_gallery3' AND tag.tagid = $tagid)" : '';
        $query .= "WHERE ($like";
        $query .= $tagid ? " OR (tag.itemid = gallery3.gallery3id AND tag.plugin='jojo_gallery3' AND tag.tagid = $tagid))" : ')';
        $query .= ($language) ? "AND gallery3.language = '$language' " : '';
        $query .= "AND language.active = 'yes' ";
        $query .= " ORDER BY relevance DESC LIMIT 100";

        $data = Jojo::selectQuery($query, array($keywords_str, $keywords_str));


        if (_MULTILANGUAGE) {
            global $page;
            $mldata = Jojo::getMultiLanguageData();
            $homes = $mldata['homes'];
        } else {
            $homes = array(1);
        }

        foreach ($data as $d) {
            $pagePermissions->getPermissions('gallery', $d['gallery3id']);
            if (!$pagePermissions->hasPerm($_USERGROUPS, 'view')) {
                continue;
            }
            $result = array();
            $result['relevance'] = $d['relevance'];
            $result['title'] = $d['name'];
            $result['body'] = $d['body'];
            $result['url'] = Jojo_Plugin_Jojo_gallery3::getUrl($d['gallery3id'], $d, $d['language'], $d['category']);
            $result['absoluteurl'] = _SITEURL. '/' . $result['url'];
            $result['id'] = $d['gallery3id'];
            $result['plugin'] = 'jojo_gallery3';
            $result['type'] = isset($d['pg_title']) ? $d['pg_title'] : 'Galleries';
            $results[] = $result;
        }

        if ($boolean && stripos($booleankeyword_str, '+') === 0  ) {
            $like = '1';
            foreach ($keywords as $keyword) {
                $like .= sprintf(" AND (g3i.caption LIKE '%%%s%%' OR g3i.gi_name LIKE '%%%s%%')", JOJO::clean($keyword), JOJO::clean($keyword));
            }
        } elseif ($boolean && stripos($booleankeyword_str, '"') === 0) {
            $like = "g3i.caption LIKE '%%%". implode(' ', $keywords). "%%' OR g3i.gi_name LIKE '%%%". implode(' ', $keywords) . "%%'";
        } else {
            $like = '0';
            foreach ($keywords as $keyword) {
                $like .= sprintf(" OR g3i.caption LIKE '%%%s%%' OR g3i.gi_name LIKE '%%%s%%'", JOJO::clean($keyword), JOJO::clean($keyword));
            }
        }
        $query = "SELECT g3i.gallery3id, url, g3.name, body, language, category, caption, gi_name, filename, ((MATCH(g3i.caption) AGAINST (?" . ($boolean ? ' IN BOOLEAN MODE' : '') . ") * 0.2) + (MATCH(g3i.caption, g3i.gi_name) AGAINST (?" . ($boolean ? ' IN BOOLEAN MODE' : '') . ")) * 0.8) AS relevance ";
        $query .= ", p.pg_url, p.pg_title ";
        $query .= "FROM {gallery3_image} as g3i ";
        $query .= "LEFT JOIN {gallery3} AS g3 ON (g3i.gallery3id = g3.gallery3id) ";
        $query .= $_CATEGORIES ? " LEFT JOIN {gallerycategory} gc ON (g3.category=gc.gallerycategoryid) LEFT JOIN {page} p ON (gc.gc_url=p.pg_url AND p.pg_language=g3.language)" : "LEFT JOIN {page} p ON (p.pg_link='Jojo_Plugin_Jojo_gallery3' AND p.pg_language=g3.language)";
        $query .= "LEFT JOIN {language} AS language ON (g3.language = languageid) ";
        $query .= "WHERE $like";
        $query .= ($language) ? "AND g3.language = '$language' " : '';
        $query .= "AND language.active = 'yes' ";
        $query .= " ORDER BY relevance DESC LIMIT 100";
        $data = Jojo::selectQuery($query, array($keywords_str, $keywords_str));

        if (_MULTILANGUAGE) {
            global $page;
            $mldata = Jojo::getMultiLanguageData();
            $homes = $mldata['homes'];
        } else {
            $homes = array(1);
        }

        foreach ($data as $d) {
            $pagePermissions->getPermissions('gallery', $d['gallery3id']);
            if (!$pagePermissions->hasPerm($_USERGROUPS, 'view')) {
                continue;
            }
            $result = array();
            $result['relevance'] = $d['relevance'];
            $result['title'] = ($d['gi_name'] ? $d['gi_name'] : $d['filename'] );
            $result['body'] = $d['caption'];
            $result['image'] = 'gallery3/' . $d['gallery3id'] . '/' . $d['filename'];
            $result['url'] = Jojo_Plugin_Jojo_gallery3::getUrl($d['gallery3id'], $d, $d['language'], $d['category']);
            $result['absoluteurl'] = _SITEURL. '/' . $result['url'];
            $result['id'] = $d['gallery3id'];
            $result['plugin'] = 'jojo_gallery3';
            $result['type'] = isset($d['pg_title']) ? $d['pg_title'] : 'Galleries';
            $results[] = $result;
        }



        /* Return results */
        return $results;
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

}