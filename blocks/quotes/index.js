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

	var ServerSideRender = wp.components.ServerSideRender;
	var InspectorControls = wp.editor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var SelectControl = wp.components.SelectControl;
	var RadioControl = wp.components.RadioControl;


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
				el( InspectorControls, {},
					el( PanelBody, { title: __('Filters'), },
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
					),
					el( PanelBody, { title: __('Sorting'), },
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
					),
				),
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
			return null;
		}
	} );
} )(
	window.wp
);
