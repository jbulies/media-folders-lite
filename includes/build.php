<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class MediaFoldersBuild {

	public function __construct() {
		// Set admin notice activation
		add_action( 'admin_notices', [$this, 'mediafolders_admin_notice'] );

		// Set plugin action links
		add_filter('plugin_action_links_' . MEDIAFOLDERS_PLUGIN_BASENAME, array($this, 'mediafolders_plugin_action_links'));

		// Set plugin row meta
		add_filter('plugin_row_meta', array($this, 'mediafolders_plugin_row_meta'), 10, 2);
		
		// Set admin footer text
		add_filter('admin_footer_text', array($this, 'mediafolders_admin_footer_text'), 1);

		// Add styles
		add_action('admin_enqueue_scripts', [$this, 'mediafolders_style'] );

		// Select option
		add_action( 'pre-upload-ui', [$this, 'mediafolders_select'] );

		// Media table
		add_filter( 'manage_media_columns', function( $columns ) {
			$columns['folder'] = __( 'Folder', MEDIAFOLDERS_TEXT_DOMAIN );
			return $columns;
		} );

		add_action( 'manage_media_custom_column', function( $column_name, $post_ID ) {
			if( 'folder' == $column_name ) {
				$file = wp_get_post_terms( $post_ID, 'mediafolders-taxonomy', array( 'fields' => 'names' ) );
				echo esc_html( implode( ' ', $file ) );
			}
		}, 10, 2 );

		// Register setting
		add_action( 'admin_init', [$this, 'mediafolders_register_setting'] );
	}

	function mediafolders_style() {
		wp_enqueue_style('admin-styles', MEDIAFOLDERS_PLUGIN_URL . '/assets/css/style.css');
	}

	function mediafolders_admin_notice() {
		// Check transient, if available display notice
		if( get_transient( 'admin-notice' ) ) {
			?>
			<div class="updated notice is-dismissible">
				<p>
					<?php _e( 'Thank you for choosing Media Folders Lite!', MEDIAFOLDERS_TEXT_DOMAIN ); ?>
					<strong>
						<?php _e( 'Configure your folders in Settings > Media', MEDIAFOLDERS_TEXT_DOMAIN ); ?>
					</strong>
				</p>
			</div>
			<?php
			// Delete transient, only display this notice once.
			delete_transient( 'admin-notice' );
		}
	}

	function mediafolders_plugin_action_links( $actions ) {

		$actions['settings'] = '<a href="options-media.php">' . __( 'Settings', MEDIAFOLDERS_TEXT_DOMAIN ) . '</a>';
		
		return $actions;
	}

	function mediafolders_plugin_row_meta( $plugin_meta, $plugin_file ) {

		if ( strpos( $plugin_file, MEDIAFOLDERS_PLUGIN_FILE_NAME ) !== false ) {
			$plugin_meta[] = sprintf(
				'<strong>%1$s %2$s</strong>',
				__( 'Please rate us', MEDIAFOLDERS_TEXT_DOMAIN ),
				'<a href="https://wordpress.org/support/plugin/media-folders-lite/reviews?rate=5#new-post" target="_blank" class="mediafolders-rating-link">&#9733;&#9733;&#9733;&#9733;&#9733;</a>'
			);
		}

		return $plugin_meta;
	}

	function mediafolders_admin_footer_text( $footer_text ) {
		// We are making sure that this message will be only on Options Media page
		if ( function_exists( 'get_current_screen' ) ) {
			$current_screen = get_current_screen();

			if ( $current_screen->id === 'options-media' ) {
				$footer_text = sprintf(
					__('If you like %1$s please leave us a %2$s rating. A huge thanks in advance!', MEDIAFOLDERS_TEXT_DOMAIN ),
					sprintf('<strong>%s</strong>', __( 'Media Folders Lite', MEDIAFOLDERS_TEXT_DOMAIN )),
					'<a href="https://wordpress.org/support/plugin/media-folders-lite/reviews?rate=5#new-post" target="_blank" class="mediafolders-rating-link">&#9733;&#9733;&#9733;&#9733;&#9733;</a>'
				);
			}
		}

		return $footer_text;
	}

	function mediafolders_register_setting() {
		register_setting(
			'media',
			'mediafolders_option',
			[
				'type' => 'string',
				'default' => NULL,
			]
		);

		add_settings_section(
			'mediafolders_section',
			__( 'Media Upload Folders', MEDIAFOLDERS_TEXT_DOMAIN ),
			'',
			'media'
		);

		add_settings_field(
			'folders',
			__( 'Define your folders', MEDIAFOLDERS_TEXT_DOMAIN ),
			array( $this, 'mediafolders_input_callback' ),
			'media',
			'mediafolders_section'
		);
	}
	
	function mediafolders_input_callback() {
		?>
		<p>
			<?php _e( 'Define your folders one per line.', MEDIAFOLDERS_TEXT_DOMAIN ); ?>
		</p>
		<p>
			<b> <?php _e( 'Example:', MEDIAFOLDERS_TEXT_DOMAIN ); ?> </b>
			<br> <?php _e( 'People', MEDIAFOLDERS_TEXT_DOMAIN ); ?>
			<br> <?php _e( 'People/Smiling', MEDIAFOLDERS_TEXT_DOMAIN ); ?>
		</p>
		<ul></ul>
		<textarea id="mediafolders_option" name="mediafolders_option" class="regular-text ltr" rows="7"><?php echo esc_textarea( get_option( 'mediafolders_option' ) ); ?></textarea>
		<ul></ul>
		<p>
			<b> <?php _e( 'Donate link:', MEDIAFOLDERS_TEXT_DOMAIN ); ?> </b>
			<a href="https://tppay.me/l2447ony"><?php _e( 'Thanks', MEDIAFOLDERS_TEXT_DOMAIN ); ?><a/>
		</p>
		<?php
	}

	function mediafolders_select() {
		$folders  = explode( "\r\n", esc_attr( get_option( 'mediafolders_option' ) ) ); ?>
		<h2 class="mediafolders-select-title">
			<?php _e( 'Select the folder', MEDIAFOLDERS_TEXT_DOMAIN ); ?>
		</h2>
		<select class="mediafolders-select-js" onchange="document.cookie='mediafolders_cookie=' + event.target.value + ';path=<?php echo esc_attr( COOKIEPATH ); ?>'">
			<option value="">
				<?php _e( '- Default', MEDIAFOLDERS_TEXT_DOMAIN ); ?>
			</option>
			<?php foreach ( $folders as $folder ) {
				$folder = trim( $folder );
				echo '<option value="' . esc_attr( $folder ) . '">' . esc_html( $folder ) . '</option>';
			} ?>
		</select>
		<img class="mediafolders-img-js" onload="var match = document.cookie.match( new RegExp( '(^| )mediafolders_cookie=([^;]+)' ) ); document.getElementsByClassName('mediafolders-select-js')[0].value = match ? match[2] : '';" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACwAAAAAAQABAAACAkQBADs="/>
		<ul></ul>
		<?php
	}
}

new MediaFoldersBuild;