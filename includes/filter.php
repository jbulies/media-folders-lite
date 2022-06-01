<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class MediaFoldersFilter {

	public function __construct() {
		// Add media filter script.
		add_action( 'wp_enqueue_media', function() {
			wp_enqueue_script( 'media-library-taxonomy-filter', MEDIAFOLDERS_PLUGIN_URL . 'assets/js/filter.js', [
				'media-editor',
				'media-views',
			] );
			// Load 'terms' into a JavaScript variable that filter.js has access to.
			wp_localize_script( 'media-library-taxonomy-filter', 'MediaLibraryTaxonomyFilterData', [
				'filter' => [
					'mediafolders-taxonomy'	=> [
						'data'	=> get_terms( 'mediafolders-taxonomy', [ 'hide_empty' => false ] ),
						'label'	=> __( 'All folders', MEDIAFOLDERS_TEXT_DOMAIN ),
					],
				],
			] );
		} );
	}
}

new MediaFoldersFilter;