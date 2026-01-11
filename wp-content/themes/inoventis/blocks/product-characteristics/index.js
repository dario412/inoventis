/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { useEntityProp } from '@wordpress/core-data';
import { PanelBody, Button, TextControl, TextareaControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { trash, plus } from '@wordpress/icons';

registerBlockType( 'inoventis/product-characteristics', {
	edit: ( { attributes, setAttributes, context } ) => {
		const blockProps = useBlockProps( {
			className: 'product-characteristics-editor',
		} );
		
		const postType = context?.postType || 'product';
		const postId = context?.postId;
		
		const [ meta, setMeta ] = useEntityProp( 'postType', postType, 'meta', postId );
		const currentChars = ( meta && Array.isArray( meta._product_characteristics ) ) ? meta._product_characteristics : [];

		const updateChars = ( newChars ) => {
			setMeta( {
				...( meta || {} ),
				_product_characteristics: newChars,
			} );
		};

		const addCharacteristic = () => {
			const newChars = [ ...currentChars, { title: '', text: '' } ];
			updateChars( newChars );
		};

		const updateCharacteristic = ( index, field, value ) => {
			const newChars = [ ...currentChars ];
			newChars[ index ] = {
				...newChars[ index ],
				[ field ]: value,
			};
			updateChars( newChars );
		};

		const removeCharacteristic = ( index ) => {
			const newChars = currentChars.filter( ( _, i ) => i !== index );
			updateChars( newChars );
		};

		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Characteristics', 'inoventis' ) }>
						<p>{ __( 'Add characteristics that will be displayed as an accordion on the frontend.', 'inoventis' ) }</p>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<div className="characteristics-editor-header">
						<h3>{ __( 'Product Characteristics', 'inoventis' ) }</h3>
						<Button
							variant="primary"
							icon={ plus }
							onClick={ addCharacteristic }
						>
							{ __( 'Add Characteristic', 'inoventis' ) }
						</Button>
					</div>
					{ currentChars.length === 0 ? (
						<p className="no-items-message">
							{ __( 'No characteristics added yet. Click "Add Characteristic" to get started.', 'inoventis' ) }
						</p>
					) : (
						<div className="characteristics-list">
							{ currentChars.map( ( char, index ) => (
								<div key={ index } className="characteristic-row-editor">
									<div className="char-fields">
										<TextControl
											label={ __( 'Title', 'inoventis' ) }
											value={ char.title || '' }
											onChange={ ( value ) => updateCharacteristic( index, 'title', value ) }
											placeholder={ __( 'Enter characteristic title', 'inoventis' ) }
											_next40pxDefaultSize={ true }
											_nextHasNoMarginBottom={ true }
										/>
										<TextareaControl
											label={ __( 'Description', 'inoventis' ) }
											value={ char.text || '' }
											onChange={ ( value ) => updateCharacteristic( index, 'text', value ) }
											placeholder={ __( 'Enter characteristic description', 'inoventis' ) }
											rows={ 4 }
											_nextHasNoMarginBottom={ true }
										/>
									</div>
									<Button
										variant="secondary"
										isDestructive
										icon={ trash }
										onClick={ () => removeCharacteristic( index ) }
									>
										{ __( 'Remove', 'inoventis' ) }
									</Button>
								</div>
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

