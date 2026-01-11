# Navbar Development Guide

## Where is the Navbar Built?

The navbar is built in **`header.php`** using WordPress's native `wp_nav_menu()` function. This is the main location where you'll customize the navbar structure.

### Main Files:

1. **`header.php`** - HTML structure of the navbar
   - Location: `/wp-content/themes/inoventis/header.php`
   - Contains: Logo/branding, navigation menu structure, mobile toggle button

2. **`assets/css/navigation.css`** - All navbar styling
   - Location: `/wp-content/themes/inoventis/assets/css/navigation.css`
   - Contains: Desktop menu, mobile menu, dropdowns, hover effects, responsive breakpoints

3. **`assets/js/navigation.js`** - Mobile menu functionality
   - Location: `/wp-content/themes/inoventis/assets/js/navigation.js`
   - Contains: Mobile toggle functionality, click handlers

4. **`functions.php`** - Menu registration and asset enqueuing
   - Location: `/wp-content/themes/inoventis/functions.php`
   - Contains: Menu location registration, CSS/JS enqueuing

## How to Customize the Navbar

### 1. Modify Navbar Structure (header.php)

Edit the HTML structure in `header.php`:

```php
<header id="masthead" class="site-header">
    <div class="header-container">
        <!-- Logo/Branding Section -->
        <div class="site-branding">
            <!-- Your logo/branding code -->
        </div>

        <!-- Navigation Menu Section -->
        <nav id="site-navigation" class="main-navigation">
            <!-- Menu toggle button (mobile) -->
            <button class="menu-toggle">...</button>
            
            <!-- WordPress menu -->
            <?php wp_nav_menu(...); ?>
        </nav>
    </div>
</header>
```

### 2. Style the Navbar (navigation.css)

Customize colors, spacing, fonts in `assets/css/navigation.css`:

```css
/* Change header background */
.site-header {
    background: #ffffff;
    border-bottom: 1px solid #e0e0e0;
}

/* Change menu item colors */
.nav-menu a {
    color: #333333;
}

.nav-menu a:hover {
    color: #0073aa; /* Your brand color */
}
```

### 3. Add Menu Items (WordPress Admin)

1. Go to **Appearance → Menus** in WordPress admin
2. Create or edit a menu
3. Add pages, posts, or custom links
4. Assign to "Primary Menu" location
5. Save

### 4. Customize wp_nav_menu Arguments

In `header.php`, you can modify the `wp_nav_menu()` arguments:

```php
wp_nav_menu(
    array(
        'theme_location'  => 'primary',      // Menu location
        'menu_id'         => 'primary-menu',  // CSS ID
        'menu_class'      => 'nav-menu',      // CSS class
        'container'       => false,           // No wrapper div
        'fallback_cb'     => false,           // No fallback menu
        'depth'           => 3,               // Submenu depth
        'walker'          => '',              // Custom walker class
    )
);
```

## Alternative: Block-Based Navigation

If you prefer a Gutenberg block-based approach:

1. **Create a custom Navigation Block** in `/blocks/navigation-block/`
2. Use WordPress's `core/navigation` block directly in templates
3. This allows editing navigation in the block editor

## Features Included

✅ Responsive mobile menu with hamburger toggle  
✅ Dropdown submenu support (3 levels deep)  
✅ Smooth hover effects  
✅ Accessible (ARIA labels, keyboard navigation)  
✅ Customizable colors via CSS variables  
✅ Logo support (custom logo or site title)  

## Quick Customization Examples

### Change Navbar Height
```css
.header-container {
    padding: 1.5rem 2rem; /* Increase/decrease padding */
}
```

### Change Menu Item Spacing
```css
.nav-menu {
    gap: 3rem; /* Increase gap between items */
}
```

### Add Background to Navbar
```css
.site-header {
    background: linear-gradient(to right, #667eea, #764ba2);
}
```

### Make Navbar Sticky
```css
.site-header {
    position: sticky;
    top: 0;
    z-index: 1000;
    background: #ffffff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
```

## Need Help?

- Check WordPress Codex: https://developer.wordpress.org/reference/functions/wp_nav_menu/
- WordPress Menu Walker: https://developer.wordpress.org/reference/classes/walker_nav_menu/

