/**
 * Copyright (c) 2014, CKSource - Frederico Knabben. All rights reserved.
 * Licensed under the terms of the MIT License (see LICENSE.md).
 *
 * The abbr plugin dialog window definition.
 *
 * Created out of the CKEditor Plugin SDK:
 * http://docs.ckeditor.com/#!/guide/plugin_sdk_sample_1
 */

// Our dialog definition.
CKEDITOR.dialog.add( 'abbrDialog', function( editor ) {
	return {

		// Basic properties of the dialog window: title, minimum size.
		title: 'Video Properties',
		minWidth: 400,
		minHeight: 200,

		// Dialog window content definition.
		contents: [
			{
				// Definition of the Basic Settings dialog tab (page).
				id: 'tab-basic',
				label: 'Basic Settings',

				// The tab content.
				elements: [
					{
						// Text input field for the abbreviation text.
						type: 'text',
						id: 'video_url',
						label: 'Video URL',

						// Validation checking whether the field is not empty.
						validate: CKEDITOR.dialog.validate.notEmpty( "Video Url field cannot be empty." ),

						// Called by the main setupContent method call on dialog initialization.
						setup: function( element ) {
							this.setValue( element.getText() );
						},

						// Called by the main commitContent method call on dialog confirmation.
						commit: function( element ) {
							element.setText( this.getValue() );
						}
					},
					{
						// Text input field for the abbreviation title (explanation).
						type: 'text',
						id: 'poster_url',
						label: 'Video Poster URL',

						// Called by the main setupContent method call on dialog initialization.
						setup: function( element ) {
							this.setValue( element.getAttribute(  ) );
						},

						// Called by the main commitContent method call on dialog confirmation.
						commit: function( element ) {
							element.setAttribute();
						}
					},
					{
						// Text input field for the abbreviation title (explanation).
						type: 'text',
						id: 'video_id',
						label: 'Video ID',
						validate: CKEDITOR.dialog.validate.notEmpty( "Video Id field cannot be empty. Please enter random number" ),

						// Called by the main setupContent method call on dialog initialization.
						setup: function( element ) {
							this.setValue( element.getAttribute(  ) );
						},

						// Called by the main commitContent method call on dialog confirmation.
						commit: function( element ) {
							element.setAttribute();
						}
					}
				]
			}
		],

		// Invoked when the dialog is loaded.
		onShow: function() {

			// Get the selection from the editor.
			var selection = editor.getSelection();

			// Get the element at the start of the selection.
			var element = selection.getStartElement();

			// Get the <abbr> element closest to the selection, if it exists.
			if ( element )
				element = element.getAscendant( 'abbr', true );

			// Create a new <abbr> element if it does not exist.
			if ( !element || element.getName() != 'abbr' ) {
				element = editor.document.createElement( 'abbr' );

				// Flag the insertion mode for later use.
				this.insertMode = true;
			}
			else
				this.insertMode = false;

			// Store the reference to the <abbr> element in an internal property, for later use.
			this.element = element;

			// Invoke the setup methods of all dialog window elements, so they can load the element attributes.
			if ( !this.insertMode )
				this.setupContent( this.element );
		},

		// This method is invoked once a user clicks the OK button, confirming the dialog.
		onOk: function() {

			// The context of this function is the dialog object itself.
			// http://docs.ckeditor.com/#!/api/CKEDITOR.dialog
			var dialog = this;

			// Create a new <abbr> element.
			var abbr = this.element;


			var content = '';
			content += '<video id="'+dialog.getValueOf( 'tab-basic', 'video_id' )+'" class="video-js vjs-default-skin" controls';
			content += ' preload="auto" width="auto" height="auto" poster="'+dialog.getValueOf( 'tab-basic', 'poster_url' )+'"';
			content += ' data-setup="{}">';
			content += '<source src="'+dialog.getValueOf( 'tab-basic', 'video_url' )+'" type="video/mp4">';
			content += '</video>';

			var element = CKEDITOR.dom.element.createFromHtml( content );
			var instance = this.getParentEditor();
			instance.insertElement(element);
		}
	};
});
