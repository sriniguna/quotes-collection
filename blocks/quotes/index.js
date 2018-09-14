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
	var BlockControls = wp.editor.BlockControls;
	var ContrastChecker = wp.editor.ContrastChecker;
	var InspectorControls = wp.editor.InspectorControls;
	var PanelColorSettings = wp.editor.PanelColorSettings;

	var CheckboxControl = wp.components.CheckboxControl;
	var PanelBody = wp.components.PanelBody;
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
		/**
		 * This is the display title for your block, which can be translated with `i18n` functions.
		 * The block inserter will show this name.
		 */
		title: __( 'Quotes Collection' ),

		icon: 'testimonial',

		/**
		 * Blocks are grouped into categories to help users browse and discover them.
		 * The categories provided by core are `common`, `embed`, `formatting`, `layout` and `widgets`.
		 */
		category: 'widgets',


		/**
		 * Optional block extended support features.
		 */
		supports: {
			// Removes support for an HTML mode.
			html: false,
		},

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
				el(
					ServerSideRender, {
						block: 'quotes-collection/quotes',
						attributes: props.attributes,
					}
				),
				el( BlockControls, {},
					el( AlignmentToolbar, {
						value: props.attributes.textAlign,
						onChange: ( value ) => { props.setAttributes( { textAlign: value } ); },
					}),
				),
				el( InspectorControls, {},
					el( PanelBody, { title: __('Filters'), initialOpen: false },
						el( TextControl, {
							label: __('Author'),
							value: props.attributes.author,
							onChange: ( value ) => { props.setAttributes( { author: value } ); },
						} ),
						el( TextControl, {
							label: __('Source'),
							value: props.attributes.source,
							onChange: ( value ) => { props.setAttributes( { source: value } ); },
						} ),
						el( TextControl, {
							label: __('Tags'),
							help: __('Comma separated'),
							value: props.attributes.tags,
							onChange: ( value ) => { props.setAttributes( { tags: value } ); },
						} ),
						el( TextControl, {
							label: __('Limit'),
							help: __('The maximum number of quotes to be displayed on a single page, i.e., when paging is off.'),
							type: 'number',
							min: 1,
							max: 100,
							value: props.attributes.limit,
							onChange: ( value ) => { props.setAttributes( { limit: value } ); },
						}),
					), // </PanelBody>
					el( PanelBody, { title: __('Sorting'), initialOpen: false },
						el( SelectControl, {
							label: __('Order by'),
							value: props.attributes.orderby,
							onChange: ( value ) => { props.setAttributes( { orderby: value } ); },
							options: [
								{ value: 'quote_id', label: __('Quote ID')},
								{ value: 'author', label: __('Author')},
								{ value: 'source', label: __('Source')},
								{ value: 'time_added', label: __('Time Added')},
								{ value: 'random', label: __('Random')},
							]
						} ),
						el( RadioControl, {
							label: __('Order'),
							selected: props.attributes.order,
							onChange: ( option ) => { props.setAttributes( { order: option } ); },
							options: [
								{ label: __('Ascending'), value: 'ASC' },
								{ label: __('Descending'), value: 'DESC' },
							],
						} ),
					), // </PanelBody>
					el( PanelBody, { title: __('Paging'), initialOpen: false },
						el( ToggleControl, {
							label: __('Paging'),
							checked: props.attributes.paging,
							onChange: ( state ) => { props.setAttributes( { paging: state } ); },
						} ),
						el( TextControl, {
							label: __('Limit per page'),
							help: __('The maximum number of quotes to be displayed per page.'),
							type: 'number',
							min: 1,
							max: 100,
							value: props.attributes.limit_per_page,
							onChange: ( value ) => { props.setAttributes( { limit_per_page: value } ); },
						}),
					), // </PanelBody>
					el( PanelColorSettings, {
							title: __('Color Settings'),
							initialOpen: false,
							colorSettings: [
								{
									value: props.attributes.backgroundColor,
									onChange: (color) => { props.setAttributes( { backgroundColor: (color) ? color: '' } ); },
									label: __('Background Color'),
								},
								{
									value: props.attributes.textColor,
									onChange: (color) => { props.setAttributes( { textColor: (color) ? color: '' } ); },
									label: __('Text Color'),
								},
							],
						},
						el( ContrastChecker, {
							textColor: props.attributes.textColor,
							backgroundColor: props.attributes.backgroundColor,
						}),
					), // </PanelColorSettings>
					el( PanelBody, { title: __('Attribution Settings'), initialOpen: false, },
						el( CheckboxControl, {
							label: __('Show author'),
							checked: props.attributes.showAuthor,
							onChange: (state) => { props.setAttributes( { showAuthor: state } ); },
						} ),
						el( CheckboxControl, {
							label: __('Show source'),
							checked: props.attributes.showSource,
							onChange: (state) => { props.setAttributes( { showSource: state } ); },
						} ),
						el( 'div', {},
						 	el( 'label', {}, __('Attribution Alignment') ),
							el( AlignmentToolbar, {
								title: __('Attribution Alignment'),
								value: props.attributes.attributionAlign,
								onChange: (alignment) => { props.setAttributes( { attributionAlign: alignment } ); },
							}),
						), // </div>
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
			return null;
		}
	} );
} )(
	window.wp
);
