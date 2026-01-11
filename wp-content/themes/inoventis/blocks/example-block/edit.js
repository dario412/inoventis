/**
 * WordPress dependencies
 */
import { useBlockProps, RichText, InspectorControls, ColorPalette } from '@wordpress/block-editor';
import { PanelBody, __experimentalInputControl as InputControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export default function Edit( { attributes, setAttributes } ) {
	const { title, content, backgroundColor } = attributes;
	const blockProps = useBlockProps( {
		style: {
			backgroundColor: backgroundColor,
		}
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Block Settings', 'inoventis' ) }>
					<p>{ __( 'Background Color', 'inoventis' ) }</p>
					<InputControl
						value={ backgroundColor }
						onChange={ ( value ) => setAttributes( { backgroundColor: value } ) }
						type="color"
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<RichText
					tagName="h2"
					value={ title }
					onChange={ ( value ) => setAttributes( { title: value } ) }
					placeholder={ __( 'Enter title...', 'inoventis' ) }
				/>
				<RichText
					tagName="p"
					value={ content }
					onChange={ ( value ) => setAttributes( { content: value } ) }
					placeholder={ __( 'Enter content...', 'inoventis' ) }
				/>
			</div>
		</>
	);
}

