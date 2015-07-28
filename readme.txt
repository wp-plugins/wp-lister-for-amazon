=== WP-Lister Lite for Amazon ===
Contributors: wp-lab
Tags: amazon, woocommerce, products, export
Requires at least: 4.0
Tested up to: 4.2.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

List products from WordPress on Amazon.

== Description ==

WP-Lister for Amazon integrates your WooCommerce product catalog with your inventory on Amazon.

= Features =

* list any number of items
* supports all official Amazon feed files
* supports product variations
* view buy box price and competitor prices
* includes SKU generator tool

= More information and Pro version =

Visit http://www.wplab.com/plugins/wp-lister-for-amazon/ to read more about WP-Lister and the Pro version - including documentation, installation instructions and user reviews.

WP-Lister Pro for Amazon will not only help you list items, but synchronize sales and orders across platforms and features automatic repricing.

== Installation ==

1. Install WP-Lister for Amazon either via the WordPress.org plugin repository, or by uploading the files to your server
2. After activating the plugin, visit the Amazon account settings page and follow the setup instructions at http://docs.wplab.com/article/85-first-time-setup 

== Frequently Asked Questions ==

= What are the requirements to run WP-Lister? =

WP-Lister requires a recent version of WordPress (4.x) and WooCommerce (2.2 or newer) installed. Your server should run on Linux and have PHP 5.3 or better with cURL support.

= Does WP-Lister support windows servers? =

No, and there are no plans on adding support for IIS.

= Are there any more FAQ? =

Please check out our growing knowledgebase at http://www.wplab.com/plugins/wp-lister-for-amazon/faq/

== Changelog ==

= 0.9.6.6 =
* added filter option to hide empty fields in profile editor
* added Industrial & Scientific feed templates for amazon.com
* added support for WooCommerce CSV importer 3.x

= 0.9.6.5.4 =
* added optional field for item condition and condition note on variation level
* added options to specify how long feeds, reports and order data should be kept in the database
* order details page: enter shipping time as local time instead of UTC
* view report: added search box to filter results / limit view to 1000 rows by default
* regard shipping discount when creating orders in WooCommerce (fix shipping total)
* fixed search box on import preview page - returned no results when searching for exact match ASIN or SKU

= 0.9.6.5.3 =
* fixed saving variations via AJAX on WooCommerce 2.4 beta
* show warning on edit product page if variations have no SKU set
* improved SKU mismatch warning on listings page in case the WooCommerce SKU is empty
* edit product: trim spaces from ASINs and UPCs automatically
* when duplicating a profile, jump to edit profile page

= 0.9.6.5.2 =
* shipping feed: make sure carrier-name is not empty if carrier-code is 'Other' (prevent Error 99021)
* edit order page: fixed field for custom service provider name not showing when tracking provider is set to "Other"
* fixed setup warnings not being shown (like missing cURL warning message)

= 0.9.6.5.1 =
* improved performance of generating import preview page
* fixed possible error code 200 when processing import queue

= 0.9.6.5 =
* added support for custom order statuses on settings page
* added gallery fallback option to use attached images if there is no WooCommerce Product Gallery (fixed issue with WooCommerce Dynamic Gallery plugin)
* added loading indicator on edit profile page
* added missing SDK file MarketplaceWebServiceProducts/Model/ErrorResponse.php
* added button to manually convert custom tables to utf8mb4 on WordPress 4.2+ (fix "Illegal mix of collations" sql error)
* improved Amazon column on Products page - show all listings for each product (but group variation listings)
* make sure the latest changes are submitted - even if a feed is "stuck" as submitted
* optimized memory footprint when processing import queue (fixed loading task list for 20k+ items on 192M RAM)
* improved processing of browse tree guide files - link db records to tpl_id to be able to clean incorrectly imported data automatically
* fixed php warning in ajax request when enabling all images on edit product page
* fixed issue with SWVG and Sports feed templates ok Amazon UK

= 0.9.6.4.2 =
* added option to request FBA shipment report every 3 hours
* added Clothing feed template for amazon.ca

= 0.9.6.4.1 =
* fixed possible php error during import 

= 0.9.6.4 =
* added option to set a default product category for products imported from Amazon (advanced settings page) 
* added option to automatically create matched listings for all products with ASINs (developer tools page) 
* improved profile editor for spanish feed templates 
* fixed some CE feed templates not being imported properly (amazon.es) 
* fixed possible fatal error during import 

= 0.9.6.3 =
* added option to process only selected rows when importing / updating products from merchant report 
* added option to enable Brand Registry / UPC exemption for account 
* brand registry: create listings for newly added child variations automatically, even if no UPC or ASIN is provided 
* fixed issue where items listed on multiple marketplaces using the same account would stay "submitted" 
* fixed matching product from edit product page - selected ASIN was removed if products was updated right after matching 
* fixed "View in WP-Lister" toolbar link on frontend 
* addedtooltips for report processing options on import page 
* import process: fixed creating additional (new / missing) variations for existing variable products in WooCommerce 
* regard "fall back to seller fulfilled" option when processing FBA inventory reports - skip zero qty rows entirely if fall back is enabled 

= 0.9.6.2 =
* added option to search / filter report rows in import preview 
* automatically fill in variation attribute columns like size_name and color_name  
* show number of offers considered next to lowest offer price in listings table 
* changed labeling from "imported" to "queued" - and updated text on import and settings pages 
* added developer tool buttons to clean the database - remove orphaned child variations and remove listings where the WooCommerce product has been deleted 
* fixed issue where selecting a category for Item Type Keyword column would insert browse node id instead of keyword (profile editor and edit product page) 
* make sure the customer state (address) is stored as two letter code in created WooCommerce orders (Amazon apparently returns either one or the other) 
* fixed search box on SKU generator page not showing products without listings 
* fixed formatting on ListMarketplaceParticipations response (log details) 
* fixes issue with attribute_ shortcodes on child variations inserting the same size/color value for all variations 

= 0.9.6.1 =
* added option to hide (exclude) specific variations from being listed on Amazon 
* added option to set WooCommerce order status for orders marked as shipped on Amazon 
* added "Sports & Outdoors" category for Amazon CA 
* regard WordPress timezone setting when creating orders 
* automatically update variation_theme for affected items when updating a listing profile 
* make sure sale_price is not higher than standard_price / price - Amazon might silently ignore price updates otherwise 
* fixed issue preparing listings when listing title is longer than 255 characters 
* fixed duplicate ASINs being skipped when importing products from merchant report 
* don't warn about duplicate ASINs if the SKU is unique 
* added action hook wpla_prepare_listing to create new listings from products 

= 0.9.6 =
* Initial release on wordpress.org

