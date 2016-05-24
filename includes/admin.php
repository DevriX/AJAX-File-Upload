<?php

if ( ! class_exists('AJAX_file_upload_admin') ) :

class AJAX_file_upload_admin
{

	protected static $instance = null;

	public function __construct() {

		$this->to_translate = array( "Here's your uploaded media URI:", "Error occured while processing your file. Please try again.", "upload", "choose file", "remove", "Extension \"%s\" not allowed. Allowed extensions are \"%s\"", "Maximum file size \"%s\" KB breached. Your file size was \"%s\" KB", "You must be logged-in to upload", "Permissions don't allow you to upload", "Error occured while processing your file" );

		$AJAX_file_upload = new AJAX_file_upload();
		$this->translations = array();

		foreach( $this->to_translate as $i => $string ) {
			$this->translations[$i] = $AJAX_file_upload->translate( $string );
		}

	}

	public function _init() {
		add_action('admin_menu', array( &$this, 'admin_menu' ) );
		add_filter( "ajax_file_upload_settings", array( &$this, "filter_settings" ), 0 );
		add_filter( "admin_init", array( &$this, "redirect" ) );
	}

	public static function init() {
		$class = null == self::$instance ? new self : self::$instance;
		return $class->_init();
	}

	public function admin_menu() {
		add_options_page( 'AJAX file upload', 'AJAX file upload', 'manage_options', 'ajax-file-upload', array( &$this, 'screen' ) );
		add_submenu_page(
			'',
			"Welcome to AJAX File Upload",
			'',
			'manage_options',
			'afu-about',
			array( &$this, 'about_screen' )
		);
	}

	public function about_screen() {
		require 'afu-about.php';
	}

	public function update() {

		if( ! isset( $_POST['_afu_nonce'] ) || !wp_verify_nonce( $_POST['_afu_nonce'], '_afu_nonce' ) ) {
			return;
		}

		$settings = isset( $_POST["settings"] ) ? $_POST["settings"] : array();

		if( ! empty( $settings["max"] ) ) {
			if( (int) $settings["max"] > 0 ) {
				$settings["max"] = (int) $settings["max"];
			}
		}

		if( ! empty( $settings["ext"] ) ) {
			
			$settings["ext"] = explode( ",", $settings["ext"] );

			foreach( $settings["ext"] as $i => $ext ) {
				$settings["ext"][$i] = preg_replace('/\s+/', '', $ext);
			}

		}

		if( empty( $settings["permission"] ) ) {
			unset( $settings["permission"] );
		} else {
			$settings["permission"] = sanitize_text_field( $settings["permission"] );
		}

		update_option( "afu_settings", json_encode( $settings ) );

		if( ! empty( $_POST["translate"] ) ) {

			$object = array();
			foreach( $_POST["translate"] as $i => $string ) {
				$object[ $this->to_translate[$i] ] = sanitize_text_field( $string );
			}

			update_option( "afu_transaltions", base64_encode( json_encode( $object ) ) );

			wp_redirect( "options-general.php?page=ajax-file-upload&updated=true" );
			exit;
		}

		echo '<div id="updated" class="updated notice is-dismissible"><p>Settings saved successfully.</p></div>';

	}

	public function filter_settings( $settings ) {

		$meta = get_option( "afu_settings");

		if( $meta ) {

			$meta = json_decode( $meta );

			if( ! empty( $meta->max ) ) {
				$settings->max_size = (int) $meta->max;
			}

			if( ! empty( $meta->ext ) ) {
				$settings->extensions = (array) $meta->ext;
			}

			if( ! empty( $meta->permission ) ) {
				$settings->default_permission = (string) $meta->permission;
			}

		}

		return $settings;

	}

	public function screen() {

		$this->update();

		?>

			<div class="wrap">
			
				<h2 style="display:inline-block">AJAX file upload <a href="index.php?page=afu-about" class="page-title-action">Documentation</a></h2>

				<form method="post">
					
					<p>Hello there! The following are only default settings which can be totally ignored when you set the <a href="index.php?page=afu-about#afu-shortcode">shortcode attributes</a>. For example, you have <code>[ajax-file-upload unique_identifier=upload_cover_photo max_size=5000]</code> in this case the max. upload size will be the one set in the shortcode attribute which is 5000 kb (5 MB)</p>

					<p>In other words, these settings will be used only when they are not added to shortcode attribute or a <code>unique_identifier</code> attribute is not set. This attribute is used to get settings for the current shortcode from the database (security procedure, anyone can manipulate settings if you call them through the markup)</p>
					
					<p>
						<label>
							<strong style="font-size:110%;display:block;">Max. upload size: <a href="index.php?page=afu-about#max_size">?</a></strong>
							<input name="settings[max]" value="<?php echo ajax_file_upload_settings()->max_size; ?>" type="number" size="50" />
						</label>
					</p>

					<p>
						<label>
							<strong style="font-size:110%;display:block;">Allowed file extensions: <a href="index.php?page=afu-about#allowed_extensions">?</a></strong>
							<input name="settings[ext]" value="<?php echo implode( ", ", ajax_file_upload_settings()->extensions ); ?>" type="text" size="50" />
						</label>
					</p>

					<p>
						<label>
							<strong style="font-size:110%;display:block;">Default upload permission: <a href="index.php?page=afu-about#permissions">?</a></strong>

							This permission will be needed for a user to process the file upload.
							
							<br/><em>Allowed strings:</em>

							<br/>- <strong>custom user role</strong> ( e.g administrator, author ): requires a user to be logged-in and have this role in order to upload
							<br/>- <strong>logged_in</strong>: requires a user to be logged-in in order to upload
							<br/>- <strong>all</strong>: everyone can process a file upload, even logged-out users.
							<br/>

							<input name="settings[permission]" value="<?php echo ajax_file_upload_settings()->default_permission; ?>" type="text" size="50" />
						</label>
					</p>

					<p>
						
					</p>
						<strong style="font-size:110%;display:block;">Some basic optional translations:</strong><br/>

						<?php foreach( $this->to_translate as $i => $string ) : ?>

							<label style="display:block">
								<input type="text" name="translate[<?php echo $i; ?>]" value="<?php echo $string !== stripslashes($this->translations[$i]) ? stripslashes($this->translations[$i]) : ''; ?>" size="40" />
								<?php echo $string; ?>
							</label>

						<?php endforeach; ?>

					<p>
						<?php wp_nonce_field( '_afu_nonce', '_afu_nonce' ); ?>
						<?php submit_button(); ?>
					</p>

					<p>More: <a href="index.php?page=afu-about#js-events">AFU custom DOM events (JavaScript)</a> - <a href="index.php?page=afu-about#the-function">Using <code>ajax_file_upload()</code> function</a></p>


				</form>

			</div>

		<?php

	}

	public function redirect() {

		if( ! get_option( "afu_0.1_about_rdr" ) ) {
			update_option( "afu_0.1_about_rdr", time() );
			wp_redirect( "index.php?page=afu-about" );
			exit;
		}

	}


}

endif;

AJAX_file_upload_admin::init();