# Mega Menu Guide

This theme supports mega menus for navigation items with submenus. You can create multi-column mega menus directly from the WordPress admin.

## How to Create a Mega Menu

### Step 1: Create Your Menu Structure

1. Go to **WordPress Admin → Appearance → Menus**
2. Create or edit your menu
3. Add your menu items with submenus as usual

### Step 2: Enable Mega Menu for a Menu Item

For any top-level menu item that has submenus:

1. **Click the menu item** to expand its settings
2. Look for the **CSS Classes** field (if not visible, click "Screen Options" at the top right and check "CSS Classes")
3. Add one of these classes:
   - `mega-menu` - Automatic column layout (responsive)
   - `mega-menu-2cols` - 2 columns
   - `mega-menu-3cols` - 3 columns
   - `mega-menu-4cols` - 4 columns
4. Click **Save Menu**

### Example Structure

```
Products (top-level item with class: mega-menu-3cols)
├── Category 1
│   ├── Product 1
│   ├── Product 2
│   └── Product 3
├── Category 2
│   ├── Product 4
│   └── Product 5
└── Category 3
    └── Product 6
```

This will display as a 3-column mega menu with each top-level submenu item (Category 1, 2, 3) as a column header.

## Mega Menu Features

### Automatic Layout
- **`mega-menu`** - Automatically adjusts columns based on content (responsive)

### Fixed Column Layouts
- **`mega-menu-2cols`** - Always shows 2 columns
- **`mega-menu-3cols`** - Always shows 3 columns  
- **`mega-menu-4cols`** - Always shows 4 columns

### Styling
- **Column Headers**: Top-level submenu items are styled as bold column headers
- **Nested Items**: Sub-items under each column are styled normally
- **Width**: Mega menus automatically center and adjust width (600px - 1200px)
- **Spacing**: Clean spacing with 2rem gaps between columns

## Visual Example

**Normal Submenu:**
```
Products ▼
  ├─ Category 1
  ├─ Category 2
  └─ Category 3
```

**Mega Menu (3 columns):**
```
Products ▼
┌─────────────────────────────────────┐
│ Category 1  │ Category 2  │ Category 3│
│   Item 1    │   Item 1    │   Item 1  │
│   Item 2    │   Item 2    │           │
│   Item 3    │             │           │
└─────────────────────────────────────┘
```

## Customization

### Change Mega Menu Width

Edit `style.css` and modify:
```css
.nav-menu li.mega-menu > .sub-menu {
	min-width: 600px;  /* Minimum width */
	max-width: 1200px; /* Maximum width */
}
```

### Change Column Gap

Edit `style.css`:
```css
.nav-menu li.mega-menu > .sub-menu {
	gap: 2rem; /* Space between columns */
}
```

### Change Column Count Breakpoints

The auto layout (`mega-menu` class) uses CSS Grid with `repeat(auto-fit, minmax(200px, 1fr))`. Adjust the `200px` value to change when columns break.

## Tips

1. **Use Column Headers**: Make your top-level submenu items bold/descriptive as they become column headers
2. **Balance Columns**: Try to have similar amounts of content in each column
3. **Mobile Responsive**: Mega menus automatically stack on mobile devices
4. **Test**: Always test your mega menus on different screen sizes

## Troubleshooting

**Mega menu not showing?**
- Make sure the menu item has submenus
- Verify the CSS class was added correctly
- Clear browser cache

**Columns not displaying correctly?**
- Check if you have enough submenu items
- Try a different column class (2cols, 3cols, 4cols)
- Check browser console for CSS errors

**Menu too wide/narrow?**
- Adjust `min-width` and `max-width` in CSS
- Or use fixed column classes instead of auto


