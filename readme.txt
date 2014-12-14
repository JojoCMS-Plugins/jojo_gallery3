jojo_gallery3
=============

A multipurpose image gallery script. Still very much under construction so not recommended for production use.

It is intended that the backend be used in a very generic fashion, with a number of frontend options available to give completely different looks.

Features:
=========
-Multiple galleries
-Images can be uploaded via the admin page or FTP for bulk uploads
-Automatic resizing and positioning
-Galleries exist in the "galleries" section of the site
-Individual galleries can be placed inline with content, anywhere on the site
-Search engine friendly (of course)
-Degrades well for non-javascript user agents
-Layout based on an improved version of the Magazine Layout system, www.ragepank.com/magazine-layout/
-Future support for a more standard image layout
-Future support for cropped square or original dimension images
-Galleries appear in sitemap and XML sitemap
-Galleries can be tagged, and will appear within the overall tags system

Custom layout
=============
Set the gallery to "custom" layout. Then copy jojo_gallery3_custom.tpl to themes/yourtheme/templates/ and customize however you like.

Known bugs
==========
-Magazine layout2 does not work reliably in IE7, and is untested on IE6 (so let's just assume it's completely fucked in IE6).
-CSS for the Magazine2 system clashes with the CSS on the default Refresh theme, which wouldn't normally happen with most themes.
-Images can't be uploaded until the new gallery has been saved.
-System should be able to be named anything, however at present it must be named /gallery/ awaiting some small completions.
-Galleries need to be able to be configured on a per-gallery basis. Currently all settings are hard-coded in the script.
-Internationalisation is complete.  However, tags are not using the correct url from the tags plugin.  This will need to be implemented
 correctly when an active page is found to be using this plugin on a multilanguage site.

Requirements
============
Requires a recent build of Jojo from the SVN repository. 1.0a3 will not work with this plugin.