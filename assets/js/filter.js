/* global MediaLibraryTaxonomyFilterData, _ */
( function () {
	const filter = {
		initialize: function () {
			this.setupFilter();
		},
		createFilter: function ( item, slug ) {
			return wp.media.view.AttachmentFilters.extend( {
				id: 'media-attachment-taxonomy-filter-' + slug,

				createFilters: function () {
					let filters = {};
					// Formats the 'terms' we've included via wp_localize_script().
					_.each( item.data || {}, function ( value, index ) {
						filters[index] = {
							text: value.name,
							props: {
								// Key needs to be the WP_Query var for the taxonomy.
								[slug]: value.slug
							}
						};
					} );
					filters.all = {
						text: item.label,
						props: {
							// Key needs to be the WP_Query var for the taxonomy.
							[slug]: ''
						},
						priority: 10
					};
					this.filters = filters;
				}
			} );
		},
		setupFilter: function () {
			/**
			 * Create a new instance of array we later will instantiate
			 */
			const filterList = {};
			for ( const slug in MediaLibraryTaxonomyFilterData.filter ) {
				const key = slug.charAt( 0 ).toUpperCase() + slug.slice( 1 );
				filterList[`MediaLibraryFilter${key}`] = this.createFilter( MediaLibraryTaxonomyFilterData.filter[slug], slug );
			}
			/**
			 * Extend and override wp.media.view.AttachmentsBrowser to include our new filter
			 */
			let AttachmentsBrowser = wp.media.view.AttachmentsBrowser;
			wp.media.view.AttachmentsBrowser = wp.media.view.AttachmentsBrowser.extend( {
				createToolbar: function () {
					// Make sure to load the original toolbar.
					AttachmentsBrowser.prototype.createToolbar.call( this );
					// Setup our filters from array.
					for ( const key in filterList ) {
						this.toolbar.set( key, new filterList[key]( {
							controller: this.controller,
							model: this.collection.props,
							priority: -75
						} ).render() );
					}
				}
			} );
		}
	};

	filter.initialize();
} )();