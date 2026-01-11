# WordPress Migration Instructions

## Problem: Images Not Showing After Database Import

After importing the database, images don't show because:
1. Database contains URLs pointing to `localhost:8080` 
2. Image files need to be uploaded to the server

## Solution

### Step 1: Upload Image Files to Server

The `wp-content/uploads` folder will be deployed with your git push. Make sure it's included in your repository.

If you need to upload separately:
- Local path: `wp-content/uploads/` (5.1 MB)
- Server path: `wp-content/uploads/` (same location)

### Step 2: Update URLs in Database

#### Using phpMyAdmin (Recommended)

1. Open phpMyAdmin on your Hostinger server
2. Select your WordPress database
3. Go to the "SQL" tab
4. Open the file `update_database_urls.sql` from this repository
5. **Replace `YOUR_DOMAIN_HERE` with your actual domain** (e.g., `inoventis.com`)
6. Copy and paste the SQL queries into phpMyAdmin
7. Click "Go" to execute

**Important:** 
- Replace `YOUR_DOMAIN_HERE` with your actual domain
- Use `https://` if your site uses SSL, or `http://` if not
- Example: `https://inoventis.com` or `http://inoventis.com`

#### Option B: Using WordPress Admin (Easier)

1. Install the "Better Search Replace" plugin
2. Go to Tools → Better Search Replace
3. Search for: `http://localhost:8080`
4. Replace with: `https://yourdomain.com` (your actual domain)
5. Select all tables
6. Click "Run Search/Replace"

### Step 3: Verify

- Check that images load on your site
- Check that links work correctly
- Test the WordPress admin panel

## Notes

- Make sure you use `http://` or `https://` consistently based on your server setup
- If your site uses HTTPS, make sure to use `https://` in the replacement
- Always backup your database before making changes!

