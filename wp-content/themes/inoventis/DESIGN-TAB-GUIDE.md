# Design Tab Guide

The Design tab is now fully enabled in your WordPress theme! You can customize colors, fonts, spacing, borders, shadows, and more directly from the WordPress block editor.

## Where to Find the Design Tab

1. **In the Block Editor:**
   - Open any post or page in the WordPress editor
   - Select any block
   - Look at the right sidebar panel
   - You'll see tabs: **Block** and **Design** (sometimes labeled as **Styles**)

2. **In the Site Editor (Full Site Editing):**
   - Go to **Appearance → Editor** in WordPress admin
   - Select any template or template part
   - The Design tab will be available in the right sidebar

## What You Can Customize

### 🎨 Colors
- **Background Color** - Set custom background colors or use preset palette
- **Text Color** - Choose text colors from palette or custom
- **Link Color** - Customize link colors throughout the site
- **Custom Colors** - Add any hex color code
- **Gradients** - Use preset gradients or create custom ones
- **Duotone** - Apply duotone effects to images

**Preset Color Palette:**
- Primary (#0073aa)
- Secondary (#005177)
- Accent (#00a0d2)
- Background (#ffffff)
- Foreground (#1e1e1e)
- Plus: White, Black, Gray Light, Gray Dark

### ✍️ Typography
- **Font Family** - Choose from System, Serif, or Monospace
- **Font Size** - Select from presets or enter custom size
  - Presets: XS, Small, Medium, Large, X-Large, 2X Large, 3X Large, Huge
- **Font Weight** - Bold, normal, light, etc.
- **Font Style** - Italic, normal
- **Line Height** - Adjust line spacing
- **Letter Spacing** - Control character spacing
- **Text Decoration** - Underline, strikethrough, etc.
- **Text Transform** - Uppercase, lowercase, capitalize

### 📏 Spacing
- **Padding** - Add padding to all sides or individually (top, right, bottom, left)
- **Margin** - Control margins around blocks
- **Custom Spacing** - Use any unit: px, em, rem, vh, vw, %

**Preset Spacing Sizes:**
- 2X Extra Small (0.25rem)
- Extra Small (0.5rem)
- Small (0.75rem)
- Medium (1rem)
- Large (1.5rem)
- Extra Large (2rem)
- 2X Large (3rem)
- 3X Large (4rem)

### 🖼️ Borders
- **Border Color** - Choose border colors
- **Border Width** - Set border thickness
- **Border Style** - Solid, dashed, dotted, etc.
- **Border Radius** - Round corners

### 🌑 Shadows
- **Box Shadow** - Apply shadows to blocks
- **Preset Shadows:**
  - Small
  - Medium
  - Large
- **Custom Shadows** - Create your own shadow effects

### 🔗 Links
- **Link Color** - Global link color setting
- **Link Hover** - Customize hover states

## How to Use

### For Individual Blocks:
1. Select a block in the editor
2. Open the **Design** tab (or **Styles** tab) in the right sidebar
3. Customize colors, typography, spacing, etc.
4. Changes apply immediately and can be previewed

### For Global Theme Settings:
1. Go to **Appearance → Editor**
2. Click on the **Styles** icon in the top toolbar (paintbrush icon)
3. Customize:
   - **Colors** - Global color palette
   - **Typography** - Default fonts and sizes
   - **Layout** - Content width, etc.

### For Site-Wide Styles:
1. In Site Editor → **Styles**
2. Customize:
   - **Color Palette** - Add/edit theme colors
   - **Typography** - Set default font families
   - **Layout** - Control content and wide sizes

## Configuration Files

The design settings are configured in:

1. **`theme.json`** - Main configuration file
   - Location: `/wp-content/themes/inoventis/theme.json`
   - Contains: All color palettes, font settings, spacing presets, etc.

2. **`functions.php`** - Theme support features
   - Location: `/wp-content/themes/inoventis/functions.php`
   - Contains: Theme support declarations for appearance tools

## Customizing the Design Settings

### Adding New Colors to Palette

Edit `theme.json` and add to the `color.palette` array:

```json
{
	"slug": "my-color",
	"color": "#ff0000",
	"name": "My Custom Color"
}
```

### Adding New Font Families

Edit `theme.json` and add to the `typography.fontFamilies` array:

```json
{
	"fontFamily": "'Roboto', sans-serif",
	"slug": "roboto",
	"name": "Roboto"
}
```

Then enqueue the font in `functions.php` or add to `<head>`.

### Adding New Font Sizes

Edit `theme.json` and add to the `typography.fontSizes` array:

```json
{
	"slug": "custom-size",
	"size": "1.75rem",
	"name": "Custom Size"
}
```

## Tips

1. **Use CSS Variables:** The theme uses CSS variables (e.g., `var(--wp--preset--color--primary)`) which makes styling consistent
2. **Start with Presets:** Use preset colors and sizes for consistency
3. **Test Responsively:** Check how design changes look on mobile devices
4. **Global vs Local:** Use Site Editor styles for global changes, block styles for specific elements

## Troubleshooting

**Design tab not showing?**
- Make sure the theme is activated
- Clear browser cache
- Check that `appearanceTools: true` is in `theme.json`

**Colors not applying?**
- Verify the color palette in `theme.json` is valid JSON
- Check browser console for errors
- Ensure CSS is being enqueued properly

**Fonts not working?**
- Make sure font families are properly enqueued
- Check font names match exactly (including quotes for font families with spaces)
- Verify font files are accessible if using custom fonts

## Resources

- [WordPress Theme.json Documentation](https://developer.wordpress.org/block-editor/how-to-guides/themes/theme-json/)
- [Block Editor Design Tools](https://developer.wordpress.org/block-editor/how-to-guides/themes/theme-json/#appearance-tools)
- [Color Palette Guide](https://developer.wordpress.org/block-editor/how-to-guides/themes/theme-json/#color)

