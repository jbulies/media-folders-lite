<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class MediaFoldersTaxonomy {

	public function __construct() {
		// Register new taxonomy to apply to attachments
		add_action( 'init', [$this, 'mediafolders_add_taxonomy'] );

		// Set term on upload
		add_action('add_attachment', function($post_id) {

			$selected_folder = sanitize_text_field( filter_input( INPUT_COOKIE, 'mediafolders_cookie' ) );

			$default = __( '- Default', MEDIAFOLDERS_TEXT_DOMAIN );

			if ( empty ( $selected_folder ) ) {
				wp_set_post_terms( $post_id, $default, 'mediafolders-taxonomy' );
			}
			else {
				wp_set_post_terms( $post_id, $selected_folder, 'mediafolders-taxonomy' );
			}

		} );

		// Fix default term
		add_action( 'admin_init', [$this, 'mediafolders_fix_default_term'] );

		// Remove unused term
		add_action( 'admin_init', [$this, 'mediafolders_remove_unused_term'] );
	}

	// Register new taxonomy to apply to attachments
	function mediafolders_add_taxonomy() {

		$args = array(
			'public' => false,
			'show_ui' => false,
			'show_in_rest' => false,
			'show_admin_column' => false,
			'rewrite' => false,
			'update_count_callback' => '_update_generic_term_count'
		);

		register_taxonomy( 'mediafolders-taxonomy', 'attachment', $args );
	}

	// Remove unused term
	function mediafolders_remove_unused_term() {

		$terms = get_terms( [
			'taxonomy'		=> 'mediafolders-taxonomy',
			'hide_empty'	=> false,
		] );

		foreach ( $terms as $term ) {
			if ( 0 === $term->count ) {
				wp_delete_term( $term->term_id, 'mediafolders-taxonomy' );
			}
		}
	}

	// In Media Folders Lite, the WordPress localization API is implemented to perform the translations,
	// but what would happen if the user downloads the plugin without translations, uses it and then downloads the translations?
	// The result would be that the user would have two terms (- Default) one translated and one not.
	// For a specific case like that we apply the following correction.
	function mediafolders_fix_default_term() {

		if ( get_locale() !== 'en_*') {

			$default = __( '- Default', MEDIAFOLDERS_TEXT_DOMAIN );
			$term_default = term_exists( '- Default', 'mediafolders-taxonomy' );
			$term_translated = term_exists( $default, 'mediafolders-taxonomy' );

			if ( $term_default !== 0 && $term_default !== null && $term_translated !== 0 && $term_translated !== null ) {

				if ( $default !== '- Default' ) {

					$posts = get_posts( array(
						'post_type' => 'attachment',
						'numberposts' => -1,
						'tax_query' => array(
							array(
								'taxonomy' => 'mediafolders-taxonomy',
								'field' => 'name', 
								'terms' => '- Default',
							)
						)
					));

					if ( !empty ( $posts ) ) {

						foreach( $posts as $post ) {
							wp_set_post_terms( $post->ID, $default, 'mediafolders-taxonomy' );
						}

					}
				}
			}
		}
	}
}

new MediaFoldersTaxonomy;