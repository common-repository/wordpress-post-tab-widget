<?php
/*
Plugin Name: Wordpress Post Tab Widget
Plugin URI: http://www.prosatya.com
Description:  Wordpress Post Tab Widget Showcases your most commented , letest, most shared posts to your visitors on your wordpress blog's sidebar. Two type widget you can implement one Jquery Tabs and other Jquery Accordion
Version: 1.0.0
Author: Satyanaayan Verma @prosatya 
Author URI: http://www.prosatya.com
License: GPL2
*/

if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) exit('Please do not load this page directly');

/**
 * Load Wordpress Post Tab Widget to widgets_init.
 */
add_action('widgets_init', 'load_wptw');

function load_wptw() {
	register_widget('WordpressPostTabWidget');
}

/**
 * Wordpress Post Tab Widget Class.
 */

class WordpressPostTabWidget extends WP_Widget {
		public  $version = "1.0.0";
		public  $pluginDir = "";
		
		// constructor
		function __construct() {
			global $wp_version;
				
			// widget settings
			$widget_ops = array( 'classname' => 'wordpress-posts-tab', 'description' => 'The Wordpress Post Tab Widget Classs on your blog.' );
	
			// widget control settings
			$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'wptw' );
	
			// create the widget
			$this->WP_Widget( 'wptw', 'Wordpress Post Tab Widget', $widget_ops, $control_ops );
			
			// set plugin path 
			if (empty($this->pluginDir)) $this->pluginDir = WP_PLUGIN_URL . '/wordpress-post-tab-widget';
			
		
			// add  stylesheet hook 
			// add_action('wp_head', array(&$this, 'wptw_print_stylesheet'),1);
			
			// add  js hook 
			// add_action('wp_head', array(&$this, 'wptw_print_javascript'),9);
			
			// activate textdomain for translations
			add_action('init', array(&$this, 'wptw_textdomain'));
			
			
			// Wordpress version check
			if (version_compare($wp_version, '2.8.0', '<')) add_action('admin_notices', array(&$this, 'wptw_update_warning'));
			
