# Product Blocks Guide

## How to Add Product Blocks

### Step 1: Build the Blocks

First, you need to build the JavaScript files for the blocks to work. Open your terminal and run:

```bash
cd wp-content/themes/inoventis
npm install  # Only needed the first time
npm run build
```

Or for development with auto-rebuild:

```bash
npm run start
```

### Step 2: Find the Blocks in WordPress Editor

1. **Go to WordPress Admin** → **Products** → **Add New** (or edit an existing product)
2. **Make sure you're in the Block Editor** (not Classic Editor)
3. **Click the "+" button** to add a new block
4. **Look for the "Product Fields" category** in the block inserter
5. You'll see these blocks:
   - 📝 **Product Short Description** - Rich text editor for short description
   - 📋 **Product Specifications** - Table with title/value rows
   - 📝 **Product Characteristics** - Accordion items with title and description
   - 🖼️ **Product Detailed Images** - Grid of images with captions
   - 🖼️ **Product Gallery** - Gallery of product images

### Step 3: Add Blocks to Your Product

1. Click on the **"+"** button in the editor
2. Search for "Product" or browse the **"Product Fields"** category
3. Click on any block to add it to your product page
4. The block will appear in the editor where you can edit it

### Step 4: Edit Block Content

Each block has its own editing interface:

- **Product Short Description**: Rich text editor (bold, italic, links, etc.)
- **Product Specifications**: Click "Add Specification" to add rows
- **Product Characteristics**: Click "Add Characteristic" to add items
- **Product Detailed Images**: Click "Add Image" to upload images with captions
- **Product Gallery**: Click "Add Gallery Images" to upload multiple images

### Block Locations

The blocks are located in:
```
wp-content/themes/inoventis/blocks/
├── product-short-description/
├── product-specifications/
├── product-characteristics/
├── product-detailed-images/
└── product-gallery/
```

### Troubleshooting

**Blocks not showing up?**
1. Make sure you ran `npm run build`
2. Clear your browser cache
3. Make sure you're editing a **Product** post type (not a regular post)
4. Check that you're in the **Block Editor**, not Classic Editor

**Blocks not saving?**
1. Make sure the meta fields are registered (they should be in functions.php)
2. Check browser console for JavaScript errors
3. Make sure you have proper permissions to edit posts

### Notes

- All blocks save data to post meta fields
- Blocks only appear when editing **Product** post type
- The old meta boxes are hidden in block editor but still work in classic editor
- Data is automatically saved when you save the product

