window.addEventListener( 'load', function() {
	var registerPlugin = wp.plugins.registerPlugin;
	var PluginDocumentSettingPanel = wp.editPost.PluginDocumentSettingPanel;
	var Button = wp.components.Button;
	var el = wp.element.createElement;

	var MediaFolders = {
		textContent: 'Select the folder where you want to save the images or files that you upload to this post.',
		textButton: 'Select Folder',
		...window.MediaFolders
	};

	function onButtonClick() {
		var popup = document.querySelector( '.mediafolders-popup' );
		popup.classList.add( 'open' );
	}

	// Create elements for panel on sidebar
	function MediaFoldersDocumentSetting() {
		return el(

			PluginDocumentSettingPanel,
			{
				name: 'media-folders-lite',
				title: 'Media Folders Lite',
				icon: 'open-folder',
				initialOpen: true,
			},
			MediaFolders.textContent,
			
			el(
				Button,
				{
					variant: 'secondary',
					className: 'mediafolders-panel-button',
					onClick: onButtonClick,
				},
				MediaFolders.textButton,
			),

		);
	}

	registerPlugin( 'media-folders-lite', {
		render: MediaFoldersDocumentSetting,
	} );

	// Close the pop up by clicking on the X or Done!
	var btnClosePopup1 = document.querySelector( '.mediafolders-popup-close' );
	var btnClosePopup2 = document.querySelector( '.mediafolders-popup-button > button' );
	if ( btnClosePopup1 ) {
		btnClosePopup1.addEventListener( 'click', () => {
			var popup = document.querySelector( '.mediafolders-popup' );
			popup.classList.remove( 'open' );
		});
	}
	if ( btnClosePopup2 ) {
		btnClosePopup2.addEventListener( 'click', () => {
			var popup = document.querySelector( '.mediafolders-popup' );
			popup.classList.remove( 'open' );
		});
	}
});