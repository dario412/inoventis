# Site Editor Guide - Global Theme Styling

Your theme is now fully configured for Full Site Editing! You can customize all fonts, colors, spacing, headings, links, and buttons globally from the WordPress Site Editor.

## How to Access the Site Editor

1. Go to **WordPress Admin → Appearance → Editor**
   - Or go directly to: `your-site.com/wp-admin/site-editor.php`

2. You'll see the Site Editor interface with:
   - Left sidebar: Templates and patterns
   - Center: Live preview of your site
   - Right sidebar: Settings and Styles panels

## Global Styles - Where to Customize Everything

### Access Global Styles

1. In the Site Editor, click the **Styles** icon (paintbrush) in the top toolbar
2. Or click **Browse styles** → **Styles**

### What You Can Customize

#### 🎨 Colors
- **Palette**: Add/edit theme colors
  - Primary, Secondary, Accent colors
  - Background and Foreground colors
  - Custom colors
- **Gradients**: Create and use gradient backgrounds
- **Default Colors**: Set default background and text colors

**Where to find it:**
- Styles → Colors → Palette
- Styles → Colors → Default colors

#### ✍️ Typography

**Global Typography Settings:**
- **Font Family**: Choose default font (System, Serif, Monospace)
- **Font Size**: Default text size
- **Font Weight**: Default weight
- **Line Height**: Default line spacing
- **Letter Spacing**: Character spacing

**Heading Styles (H1-H6):**
- **H1**: Font size, weight, line height, color, margins
- **H2**: Font size, weight, line height, color, margins
- **H3**: Font size, weight, line height, color, margins
- **H4**: Font size, weight, line height, color, margins
- **H5**: Font size, weight, line height, color, margins
- **H6**: Font size, weight, line height, color, margins

**Where to find it:**
- Styles → Typography → Text
- Styles → Typography → Headings → H1, H2, H3, H4, H5, H6

#### 🔗 Links

**Link Styles:**
- **Color**: Link text color
- **Text Decoration**: Underline style
- **Font Size**: Link font size
- **Hover Color**: (Customizable via CSS or in block editor)

**Where to find it:**
- Styles → Typography → Links

#### 🔘 Buttons

**Button Styles:**
- **Background Color**: Button background
- **Text Color**: Button text color
- **Font Size**: Button text size
- **Font Weight**: Button text weight
- **Padding**: Button padding (top, right, bottom, left)
- **Border Radius**: Rounded corners
- **Border**: Border width, style, color

**Where to find it:**
- Styles → Blocks → Button
- Or select any button block → Block settings → Styles

#### 📏 Spacing

**Global Spacing:**
- **Spacing Scale**: Configure spacing scale
- **Spacing Sizes**: Preset spacing sizes
- **Block Gap**: Default gap between blocks
- **Padding**: Global padding settings
- **Units**: px, em, rem, vh, vw, %

**Where to find it:**
- Styles → Layout → Spacing

#### 🖼️ Borders

**Border Settings:**
- **Color**: Border color
- **Width**: Border thickness
- **Style**: Solid, dashed, dotted, etc.
- **Radius**: Border radius for rounded corners

**Where to find it:**
- Styles → Blocks → Any block → Border settings
- Or in individual block settings

#### 🌑 Shadows

**Shadow Presets:**
- Small shadow
- Medium shadow
- Large shadow
- Custom shadows

**Where to find it:**
- Styles → Blocks → Any block → Shadow settings

## Step-by-Step: Customizing Global Styles

### 1. Customize Headings (H1-H6)

1. Go to **Appearance → Editor**
2. Click **Styles** icon (paintbrush)
3. Click **Typography** → **Headings**
4. Select **H1** (or H2, H3, etc.)
5. Customize:
   - Font Family
   - Font Size
   - Font Weight
   - Line Height
   - Text Color
   - Letter Spacing
6. Repeat for other heading levels
7. Click **Save** or **Publish**

### 2. Customize Links

1. In Styles → **Typography** → **Links**
2. Set:
   - Color
   - Text Decoration (underline, none, etc.)
   - Font Size
   - Font Weight
3. Save changes

### 3. Customize Buttons

**Global Button Style:**
1. In Styles → **Blocks** → **Button**
2. Customize:
   - Background Color
   - Text Color
   - Font Size
   - Font Weight
   - Padding (all sides)
   - Border Radius
   - Border

**Individual Button:**
1. Add a Button block
2. Select the button
3. Use Block settings → Styles tab
4. Customize colors, typography, spacing, border

### 4. Customize Colors

1. In Styles → **Colors**
2. **Edit Palette:**
   - Click on any color in the palette
   - Change hex code
   - Update name
   - Add new colors
3. **Default Colors:**
   - Set default background color
   - Set default text color

### 5. Customize Fonts

**Add New Font Family:**
1. Edit `theme.json` and add to `typography.fontFamilies`
2. Enqueue the font in `functions.php`
3. The font will appear in the editor

**Set Default Font:**
1. In Styles → **Typography** → **Text**
2. Select Font Family
3. Set Font Size, Weight, Line Height
4. Save

### 6. Customize Spacing

1. In Styles → **Layout** → **Spacing**
2. Configure:
   - Spacing Scale
   - Spacing Sizes (presets)
   - Block Gap (default gap between blocks)
3. Save

## Current Default Styles

Based on your `theme.json`:

### Headings
- **H1**: 3rem, Bold (700), Line height 1.2
- **H2**: 2.5rem, Bold (700), Line height 1.3
- **H3**: 2rem, Semi-bold (600), Line height 1.4
- **H4**: 1.5rem, Semi-bold (600), Line height 1.4
- **H5**: 1.25rem, Semi-bold (600), Line height 1.5
- **H6**: 1rem, Semi-bold (600), Line height 1.5

### Links
- Color: Accent (#00a0d2)
- Decoration: Underline

### Buttons
- Background: Accent (#00a0d2)
- Text: White
- Font Size: Medium (1rem)
- Font Weight: 600 (Semi-bold)
- Padding: Medium (vertical), Large (horizontal)
- Border Radius: 4px

### Colors
- Primary: #0073aa
- Secondary: #005177
- Accent: #00a0d2
- Background: #ffffff
- Foreground: #1e1e1e
- Plus: White, Black, Gray Light, Gray Dark

## Tips

1. **Global vs Local**: Changes in Styles apply globally. Individual block settings override global styles.

2. **Preview Changes**: The Site Editor shows live preview. Click outside to see changes applied.

3. **Save vs Publish**: 
   - **Save** saves draft changes
   - **Publish** makes changes live

4. **Undo/Redo**: Use browser back/forward or undo button in toolbar

5. **Reset**: To reset a style, click the reset icon (circular arrow) next to the setting

## Editing theme.json Directly

For advanced customization, you can edit `theme.json` directly:

**Location:** `/wp-content/themes/inoventis/theme.json`

**What you can change:**
- Color palette
- Font families and sizes
- Spacing presets
- Default heading styles
- Button default styles
- And more...

**Important:** After editing `theme.json`, refresh the Site Editor to see changes.

## Need Help?

- **WordPress Documentation**: https://wordpress.org/documentation/article/site-editor/
- **Theme.json Reference**: https://developer.wordpress.org/block-editor/reference-guides/theme-json/
- **Global Styles Guide**: https://wordpress.org/documentation/article/global-styles/

Your theme is now fully ready for global styling! All changes you make in the Site Editor will apply across your entire website.

