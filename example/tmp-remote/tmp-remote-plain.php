<?php
/**
 * the content (the REST response body) has been passed to get_template_part in the $args parameter
 * this template 
 * you can of course include other fields e.g. $args->modified
 */
echo $args->content->rendered;
?>
