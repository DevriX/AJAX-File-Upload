<?php

/*
Plugin Name: AJAX File Upload
Plugin URI: 
Description: AJAX file Upload - fast and easy front-end WordPress file uploader with shortcodes
Author: Samuel Elh
Version: 0.1
Author URI: http://samelh.com
*/

if ( ! class_exists('AJAX_file_upload') ) :

class AJAX_file_upload
{
	protected static $instance = null;

	public function __construct() {
		
		$settings = new stdClass();
		$settings->max_size = 2000;
		$settings->extensions = array( 'png', 'jpg', 'bmp', 'gif', 'txt', 'mp3', 'mp4', '3gp' );
		$settings->default_permission = 'all';

		$this->settings = apply_filters( "ajax_file_upload_settings", $settings );

	}
	
	public function _init() {

		add_shortcode('ajax-file-upload', array( &$this, '_shortcode' ));
		add_action( 'wp_ajax_ajax_file_upload', array( &$this, 'ajax' ) );
		add_action( 'wp_ajax_nopriv_ajax_file_upload', array( &$this, 'ajax' ) );
		add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );
		add_action( 'wp_footer', array( &$this, 'wp_footer' ) );

		if( file_exists( $this->template_path( 'includes/admin.php', true ) ) ) {
			require $this->template_path( 'includes/admin.php', true );
		}

