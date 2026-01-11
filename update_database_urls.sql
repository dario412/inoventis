-- WordPress Database URL Update Script
-- IMPORTANT: Run this in phpMyAdmin after importing the database
-- Replace 'YOUR_DOMAIN_HERE' with your actual domain (e.g., inoventis.com)

-- Update WordPress site URL and home URL
UPDATE wp_options 
SET option_value = REPLACE(option_value, 'http://localhost:8080', 'https://YOUR_DOMAIN_HERE') 
WHERE option_name IN ('home', 'siteurl');

-- Update post GUIDs
UPDATE wp_posts 
SET guid = REPLACE(guid, 'http://localhost:8080', 'https://YOUR_DOMAIN_HERE');

-- Update post content (images, links, etc.)
UPDATE wp_posts 
SET post_content = REPLACE(post_content, 'http://localhost:8080', 'https://YOUR_DOMAIN_HERE');

-- Update post meta (featured images, custom fields, etc.)
UPDATE wp_postmeta 
SET meta_value = REPLACE(meta_value, 'http://localhost:8080', 'https://YOUR_DOMAIN_HERE');

-- Update comment meta
UPDATE wp_commentmeta 
SET meta_value = REPLACE(meta_value, 'http://localhost:8080', 'https://YOUR_DOMAIN_HERE');

-- Update user meta
UPDATE wp_usermeta 
SET meta_value = REPLACE(meta_value, 'http://localhost:8080', 'https://YOUR_DOMAIN_HERE');

-- Update options (widgets, themes, etc.)
UPDATE wp_options 
SET option_value = REPLACE(option_value, 'http://localhost:8080', 'https://YOUR_DOMAIN_HERE')
WHERE option_name NOT IN ('home', 'siteurl');

