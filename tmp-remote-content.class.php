<?php
// namespace TmpRest;
    class TmpRestContent
    {
        private static $instance;

        private $plugin_directory;
        private $plugin_url;
        private $search_results_path;
        private $single_record_path;
        private $remote_content_replace_paths;
        private $rest_route;
        private $iiif_path;
        private $media_path;

        function __construct()
        {
            $this->plugin_directory = WP_PLUGIN_DIR . '/tmp-remote-content';
            $this->plugin_url = plugins_url() . '/tmp-remote-content';
            $this->rest_route = get_option('tmp_remote_content_rest_route');
            $this->remote_content_template = "".get_option('tmp_remote_content_template');
            $this->remote_content_replace_paths = $this->parse_replacements(get_option('tmp_remote_content_replace_paths'));
            add_action('admin_menu', array($this, 'add_menu_item'));
            add_action('admin_init', array($this, 'register_settings'));
            add_action( 'save_post', array($this, 'tmp_remote_clear_post_transients'), 10, 3 );
        }
        
        function add_shortcode() {
            add_shortcode( 'tmp_content', array( &$this, 'remote_content_shortcode' ) );
        }


        public function remote_content_shortcode( $atts, $content=null ) {
            //the cache attribute isn't actually being used at the moment
            $attributes = shortcode_atts( array(
                    'postid' => 0,
                    'route' => false,
                    'template' => false,
                    'cache' => true,
                    'cache_ttl' => 3600,
                    ), $atts );
            $content = $this->get_remote_api_data($attributes);
            return $content;
        }

        // clear out transients for any tmp_content shortcodes in the current page. Triggered by saving, so it's sort of under author control
        function tmp_remote_clear_post_transients($post_id, $post, $update){
            //only do this when it's a proper save: look through the post for tmp_content shortcodes and clear their transients 
            if ( 'auto-draft' === $post->post_status) {
                return;
            }

            $count = preg_match_all( '/' . get_shortcode_regex() . '/', $post->post_content, $matches, PREG_SET_ORDER );
            if ($count) {
                foreach ( $matches as $match) { 
                    // Only if this is a tmp_content shortcode
                    if ( $match[2] == "tmp_content" ) {
                        // Parse shortcode atts.
                        $shortcode_data = shortcode_parse_atts( $match[3]);
                        trim($shortcode_data['route'])<>""?$route=$shortcode_data['route']:$route = $this->rest_route;
                        $key = md5($route.$shortcode_data['postid']);
                        delete_transient( $key );
                    }
                }                    
            } 
        }

        function get_remote_api_data($attributes) {
            extract( $attributes);// turn the attribute array into variables
            $cache_ttl = is_numeric($cache_ttl)?$cache_ttl:HOUR_IN_SECONDS; //if no integer present for the cache TTL, set it to an hour
            $url = $this->rest_route;
            $url = trim($route)<>""?$route:$url;   //if a specific REST URL has been provided use that instead
            $url.=$postid;    //append the postid to all URLs

            $transient_handle = md5($url);
            $is_cache = strtolower( $cache ) != 'false';

            $template_path = (null!==$template && trim($template)!=="")?"tmp-remote/tmp-remote-".$template:false;   //if a specific template has been provided use that
            if(!$template_path && (null!==$this->remote_content_template && $this->remote_content_template!=="")){
                $template_path="tmp-remote/tmp-remote-".$this->remote_content_template;
            }

// using transients
            $remote_content = get_transient($transient_handle);
            if( empty($remote_content) ){

                $remote_response = wp_remote_get($url, array(
                    'timeout'     => 20,
                ));
                $remote_body = wp_remote_retrieve_body($remote_response);
                if( empty($remote_body) ){
                    return false;
                }else{
                    $remote_content= json_decode($remote_body);
                    set_transient( $transient_handle, $remote_content, $cache_ttl );
                }
            }
            if($template_path){  // if there's a template set use it, passing in the respon se body in the $args parameter
                ob_start();
                get_template_part( $template_path, null, $remote_content );
                return  ob_get_clean();
            }else{  //otherwise return the rendered content of the post, passing through any replacement pairs.
                $c=$remote_content->content->rendered;
                if(is_array($this->remote_content_replace_paths) && count($this->remote_content_replace_paths)>0){
                    foreach($this->remote_content_replace_paths as $rep){
                        $c = $this->filter_replace_path($rep['find'],$rep['replace'],$c);
                    }
                }
                return $c;
            }
        }

        function parse_replacements($fields) {
            $reps_lines = preg_split ('/$\R?^/m', $fields);
//            var_dump($fields);
            $all_reps = [];
            foreach($reps_lines as $reps_line) {
                $reps = array_map('trim', explode('|', $reps_line));
                if (count($reps) <> 2) continue;
                $all_reps[] = $reps;
            }
            if (empty($all_reps)) return [];
            $return_reps = [];
            foreach($all_reps as $rep) {
                array_push($return_reps,['find' => $rep[0], 'replace' => $rep[1]]);
            }
            return $return_reps;
        }
        

        //=====================================
        function validate_url($url)
        {
            return filter_var($url, FILTER_VALIDATE_URL) ? $url : '';
        }
        
        function register_settings()
        {
            add_settings_section('tmp_remote_content_settings', 'Content connection settings', array($this, 'generate_settings_group_content'), 'tmp_remote_content_settings');
            register_setting('tmp_remote_content_settings', 'tmp_remote_content_rest_route', ['sanitize_callback' => array($this, 'validate_url')]);
            add_settings_field('tmp_remote_content_rest_route', 'Default remote REST route. If a default is not supplied you will need to add a route in every shortcode.', array($this, 'generate_settings_field_input_text'), 'tmp_remote_content_settings', 'tmp_remote_content_settings', array('field' => 'tmp_remote_content_rest_route','default'=>"https://<CHANGE FOR YOUR SITE>/wp-json/wp/v2/pages/"));

            add_settings_section('tmp_remote_content_local_settings', 'Local setup', array($this, 'generate_settings_group_content'), 'tmp_remote_content_settings');
            register_setting('tmp_remote_content_local_settings', 'tmp_remote_content_template');
            add_settings_field('tmp_remote_content_template', 'Default template. A partial slug for a template file in a "tmp-remote" directory in the active (sub)theme. Template files must be named in the form "tmp-remote-&lt;partial slug&gt;.php". Leave empty to default to unaltered rendering', array($this, 'generate_settings_field_input_text'), 'tmp_remote_content_settings', 'tmp_remote_content_local_settings', array('field' => 'tmp_remote_content_template','default'=>""));

            register_setting('tmp_remote_content_settings', 'tmp_remote_content_replace_paths');
            add_settings_field('tmp_remote_content_replace_paths', 'Replacement patterns. One find/replace pair per line, which will be used in str_replace for the whole returned content. Use the format:<br />&lt;find&gt;|&lt;replace&gt;', array($this, 'generate_settings_field_input_textarea'), 'tmp_remote_content_settings', 'tmp_remote_content_local_settings', array('field' => 'tmp_remote_content_replace_paths'));

        }

        function add_menu_item()
        {
            add_options_page('The Museum Platform remote content configuration', 'TMP Remote Content', 'manage_options', 'tmp_content', array($this, 'generate_settings_page'));
        }

        function generate_settings_page()
        {
            include($this->plugin_directory . '/views/settings.php');
        }

        function generate_settings_group_content($group)
        {
            $group_id = $group['id'];
            switch ($group_id) {
                case 'tmp_remote_content_remote_settings':
                    $message = 'These settings relate to the URLs to remote resources';
                    break;
                case 'tmp_remote_content_local_settings':
                    $message = 'These settings relate to the presentation of content on this site. The system will use defaults if these are not overridden.';
                    break;
                default:
                    $message = '';
            }
            echo $message;
        }

        function generate_settings_field_input_text($args)
        {
            $field = $args['field'];
            $value = get_option($field);
            if (empty($value) && isset($args['default'])) $value = $args['default'];
            $width = '500px';
            echo sprintf('<input type="text" name="%s" id="%s" value="%s" style="width: %s" />', $field, $field, $value, $width);
        }
        function generate_settings_field_input_textarea($args)
        {
            $field = $args['field'];
            $value = get_option($field);
            if (empty($value) && isset($args['default'])) $value = $args['default'];
            echo sprintf('<textarea type="text" name="%s" id="%s" rows="5" style="width: 500px" />%s</textarea>', $field, $field, $value);
    
            if ($field == 'es_objects_presentation_facets') echo '<br /><small style="display: inline-block; padding-top: 5px">Formatted similarly to how ACF supports options for a group.<br />One per line in "&lt;facet_name&gt; : &lt;field_name&gt;" format. Optionally, this can be followed by a maximum count (size) of aggregation values thus: "&lt;facet_name&gt; : &lt;field_name&gt; : &lt;size&gt;".<br />You must specify a valid field name or everything will break.</small>';
            if ($field == 'es_objects_datatypes') echo '<br /><small style="display: inline-block; padding-top: 5px">One entity name (@datatype.type) per line.</small>';
        }
    
        function filter_replace_path($find, $replace, $content)
        {
            $newcontent = str_replace($find,$replace,$content);
            return $newcontent;
        }


    }
    
  