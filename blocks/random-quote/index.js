( function( wp ) {
	/**
	 * Registers a new block provided a unique name and an object defining its behavior.
	 * @see https://github.com/WordPress/gutenberg/tree/master/blocks#api
	 */
	var registerBlockType = wp.blocks.registerBlockType;
	/**
	 * Returns a new element of given type. Element is an abstraction layer atop React.
	 * @see https://github.com/WordPress/gutenberg/tree/master/element#element
	 */
	var el = wp.element.createElement;
	/**
	 * Retrieves the translation of text.
	 * @see https://github.com/WordPress/gutenberg/tree/master/i18n#api
	 */
	var __ = wp.i18n.__;

	var AlignmentToolbar = wp.editor.AlignmentToolbar;
	var ContrastChecker = wp.editor.ContrastChecker;
	var InspectorControls = wp.editor.InspectorControls;
	var PanelColorSettings = wp.editor.PanelColorSettings;

	var CheckboxControl = wp.components.CheckboxControl;
	var PanelBody = wp.components.PanelBody;
	var PanelRow = wp.components.PanelRow;
	var RangeControl = wp.components.RangeControl;
	var ServerSideRender = wp.components.ServerSideRender;
	var TextControl = wp.components.TextControl;
	var ToggleControl = wp.components.ToggleControl;

	/**
	 * Every block starts by registering a new block type definition.
	 * @see https://wordpress.org/gutenberg/handbook/block-api/
	 */
	registerBlockType( 'quotes-collection/random-quote', {
		title: __( 'Random Quote', 'quotes-collection' ),
		icon: 'testimonial',
		category: 'quotes-collection',

		/**
		 * The edit function describes the structure of your block in the context of the editor.
		 * This represents what the editor will render when the block is used.
		 * @see https://wordpress.org/gutenberg/handbook/block-edit-save/#edit
		 *
		 * @param {Object} [props] Properties passed from the editor.
		 * @return {Element}       Element to render.
		 */
		edit: function( props ) {
			return [
				// onClick listener to ensure the links inside block don't misbehave
				el('div', { onClick: (e) => { e.preventDefault(); } },
					/**
					 * The ServerSideRender element uses the REST API to automatically
					 * call the render function in the PHP code whenever it needs to get
					 * an updated view of the block.
					 */
					el(
						ServerSideRender, {
							block: 'quotes-collection/random-quote',
							attributes: props.attributes,
						}
					),
				),

				// InspectorControls lets you add controls to the Block sidebar
				el( InspectorControls, {},

					el( PanelColorSettings, {
							title: __('Presentation', 'quotes-collection'),
							initialOpen: false,
							colorSettings: [
								{
									value: props.attributes.backgroundColor,
									onChange: (color) => { props.setAttributes( { backgroundColor: (color) ? color: '' } ); },
									label: __('Background Color', 'quotes-collection'),
								},
								{
									value: props.attributes.textColor,
									onChange: (color) => { props.setAttributes( { textColor: (color) ? color: '' } ); },
									label: __('Text Color', 'quotes-collection'),
								},
							],
						},
						el( ContrastChecker, {
							textColor: props.attributes.textColor,
							backgroundColor: props.attributes.backgroundColor,
						}),
						el( PanelRow, {},
							el( 'label', {}, __('Text Align', 'quotes-collection') ),
							el( AlignmentToolbar, {
								value: props.attributes.textAlign,
								onChange: (alignment) => { props.setAttributes( { textAlign: alignment } ); },
							}),
						), // </PanelRow>
						el( PanelRow, {},
							el( 'label', {}, __('Attribution Align', 'quotes-collection') ),
							el( AlignmentToolbar, {
								value: props.attributes.attributionAlign,
								onChange: (alignment) => { props.setAttributes( { attributionAlign: alignment } ); },
							}),
						), // </PanelRow>
						el( 'div', {
							style: { marginTop: '20px' }
						},
							el( CheckboxControl, {
								label: __('Fixed Height', 'quotes-collection'),
								checked: props.attributes.fixedHeight,
								onChange: (state) => { props.setAttributes( { fixedHeight: state } ); },
							}),
							el( RangeControl, {
								// label: __('Height'),
								min: 75,
								max: 500,
								step: 5,
								value: props.attributes.height,
								onChange: ( value ) => {
									if( isNaN( parseInt(value) ) || value < 0 ) {
										props.setAttributes( { height: 50 } );
									} else {
										props.setAttributes( { height: value } );
									}
								},
							}),
						), // </div>
					), // </PanelColorSettings>

					el( PanelBody, { title: __('Content Settings', 'quotes-collection'), initialOpen: false },
						el( CheckboxControl, {
							label: __('Show Author'),
							checked: props.attributes.showAuthor,
							onChange: (state) => { props.setAttributes( { showAuthor: state } ); },
						} ),
						el( CheckboxControl, {
							label: __('Show Source'),
							checked: props.attributes.showSource,
							onChange: (state) => { props.setAttributes( { showSource: state } ); },
						} ),
						el( TextControl, {
							label: __('Filter by Tags', 'quotes-collection'),
							help: __('Comma separated', 'quotes-collection'),
							value: props.attributes.tags,
							onChange: ( value ) => { props.setAttributes( { tags: value } ); },
						} ),
						el( RangeControl, {
							label: __('Character Limit', 'quotes-collection'),
							help: __('Total number of characters including white spaces. Larger quotes are ignored.', 'quotes-collection'),
							min: 100,
							max: 2500,
							step: 100,
							value: props.attributes.charLimit,
							onChange: ( value ) => {
								if( isNaN( parseInt(value) ) || value < 0 ) {
									props.setAttributes( { charLimit: 500 } );
								} else {
									props.setAttributes( { charLimit: value } );
								}
							},
						}),
					), // </PanelBody>

					el( PanelBody, { title: __('Refresh Settings', 'quotes-collection'), initialOpen: false },
						el( ToggleControl, {
							label: __('Random Refresh', 'quotes-collection'),
							checked: props.attributes.randomRefresh,
							onChange: ( state ) => { props.setAttributes( { randomRefresh: state } ); },
						} ),
						el( ToggleControl, {
							label: __('Auto Refresh', 'quotes-collection'),
							checked: props.attributes.autoRefresh,
							onChange: ( state ) => { props.setAttributes( { autoRefresh: state } ); },
						} ),
						el( RangeControl, {
							label: __('Refresh Interval', 'quotes-collection'),
							help: __('For auto refresh. In seconds.', 'quotes-collection'),
							min: 3,
							max: 60,
							value: props.attributes.refreshInterval,
							onChange: ( value ) => {
								if( isNaN( parseInt(value) ) || value < 0 ) {
									props.setAttributes( { refreshInterval: 5 } );
								} else {
									props.setAttributes( { refreshInterval: value } );
								}
							},
						}),

					),

				), // </InspectorControls>


			];
		},

		/**
		 * The save function defines the way in which the different attributes should be combined
		 * into the final markup, which is then serialized by Gutenberg into `post_content`.
		 * @see https://wordpress.org/gutenberg/handbook/block-edit-save/#save
		 *
		 * @return {Element}       Element to render.
		 */
		save: function() {
			// We're going to be rendering in PHP, so save() can just return null.
			return null;
		}
	} );
} )(
	window.wp
);
