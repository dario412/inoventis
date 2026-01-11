# Inoventis Theme

A custom WordPress theme with custom Gutenberg blocks for the Inoventis project.

## Features

- ✅ Modern block editor support
- ✅ Custom Gutenberg blocks
- ✅ `theme.json` configuration
- ✅ Build system with `@wordpress/scripts`
- ✅ Example custom block included

## Setup

### 1. Install Dependencies

Navigate to the theme directory and install npm packages:

```bash
cd wp-content/themes/inoventis
npm install
```

### 2. Build Blocks

Build the blocks for production:

```bash
npm run build
```

Or start the development watch mode:

```bash
npm run start
```

### 3. Activate Theme

1. Go to WordPress Admin → Appearance → Themes
2. Activate the "Inoventis" theme

## Creating Custom Blocks

### Quick Start

1. Copy the `blocks/example-block` directory
2. Rename it to your block name (e.g., `my-custom-block`)
3. Update the block name in `block.json`:
   ```json
   {
     "name": "inoventis/my-custom-block",
     "title": "My Custom Block"
   }
   ```
4. Update the import in `src/index.js` to include your new block
5. Run `npm run build` or `npm run start`

### Block Structure

```
blocks/
└── my-custom-block/
    ├── block.json      # Block metadata and configuration
    ├── edit.js         # Block editor component
    ├── save.js         # Block frontend save function
    ├── index.js        # Block registration
    ├── style.css       # Frontend styles
    └── editor.css      # Editor-only styles
```

### Block.json Structure

The `block.json` file defines your block's metadata:

- `name`: Unique block identifier (e.g., `inoventis/my-block`)
- `title`: Display name in the block inserter
- `category`: Block category (text, media, design, etc.)
- `attributes`: Block data schema
- `supports`: Block features (colors, spacing, etc.)

### Example Block Files

#### edit.js (Editor Component)
```javascript
import { useBlockProps, RichText } from '@wordpress/block-editor';

export default function Edit( { attributes, setAttributes } ) {
	return (
		<div { ...useBlockProps() }>
			<RichText
				value={ attributes.text }
				onChange={ ( value ) => setAttributes( { text: value } ) }
			/>
		</div>
	);
}
```

#### save.js (Frontend Output)
```javascript
import { useBlockProps, RichText } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	return (
		<div { ...useBlockProps.save() }>
			<RichText.Content value={ attributes.text } />
		</div>
	);
}
```

## Development

### Build Commands

- `npm run build` - Build for production
- `npm run start` - Start development watch mode
- `npm run packages-update` - Update WordPress packages

### File Structure

```
inoventis/
├── blocks/              # Custom block definitions
│   └── example-block/  # Example block
├── src/                 # Source files for build
│   └── index.js        # Main entry point
├── build/               # Compiled output (generated)
├── template-parts/      # PHP template parts
├── functions.php        # Theme functions
├── theme.json          # Block editor configuration
└── package.json        # NPM dependencies
```

## Resources

- [WordPress Block Editor Handbook](https://developer.wordpress.org/block-editor/)
- [Block Development Examples](https://github.com/WordPress/gutenberg-examples)
- [@wordpress/scripts Documentation](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-scripts/)

## Notes

- Always run `npm run build` after creating or modifying blocks
- Use `npm run start` during development for automatic rebuilds
- Block styles are automatically loaded from `style.css` in each block directory
- Editor styles are automatically loaded from `editor.css` in each block directory

