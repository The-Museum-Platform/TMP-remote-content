You can use this directory and these templates as models for your own. Copy this directory into your active theme directory and modify the templates as needed, or add more as required. 
Always give the template file a name starting "tmp-remote", as this is used as a prefix in combination with the directory name.

Default template (tmp-remote-default.php)
The plugin's default template setting can be left empty, in which case the remote content will be output plain (i.e. as the rendered HTML of the page content). Otherwise, set a default template for use unless overridden. The included example just wraps the remote contents in a <div> with a class.
Shortcode template attribute value: "default" e.g. 
[tmp_content postid="10" template="default"][/tmp_content]
or as a self-closing tag:
[tmp_content postid="10" template="default" /]

Plain (tmp-remote-plain.php)
If you have set a default template but want for a specific instance to render the output unaltered, this template returns just the original rendered HTML
Shortcode template attribute value: "plain" e.g. [tmp_content postid="10" template="plain"/]