			// set version
			$wptw_ver = get_option('wptw_ver');
			if (!$wptw_ver) {
				add_option('wptw_ver', $this->version);
			} else if (version_compare($wptw_ver, $this->version, '<')) {
				update_option('wptw_ver', $this->version);
			}
		
		}
	
		// builds Wordpress Post Tab Widget
		function widget($args, $instance){
			extract($args);
			echo "<!-- Wordpress Post Tab Widget Plugin v". $this->version ."  -->"."\n";
			echo $before_widget . "\n";
			
			// has user set a title?
			if ($instance['title'] != '') {
					echo $before_title . htmlspecialchars_decode($instance['title'], ENT_QUOTES) . $after_title;
			}else{
					echo $before_title . htmlspecialchars_decode('Wordpress Post Tab Widget', ENT_QUOTES) . $after_title;
			}
			echo $this->get_popular_tab_posts($instance);			
			echo $after_widget . "\n";
			echo "<!-- End Wordpress Post Tab Widget v". $this->version ." -->"."\n";
			
		}
		
		// prints popular posts
		function get_popular_tab_posts($instance, $echo = true) {
			global $wpdb;
			echo "<!-- Start Wordpress Post Tab Widget content v". $this->version ." -->"."\n";
			$mostpopular1 = $wpdb->get_results("SELECT $wpdb->posts.ID, $wpdb->posts.post_title, $wpdb->posts.guid  
													 FROM $wpdb->posts  
													 WHERE $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_password = ''
													 ORDER BY comment_count DESC LIMIT " . $instance['limit'] . "");
			$mostpopular2 = $wpdb->get_results("SELECT $wpdb->posts.ID, $wpdb->posts.post_title, $wpdb->posts.guid  
													 FROM $wpdb->posts  
													 WHERE $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_password = ''
													 ORDER BY to_ping ,pinged DESC LIMIT " . $instance['limit'] . "");
			$mostpopular3 = $wpdb->get_results("SELECT $wpdb->posts.ID, $wpdb->posts.post_title, $wpdb->posts.guid  
													 FROM $wpdb->posts  
													 WHERE $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_password = ''
													 ORDER BY post_date DESC LIMIT " . $instance['limit'] . "");
			//TODO: Use wordpess stylesheet and javascrrpt hook 
			// TODO: Seprate js and css code from the plugin
			?> 
		<link type="text/css" href="<?php echo $this->pluginDir?>/css/ui-lightness/jquery-ui-1.8.18.custom.css" rel="stylesheet" />	
		<script type="text/javascript" src="<?php echo $this->pluginDir?>/js/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="<?php echo $this->pluginDir?>/js/jquery-ui-1.8.18.custom.min.js"></script>
		<script type="text/javascript">
			$(function(){
				// Accordion
				$("#accordion").accordion({ header: "h3" });
				// Tabs
				$('#tabs').tabs();
				});
		</script>
		<style type="text/css">
			/*demo page css*/
			.demoHeaders { margin-top: 2em; }
			#dialog_link {padding: .4em 1em .4em 20px;text-decoration: none;position: relative;}
			#dialog_link span.ui-icon {margin: 0 5px 0 0;position: absolute;left: .2em;top: 50%;margin-top: -8px;}
			ul#icons {margin: 0; padding: 0;}
			ul#icons li {margin: 2px; position: relative; padding: 4px 0; cursor: pointer; float: left;  list-style: none;}
			ul#icons span.ui-icon {float: left; margin: 0 4px;}
		</style>
		<?php 
		if($instance['type']=='acc'){?>
		<div style="width:255px;" id="accordion">
			<div>
				<h3><a href="#"><?php echo $instance['tab1title'];?></a></h3>
				<div><ul><?php
		if ( !is_array($mostpopular1) || empty($mostpopular1) ) {
			$content .= "<p>".__('Sorry. No data so found.', 'wordpress-post-tab-widget')."</p>"."\n";
		} else { 
			foreach ($mostpopular1 as $wppost) {					
			 ?>
			<li style=""><a href="<?php echo $wppost->guid;?>" alt=""<?php echo $wppost->post_title;?>><?php echo $wppost->post_title;?></a> </li> 
			<?php } ?> 
		<?php } ?>
		</ul>
		</div>
			</div>
			<div>
				<h3><a href="#"><?php echo $instance['tab2title'];?></a></h3>
				<div><ul><?php
		if ( !is_array($mostpopular2) || empty($mostpopular2) ) {
			$content .= "<p>".__('Sorry. No data so far.', 'wordpress-post-tab-widget')."</p>"."\n";
		} else { 
			foreach ($mostpopular2 as $wppost) {					
			 ?>
			<li class="tabsul"><a href="<?php echo $wppost->guid;?>" alt=""<?php echo $wppost->post_title;?>><?php echo $wppost->post_title;?></a> </li> 
			<?php } ?> 
		<?php } ?>
		</ul>
		</div>
			</div>
			<div>
				<h3><a href="#"><?php echo $instance['tab3title'];?></a></h3>
				<div>
				<ul><?php
		if ( !is_array($mostpopular3) || empty($mostpopular3) ) {
			$content .= "<p>".__('Sorry. No data so found.', 'wordpress-post-tab-widget')."</p>"."\n";
		} else { 
			foreach ($mostpopular3 as $wppost) {					
			 ?>
			<li class="tabsul"><a href="<?php echo $wppost->guid;?>" alt=""<?php echo $wppost->post_title;?>><?php echo $wppost->post_title;?></a> </li> 
			<?php } ?> 
		<?php } ?>
		</ul>
		</div>
			</div>
		</div>
	<?php }else{?>
		<div id="tabs">
			<ul class="ui-tabs" style="height: 33px;">
			<li class="tabsul"><a href="#tabs-1"><?php echo $instance['tab1title'];?></a></li>
			<li class="tabsul"><a href="#tabs-2"><?php echo $instance['tab2title'];?></a></li>
			<li class="tabsul"><a href="#tabs-3"><?php echo $instance['tab3title'];?></a></li>
		</ul>
		<div id="tabs-1">
		<?php
		if ( !is_array($mostpopular1) || empty($mostpopular1) ) {
			$content .= "<p>".__('Sorry. No data so found.', 'wordpress-post-tab-widget')."</p>"."\n";
		} else { 
			foreach ($mostpopular1 as $wppost) {					
			 ?>
			<li><a href="<?php echo $wppost->guid;?>" alt=""<?php echo $wppost->post_title;?>><?php echo $wppost->post_title;?></a> </li> 
			<?php } ?> 
		<?php } ?>
		</div>
		<div id="tabs-2">
		<?php
		if ( !is_array($mostpopular2) || empty($mostpopular2) ) {
			$content .= "<p>".__('Sorry. No data so found.', 'wordpress-post-tab-widget')."</p>"."\n";
		} else { 
			foreach ($mostpopular2 as $wppost) {					
			 ?>
			<li><a href="<?php echo $wppost->guid;?>" alt=""<?php echo $wppost->post_title;?>><?php echo $wppost->post_title;?></a> </li> 			
			<?php }	?> 
		<?php } ?>
		</div>
		<div id="tabs-3">
			<?php
		if ( !is_array($mostpopular3) || empty($mostpopular3) ) {
			$content .= "<p>".__('Sorry. No data so found.', 'wordpress-post-tab-widget')."</p>"."\n";
		} else { 
			foreach ($mostpopular3 as $wppost) {					
		 ?>
			<li><a href="<?php echo $wppost->guid;?>" alt=""<?php echo $wppost->post_title;?>><?php echo $wppost->post_title;?></a> </li> 			
			<?php }	?>
		<?php } ?>
		</div>
		</div>
		<?php } ?>		
		<?php echo "<!-- End Wordpress Post Tab Widget v". $this->version ." -->"."\n";
		}
		
		// updates each widget instance when user clicks the "save" button
		function update($new_instance, $old_instance) {	
			$instance = $old_instance;
			
			$instance['title'] = ($this->magicquotes) ? htmlspecialchars( stripslashes(strip_tags( $new_instance['title'] )), ENT_QUOTES ) : htmlspecialchars( strip_tags( $new_instance['title'] ), ENT_QUOTES );
			$instance['tab1title'] = ($this->magicquotes) ? htmlspecialchars( stripslashes(strip_tags( $new_instance['tab1title'] )), ENT_QUOTES ) : htmlspecialchars( strip_tags( $new_instance['tab1title'] ), ENT_QUOTES );
			$instance['tab2title'] = ($this->magicquotes) ? htmlspecialchars( stripslashes(strip_tags( $new_instance['tab2title'] )), ENT_QUOTES ) : htmlspecialchars( strip_tags( $new_instance['tab2title'] ), ENT_QUOTES );
			$instance['tab3title'] = ($this->magicquotes) ? htmlspecialchars( stripslashes(strip_tags( $new_instance['tab3title'] )), ENT_QUOTES ) : htmlspecialchars( strip_tags( $new_instance['tab3title'] ), ENT_QUOTES );
			$instance['limit'] = is_numeric($new_instance['limit']) ? $new_instance['limit'] : 10;
			$instance['type'] = $new_instance['type'];
	
			return $instance;
		}
		// widget's form
		function form($instance) {
			// set default values			
			$defaults = array(
				'title' => __('All Post', 'wordpress-post-tab-widget'),
				'tab1title' => __('Comment', 'wordpress-post-tab-widget'),
				'tab2title' => __('Shared', 'wordpress-post-tab-widget'),
				'tab3title' => __('Letest', 'wordpress-post-tab-widget'),
				'limit' => 10,
				'type' => 'tab'
			);
			
			// update instance's default options
			$instance = wp_parse_args( (array) $instance, $defaults );
			// form
			?>
            <p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'wordpress-post-tab-widget'); ?></label>
            <input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" class="widefat" /></p>
      		<p><label for="<?php echo $this->get_field_id( 'tab1title' ); ?>"><?php _e('First Tab Title:(View by comment)', 'wordpress-post-tab-widget'); ?></label>
            <input id="<?php echo $this->get_field_id( 'tab1title' ); ?>" name="<?php echo $this->get_field_name( 'tab1title' ); ?>" value="<?php echo $instance['tab1title']; ?>" class="widefat" /></p>
			<p><label for="<?php echo $this->get_field_id( 'tab2title' ); ?>"><?php _e('Second Tab Title:(pingback and tracback)', 'wordpress-post-tab-widget'); ?></label>
            <input id="<?php echo $this->get_field_id( 'tab2title' ); ?>" name="<?php echo $this->get_field_name( 'tab2title' ); ?>" value="<?php echo $instance['tab2title']; ?>" class="widefat" /></p>
            <p><label for="<?php echo $this->get_field_id( 'tab3title' ); ?>"><?php _e('Third Tab Title:(Letest)', 'wordpress-post-tab-widget'); ?></label>
            <input id="<?php echo $this->get_field_id( 'tab3title' ); ?>" name="<?php echo $this->get_field_name( 'tab3title' ); ?>" value="<?php echo $instance['tab3title']; ?>" class="widefat" /></p>
            <p><label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e('Show up to:', 'wordpress-post-tab-widget'); ?></label><br />
            <input id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" value="<?php echo $instance['limit']; ?>"  class="widefat" style="width:50px!important" /> <?php _e('posts', 'wordpress-post-tab-widget'); ?></p>
             <p><label for="<?php echo $this->get_field_id( 'type' ); ?>"><?php _e('Sort posts by:', 'wordpress-post-tab-widget'); ?></label>
            <select id="<?php echo $this->get_field_id( 'type' ); ?>" name="<?php echo $this->get_field_name( 'type' ); ?>" class="widefat">
            	<option value="tab" <?php if ( 'tab' == $instance['type'] ) echo 'selected="selected"'; ?>><?php _e('Tabs', 'wordpress-post-tab-widget'); ?></option>
                <option value="acc" <?php if ( 'acc' == $instance['type'] ) echo 'selected="selected"'; ?>><?php _e('Accordion', 'wordpress-post-tab-widget'); ?></option>
            </select>
            </p>
            <?php
	}
	
	
	// plugin localization 
	function wptw_textdomain() {
		$plugin_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
		load_plugin_textdomain( 'wordpress-post-tab-widget', null, $plugin_dir );
	}
	
	// insert Wordpress Post Tab Widget' stylesheet in theme's head section, just in case someone needs it
	function wptw_print_stylesheet() {
		wp_enqueue_style( 'wptw', $this->pluginDir."/css/wptw.css", '', 1.0);
		wp_enqueue_style( 'wptw-j', $this->pluginDir."/css/ui-lightness/jquery-ui-1.8.18.custom.css", '', 1.0);
	}
		// insert Wordpress Post Tab Widget' javascript in theme's head section, just in case someone needs it
	function wptw_print_javascript() {
		//TODO: Fixed Jquery duplicate issue 
		  wp_enqueue_script('jquery', $this->pluginDir."/js/jquery-1.7.1.js", '', 1.0, false);
		  wp_enqueue_script('jquery-ui', $this->pluginDir."/js/jquery-ui-1.8.18.custom.min", '', 1.0, false);
		  wp_enqueue_script('jquery-wptw', $this->pluginDir."/js/wptw.js", '', 1.0, false);
	}
	// plugin deactivation
	function wptw_deactivation() {
		// Will use when required
	}
	
	// plugin deactivation
	function wptw_activation() {
		// Will use when required
	}
	//  create table and add variable if required 
	function wptw_install(){
	// Will add activation hook
	}
	//  remove table and variable if added 
	function wptw_unstall(){
		// Will add activation hook
	}
	
}
// register activation hook
register_activation_hook(__FILE__ , array('WordpressPostTabWidget', 'wptw_install'));

//register deactivation hook
register_activation_hook(__FILE__ , array('WordpressPostTabWidget', 'wptw_unstall'));

