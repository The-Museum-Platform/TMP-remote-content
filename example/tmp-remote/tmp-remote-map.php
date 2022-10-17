<link rel="stylesheet" id="tmp-mapbox-css" href="https://api.tiles.mapbox.com/mapbox-gl-js/v1.3.1/mapbox-gl.css" media="all">
<script src="https://api.tiles.mapbox.com/mapbox-gl-js/v1.3.1/mapbox-gl.js" id="tmp-mapbox-js"></script>
<!--for spiderified clusters-->
<script src='https://appstg.themuseumplatform.com/hmsc/wp-content/plugins/mp-gutenberg-pagebuilder/js/mapboxgl-spiderifier/index.js?ver=5.8.1' id='mapboxgl-spiderifier-js'></script>
<!--for the geocoding search box, when used. Overkill otherwise-->
<script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.7.0/mapbox-gl-geocoder.min.js"></script>
<link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.7.0/mapbox-gl-geocoder.css" type="text/css">
<?php
echo $args->content->rendered;
//echo '<p>Source page: <a href="'.$args->link.'" target="_blank">'.$args->title->rendered.'</a></p>';
?>