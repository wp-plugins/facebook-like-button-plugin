<?php
/**
 * Plugin Name: Facebook Like Button Plugin
 * Plugin URI: http://martinj.net/wordpress-plugins/facebook-like-button
 * Description: The new Facebook like button.
 * Version: 1.1
 * Author: Martin Jonsson
 * Author URI: http://martinj.net
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

function facebook_like_button_plugin_output($out = '') {
	global $post;
	$options = unserialize(get_option('facebook_like_button_plugin_options'));
	$options = facebook_like_button_plugin_defaults($options);			
	
	if (!$options['show_on_pages'] && is_page()) return $out;
	if (!$options['show_on_home'] && is_home()) return $out;
	$iframe = 
		'<iframe src="http://www.facebook.com/plugins/like.php?href='.urlencode(get_permalink($post->id)).
		'&amp;layout=' .$options['layout']. 
			'&amp;'. ($options['show_faces'] ? 'show_faces=true' : '') .
			'&amp;width=' . $options['width'] .
			'&amp;action=' . $options['action'] .
			(strlen($options['font']) > 0 ? '&amp;font=' . $options['font'] : '') .
			'&amp;colorscheme=' . $options['colorscheme'] .
			'" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; overflow:hidden; width:'.$options['width'].'px;'.($options['height'] ? 'height:'.$options['height'].'px;': '').($options['iframe_style'] ? $options['iframe_style'] : '').'"></iframe>';
	
	if ($options['position'] == 'top') {
		$out = $iframe . $out;
	} else {
		$out .= $iframe;
	}
		
	return $out;
}

function facebook_like_button_plugin_wp_head() {
	global $post;
	if (is_home()) return;

	$options = unserialize(get_option('facebook_like_button_plugin_options'));
	$options = facebook_like_button_plugin_defaults($options);			
	
	if (!$options['show_on_pages'] && $post->post_type == 'page') return;
	
	echo '<meta property="og:title" content="'.$post->post_title.'" />';
}

function facebook_like_button_plugin_defaults($options) {
	if (!isset($options['show_on_pages'])) $options['show_on_pages'] = false;	
	if (!isset($options['show_on_home'])) $options['show_on_home'] = false;	
	if (!isset($options['show_faces'])) $options['show_faces'] = true;
	if (!$options['layout']) $options['layout'] = 'standard';
	if (!$options['width']) $options['width'] = '450';
	if (!$options['action']) $options['action'] = 'like';
	if (!$options['colorscheme']) $options['colorscheme'] = 'light';
	if (!$options['position']) $options['position'] = 'bottom';
	if (!isset($options['iframe_style'])) $options['iframe_style'] = 'margin-top:5px;';		
	
	return $options;
}

function facebook_like_button_plugin_options() {
	if ($_POST["fb_like_button_submit"]) {
		
		$submitted_options = array();		
		$submitted_options['show_on_pages'] = stripslashes($_POST["show_on_pages"]);		
		$submitted_options['show_on_home'] = stripslashes($_POST["show_on_home"]);		
		
		$submitted_options['layout'] = stripslashes($_POST["layout"]);
		$submitted_options['show_faces'] = isset($_POST["show_faces"]) ? true : false;
		$submitted_options['width'] = stripslashes($_POST["width"]);
		$submitted_options['height'] = stripslashes($_POST["height"]);		
		$submitted_options['action'] = stripslashes($_POST["action"]);
		$submitted_options['font'] = stripslashes($_POST["font"]);
		$submitted_options['colorscheme'] = stripslashes($_POST["colorscheme"]);
		$submitted_options['position'] = stripslashes($_POST["position"]);
		$submitted_options['iframe_style'] = stripslashes($_POST["iframe_style"]);
				
		update_option('facebook_like_button_plugin_options', serialize($submitted_options));
	}
	
	$options = unserialize(get_option('facebook_like_button_plugin_options'));
	$options = facebook_like_button_plugin_defaults($options);
	
	echo '
		<div>
		<form method="post">
		<div class="wrap">
			<h2>Facebook Like Button Plugin</h2>
			<h3 class="title">Configuration</h3>				
			<dl>
				<dt>
					Show on Home
				</dt>
					<dd>
						<input name="show_on_home" id="param_show_on_home" value="true" '.($options['show_on_home'] ? 'checked="1"' : '').' type="checkbox"><label 	for="param_show_on_home">Show in post listing on Home Page?</label>					
					</dd>
			
				<dt>
					Show on Pages
				</dt>
					<dd>
						<input name="show_on_pages" id="param_show_on_pages" value="true" '.($options['show_on_pages'] ? 'checked="1"' : '').' type="checkbox"><label for="param_show_on_pages">Show the button on pages?</label>					
					</dd>
				<dt>
					Position
				</dt>
					<dd>
						<select name="position">
							<option value="bottom" '. ($options['position'] == 'bottom' ? 'selected="1"' : '') . '>bottom</option>
							<option value="top" '. ($options['position'] == 'top' ? 'selected="1"' : '') . '>top</option>
						</select>
						<span>Where to display the button in relation to Post/Page.</span>
					</dd>
			</dl>
			
			<h3 class="title">Customization</h3>				
			<dl>
				<dt>
					<label for="param_layout">Layout Style</label>
				</dt>
					<dd>
						<select name="layout" id="param_layout">
							<option value="standard" '. ($options['layout'] == 'standard' ? 'selected="1"' : '') . '>standard</option>
							<option value="button_count" '. ($options['layout'] == 'button_count' ? 'selected="1"' : '') . '>button_count</option>
						</select>
						<span>determines the size and amount of social context next to the button</span>
					</dd>
				<dt>
					Show Faces
				</dt>
					<dd>
						<input name="show_faces" id="param_show_faces" value="true" '.($options['show_faces'] ? 'checked="1"' : '').' type="checkbox"><label for="param_show_faces">Show profile pictures below the button.</label>
					</dd>
				<dt>
					<label for="param_width">Width</label>
				</dt>
					<dd>
						<input name="width" id="param_width" value="'.$options['width'].'" class="small-text" type="text">
						<span>the width of the plugin, in pixels</span>
					</dd>
				<dt>
					<label for="param_height">Height</label>
				</dt>
					<dd>
						<input name="height" id="param_height" value="'.$options['height'].'" class="small-text" type="text">
						<span>the height in pixels. If you want to show faces don\'t make this to small, otherwise 30 is a good height.</span>
					</dd>
				<dt>
					<label for="param_action">Verb to display</label>
				</dt>
					<dd>
						<select name="action" id="param_action">
							<option value="like" '. ($options['action'] == 'like' ? 'selected="1"' : '') . '>like</option>
							<option value="recommend" '. ($options['action'] == 'recommend' ? 'selected="1"' : '') . '>recommend</option>
						</select>
						<span>The verb to display in the button. Currently only \'like\' and \'recommend\' are supported.</span>
					</dd>
				<dt>
					<label for="param_font">Font</label> 		
				</dt>
					<dd>
						<select name="font" id="param_font">
							<option '. (!$options['font'] ? 'selected="1"' : '') . '></option>
							<option value="arial" '. ($options['font'] == 'arial' ? 'selected="1"' : '') . '>arial</option>
							<option value="lucida grande" '. ($options['font'] == 'lucida grande' ? 'selected="1"' : '') . '>lucida grande</option>
							<option value="segoe ui" '. ($options['font'] == 'segoe ui' ? 'selected="1"' : '') . '>segoe ui</option>
							<option value="tahoma" '. ($options['font'] == 'tahoma' ? 'selected="1"' : '') . '>tahoma</option>
							<option value="trebuchet ms" '. ($options['font'] == 'trebuchet ms' ? 'selected="1"' : '') . '>trebuchet ms</option>
							<option value="verdana" '. ($options['font'] == 'verdana' ? 'selected="1"' : '') . '>verdana</option>
						</select>
						<span>the font of the plugin</span>
					</dd>
				<dt>
					<label for="param_colorscheme">Color Scheme</label>
				</dt>
					<dd>
						<select name="colorscheme" id="param_colorscheme">
							<option value="light" '. ($options['colorscheme'] == 'light' ? 'selected="1"' : '') . '>light</option>
							<option value="dark" '. ($options['colorscheme'] == 'dark' ? 'selected="1"' : '') . '>dark</option>
						</select>
						<span>The color scheme of the plugin.</span>
					</dd>
				<dt>
					<label for="param_iframe_style">CSS style</label>
				</dt>
					<dd>
						<input name="iframe_style" id="param_iframe_style" value="'.$options['iframe_style'].'" class="regular-text" type="text">
						<span>Extra css styling for the iframe.</span>
					</dd>					
			</dl>
			<p class="submit">
				<input type="submit" name="fb_like_button_submit" value="Update Options &raquo;" />
			</p>
		</div>
		</form>
		</div>	
	';
}

function add_facebook_like_button_plugin_submenu() {
    add_submenu_page('plugins.php', 'Facebook Like Button', 'Facebook Like Button', 10, __FILE__, 'facebook_like_button_plugin_options'); 
}

add_action('admin_menu', 'add_facebook_like_button_plugin_submenu');
add_action('the_content', 'facebook_like_button_plugin_output', 99);
add_action('wp_head', 'facebook_like_button_plugin_wp_head');
?>