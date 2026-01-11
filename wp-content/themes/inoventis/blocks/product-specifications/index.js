/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { useEntityProp } from '@wordpress/core-data';
import { PanelBody, Button, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { trash, plus } from '@wordpress/icons';

registerBlockType( 'inoventis/product-specifications', {
	edit: ( { attributes, setAttributes, context } ) => {
		const blockProps = useBlockProps( {
			className: 'product-specifications-editor',
		} );
		
		const postType = context?.postType || 'product';
		const postId = context?.postId;
		
		const [ meta, setMeta ] = useEntityProp( 'postType', postType, 'meta', postId );
		const currentSpecs = ( meta && Array.isArray( meta._product_specifications ) ) ? meta._product_specifications : [];

		const updateSpecs = ( newSpecs ) => {
			setMeta( {
				...( meta || {} ),
				_product_specifications: newSpecs,
			} );
		};

		const addSpecification = () => {
			const newSpecs = [ ...currentSpecs, { title: '', value: '' } ];
			updateSpecs( newSpecs );
		};

		const updateSpecification = ( index, field, value ) => {
			const newSpecs = [ ...currentSpecs ];
			newSpecs[ index ] = {
				...newSpecs[ index ],
				[ field ]: value,
			};
			updateSpecs( newSpecs );
		};

		const removeSpecification = ( index ) => {
			const newSpecs = currentSpecs.filter( ( _, i ) => i !== index );
			updateSpecs( newSpecs );
		};

		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Specifications', 'inoventis' ) }>
						<p>{ __( 'Add specification rows that will be displayed in a table format.', 'inoventis' ) }</p>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<div className="specifications-editor-header">
						<h3>{ __( 'Product Specifications', 'inoventis' ) }</h3>
						<Button
							variant="primary"
							icon={ plus }
							onClick={ addSpecification }
						>
							{ __( 'Add Specification', 'inoventis' ) }
						</Button>
					</div>
					{ currentSpecs.length === 0 ? (
						<p className="no-items-message">
							{ __( 'No specifications added yet. Click "Add Specification" to get started.', 'inoventis' ) }
						</p>
					) : (
						<div className="specifications-list">
							{ currentSpecs.map( ( spec, index ) => (
								<div key={ index } className="specification-row-editor">
									<div className="spec-fields">
										<TextControl
											label={ __( 'Title', 'inoventis' ) }
											value={ spec.title || '' }
											onChange={ ( value ) => updateSpecification( index, 'title', value ) }
											placeholder={ __( 'Enter specification title', 'inoventis' ) }
											_next40pxDefaultSize={ true }
											_nextHasNoMarginBottom={ true }
										/>
										<TextControl
											label={ __( 'Value', 'inoventis' ) }
											value={ spec.value || '' }
											onChange={ ( value ) => updateSpecification( index, 'value', value ) }
											placeholder={ __( 'Enter specification value', 'inoventis' ) }
											_next40pxDefaultSize={ true }
											_nextHasNoMarginBottom={ true }
										/>
									</div>
									<Button
										variant="secondary"
										isDestructive
										icon={ trash }
										onClick={ () => removeSpecification( index ) }
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

