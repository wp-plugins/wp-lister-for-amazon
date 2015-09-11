=== WP-Lister Lite for Amazon ===
Contributors: wp-lab
Tags: amazon, woocommerce, products, export
Requires at least: 4.0
Tested up to: 4.3
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

List products from WordPress on Amazon.

== Description ==

WP-Lister for Amazon integrates your WooCommerce product catalog with your inventory on Amazon.

= Features =

* list any number of items
* supports all official Amazon category feeds
* supports product variations
* view buy box price and competitor prices
* includes SKU generator tool

= More information and Pro version =

Visit http://www.wplab.com/plugins/wp-lister-for-amazon/ to read more about WP-Lister and the Pro version - including documentation, installation instructions and user reviews.

WP-Lister Pro for Amazon will not only help you list items, but synchronize sales and orders across platforms and features an automatic repricing tool.

== Installation ==

1. Install WP-Lister for Amazon either via the WordPress.org plugin repository, or by uploading the files to your server
2. After activating the plugin, visit the Amazon account settings page and follow the setup instructions at http://docs.wplab.com/article/85-first-time-setup 

== Frequently Asked Questions ==

= What are the requirements to run WP-Lister? =

WP-Lister requires a recent version of WordPress (4.x) and WooCommerce (2.2 or newer) installed. Your server should run on Linux and have PHP 5.3 or better with cURL support.

= Does WP-Lister support windows servers? =

No, and there are no plans on adding support for IIS.

= Are there any more FAQ? =

Yes, there are. Please check out our growing knowledgebase at http://www.wplab.com/plugins/wp-lister-for-amazon/faq/

== Changelog ==
= 0.9.6.7 =
* fixed issue where activity indicator could show reports in progress when all reports were already processed 
* improved multiple offers indicator on repricing page - explain possible up-pricing issues in tooltip 
* feed generation: leave external_product_id_type empty if there is no external_product_id (parent variations) 
* skip invalid rows when processing inventory report - prevent inserting empty rows in amazon_listings 
* don't allow processing an inventory report that has localized column headers 
* added filter hook wpla_filter_imported_product_data and wpla_filter_imported_condition_html 

= 0.9.6.6.8 =
* fixed issue where sale dates were sent if sale price was intentionally left blank in listing profile 
* fixed inline price editor for large amounts - remove thousands separator from edit price field 
* fixed no change option in min/max price wizard  

= 0.9.6.6.7 =
* fixed sale start and end date not being set automatically 
* fixed repricing changelog showing integer prices when force decimal comma option was enabled  
* feed generation: leave external_product_id_type empty if there is no external_product_id (parent variations) 

= 0.9.6.6.6 =
* added warning note on import page about sale prices not being imported, but being removed when an imported product is updated 
* fixed issue where sale start and end date would be set for rows without a price (like parent variations in a listing data feed) 

= 0.9.6.6.5 =
* added warning on listing page if listings linked to missing products are found 
* added support for tracking details set by Shipment Tracking and Shipstation plugins (use their tracking number and provider in Order Fulfillment feed) 
* if no sale price is set send regular price with sale end date in the past (the only way to remove previously sent sale prices) 
* fixed stored number of pending feeds when multiple accounts are checked 

= 0.9.6.6.4 =
* include item condition note in imported product description 
* automatically create matched listing for simple products when ASIN is entered manually 
* trigger new Price&Quantity feed when updating min/max prices from WooCommerce (tools page) 
* updating reports checks pending ReportRequestIds only (make sure that each report is processed using the account it was requested by) 
* fixed issue where reports for different marketplaces would return the same results 
* fixed shipping date not being sent as UTC when order is manually marked as shipped 
* fixed importing books with multiple authors 
* added more feed templates for amazon.ca 

= 0.9.6.6.3 =
* added option to filter orders by Amazon account on WooCommerce Orders page 
* added prev/next buttons to import preview and fixed select all checkbox on search results 
* import book specific attributes - like author, publisher, binding and date published 
* extended option to set how often to request FBA Shipment reports to apply to FBA Inventory report as well 
* fixed importing item condition and condition note when report contains special characters 
* fixed possible error updating min/max prices 

= 0.9.6.6.2 =
* profile editor: do not require external_product_id if assigned account has the brand registry option enabled 
* update wp_amazon_listings.account_id when updating / applying listing profile 
* fixed issue where FBA enabled products would be marked as out of stock in WooCommerce if FBA stock is zero but still stock left in WC 
* fixed rare issue saving report processing options on import page 

= 0.9.6.6.1 =
* added option to import variations as simple products 
* fall back to import as simple product if there are no variation attributes on the parent listing (fix importing "variations without attributes") 
* fixed issue importing images for very long listing titles 
* improved error handling during importing process 

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

