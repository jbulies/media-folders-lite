<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class MediaFolders {

	public function __construct() {
		// Before upload
		add_filter( 'wp_handle_upload_prefilter', function( $file ) {
			add_filter( 'upload_dir', [$this, 'mediafolders_upload_dir'] );

			return $file;
		} );
		// After upload
		add_filter( 'wp_handle_upload', function( $file ) {
			remove_filter( 'upload_dir', [$this, 'mediafolders_upload_dir'] );

			return $file;
		} );
		// Create folders
		add_action( 'update_option_mediafolders_option', function() {
			$folders = explode( "\r\n", esc_attr( get_option( 'mediafolders_option' ) ) );
			$upload = wp_upload_dir();

			foreach( $folders as $folder ) {
				$upload_dir = $upload['basedir'];
				$upload_dir = $upload_dir . '/' . $folder;
				if ( !file_exists( $upload_dir ) ) {
					mkdir( $upload_dir, 0777, true );
				}
			}
		} );
	}

	public function mediafolders_upload_dir( $dirs ) {
		$selected_folder = sanitize_text_field( filter_input( INPUT_COOKIE, 'mediafolders_cookie' ) );

		if ( empty( $selected_folder ) ) {
			return $dirs;
		}

		$folders = explode( "\r\n", esc_attr( get_option( 'mediafolders_option' ) ) );

		if ( in_array( $selected_folder, $folders ) ) {
			$dirs['subdir'] = '/' . $selected_folder;
			$dirs['path'] = $dirs['basedir'] . '/' . $selected_folder;
			$dirs['url'] = $dirs['baseurl'] . '/' . $selected_folder;
		}

		return $dirs;
	}
}

new MediaFolders;