<script src="https://cdn.knightlab.com/libs/timeline3/3.8.18/js/timeline-min.js" id="kl-timeline-js"></script>
<link rel="stylesheet" id="kl-timeline-css" href="https://cdn.knightlab.com/libs/timeline3/3.8.18/css/timeline.css" media="all">
<?php
/**
 * the content (the REST response body) has been passed to get_template_part in the $args parameter
 * the original post as HTML is therefore in $args->content->rendered
 * you can of course include other fields e.g. $args->modified
 */
echo $args->content->rendered;
?>