<script src="https://cdn.knightlab.com/libs/timeline3/3.8.18/js/timeline-min.js" id="kl-timeline-js"></script>
<link rel="stylesheet" id="kl-timeline-css" href="https://cdn.knightlab.com/libs/timeline3/3.8.18/css/timeline.css" media="all">
<link rel="stylesheet" id="tmp-mapbox-css" href="https://api.tiles.mapbox.com/mapbox-gl-js/v1.3.1/mapbox-gl.css" media="all">
<script src="https://api.tiles.mapbox.com/mapbox-gl-js/v1.3.1/mapbox-gl.js" id="tmp-mapbox-js"></script>
<link rel="stylesheet" href="https://unpkg.com/swiper@6/swiper-bundle.min.css" id="tmp-swiper-css"  media="all"/>
<script src="https://unpkg.com/swiper@6/swiper-bundle.min.js" id="tmp-swiper-js"></script>
<?php
/**
 * This template includes the JS and CSS for:
 *      timeline
 *      map
 *      swiper
 * All using specific versions from their respective CDNs
 * 
 * Note that a particular site may also wish to include CSS (in particular) from their TMP site, including that managed in the Customizer. Add it to the <link>s above
 * 
 * the content (the REST response body) has been passed to get_template_part in the $args parameter
 * the original post as HTML is therefore in $args->content->rendered
 * you can of course include other fields e.g. $args->modified
 */
echo $args->content->rendered;
?>