<link rel="stylesheet" href="https://unpkg.com/swiper@6/swiper-bundle.min.css" id="tmp-swiper-css"  media="all"/>
<script src="https://unpkg.com/swiper@6/swiper-bundle.min.js" id="tmp-swiper-js"></script>
<?php
/**
 * the content (the REST response body) has been passed to get_template_part in the $args parameter
 * the original post as HTML is therefore in $args->content->rendered
 * you can of course include other fields e.g. $args->modified
 */
echo $args->content->rendered;
?>