		add_filter( "plugin_action_links_" . plugin_basename(__FILE__), array( &$this, 'push_admin_links' ) );

	}

	public static function shortcode( $atts, $custom = false ) {
		$class = null == self::$instance ? new self : self::$instance;
		return $class->_shortcode( $atts, $custom );
	}

	public static function init() {
		$class = null == self::$instance ? new self : self::$instance;
		return $class->_init();
	}

	public static function settings() {
		$class = null == self::$instance ? new self : self::$instance;
		return $class->$settings;
	}


	public function _shortcode( $atts, $custom = false ) {

		$a = shortcode_atts( array(

			'unique_identifier'						=> '',
			'max_size' 								=> '',
			'allowed_extensions' 					=> '',
	        'permissions' 							=> '',
	        'on_success_alert'						=> '',
	        'on_success_set_input_value'			=> '',
	        'on_fail_alert'							=> '',
	        'set_background_image'					=> '',
	        'set_image_source'						=> '',
	        'disallow_remove_button'				=> '',
	        'disallow_reupload'						=> '',
	        'upload_button_value'					=> '',
	        'select_file_button_value'				=> '',
	        'remove_file_button_value'				=> '',
	        'show_preloader'						=> '',
	        'default_loading_text'					=> '',
	        'on_success_dialog_prompt_value'		=> '',
	        'on_fail_alert_error_message'			=> '',

	    ), $atts );

	    $data_task = array();

	    foreach ( $a as $id => $att ) {

	    	if( empty( $att ) ) {
	    		$a[$id] = $this->default_atts_values( array( $id => $att ) );
	    	}

	    	switch( $id ) {

	    		case 'max_size':
	    			$a[$id] = (int) $a[$id];
	    			break;

	    		case 'allowed_extensions':
	    			
	    			if( ! is_array( $a[$id] ) ) {
	    				$a[$id] = explode( ",", $a[$id] );
	    				$a[$id] = array_filter( array_unique( $a[$id] ) );
	    			}

	    			break;

	    		case 'on_success_dialog_prompt_value':

	    			if( isset( $a[$id] ) && empty( $a[$id] ) ) {
	    				$a[$id] = $this->translate( "Here's your uploaded media URI:" );
	    			}	
	    			break;

	    		// validate other atts !!

	    	}

	    	$data_task[$id] = $a[$id];

	    }

	   	foreach ( $data_task as $id => $att ) { if ( ! $att ) unset( $data_task[$id] ); }


	   	// base64 encode

	   	update_option(
	   		"afu_" . preg_replace('/[^\da-z]/i', '', $a['unique_identifier']) . "_upload_settings",
	   		base64_encode(json_encode( $data_task ))
	   	);

	    $data_task = str_replace( '"', '&quot;', json_encode( $data_task ) );

	    require $this->template_path( 'includes/shortcode-template.php', true );

	}

	public function default_atts_values( $att ) {

		if( ! is_array( $att ) ) { return; }

		foreach ( $att as $id => $value ) {

			switch ( $id ) {

				case 'max_size':
					return $this->settings->max_size; // 1 MB
					break;

				case 'permissions':
					return 'all';
					break;

				case 'on_fail_alert':
					return $this->translate('Error occured while processing your file. Please try again.');
					break;

				case 'upload_button_value':
					return $this->translate('upload');
					break;

				case 'select_file_button_value':
					return $this->translate('choose file');
					break;

				case 'remove_file_button_value':
					return $this->translate('remove');
					break;

				case 'allowed_extensions':
					return $this->settings->extensions;
					break;

				case 'disallow_remove_button':
					return false;
					break;

				case 'disallow_reupload':
					return false;
					break;

				case 'default_loading_text':
					return 'uploading..';
					break;

				case 'on_fail_alert_error_message':
					return true;
					break;

				default:
					break;

			}

		}

		return;

	}
	
	public function upload() {}

	public function process_file( $file = array() ) {
	
		do_action('afu_before_start_upload', $file);

		require_once( ABSPATH . 'wp-admin/includes/admin.php' );
		
		$args = wp_handle_upload( $file, array('test_form' => false ) );
		
		if( isset( $args['error'] ) || isset( $args['upload_error_handler'] ) ) {
			return false;
		} else {
			do_action('afu_after_upload_done', $args);
			return apply_filters( 'afu_returned_file_url', $args['url'], $args );
		}

		return;
	
	}

	public function ajax() {

		$response = array();
		$response['success'] = false;

		if( ! isset( $_REQUEST['_afu_nonce'] ) || !wp_verify_nonce( $_REQUEST['_afu_nonce'], '_afu_nonce' ) ) {
			header("Content-type: application/json; charset=utf-8");
			$response["error_message"] = "Error while uploading: authenticate error";
			echo json_encode( $response );
			exit;
		}

		if( ! empty( $_FILES ) ) {
		
			foreach( $_FILES as $file ) {

				if( ! empty( $_REQUEST['id'] ) ) {
					$settings = get_option( "afu_" . preg_replace('/[^\da-z]/i', '', $_REQUEST['id']) . "_upload_settings" );
					$settings = json_decode( base64_decode( $settings ) );
				}

				$defaul_settings = $this->settings;

				if( empty( $settings->max_size ) ) {
					$settings->max_size = $defaul_settings->max_size;
				}

				if( empty( $settings->permissions ) ) {
					$settings->permission = $defaul_settings->default_permission;
				} else {
					$settings->permission = $settings->permissions;
					unset( $settings->permissions );
				}

				if( empty( $settings->allowed_extensions ) || ! is_array( $settings->allowed_extensions ) ) {
					$settings->extensions = $defaul_settings->extensions;
				} else {
					$settings->extensions = $settings->allowed_extensions;
				}

				$bail = false;
				$pathinfo = pathinfo( $file['name'] );

				if( empty( $pathinfo ) || ! is_array( $file ) ) {
					$bail = true;
				}

				// convert file size to KB
				$file['size'] = intval( $file['size'] / 1024 );

				if( ! $pathinfo['extension'] || ! in_array( strtolower($pathinfo['extension']), $settings->extensions ) ) {
					$bail = true;
					$response['error_message'] = sprintf(
						$this->translate("Extension \"%s\" not allowed. Allowed extensions are \"%s\""),
						$pathinfo['extension'],
						implode( ", ", $settings->extensions )
					);
				}

				elseif ( $file['size'] > $settings->max_size ) {
					$bail = true;
					$response['error_message'] = sprintf(
						$this->translate("Maximum file size \"%s\" KB breached. Your file size was \"%s\" KB"),
						$settings->max_size,
						$file['size']
					);
				}


				if( ! $bail )  {
					if( "all" == $settings->permission ) {
						$bail = false;
					}
					elseif ( "logged_in" == $settings->permission ) {
						if( ! is_user_logged_in() ) {
							$bail = true;
							$response['error_message'] = sprintf(
								$this->translate("You must be logged-in to upload")
							);
						}
					}
					else {
						// custom role
						if( ! in_array( $settings->permission, wp_get_current_user()->roles ) ) {
							$bail = true;
							$response['error_message'] = sprintf(
								$this->translate("Permissions don't allow you to upload")
							);	
						}
					}
				}

				$bail = apply_filters( "afu_bail_upload", $bail, $file, $settings );

				if( ! $bail ) {
					$media = $this->process_file( $file );
					$response['success'] = false !== $media;
					$response['media_uri'] = $media;
				} else {
					$response['success'] = false;
				}

				if( false === $response['success'] && empty( $response['error_message'] ) ) {
					$response['error_message'] = $this->translate("Error occured while processing your file" );
				}

				$response["settings"] = $settings;
				$response["file"] = $file;

			}
		}

		header("Content-type: application/json; charset=utf-8");
		echo json_encode( $response );
		exit;

	}

	public function translate( $string ) {

		$meta = get_option( "afu_transaltions" );

		if( $meta ) {
			$meta = json_decode( base64_decode( $meta ), true );

			if( ! empty( $meta[$string] ) ) {
				return (string) stripslashes( $meta[$string] );
			}
		}

		return $string;

	}

	public function enqueue_scripts() {

		wp_enqueue_style( 'afu', $this->template_path( 'assets/css/style.css' ) );
		wp_enqueue_style( 'afu-icons', $this->template_path( 'assets/fontello/css/afu.css' ) );
		wp_enqueue_style( 'afu-icons-animation', $this->template_path( 'assets/fontello/css/animation.css' ) );

		wp_enqueue_script(
			'afu',
			$this->template_path( 'assets/js/main.js' ),
			array('jquery'),
			null
		);

	}

	public function wp_footer() {
		?>
			<script type="text/javascript">
				/* <![CDATA[ */
				var ajax_file_upload = {
					create_event: function(name, data) {
						if( "object" !== typeof data ) { data = []; }
						var e = document.createEvent('Event');
						e.initEvent(name, true, true);
						e.data = data;
						document.dispatchEvent(e);
					},
					"ajax_path": "<?php echo admin_url('admin-ajax.php'); ?>"
				}
				/* ]]> */
			</script>
		<?php
	}

	public function template_path( $path, $return_path = false ) {

		$dir_name = str_replace( '/ajax-file-upload.php', '', plugin_basename( __FILE__ ) );
		$child_base = get_stylesheet_directory() . '/' . $dir_name . '/';

		if( file_exists( $child_base . $path ) ) {
			$base = $return_path ? get_stylesheet_directory() : get_stylesheet_directory_uri();
			return $base . '/' . $dir_name . '/' . $path;
		} else {
			$base = $return_path ? plugin_dir_path(__FILE__) : plugin_dir_url(__FILE__);
			return $base . $path;
		}

	}

	public function push_admin_links( $links ) {
	
		array_push( $links, '<a href="options-general.php?page=ajax-file-upload">' . __( 'Settings' ) . '</a>' );
		array_push( $links, '<a href="index.php?page=afu-about">' . __( 'About' ) . '</a>' );

		return $links;

	}

}

endif;

// initialize the plugin
AJAX_file_upload::init();


/**
  * Initialize the function ajax_file_upload( arguments )
  * and other functions
  */

add_action('init', 'ajax_file_upload_init_function');

function ajax_file_upload_init_function() {

	/**
	  * Use it in your template if you don't want to use the shortcode
	  * or do_shortcode in your coding.
	  * attributes this time can be inserted as array in first param
	  *
	  * @since 0.1
	  * @param $atts array attributes (optional)
	  * @return str render shortcode template
	  */

	if ( ! function_exists('ajax_file_upload') ) {
		function ajax_file_upload( $atts = array() ) {
			return AJAX_file_upload::shortcode( $atts, true );
		}
	}

	if ( ! function_exists('ajax_file_upload_settings') ) {
		function ajax_file_upload_settings() {
			$class = new AJAX_file_upload();
			return $class->settings;
		}
	}

}
