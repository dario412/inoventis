/**
 * WordPress dependencies
 */
import { useBlockProps, RichText } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const { title, content, backgroundColor } = attributes;
	const blockProps = useBlockProps.save( {
		style: {
			backgroundColor: backgroundColor,
		}
	} );

	return (
		<div { ...blockProps }>
			<RichText.Content tagName="h2" value={ title } />
			<RichText.Content tagName="p" value={ content } />
		</div>
	);
}

