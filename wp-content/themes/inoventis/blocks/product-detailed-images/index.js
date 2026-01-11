/**
 * WordPress dependencies
 */
/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { useEntityProp } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';
import { PanelBody, Button, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { trash, plus, upload } from '@wordpress/icons';

function DetailedImageItem( { item, index, onUpdate, onRemove, onSelectImage } ) {
	const imageId = item.image_id || 0;
	const image = useSelect( ( select ) => {
		if ( ! imageId ) return null;
		return select( 'core' ).getMedia( imageId );
	}, [ imageId ] );

	return (
		<div className="detailed-image-row-editor">
			<div className="image-fields">
				<MediaUploadCheck>
					<MediaUpload
						onSelect={ ( media ) => onSelectImage( index, media ) }
						allowedTypes={ [ 'image' ] }
						value={ imageId }
						render={ ( { open } ) => (
							<div className="image-upload-wrapper">
								{ imageId > 0 && image ? (
									<div className="image-preview">
										<img src={ image.source_url } alt={ image.alt_text || '' } />
										<Button
											variant="secondary"
											onClick={ () => onUpdate( index, 'image_id', 0 ) }
										>
											{ __( 'Remove Image', 'inoventis' ) }
										</Button>
									</div>
								) : (
									<Button
										variant="secondary"
										icon={ upload }
										onClick={ open }
									>
										{ __( 'Upload Image', 'inoventis' ) }
									</Button>
								) }
							</div>
						) }
					/>
				</MediaUploadCheck>
				<TextControl
					label={ __( 'Caption', 'inoventis' ) }
					value={ item.text || '' }
					onChange={ ( value ) => onUpdate( index, 'text', value ) }
					placeholder={ __( 'Enter image caption', 'inoventis' ) }
					_next40pxDefaultSize={ true }
					_nextHasNoMarginBottom={ true }
				/>
			</div>
			<Button
				variant="secondary"
				isDestructive
				icon={ trash }
				onClick={ () => onRemove( index ) }
			>
				{ __( 'Remove', 'inoventis' ) }
			</Button>
		</div>
	);
}

registerBlockType( 'inoventis/product-detailed-images', {
	edit: ( { attributes, setAttributes, context } ) => {
		const blockProps = useBlockProps( {
			className: 'product-detailed-images-editor',
		} );
		
		const postType = context?.postType || 'product';
		const postId = context?.postId;
		
		const [ meta, setMeta ] = useEntityProp( 'postType', postType, 'meta', postId );
		const currentImages = ( meta && Array.isArray( meta._product_detailed_images ) ) ? meta._product_detailed_images : [];

		const updateImages = ( newImages ) => {
			setMeta( {
				...( meta || {} ),
				_product_detailed_images: newImages,
			} );
		};

		const addImage = () => {
			const newImages = [ ...currentImages, { image_id: 0, text: '' } ];
			updateImages( newImages );
		};

		const updateImage = ( index, field, value ) => {
			const newImages = [ ...currentImages ];
			newImages[ index ] = {
				...newImages[ index ],
				[ field ]: value,
			};
			updateImages( newImages );
		};

		const removeImage = ( index ) => {
			const newImages = currentImages.filter( ( _, i ) => i !== index );
			updateImages( newImages );
		};

		const onSelectImage = ( index, media ) => {
			updateImage( index, 'image_id', media.id );
		};

		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Detailed Images', 'inoventis' ) }>
						<p>{ __( 'Add detailed product images with captions that will be displayed in a grid.', 'inoventis' ) }</p>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<div className="detailed-images-editor-header">
						<h3>{ __( 'Product Detailed Images', 'inoventis' ) }</h3>
						<Button
							variant="primary"
							icon={ plus }
							onClick={ addImage }
						>
							{ __( 'Add Image', 'inoventis' ) }
						</Button>
					</div>
					{ currentImages.length === 0 ? (
						<p className="no-items-message">
							{ __( 'No images added yet. Click "Add Image" to get started.', 'inoventis' ) }
						</p>
					) : (
						<div className="detailed-images-list">
							{ currentImages.map( ( item, index ) => (
								<DetailedImageItem
									key={ index }
									item={ item }
									index={ index }
									onUpdate={ updateImage }
									onRemove={ removeImage }
									onSelectImage={ onSelectImage }
								/>
							) ) }
						</div>
					) }
				</div>
			</>
		);
	},

	save: () => {
		return null; // Dynamic block
	},
} );

