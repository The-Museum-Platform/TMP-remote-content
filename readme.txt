=== The Museum Platform Remote Content Shortcode Plugin ===
Version: 0.1
Author: Jeremy Ottevanger / The Museum Platform https://themuseumplatform.com/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

This plugin provides functionality to pull in WordPress content from one site into another WordPress site (or indeed the same one) using a simple shortcode. You can use the Gutenberg shortcode block to do the job, if you use Gutenberg, or write it by hand. We like it best if it's pulling in TMP content but hey, whatever works for you! 

NOTE: If you need to grab arbitrary content from other sources i.e. not from a WordPress REST endpoint, this isn't the plugin for you. Instead look at something like Remote Content Shortcode (http://www.doublesharp.com)


== Configuring the plugin ==
The plugin has two configuration options, set via the settings page at:
	https://<site>/wp-admin/options-general.php?page=tmp_content

= Default REST route =
This is where shortcodes will get their data from. It will be something like:
	https://<remote site domain>/wp-json/wp/v2/pages/
You might use the /posts/ route instead, or indeed some other content type; this is just the default after all.
If the default REST route is not set you will need to provide one in each shortcode

= Default template =
This is the default way in which you wish to render shortcodes. If left unset then the default behaviour will be to return unaltered the rendered HTML of the content area from your remote content. This may well be fine, but you can also pass the REST API's response body to a template to modify before rendering. Because some blocks (including many TMP blocks) use JavaScript and CSS libraries that may not otherwise be included in your site, you may need to add them in via a custom template. And there may be many other reasons why you'd wish to modify the content before putting it into your page, for example to enclose it in an element or to parse and remove or replace some of the content.
See below for details on how to name templates and refer to them here.
The default template is entirely optional.

== Simplest shortcode usage ==
You can use the Gutenberg "shortcode" block to insert the code, or the classic editor. The shortcode in its simplest form looks something like this:
	[tmp_content postid="290" /]
You can use it as a self-closing tag i.e. with the "/" before the closing bracket; or you can use a closing tag instead. If you do this then any content between tags will be ignored.
The only mandatory attribute is postid, which is the ID of the post or page (or even another content type) that can be found at the default REST route that you have configured for the plugin.
The shortcode will be replaced by the ENTIRE main contents of the page i.e. "the_content()", unless a template is applied in which case it can manipulate the body of the REST response according to your wishes.

== Configuring the shortcode ==

A few optional additional attributes can be used in the shortcode, which allow you to override both configuration options as well as caching behaviour.
Example:
	[tmp_content postid="290" route="https://my.site.com/wp-json/wp/v2/pages/" template="fancy-pants" /]
In the shortcode above, the desired resource will be located at https://my.site.com/wp-json/wp/v2/pages/290, which is the concatenation of the route and postid options. The content will be passed to a template found in your theme directory at tmp-remote/tmp-remote-fancy-pants.php - see below for rules on naming and locating template files.
There is another attribute available, cache_ttl. This sets the length of time in seconds that the content may be kept in a cache (actually, as a WordPress transient). A transient life of 10 minutes (600 seconds) would look like this:
[tmp_content postid="290" route="https://my.site.com/wp-json/wp/v2/pages/" cache_ttl="600" /]
Caching is explained further below. 

== Templates ==

= Why use a template? =
As already noted, the content of some posts depends on JavaScript and CSS libraries that may be enqueued and included in the original site, but are not included in the HTML in the REST response and may not be included in your site. In this case you may include them along with the transcluded content by referencing them in a template.

Another reason might be that you wish to put a background or border around the content, or simply wrap it in a container with a class for styling purposes.

Finally, you might wish to pass the content itself through some other processes: for instance to extract parts of it; to replace URLs; highlight search terms; or many other reasons. Using a template allows you to do all sorts of operations before rendering.

Note that if the objective is solely to ensure that the right scripts and styles are loaded into page containing the shortcode, there are alternative approaches. You might add an HTML block to the page, for example. This could be useful, for example, if you are loading several of the same type of remote resource onto the page and want to avoid loading the same library many times - or simply if you don’t have a template suited to the task.

= Naming and locating templates =
The plugin looks for templates inside your active (sub)theme, in a directory named "tmp-remote". It expects each template to have a name that starts "tmp-remote-”, followed by the "slug” of the template. The templating system adds the file extension. Thus, a "default" template may be named "tmp-remote-default.php", of which the slug is "default". Refer to it by its slug in either plugin settings or shortcode attributes.

The plugin’s example directory contains a model directory for you to copy to your theme and alter. This includes a simple default template, which does nothing more than wrap the content in a <div> and "tmp-remote-plain.php" (referred to as "plain") which returns the rendered content of the remote post completely unmodified; this is useful if you have set a default template that does more, but also on occasion want to return the content more simply. If your default has been left empty then this is already the behaviour of a shortcode where no template is specified.

= Dependencies for TMP blocks =
This section is relevant only for customers of The Museum Platform who need to embed TMP content onto a site hosted elsewhere.
Most TMP blocks require your site to have jQuery enabled. Several of them also use additional JavaScript libraries and CSS files. These libraries can be loaded from a TMP site or better still from the original CDN, using their respective <link> and <script> tags (for the javascript) in a template file - or, as already suggested, using some other mechanism on that page. The current list of dependencies is as follows:

Slider (using Swiper https://swiperjs.com/):
	https://unpkg.com/swiper@7/swiper-bundle.min.css
	https://unpkg.com/swiper@7/swiper-bundle.min.js
Timeline (using Knightlab Timeline https://timeline.knightlab.com/) :
	https://cdn.knightlab.com/libs/timeline3/latest/css/timeline.css
	https://cdn.knightlab.com/libs/timeline3/latest/js/timeline.js
Map (using Mapbox https://www.mapbox.com/):
NOTE: in order to use MapBox maps on your remote site you will need to get an API key that works for the domain of that site.
	https://api.tiles.mapbox.com/mapbox-gl-js/v1.3.1/mapbox-gl.css
	https://api.tiles.mapbox.com/mapbox-gl-js/v1.3.1/mapbox-gl.js
	There may be other libraries required for some more complex maps

== Caching ==
By default the remote content corresponding to a shortcode will be cached as a transient for up to an hour. This means that it’s saved temporarily so that your site doesn’t have to request the content every single time the page is loaded by a user. This will speed up your site, but you may feel that this could be much longer or that the content changes frequently and needs a shorter cache time. 
Regardless, the cache for your shortcode will be cleared when a page is saved, so if you are in a hurry to see a change from the TMP site reflected on your site, save the page and view it again. 

== Credits ==

This plugin was created with inspiration and the odd bit of code from:
      https://codeart.studio/fetch-and-display-remote-posts-via-wordpress-rest-api/
      Remote Content Shortcode (http://www.doublesharp.com)
      https://braadmartin.com/saving-shortcode-data-in-meta-in-wordpress/
along with some independent thought. Feel free to modify it but please keep the GPLv2 licence intact.