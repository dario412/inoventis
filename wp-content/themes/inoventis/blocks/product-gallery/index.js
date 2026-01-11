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
import { PanelBody, Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { trash, gallery } from '@wordpress/icons';

function GalleryImagesList( { galleryIds, onRemove } ) {
	const images = useSelect( ( select ) => {
		return galleryIds.map( ( id ) => select( 'core' ).getMedia( id ) ).filter( Boolean );
	}, [ galleryIds ] );

	return (
		<div className="gallery-images-grid">
			{ images.map( ( image ) => (
				<div key={ image.id } className="gallery-image-item-editor">
					<img src={ image.source_url } alt={ image.alt_text || '' } />
					<Button
						variant="secondary"
						isDestructive
						icon={ trash }
						onClick={ () => onRemove( image.id ) }
						className="remove-gallery-image"
					>
						{ __( 'Remove', 'inoventis' ) }
					</Button>
				</div>
			) ) }
		</div>
	);
}

registerBlockType( 'inoventis/product-gallery', {
	edit: ( { attributes, setAttributes, context } ) => {
		const blockProps = useBlockProps( {
			className: 'product-gallery-editor',
		} );
		
		const postType = context?.postType || 'product';
		const postId = context?.postId;
		
		const [ meta, setMeta ] = useEntityProp( 'postType', postType, 'meta', postId );
		const galleryIds = ( meta && Array.isArray( meta._product_gallery ) ) ? meta._product_gallery : [];

		const updateMetaField = ( fieldName, value ) => {
			const updatedMeta = { ...( meta || {} ) };
			updatedMeta[ fieldName ] = value;
			setMeta( updatedMeta );
		};

		const addImages = ( media ) => {
			const newIds = media.map( ( item ) => item.id );
			const updatedIds = [ ...galleryIds, ...newIds ];
			updateMetaField( '_product_gallery', updatedIds );
		};

		const removeImage = ( imageId ) => {
			const updatedIds = galleryIds.filter( ( id ) => id !== imageId );
			updateMetaField( '_product_gallery', updatedIds );
		};

		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Gallery', 'inoventis' ) }>
						<p>{ __( 'Add multiple images to create a product gallery.', 'inoventis' ) }</p>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<div className="gallery-editor-header">
						<h3>{ __( 'Product Gallery', 'inoventis' ) }</h3>
						<MediaUploadCheck>
							<MediaUpload
								onSelect={ addImages }
								allowedTypes={ [ 'image' ] }
								multiple={ true }
								value={ galleryIds }
								render={ ( { open } ) => (
									<Button
										variant="primary"
										icon={ gallery }
										onClick={ open }
									>
										{ __( 'Add Gallery Images', 'inoventis' ) }
									</Button>
								) }
							/>
						</MediaUploadCheck>
					</div>
					{ galleryIds.length === 0 ? (
						<p className="no-items-message">
							{ __( 'No images in gallery. Click "Add Gallery Images" to add images.', 'inoventis' ) }
						</p>
					) : (
						<GalleryImagesList galleryIds={ galleryIds } onRemove={ removeImage } />
					) }
				</div>
			</>
		);
	},

	save: () => {
		return null; // Dynamic block
	},
} );

