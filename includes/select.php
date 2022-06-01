<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class MediaFoldersSelect {

	public function __construct() {
		// Register script
		add_action( 'init', [$this, 'mediafolders_sidebar_plugin_register'] );

		// Enqueue script
		add_action( 'enqueue_block_editor_assets', [$this, 'mediafolders_sidebar_plugin_script_enqueue'] );

		// Load html template of the pop up in footer
		add_action( 'admin_footer', [$this, 'mediafolders_insert_popup'] );
	}

	// Register script
	function mediafolders_sidebar_plugin_register() {
		wp_register_script(
			'mediafolders-plugin-select-js',
			MEDIAFOLDERS_PLUGIN_URL . 'assets/js/select.js',
			array( 'wp-plugins', 'wp-edit-post', 'wp-element' )
		);
		$translation_array = array(
			'textContent' => __( 'Select the folder where you want to save the images or files that you upload to this post.', MEDIAFOLDERS_TEXT_DOMAIN ),
			'textButton' => __( 'Select Folder', MEDIAFOLDERS_TEXT_DOMAIN ),
		);
		wp_localize_script( 'mediafolders-plugin-select-js', 'MediaFolders', $translation_array );
	}

	// Enqueue script
	function mediafolders_sidebar_plugin_script_enqueue() {
		wp_enqueue_script( 'mediafolders-plugin-select-js' );
	}

	// Pop up html template
	function mediafolders_insert_popup() {
		$folders  = explode( "\r\n", esc_attr( get_option( 'mediafolders_option' ) ) );
		?>
		<div class="mediafolders-popup">
			<div class="mediafolders-popup-inner">
				<span class="mediafolders-popup-close">&times;</span>
				<h2 class="mediafolders-popup-title">
					<?php _e( 'Select the folder', MEDIAFOLDERS_TEXT_DOMAIN ); ?>
				</h2>
				<p>
					<?php _e( 'Select the folder where you want to save your image or file', MEDIAFOLDERS_TEXT_DOMAIN ); ?>
				</p>
				<select class="mediafolders-popup-select" onchange="document.cookie='mediafolders_cookie=' + event.target.value + ';path=<?php echo esc_attr( COOKIEPATH ); ?>'">
					<option value="">
						<?php _e( '- Default', MEDIAFOLDERS_TEXT_DOMAIN ); ?>
					</option>
					<?php foreach ( $folders as $folder ) {
						$folder = trim( $folder );
						echo '<option value="' . esc_attr( $folder ) . '">' . esc_html( $folder ) . '</option>';
					} ?>
				</select>
				<div class="mediafolders-popup-button">
					<button type="button">
						<?php _e( 'Done!', MEDIAFOLDERS_TEXT_DOMAIN ); ?>
					</button>
				</div>
				<img class="mediafolders-img-js" onload="var match = document.cookie.match( new RegExp( '(^| )mediafolders_cookie=([^;]+)' ) ); document.getElementsByClassName('mediafolders-popup-select')[0].value = match ? match[2] : '';" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACwAAAAAAQABAAACAkQBADs="/>
			</div>
		</div>
		<?php
	}
}

new MediaFoldersSelect;