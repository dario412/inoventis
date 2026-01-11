/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { useEntityProp } from '@wordpress/core-data';
import { PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

registerBlockType( 'inoventis/product-related-products', {
	edit: ( { attributes, setAttributes, context } ) => {
		const blockProps = useBlockProps( {
			className: 'product-related-products-editor',
		} );
		
		const postType = context?.postType || 'product';
		const postId = context?.postId;
		
		const [ meta, setMeta ] = useEntityProp( 'postType', postType, 'meta', postId );
		const title = ( meta && meta._product_related_products_title ) ? meta._product_related_products_title : 'Слични производи';

		const updateTitle = ( value ) => {
			const updatedMeta = { ...( meta || {} ) };
			updatedMeta._product_related_products_title = value;
			setMeta( updatedMeta );
		};

		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Related Products Settings', 'inoventis' ) }>
						<TextControl
							label={ __( 'Block Title', 'inoventis' ) }
							value={ title }
							onChange={ updateTitle }
							placeholder={ __( 'Слични производи', 'inoventis' ) }
							_next40pxDefaultSize={ true }
							_nextHasNoMarginBottom={ true }
						/>
						<p style={ { fontSize: '0.875rem', color: '#666', marginTop: '0.5rem' } }>
							{ __( 'Products will be automatically fetched from the same subcategory or category. You can reorder and select specific products in the product edit page.', 'inoventis' ) }
						</p>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<div className="related-products-editor-header">
						<h3>{ title || __( 'Слични производи', 'inoventis' ) }</h3>
					</div>
					<p className="related-products-editor-description">
						{ __( 'Related products will be displayed here. They are automatically fetched from the same subcategory or category, or you can manually select them in the product settings.', 'inoventis' ) }
					</p>
				</div>
			</>
		);
	},

	save: () => {
		return null; // Dynamic block
	},
} );

