<div class="tmp-remote-content-container">
<?php
/**
 * the content (the REST response body) has been passed to get_template_part in the $args parameter
 * the original post as HTML is therefore in $args->content->rendered
 * you can of course include other fields e.g. $args->modified
 */
echo $args->content->rendered;
//echo "<p><strong>Last updated: ".$args->modified."</strong></p>";
?>
</div>