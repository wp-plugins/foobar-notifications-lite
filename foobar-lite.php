<?php
/*
Plugin Name: Foobar Lite Notification Bars
Plugin URI: themergency.com/foobar-wordpress-plugin/
Description: Show an awesome looking notification bar on your website. PLEASE NOTE : This is the free version with limited functionality. <strong><a target="_blank" href="http://themergency.com/foobar-wordpress-plugin/"><b>Get the full version</a></strong>.
Version: 1.1
Author: Brad Vincent
Author URI: http://themergency.com/
License: GPL2
*/

define('FOOBAR_FILE_CSS', 'jquery.foobar.lite.css');
define('FOOBAR_FILE_JS', 'jquery.foobar.lite.min.js');

if (!class_exists('FoobarLiteNotifications')) {

    // Includes
    require_once "includes/WP_PluginBaseLite.php";
    require_once "includes/foobar-lite-js-generator.php";

    class FoobarLiteNotifications extends WP_PluginBaseLite {
	
        function admin_settings_init() {

          $this->admin_settings_add( array(
              'id'      => 'message',
              'title'   => __( 'Foobar Message' ),
              'desc'    => __( 'The message to display in the FooBar. This can contain HTML.'),
              'std'     => 'Enter some text that you want to show in the Foobar',
              'type'    => 'textarea',
              'section' => '',
              'tab'     => 'General',
              'class'   => 'medium_textarea'
          ) );
          
          $this->admin_settings_add( array(
              'id'      => 'height',
              'title'   => __( 'Height' ),
              'desc'    => __( 'The height of the Foobar when it is expanded / open' ),
              'std'     => '30',
              'type'    => 'text',
              'section' => '',
              'tab'     => 'General'
          ) );
          
          $this->admin_settings_add( array(
              'id'      => 'collapsedButtonHeight',
              'title'   => __( 'Collapsed Button Height' ),
              'desc'    => __( 'The height of the button when the Foobar is collapsed / closed' ),
              'std'     => '30',
              'type'    => 'text',
              'section' => '',
              'tab'     => 'General'
          ) );
          
          $this->admin_settings_add( array(
              'id'      => 'display',
              'title'   => __( 'Initial State' ),
              'desc'    => __( 'The intial state of the Foobar when the page loads'),
              'std'     => 'solid 3px #FFF',
              'type'    => 'select',
              'section' => '',
              'tab'     => 'General',
              'choices' => array('expanded' => 'Expanded', 'collapsed' => 'Collapsed')
          ) );
        
          $this->admin_settings_add( array(
              'id'      => 'speed',
              'title'   => __( 'Open / Close speed' ),
              'desc'    => __( 'The speed that the Foobar opens and closes'),
              'std'     => '200',
              'type'    => 'select',
              'section' => '',
              'tab'     => 'General',
              'choices' => array('200' => 'Normal', '500' => 'Slow', '50' => 'Fast')
          ) );
          
          $this->admin_settings_add( array(
              'id'      => 'backgroundColor',
              'title'   => __( 'Background Color' ),
              'desc'    => __( 'The hex color of the Foobar'),
              'std'     => '#336699',
              'type'    => 'text',
              'section' => '',
              'tab'     => 'Colors & Styling'
          ) );
          
          $this->admin_settings_add( array(
              'id'      => 'border',
              'title'   => __( 'Border Style' ),
              'desc'    => __( 'The CSS border style of the Foobar'),
              'std'     => 'solid 3px #FFF',
              'type'    => 'text',
              'section' => '',
              'tab'     => 'Colors & Styling'
          ) );

          $this->admin_settings_add( array(
              'id'      => 'fontColor',
              'title'   => __( 'Text Color' ),
              'desc'    => __( 'The hex color of the text inside the Foobar'),
              'std'     => '#FFFFFF',
              'type'    => 'text',
              'section' => '',
              'tab'     => 'Colors & Styling'
          ) );
          
          $this->admin_settings_add( array(
              'id'      => 'aFontColor',
              'title'   => __( 'Link Text Color' ),
              'desc'    => __( 'The hex color of the link text inside the Foobar'),
              'std'     => '#FFFFFF',
              'type'    => 'text',
              'section' => '',
              'tab'     => 'Colors & Styling'
          ) );          
          
          $this->admin_settings_add( array(
              'id'      => 'foobar_disabled',
              'title'   => __( 'Disable Foobar' ),
              'desc'    => __( 'Disable Foobar for the whole site. No Foobar will be shown!' ),
              'std'     => 'off',
              'type'    => 'checkbox',
              'section' => '',
              'tab'     => 'Advanced'
          ) );          

          $this->admin_settings_add( array(
              'id'      => 'foobar_exclude_jquery',
              'title'   => __( 'Exclude jQuery Script' ),
              'desc'    => __( 'Stop Foobar from including jQuery into the page.<br />Only use this setting to overcome issues when your theme or other plugins automattically include their own version of jQuery, resulting in javascript errors.' ),
              'std'     => 'on',
              'type'    => 'checkbox',
              'section' => '',
              'tab'     => 'Advanced'
          ) );

          $this->admin_settings_add( array(
              'id'      => 'show_debug',
              'title'   => __( 'Show Debug Info' ),
              'desc'    => __( 'Shows debug information on this settings page.' ),
              'std'     => 'off',
              'type'    => 'checkbox',
              'section' => '',
              'tab'     => 'Advanced'
          ) );
          
          if ( $this->get_option('show_debug') == 'on' ) {
            $this->admin_settings_add( array(
                'id'      => 'debug_info',
                'title'   => __( 'Settings Data' ),
                'desc'    => __( '' ),
                'std'     => '',
                'type'    => 'debug',
                'section' => '',
                'tab'     => 'Debug Info'
            ) );            
          }

        }
        
        function init() {
          $this->_plugin_title = $this->get_foobar_name();
          $this->_plugin_settings_summary = '<p><a target="_blank" href="http://themergency.com/foobar-wordpress-plugin/"><img border=0 src="'.$this->_plugin_url.'images/foobar-lite-590x75.png" /></a></p>';

          if (function_exists('is_admin_bar_showing') && function_exists('add_theme_support')) {
            add_theme_support( 'admin-bar', array( 'callback' => 'foobar_lite_admin_bar_bump_cb') );
          }

          //call base init
          parent::init();
          
          $this->_plugin_name = 'foobar-lite';

          if ( is_admin() ) {
          
            if ($this->check_admin_settings_page()) {
              add_action('admin_print_scripts',  array(&$this, "admin_foobar_js_enqueue") );
              add_action('admin_print_styles', array(&$this, "admin_foobar_css_enqueue") );
              add_action('admin_footer', array(&$this, 'admin_footer_dynamic_js') );
            }

            add_filter( 'plugin_action_links_'.plugin_basename(__FILE__), array(&$this, 'admin_plugin_actions'), -10);
          } else {
            
            add_action('wp_enqueue_scripts',  array(&$this, "frontend_foobar_js_and_css") );
            add_action('wp_head', array(&$this, 'frontend_footer_dynamic_js') );
            
          }
        }

        //plugin version
        function current_plugin_version() {
            return '1.1';
        }
        
        // register foobar CSS scripts in frontend
        function frontend_foobar_js_and_css() {
          if ( !$this->frontend_has_foobar() ) return;
        
          $this->admin_foobar_css_enqueue();
          $this->admin_foobar_js_enqueue();
        }
        
        function frontend_footer_dynamic_js() {
          if ( !$this->frontend_has_foobar() ) return;
          
          $this->admin_footer_dynamic_js();
        }

        // register foobar CSS scripts in admin
        function admin_foobar_css_enqueue() {
          $this->register_and_enqueue_css(FOOBAR_FILE_CSS);
        }

        // register foobar JS scripts in admin
        function admin_foobar_js_enqueue() {
          $this->register_and_enqueue_js(FOOBAR_FILE_JS, $this->get_foobar_js_depends());
        }
        
        function admin_footer_dynamic_js() {
          $options = get_option( $this->_plugin_name );

          $foobar_admin_js = FoobarLiteJSGenerator::generate($options, $this->_plugin_url);

          echo '<script type="text/javascript">' . $foobar_admin_js . '</script>';
        }        

        // Add the 'Settings' and 'Documentation' links to the plugin page
        function admin_plugin_actions($links) {
          $links[] = '<a href="options-general.php?page='.$this->_plugin_name.'">Settings</a>';
          $links[] = '<a target="_blank" href="http://themergency.com/foobar-wordpress-plugin/"><b>Upgrade</b></a>';
          return $links;
        }

        function get_foobar_name() {
            return 'FooBar Lite';
        }

        function admin_settings_validate($input) {
          return $input;
        }

        function custom_admin_settings_render( $args = array() ) {
          extract( $args );

          if ($type == 'debug') {
            $options = get_option( $this->_plugin_name );
            $dump = htmlentities( print_r($options, true) );
            echo '<pre>'. $dump . '</pre></td></tr>';
            
            $js = htmlentities(FoobarLiteJSGenerator::generate($options, $this->_plugin_url));
            
            echo '<tr valign="top"><td>Generated Javascript</td><td><pre>'.$js.'</pre>';
          }
        }

        function frontend_has_foobar() {
          global $has_checked_for_foobar;
          global $has_foobar;

          if (!empty($has_checked_for_foobar)) return $has_foobar;

          //if we have disabled foobar, then show nothing!
          if ( $this->get_option('foobar_disabled') == 'on' ) {
            $has_foobar = false;
          } else {
            $has_foobar = true;
          }
          
          $has_checked_for_foobar = true;
          
          return $has_foobar;
        }

        function get_foobar_js_depends() {
          if ($this->get_option('foobar_exclude_jquery') == 'on') {
            return array();
          }

          return array('jquery');
        }

    }

    load_plugin('FoobarLiteNotifications', 5);
}

function foobar_lite_admin_bar_bump_cb() { ?>
<style type="text/css">
	html { margin-top: 28px; }
	* html body { margin-top: 28px; }
</style>
<?php
}

?>