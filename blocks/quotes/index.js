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
	var RadioControl = wp.components.RadioControl;
	var SelectControl = wp.components.SelectControl;
	var ServerSideRender = wp.components.ServerSideRender;
	var TextControl = wp.components.TextControl;
	var ToggleControl = wp.components.ToggleControl;


	/**
	 * Every block starts by registering a new block type definition.
	 * @see https://wordpress.org/gutenberg/handbook/block-api/
	 */
	registerBlockType( 'quotes-collection/quotes', {
		title: __( 'Quotes', 'quotes-collection' ),
		icon: 'testimonial',
		category: 'quotes-collection',

		transforms: {
			from: [
				{
					type: 'shortcode',
					tag: 'quotcoll',
					attributes: {
						author: {
							type: 'string',
							shortcode: function( _ref ) {
								if( _ref.named.author === undefined )
									return;
								return _ref.named.author;
							},
						},
						source: {
							type: 'string',
							shortcode: function( _ref ) {
								if( _ref.named.source === undefined )
									return;
								return _ref.named.source;
							}
						},
						tags: {
							type: 'string',
							shortcode: function( _ref ) {
								if( _ref.named.source === undefined )
									return;
								return _ref.named.source;
							}
						},
						orderby: {
							type: 'string',
							shortcode: function( _ref ) {
								if( _ref.named.orderby === undefined )
									return;
								return _ref.named.orderby;
							}
						},
						order: {
							type: 'string',
							shortcode: function( _ref ) {
								if( _ref.named.order === undefined )
									return;
								return _ref.named.order;
							}
						},
						paging: {
							type: 'boolean',
							shortcode: function( _ref ) {
								if( (paging = _ref.named.paging) === undefined )
									return;
								else if( !paging || paging == "0" || paging == "false" )
									return false;
								return true;
							}
						},
						limit_per_page: {
							type: 'number',
							shortcode: function( _ref ) {
								if( _ref.named.limit_per_page === undefined )
									return;
								return parseInt( _ref.named.limit_per_page, 10 );
							}
						},
						limit: {
							type: 'number',
							shortcode: function( _ref ) {
								if( _ref.named.limit === undefined )
									return;
								return parseInt( _ref.named.limit, 10 );
							}
						},
						showAuthor: {
							type: 'boolean',
							shortcode: function( _ref ) {
								if( (show_author = _ref.named.show_author) === undefined )
									return;
								else if( !show_author || show_author == "0" || show_author == "false" )
									return false;
								return true;
							}
						},
						showSource: {
							type: 'boolean',
							shortcode: function( _ref ) {
								if( (show_source = _ref.named.show_source) === undefined )
									return;
								else if( !show_source || show_source == "0" || show_source == "false" )
									return false;
								return true;
							}
						},
					},
				},
			]
		},


		/**
		 * The edit function describes the structure of the block in the context of the editor.
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
							block: 'quotes-collection/quotes',
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
					), // </PanelColorSettings>

					el( PanelBody, { title: __('Content Settings', 'quotes-collection'), initialOpen: false },
						el( CheckboxControl, {
							label: __('Show Author', 'quotes-collection'),
							checked: props.attributes.showAuthor,
							onChange: (state) => { props.setAttributes( { showAuthor: state } ); },
						} ),
						el( CheckboxControl, {
							label: __('Show Source', 'quotes-collection'),
							checked: props.attributes.showSource,
							onChange: (state) => { props.setAttributes( { showSource: state } ); },
						} ),
						el( TextControl, {
							label: __('Filter by Author', 'quotes-collection'),
							value: props.attributes.author,
							onChange: ( value ) => { props.setAttributes( { author: value } ); },
						} ),
						el( TextControl, {
							label: __('Filter by Source', 'quotes-collection'),
							value: props.attributes.source,
							onChange: ( value ) => { props.setAttributes( { source: value } ); },
						} ),
						el( TextControl, {
							label: __('Filter by Tags', 'quotes-collection'),
							help: __('Comma separated', 'quotes-collection'),
							value: props.attributes.tags,
							onChange: ( value ) => { props.setAttributes( { tags: value } ); },
						} ),
						el( TextControl, {
							label: __('Limit', 'quotes-collection'),
							help: __('The maximum number of quotes to be displayed. Ignored when paging is on. A value of \"0\" implies no limits.', 'quotes-collection'),
							type: 'number',
							min: 0,
							max: 100,
							value: props.attributes.limit,
							onChange: ( value ) => {
								if( isNaN( parseInt(value) ) || value < 0 ) {
									props.setAttributes( { limit: 0 } );
								} else {
									props.setAttributes( { limit: value } );
								}
							},
						}),
					), // </PanelBody>

					el( PanelBody, { title: __('Sorting', 'quotes-collection'), initialOpen: false },
						el( SelectControl, {
							label: __('Order by', 'quotes-collection'),
							value: props.attributes.orderby,
							onChange: ( value ) => { props.setAttributes( { orderby: value } ); },
							options: [
								{ value: 'quote_id', label: __('Quote ID', 'quotes-collection')},
								{ value: 'author', label: __('Author', 'quotes-collection')},
								{ value: 'source', label: __('Source', 'quotes-collection')},
								{ value: 'time_added', label: __('Time Added', 'quotes-collection')},
								{ value: 'random', label: __('Random', 'quotes-collection')},
							]
						} ),
						el( RadioControl, {
							label: __('Order', 'quotes-collection'),
							selected: props.attributes.order,
							onChange: ( option ) => { props.setAttributes( { order: option } ); },
							options: [
								{ label: __('Ascending', 'quotes-collection'), value: 'ASC' },
								{ label: __('Descending', 'quotes-collection'), value: 'DESC' },
							],
						} ),
					), // </PanelBody>

					el( PanelBody, { title: __('Paging', 'quotes-collection'), initialOpen: false },
						el( ToggleControl, {
							label: __('Paging', 'quotes-collection'),
							checked: props.attributes.paging,
							onChange: ( state ) => { props.setAttributes( { paging: state } ); },
						} ),
						el( TextControl, {
							label: __('Limit per page', 'quotes-collection'),
							help: __('The maximum number of quotes to be displayed per page. Ignored when paging is off.', 'quotes-collection'),
							type: 'number',
							min: 1,
							max: 100,
							value: props.attributes.limit_per_page,
							onChange: ( value ) => {
								if( isNaN( parseInt(value) ) || value < 1 ) {
									props.setAttributes( { limit_per_page: 10 } );
								} else {
									props.setAttributes( { limit_per_page: value } );
								}
							},
						}),
					), // </PanelBody>

				), // </InspectorControls>
			]; // return
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
