/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText } from '@wordpress/block-editor';
import { useEntityProp } from '@wordpress/core-data';
import { __ } from '@wordpress/i18n';

registerBlockType( 'inoventis/product-short-description', {
	edit: ( { attributes, setAttributes, context } ) => {
		const blockProps = useBlockProps();
		const postType = context?.postType || 'product';
		const postId = context?.postId;
		
		const [ meta, setMeta ] = useEntityProp( 'postType', postType, 'meta', postId );
		const content = ( meta && meta._product_short_description ) ? meta._product_short_description : '';

		const updateContent = ( value ) => {
			const updatedMeta = meta || {};
			updatedMeta._product_short_description = value;
			setMeta( updatedMeta );
		};

		return (
			<div { ...blockProps }>
				<div className="product-short-description-editor">
					<RichText
						tagName="div"
						value={ content }
						onChange={ updateContent }
						placeholder={ __( 'Enter product short description...', 'inoventis' ) }
						allowedFormats={ [ 'core/bold', 'core/italic', 'core/link', 'core/strikethrough' ] }
					/>
				</div>
			</div>
		);
	},

	save: () => {
		return null; // Dynamic block
	},
} );

