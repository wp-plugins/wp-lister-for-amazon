<?php

class WPLA_FeedTemplateIndex {
	
	static public function get_file_index() {

		// index of feed template files - sites / categories / files
		$file_index = array(
			
			// amazon.com (US)
			'US' => array(
				'site'  => 'amazon.com',
				'code'  => 'US',
				'categories' => array(

					// Baby
					'Baby' => array(
						'title' => 'Baby',
						'templates' => array(
							'Flat.File.Baby-Template.csv',
							'Flat.File.Baby-DataDefinitions.csv',	
							'Flat.File.Baby-ValidValues.csv',								
						),
						'btguides' => array(
							'baby-products_browse_tree_guide.csv',
						),
					),

					// Beauty
					'Beauty' => array(
						'title' => 'Beauty',
						'templates' => array(
							'Flat.File.Beauty-Template.csv',
							'Flat.File.Beauty-DataDefinitions.csv',	
							'Flat.File.Beauty-ValidValues.csv',								
						),
						'btguides' => array(
							'beauty_browse_tree_guide.csv',
						),
					),

					// Camera & Photo
					'CameraAndPhoto' => array(
						'title' => 'Camera & Photo',
						'templates' => array(
							'Flat.File.CameraAndPhoto-Template.csv',
							'Flat.File.CameraAndPhoto-DataDefinitions.csv',	
							'Flat.File.CameraAndPhoto-ValidValues.csv',								
						),
						'btguides' => array(
							'electronics_browse_tree_guide.csv',
						),
					),

					// Cell Phones & Accessories 
					'Wireless' => array(
						'title' => 'Cell Phones & Accessories (Wireless)',
						'templates' => array(
							'Flat.File.Wireless-Template.csv',
							'Flat.File.Wireless-DataDefinitions.csv',	
							'Flat.File.Wireless-ValidValues.csv',								
						),
						'btguides' => array(
							'cellphone-accessories_browse_tree_guide.csv',
						),
					),

					// Computers
					'Computers' => array(
						'title' => 'Computers',
						'templates' => array(
							'Flat.File.Computers-Template.csv',
							'Flat.File.Computers-DataDefinitions.csv',	
							'Flat.File.Computers-ValidValues.csv',								
						),
						'btguides' => array(
							'electronics_browse_tree_guide.csv',
						),
					),

					// Consumer Electronics
					'ConsumerElectronics' => array(
						'title' => 'Consumer Electronics',
						'templates' => array(
							'Flat.File.ConsumerElectronics-Template.csv',
							'Flat.File.ConsumerElectronics-DataDefinitions.csv',	
							'Flat.File.ConsumerElectronics-ValidValues.csv',								
						),
						'btguides' => array(
							'electronics_browse_tree_guide.csv',
						),
					),

					// Grocery & Gourmet Food
					'FoodAndBeverages' => array(
						'title' => 'Grocery & Gourmet Food',
						'templates' => array(
							'Flat.File.FoodAndBeverages-Template.csv',
							'Flat.File.FoodAndBeverages-DataDefinitions.csv',	
							'Flat.File.FoodAndBeverages-ValidValues.csv',								
						),
						'btguides' => array(
							'grocery_browse_tree_guide.csv',
						),
					),

					// Health & Personal Care
					'Health' => array(
						'title' => 'Health & Personal Care',
						'templates' => array(
							'Flat.File.Health-Template.csv',
							'Flat.File.Health-DataDefinitions.csv',	
							'Flat.File.Health-ValidValues.csv',								
						),
						'btguides' => array(
							'health_browse_tree_guide.csv',
						),
					),

					// Home & Garden
					'Home' => array(
						'title' => 'Home & Garden',
						'templates' => array(
							'Flat.File.Home-Template.csv',
							'Flat.File.Home-DataDefinitions.csv',	
							'Flat.File.Home-ValidValues.csv',								
						),
						'btguides' => array(
							'home-kitchen_browse_tree_guide.csv',
							'garden_browse_tree_guide.csv',
							'arts-and-crafts_browse_tree_guide.csv',
						),
					),

					// Lighting
					'Lighting' => array(
						'title' => 'Lighting',
						'templates' => array(
							'Flat.File.Lighting-Template.csv',
							'Flat.File.Lighting-DataDefinitions.csv',	
							'Flat.File.Lighting-ValidValues.csv',								
						),
						'btguides' => array(
							'home-improvement_browse_tree_guide.csv',
						),
					),

					// Music
					'Music' => array(
						'title' => 'Music',
						'templates' => array(
							'Flat.File.Music-Template.csv',
							'Flat.File.Music-DataDefinitions.csv',	
							'Flat.File.Music-ValidValues.csv',								
						),
						'btguides' => array(
						),
					),

					// Musical Instruments
					'MusicalInstruments' => array(
						'title' => 'Musical Instruments',
						'templates' => array(
							'Flat.File.MusicalInstruments-Template.csv',
							'Flat.File.MusicalInstruments-DataDefinitions.csv',	
							'Flat.File.MusicalInstruments-ValidValues.csv',								
						),
						'btguides' => array(
							'musical-instruments_browse_tree_guide.csv',
						),
					),

					// Office Products
					'Office' => array(
						'title' => 'Office Products',
						'templates' => array(
							'Flat.File.Office-Template.csv',
							'Flat.File.Office-DataDefinitions.csv',	
							'Flat.File.Office-ValidValues.csv',								
						),
						'btguides' => array(
							'office-products_browse_tree_guide.csv',
						),
					),

					// Outdoors
					'Outdoors' => array(
						'title' => 'Outdoors',
						'templates' => array(
							'Flat.File.Outdoors-Template.csv',
							'Flat.File.Outdoors-DataDefinitions.csv',	
							'Flat.File.Outdoors-ValidValues.csv',								
						),
						'btguides' => array(
							'sporting-goods_browse_tree_guide.csv',
						),
					),

					// Pet Supplies
					'PetSupplies' => array(
						'title' => 'Pet Supplies',
						'templates' => array(
							'Flat.File.PetSupplies-Template.csv',
							'Flat.File.PetSupplies-DataDefinitions.csv',	
							'Flat.File.PetSupplies-ValidValues.csv',								
						),
						'btguides' => array(
							'pet-supplies_browse_tree_guide.csv',
						),
					),

					// Software & Video Games
					'SWVG' => array(
						'title' => 'Software & Video Games',
						'templates' => array(
							'Flat.File.SWVG-Template.csv',
							'Flat.File.SWVG-DataDefinitions.csv',	
							'Flat.File.SWVG-ValidValues.csv',								
						),
						'btguides' => array(
							'software_browse_tree_guide.csv',
							'videogames_browse_tree_guide.csv',
						),
					),

					// Sports
					'Sports' => array(
						'title' => 'Sports',
						'templates' => array(
							'Flat.File.Sports-Template.csv',
							'Flat.File.Sports-DataDefinitions.csv',	
							'Flat.File.Sports-ValidValues.csv',								
						),
						'btguides' => array(
							'sporting-goods_browse_tree_guide.csv',
						),
					),

					// Tools & Home Improvement
					'HomeImprovement' => array(
						'title' => 'Tools & Home Improvement',
						'templates' => array(
							'Flat.File.HomeImprovement-Template.csv',
							'Flat.File.HomeImprovement-DataDefinitions.csv',	
							'Flat.File.HomeImprovement-ValidValues.csv',								
						),
						'btguides' => array(
							'home-improvement_browse_tree_guide.csv',
						),
					),

					// Toys & Games
					'Toys' => array(
						'title' => 'Toys & Games',
						'templates' => array(
							'Flat.File.Toys-Template.csv',
							'Flat.File.Toys-DataDefinitions.csv',	
							'Flat.File.Toys-ValidValues.csv',								
						),
						'btguides' => array(
							'toys-and-games_browse_tree_guide.csv',
						),
					),

					// Video & DVD
					'Video' => array(
						'title' => 'Video & DVD',
						'templates' => array(
							'Flat.File.Video-Template.csv',
							'Flat.File.Video-DataDefinitions.csv',	
							'Flat.File.Video-ValidValues.csv',								
						),
						'btguides' => array(
						),
					),

					// BookLoader
					'BookLoader' => array(
						'title' => 'Books',
						'templates' => array(
							'Flat.File.BookLoader-Template.csv',
							'Flat.File.BookLoader-DataDefinitions.csv',	
							'Flat.File.BookLoader-ValidValues.csv',								
						),
						'btguides' => array(
						),
					),

					// Miscellaneous
					'Miscellaneous' => array(
						'title' => 'Miscellaneous',
						'templates' => array(
							'Flat.File.Miscellaneous-Template.csv',
							'Flat.File.Miscellaneous-DataDefinitions.csv',	
							// 'Flat.File.Miscellaneous-ValidValues.csv',  // Misc doesn't have valid values
						),
						'btguides' => array(
						),
					),





















					// Automotive & Powersports
					'AutoAccessory' => array(
						'title' => 'Automotive & Powersports',
						'templates' => array(
							'Flat.File.AutoAccessory-Template.csv',
							'Flat.File.AutoAccessory-DataDefinitions.csv',	
							'Flat.File.AutoAccessory-ValidValues.csv',								
							'Flat.File.TiresAndWheels-Template.csv',
							'Flat.File.TiresAndWheels-DataDefinitions.csv',	
							'Flat.File.TiresAndWheels-ValidValues.csv',								
						),
						'btguides' => array(
							'automotive_browse_tree_guide.csv',
						),
					),

					// Clothing & Accessories
					'Clothing' => array(
						'title' => 'Clothing & Accessories',
						'templates' => array(
							'Flat.File.Clothing-Template.csv',
							'Flat.File.Clothing-DataDefinitions.csv',	
							'Flat.File.Clothing-ValidValues.csv',								
						),
						'btguides' => array(
							'apparel_browse_tree_guide.csv',
						),
					),

					// Entertainment Collectibles
					'EntertainmentMemorabilia' => array(
						'title' => 'Entertainment Collectibles',
						'templates' => array(
							'Flat.File.EntertainmentMemorabilia-Template.csv',
							'Flat.File.EntertainmentMemorabilia-DataDefinitions.csv',	
							'Flat.File.EntertainmentMemorabilia-ValidValues.csv',								
						),
						'btguides' => array(
						),
					),

					// Gift Cards
					'GiftCards' => array(
						'title' => 'Gift Cards',
						'templates' => array(
							'Flat.File.GiftCards-Template.csv',
							'Flat.File.GiftCards-DataDefinitions.csv',	
							'Flat.File.GiftCards-ValidValues.csv',								
						),
						'btguides' => array(
						),
					),

					// Jewelry
					'Jewelry' => array(
						'title' => 'Jewelry',
						'templates' => array(
							'Flat.File.Jewelry-Template.csv',
							'Flat.File.Jewelry-DataDefinitions.csv',	
							'Flat.File.Jewelry-ValidValues.csv',								
						),
						'btguides' => array(
							'jewelry_browse_tree_guide.csv',
						),
					),

					// Shoes, Handbags & Sunglasses
					'Shoes' => array(
						'title' => 'Shoes, Handbags & Sunglasses',
						'templates' => array(
							'Flat.File.Shoes-Template.csv',
							'Flat.File.Shoes-DataDefinitions.csv',	
							'Flat.File.Shoes-ValidValues.csv',								
						),
						'btguides' => array(
							'shoes_browse_tree_guide.csv',
						),
					),

					// Sports Collectibles
					'SportsMemorabilia' => array(
						'title' => 'Sports Collectibles',
						'templates' => array(
							'Flat.File.SportsMemorabilia-Template.csv',
							'Flat.File.SportsMemorabilia-DataDefinitions.csv',	
							'Flat.File.SportsMemorabilia-ValidValues.csv',								
						),
						'btguides' => array(
						),
					),

					// Watches
					'Watches' => array(
						'title' => 'Watches',
						'templates' => array(
							'Flat.File.Watches-Template.csv',
							'Flat.File.Watches-DataDefinitions.csv',	
							'Flat.File.Watches-ValidValues.csv',								
						),
						'btguides' => array(
							'watches_browse_tree_guide.csv',
						),
					),


					// ListingLoader
					'ListingLoader' => array(
						'title' => 'ListingLoader',
						'templates' => array(
							'ListingLoader-Template.csv',
							'ListingLoader-DataDefinitions.csv',	
							'ListingLoader-ValidValues.csv',	
						),
						'btguides' => array(
						),
					),


				),
			),
			
			// amazon.co.uk (UK)
			'UK' => array(
				'site'  => 'amazon.co.uk',
				'code'  => 'UK',
				'categories' => array(







					// Baby
					'Baby' => array(
						'title' => 'Baby',
						'templates' => array(
							'Flat.File.Baby.uk-Template.csv',
							'Flat.File.Baby.uk-DataDefinitions.csv',	
							'Flat.File.Baby.uk-ValidValues.csv',								
						),
						'btguides' => array(
							'uk_baby-products_browse_tree_guide.csv',
						),
					),

					// Beauty
					'Beauty' => array(
						'title' => 'Beauty',
						'templates' => array(
							'Flat.File.Beauty.uk-Template.csv',
							'Flat.File.Beauty.uk-DataDefinitions.csv',	
							'Flat.File.Beauty.uk-ValidValues.csv',								
						),
						'btguides' => array(
							'uk_beauty_browse_tree_guide.csv',
						),
					),

					// Computers
					'Computers' => array(
						'title' => 'Computers & Accessories',
						'templates' => array(
							'Flat.File.Computers.uk-Template.csv',
							'Flat.File.Computers.uk-DataDefinitions.csv',	
							'Flat.File.Computers.uk-ValidValues.csv',								
						),
						'btguides' => array(
							'uk_electronics_browse_tree_guide.csv',
						),
					),

					// Consumer Electronics
					'ConsumerElectronics' => array(
						'title' => 'Consumer Electronics',
						'templates' => array(
							'Flat.File.ConsumerElectronics.uk-Template.csv',
							'Flat.File.ConsumerElectronics.uk-DataDefinitions.csv',	
							'Flat.File.ConsumerElectronics.uk-ValidValues.csv',								
						),
						'btguides' => array(
							'uk_electronics_browse_tree_guide.csv',
						),
					),

					// Grocery & Beverages
					'FoodAndBeverages' => array(
						'title' => 'Grocery & Beverages',
						'templates' => array(
							'Flat.File.FoodAndBeverages.uk-Template.csv',
							'Flat.File.FoodAndBeverages.uk-DataDefinitions.csv',	
							'Flat.File.FoodAndBeverages.uk-ValidValues.csv',								
						),
						'btguides' => array(
							'uk_food_browse_tree_guide.csv',
						),
					),

					// Health & Personal Care
					'Health' => array(
						'title' => 'Health & Personal Care',
						'templates' => array(
							'Flat.File.Health.uk-Template.csv',
							'Flat.File.Health.uk-DataDefinitions.csv',	
							'Flat.File.Health.uk-ValidValues.csv',								
						),
						'btguides' => array(
							'uk_drugstore_browse_tree_guide.csv',
						),
					),

					// Home & Garden
					'Home' => array(
						'title' => 'Home & Garden',
						'templates' => array(
							'Flat.File.Home.uk-Template.csv',
							'Flat.File.Home.uk-DataDefinitions.csv',	
							'Flat.File.Home.uk-ValidValues.csv',								
						),
						'btguides' => array(
							'uk_home-garden_browse_tree_guide.csv',
						),
					),

					// Lighting
					'Lighting' => array(
						'title' => 'Lighting',
						'templates' => array(
							'Flat.File.Lighting.uk-Template.csv',
							'Flat.File.Lighting.uk-DataDefinitions.csv',	
							'Flat.File.Lighting.uk-ValidValues.csv',								
						),
						'btguides' => array(
							'uk_lighting_browse_tree_guide.csv',
						),
					),

					// Musical Instruments
					'MusicalInstruments' => array(
						'title' => 'Musical Instruments',
						'templates' => array(
							'Flat.File.MusicalInstruments.uk-Template.csv',
							'Flat.File.MusicalInstruments.uk-DataDefinitions.csv',	
							'Flat.File.MusicalInstruments.uk-ValidValues.csv',								
						),
						'btguides' => array(
							'uk_musical-instruments_browse_tree_guide.csv',
						),
					),

					// Office
					'Office' => array(
						'title' => 'Office',
						'templates' => array(
							'Flat.File.Office.uk-Template.csv',
							'Flat.File.Office.uk-DataDefinitions.csv',	
							'Flat.File.Office.uk-ValidValues.csv',								
						),
						'btguides' => array(
							'uk_office-products_browse_tree_guide.csv',
						),
					),

					// Pet Supplies
					'PetSupplies' => array(
						'title' => 'Pet Supplies',
						'templates' => array(
							'Flat.File.PetSupplies.uk-Template.csv',
							'Flat.File.PetSupplies.uk-DataDefinitions.csv',	
							'Flat.File.PetSupplies.uk-ValidValues.csv',								
						),
						'btguides' => array(
							'uk_pet-supplies_browse_tree_guide.csv',
						),
					),

					// Software & Video Games
					'SWVG' => array(
						'title' => 'Software & Video Games',
						'templates' => array(
							'Flat.File.SWVG.uk-Template.csv',
							'Flat.File.SWVG.uk-DataDefinitions.csv',	
							'Flat.File.SWVG.uk-ValidValues.csv',								
						),
						'btguides' => array(
							'uk_software_browse_tree_guide.csv',
							'uk_games_browse_tree_guide.csv',
						),
					),

					// Sports
					'Sports' => array(
						'title' => 'Sports',
						'templates' => array(
							'Flat.File.Sports.uk-Template.csv',
							'Flat.File.Sports.uk-DataDefinitions.csv',	
							'Flat.File.Sports.uk-ValidValues.csv',								
						),
						'btguides' => array(
							'uk_sports_browse_tree_guide.csv',
						),
					),

					// Home Improvement
					'HomeImprovement' => array(
						'title' => 'Home Improvement',
						'templates' => array(
							'Flat.File.HomeImprovement.uk-Template.csv',
							'Flat.File.HomeImprovement.uk-DataDefinitions.csv',	
							'Flat.File.HomeImprovement.uk-ValidValues.csv',								
						),
						'btguides' => array(
							'uk_tools_browse_tree_guide.csv',
						),
					),

					// Toys
					'Toys' => array(
						'title' => 'Toys',
						'templates' => array(
							'Flat.File.Toys.uk-Template.csv',
							'Flat.File.Toys.uk-DataDefinitions.csv',	
							'Flat.File.Toys.uk-ValidValues.csv',								
						),
						'btguides' => array(
							'uk_kids_browse_tree_guide.csv',
						),
					),

					// BookLoader
					'BookLoader' => array(
						'title' => 'Books',
						'templates' => array(
							'Flat.File.BookLoader.uk-Template.csv',
							'Flat.File.BookLoader.uk-DataDefinitions.csv',	
							'Flat.File.BookLoader.uk-ValidValues.csv',								
						),
						'btguides' => array(
						),
					),



					// Automotive & Motorcycle
					'AutoAccessory' => array(
						'title' => 'Automotive & Motorcycle',
						'templates' => array(
							'Flat.File.AutoAccessory.uk-Template.csv',
							'Flat.File.AutoAccessory.uk-DataDefinitions.csv',	
							'Flat.File.AutoAccessory.uk-ValidValues.csv',								
						),
						'btguides' => array(
							'uk_automotive_browse_tree_guide.csv',
						),
					),

					// Clothing
					'Clothing' => array(
						'title' => 'Clothing',
						'templates' => array(
							'Flat.File.Clothing.uk-Template.csv',
							'Flat.File.Clothing.uk-DataDefinitions.csv',	
							'Flat.File.Clothing.uk-ValidValues.csv',								
						),
						'btguides' => array(
							'uk_apparel_browse_tree_guide.csv',
						),
					),

					// Jewelry
					'Jewelry' => array(
						'title' => 'Jewelry',
						'templates' => array(
							'Flat.File.Jewelry.uk-Template.csv',
							'Flat.File.Jewelry.uk-DataDefinitions.csv',	
							'Flat.File.Jewelry.uk-ValidValues.csv',								
						),
						'btguides' => array(
							'uk_jewelry_browse_tree_guide.csv',
						),
					),

					// Shoes & Accessories
					'Shoes' => array(
						'title' => 'Shoes & Accessories',
						'templates' => array(
							'Flat.File.Shoes.uk-Template.csv',
							'Flat.File.Shoes.uk-DataDefinitions.csv',	
							'Flat.File.Shoes.uk-ValidValues.csv',								
						),
						'btguides' => array(
							'uk_shoes_browse_tree_guide.csv',
						),
					),

					// Watches
					'Watches' => array(
						'title' => 'Watches',
						'templates' => array(
							'Flat.File.Watches.uk-Template.csv',
							'Flat.File.Watches.uk-DataDefinitions.csv',	
							'Flat.File.Watches.uk-ValidValues.csv',								
						),
						'btguides' => array(
							'uk_watches_browse_tree_guide.csv',
						),
					),


					// ListingLoader
					'ListingLoader' => array(
						'title' => 'ListingLoader',
						'templates' => array(
							'ListingLoader-Template.csv',
							'ListingLoader-DataDefinitions.csv',	
							'ListingLoader-ValidValues.csv',	
						),
						'btguides' => array(
						),
					),


				),
			),
			
			
			// amazon.fr (FR)
			'FR' => array(
				'site'  => 'amazon.fr',
				'code'  => 'FR',
				'categories' => array(






					// Parfum et Beauté
					'Beauty' => array(
						'title' => 'Parfum et Beauté',
						'templates' => array(
							'Flat.File.Beauty.fr-Template.csv',
							'Flat.File.Beauty.fr-DataDefinitions.csv',	
							'Flat.File.Beauty.fr-ValidValues.csv',								
						),
						'btguides' => array(
							'fr_beauty_browse_tree_guide.csv',
						),
					),

					// Informatique
					'Computers' => array(
						'title' => 'Informatique',
						'templates' => array(
							'Flat.File.Computers.fr-Template.csv',
							'Flat.File.Computers.fr-DataDefinitions.csv',	
							'Flat.File.Computers.fr-ValidValues.csv',								
						),
						'btguides' => array(
							'fr_computers_browse_tree_guide.csv',
						),
					),

					// High-Tech
					'ConsumerElectronics' => array(
						'title' => 'High-Tech',
						'templates' => array(
							'Flat.File.ConsumerElectronics.fr-Template.csv',
							'Flat.File.ConsumerElectronics.fr-DataDefinitions.csv',	
							'Flat.File.ConsumerElectronics.fr-ValidValues.csv',								
						),
						'btguides' => array(
							'fr_electronics_browse_tree_guide.csv',
						),
					),

					// Santé et Soins du corps
					'Health' => array(
						'title' => 'Santé et Soins du corps',
						'templates' => array(
							'Flat.File.Health.fr-Template.csv',
							'Flat.File.Health.fr-DataDefinitions.csv',	
							'Flat.File.Health.fr-ValidValues.csv',								
						),
						'btguides' => array(
							'fr_hpc_browse_tree_guide.csv',
						),
					),

					// Maison
					'Home' => array(
						'title' => 'Maison',
						'templates' => array(
							'Flat.File.Home.fr-Template.csv',
							'Flat.File.Home.fr-DataDefinitions.csv',	
							'Flat.File.Home.fr-ValidValues.csv',								
						),
						'btguides' => array(
							'fr_kitchen_browse_tree_guide.csv',
						),
					),

					// Luminaires et Eclairage
					'Lighting' => array(
						'title' => 'Luminaires et Eclairage',
						'templates' => array(
							'Flat.File.Lighting.fr-Template.csv',
							'Flat.File.Lighting.fr-DataDefinitions.csv',	
							'Flat.File.Lighting.fr-ValidValues.csv',								
						),
						'btguides' => array(
							'fr_luminaires-eclairage_browse_tree_guide.csv',
						),
					),

					// Fournitures Scolaires et de Bureau
					'Office' => array(
						'title' => 'Fournitures Scolaires et de Bureau',
						'templates' => array(
							'Flat.File.Office.fr-Template.csv',
							'Flat.File.Office.fr-DataDefinitions.csv',	
							'Flat.File.Office.fr-ValidValues.csv',								
						),
						'btguides' => array(
							'fr_office-products_browse_tree_guide.csv',
						),
					),

					// Animalerie
					'PetSupplies' => array(
						'title' => 'Animalerie',
						'templates' => array(
							'Flat.File.PetSupplies.fr-Template.csv',
							'Flat.File.PetSupplies.fr-DataDefinitions.csv',	
							'Flat.File.PetSupplies.fr-ValidValues.csv',								
						),
						'btguides' => array(
							'fr_pet-supplies_browse_tree_guide.csv',
						),
					),

					// Logiciels & Jeux Vidéos
					'SWVG' => array(
						'title' => 'Logiciels & Jeux Vidéos',
						'templates' => array(
							'Flat.File.SWVG.fr-Template.csv',
							'Flat.File.SWVG.fr-DataDefinitions.csv',	
							'Flat.File.SWVG.fr-ValidValues.csv',								
						),
						'btguides' => array(
							'fr_software_browse_tree_guide.csv',
							'fr_videogames_browse_tree_guide.csv',
						),
					),

					// Sports et Loisirs
					'Sports' => array(
						'title' => 'Sports et Loisirs',
						'templates' => array(
							'Flat.File.Sports.fr-Template.csv',
							'Flat.File.Sports.fr-DataDefinitions.csv',	
							'Flat.File.Sports.fr-ValidValues.csv',								
						),
						'btguides' => array(
							'fr_sports_browse_tree_guide.csv',
						),
					),

					// ToysBaby
					'ToysBaby' => array(
						'title' => 'ToysBaby',
						'templates' => array(
							'Flat.File.ToysBaby.fr-Template.csv',
							'Flat.File.ToysBaby.fr-DataDefinitions.csv',	
							'Flat.File.ToysBaby.fr-ValidValues.csv',								
						),
						'btguides' => array(
							'fr_baby_browse_tree_guide.csv',
							'fr_toys_browse_tree_guide.csv',
						),
					),

					// Bricolage
					'HomeImprovement' => array(
						'title' => 'Bricolage',
						'templates' => array(
							'Flat.File.HomeImprovement.fr-Template.csv',
							'Flat.File.HomeImprovement.fr-DataDefinitions.csv',	
							'Flat.File.HomeImprovement.fr-ValidValues.csv',								
						),
						'btguides' => array(
							'fr_tools_browse_tree_guide.csv',
						),
					),


					// Automobile
					'AutoAccessory' => array(
						'title' => 'Automobile',
						'templates' => array(
							'Flat.File.AutoAccessory.fr-Template.csv',
							'Flat.File.AutoAccessory.fr-DataDefinitions.csv',	
							'Flat.File.AutoAccessory.fr-ValidValues.csv',								
						),
						'btguides' => array(
							'fr_automotive_browse_tree_guide.csv',
						),
					),

					// Vêtements et Accessoires
					'Clothing' => array(
						'title' => 'Vêtements et Accessoires',
						'templates' => array(
							'Flat.File.Clothing.fr-Template.csv',
							'Flat.File.Clothing.fr-DataDefinitions.csv',	
							'Flat.File.Clothing.fr-ValidValues.csv',								
						),
						'btguides' => array(
							'fr_apparel_browse_tree_guide.csv',
						),
					),

					// Bijoux
					'Jewelry' => array(
						'title' => 'Bijoux',
						'templates' => array(
							'Flat.File.Jewelry.fr-Template.csv',
							'Flat.File.Jewelry.fr-DataDefinitions.csv',	
							'Flat.File.Jewelry.fr-ValidValues.csv',								
						),
						'btguides' => array(
							'fr_jewelry_browse_tree_guide.csv',
						),
					),

					// Chaussures, Sacs à Main et Maroquinerie
					'Shoes' => array(
						'title' => 'Chaussures, Sacs à Main et Maroquinerie',
						'templates' => array(
							'Flat.File.Shoes.fr-Template.csv',
							'Flat.File.Shoes.fr-DataDefinitions.csv',	
							'Flat.File.Shoes.fr-ValidValues.csv',								
						),
						'btguides' => array(
							'fr_shoes_browse_tree_guide.csv',
						),
					),

					// Montres
					'Watches' => array(
						'title' => 'Montres',
						'templates' => array(
							'Flat.File.Watches.fr-Template.csv',
							'Flat.File.Watches.fr-DataDefinitions.csv',	
							'Flat.File.Watches.fr-ValidValues.csv',								
						),
						'btguides' => array(
							'fr_watches_browse_tree_guide.csv',
						),
					),


					// ListingLoader
					'ListingLoader' => array(
						'title' => 'ListingLoader',
						'templates' => array(
							'ListingLoader-Template.csv',
							'ListingLoader-DataDefinitions.csv',	
							'ListingLoader-ValidValues.csv',	
						),
						'btguides' => array(
						),
					),


				),
			),
			

			// amazon.ca (CA)
			'CA' => array(
				'site'  => 'amazon.ca',
				'code'  => 'CA',
				'categories' => array(

					// Baby
					'Baby' => array(
						'title' => 'Baby',
						'templates' => array(
							'Flat_File_Baby_ca-Template.csv',
							'Flat_File_Baby_ca-DataDefinitions.csv',	
							'Flat_File_Baby_ca-ValidValues.csv',								
						),
						'btguides' => array(
							'ca_baby-products_browse_tree_guide.csv',
						),
					),

					// Beauty
					'Beauty' => array(
						'title' => 'Beauty',
						'templates' => array(
							'Flat_File_Beauty_ca-Template.csv',
							'Flat_File_Beauty_ca-DataDefinitions.csv',	
							'Flat_File_Beauty_ca-ValidValues.csv',								
						),
						'btguides' => array(
							'ca_beauty_browse_tree_guide.csv',
						),
					),

					// Computers
					'Computers' => array(
						'title' => 'Computers & Accessories',
						'templates' => array(
							'Flat_File_Computers_ca-Template.csv',
							'Flat_File_Computers_ca-DataDefinitions.csv',	
							'Flat_File_Computers_ca-ValidValues.csv',								
						),
						'btguides' => array(
							'ca_ce_browse_tree_guide.csv',
						),
					),

					// Grocery & Beverages
					'FoodAndBeverages' => array(
						'title' => 'Grocery & Beverages',
						'templates' => array(
							'Flat_File_FoodAndBeverages_ca-Template.csv',
							'Flat_File_FoodAndBeverages_ca-DataDefinitions.csv',	
							'Flat_File_FoodAndBeverages_ca-ValidValues.csv',								
						),
						'btguides' => array(
							'ca_grocery_browse_tree_guide.csv',
						),
					),

					// Home & Garden
					'Home' => array(
						'title' => 'Home & Garden',
						'templates' => array(
							'Flat_File_Home_ca-Template.csv',
							'Flat_File_Home_ca-DataDefinitions.csv',	
							'Flat_File_Home_ca-ValidValues.csv',								
						),
						'btguides' => array(
							'ca_garden_browse_tree_guide.csv',
							'ca_kitchen_browse_tree_guide.csv',
						),
					),

					// Toys
					'Toys' => array(
						'title' => 'Toys',
						'templates' => array(
							'Flat_File_Toys_ca-Template.csv',
							'Flat_File_Toys_ca-DataDefinitions.csv',	
							'Flat_File_Toys_ca-ValidValues.csv',								
						),
						'btguides' => array(
							'ca_toys_browse_tree_guide.csv',
						),
					),

					// Jewelry
					'Jewelry' => array(
						'title' => 'Jewelry',
						'templates' => array(
							'Flat_File_Jewelry_ca-Template.csv',
							'Flat_File_Jewelry_ca-DataDefinitions.csv',	
							'Flat_File_Jewelry_ca-ValidValues.csv',								
						),
						'btguides' => array(
							'ca_jewelry_browse_tree_guide.csv',
						),
					),

					// Watches
					'Watches' => array(
						'title' => 'Watches',
						'templates' => array(
							'Flat_File_Watches_ca-Template.csv',
							'Flat_File_Watches_ca-DataDefinitions.csv',	
							'Flat_File_Watches_ca-ValidValues.csv',								
						),
						'btguides' => array(
							'ca_watches_browse_tree_guide.csv',
						),
					),


					// ListingLoader
					'ListingLoader' => array(
						'title' => 'ListingLoader',
						'templates' => array(
							'ListingLoader-Template.csv',
							'ListingLoader-DataDefinitions.csv',	
							'ListingLoader-ValidValues.csv',	
						),
						'btguides' => array(
						),
					),

				),
			),
			

			// amazon.de (DE)
			'DE' => array(
				'site'  => 'amazon.de',
				'code'  => 'DE',
				'categories' => array(

					// Beauty
					'Beauty' => array(
						'title' => 'Beauty',
						'templates' => array(
							'Flat_File_Beauty_de-Template.csv',
							'Flat_File_Beauty_de-Data.csv',	
							'Flat_File_Beauty_de-Values.csv',								
						),
						'btguides' => array(
							'de_beauty_browse_tree_guide.csv',
						),
					),

					// Bekleidung & Accessories
					'Clothing' => array(
						'title' => 'Bekleidung & Accessories',
						'templates' => array(
							'Flat_File_Clothing_de-Template.csv',
							'Flat_File_Clothing_de-Data.csv',	
							'Flat_File_Clothing_de-Values.csv',								
						),
						'btguides' => array(
							'de_apparel_browse_tree_guide.csv',
						),
					),

					// Beleuchtung
					'Lighting' => array(
						'title' => 'Beleuchtung',
						'templates' => array(
							'Flat_File_Lighting_de-Template.csv',
							'Flat_File_Lighting_de-Data.csv',	
							'Flat_File_Lighting_de-Values.csv',								
						),
						'btguides' => array(
							'de_lighting_browse_tree_guide.csv',
						),
					),

					// Baumarkt
					'HomeImprovement' => array(
						'title' => 'Baumarkt',
						'templates' => array(
							'Flat_File_HomeImprovement_de-Template.csv',
							'Flat_File_HomeImprovement_de-Data.csv',	
							'Flat_File_HomeImprovement_de-Values.csv',								
						),
						'btguides' => array(
							'de_tools-sgp_browse_tree_guide.csv',
						),
					),

					// Computer & Zubehör
					'Computers' => array(
						'title' => 'Computer & Zubehör',
						'templates' => array(
							'Flat_File_Computers_de-Template.csv',
							'Flat_File_Computers_de-Data.csv',	
							'Flat_File_Computers_de-Values.csv',								
						),
						'btguides' => array(
							'de_computers_browse_tree_guide.csv',
						),
					),

					// Drogerie & Körperpflege
					'Health' => array(
						'title' => 'Drogerie & Körperpflege',
						'templates' => array(
							'Flat_File_Health_de-Template.csv',
							'Flat_File_Health_de-Data.csv',	
							'Flat_File_Health_de-Values.csv',								
						),
						'btguides' => array(
							'de_drugstore_browse_tree_guide.csv',
						),
					),

					// Elektronik & Foto
					'ConsumerElectronics' => array(
						'title' => 'Elektronik & Foto',
						'templates' => array(
							'Flat_File_ConsumerElectronics_de-Template.csv',
							'Flat_File_ConsumerElectronics_de-Data.csv',	
							'Flat_File_ConsumerElectronics_de-Values.csv',								
						),
						'btguides' => array(
							'de_ce_browse_tree_guide.csv',
						),
					),

					// Haus & Garten
					'Home' => array(
						'title' => 'Haus & Garten',
						'templates' => array(
							'Flat_File_Home_de-Template.csv',
							'Flat_File_Home_de-Data.csv',	
							'Flat_File_Home_de-Values.csv',								
						),
						'btguides' => array(
							'de_garden_browse_tree_guide.csv',
							'de_kitchen_browse_tree_guide.csv',
						),
					),

					// Lebensmittel & Getränke
					'FoodAndBeverages' => array(
						'title' => 'Lebensmittel & Getränke',
						'templates' => array(
							'Flat_File_FoodAndBeverages_de-Template.csv',
							'Flat_File_FoodAndBeverages_de-Data.csv',	
							'Flat_File_FoodAndBeverages_de-Values.csv',								
						),
						'btguides' => array(
							'de_food_browse_tree_guide.csv',
						),
					),

					// Spielzeug & Baby
					'ToysBaby' => array(
						'title' => 'Spielzeug & Baby',
						'templates' => array(
							'Flat_File_ToysBaby_de-Template.csv',
							'Flat_File_ToysBaby_de-Data.csv',	
							'Flat_File_ToysBaby_de-Values.csv',								
						),
						'btguides' => array(
							'de_baby_browse_tree_guide.csv',
							'de_toys_browse_tree_guide.csv',
						),
					),

					// Schmuck
					'Jewelry' => array(
						'title' => 'Schmuck',
						'templates' => array(
							'Flat_File_Jewelry_de-Template.csv',
							'Flat_File_Jewelry_de-Data.csv',	
							'Flat_File_Jewelry_de-Values.csv',								
						),
						'btguides' => array(
							'de_jewelry_browse_tree_guide.csv',
						),
					),

					// Uhren
					'Watches' => array(
						'title' => 'Uhren',
						'templates' => array(
							'Flat_File_Watches_de-Template.csv',
							'Flat_File_Watches_de-Data.csv',	
							'Flat_File_Watches_de-Values.csv',								
						),
						'btguides' => array(
							'de_watches_browse_tree_guide.csv',
						),
					),

					// ListingLoader
					'ListingLoader' => array(
						'title' => 'ListingLoader',
						'templates' => array(
							'ListingLoader-Template.csv',
							'ListingLoader-DataDefinitions.csv',	
							'ListingLoader-ValidValues.csv',	
						),
						'btguides' => array(
						),
					),


				),
			), // amazon.de

			// amazon.it (IT)
			'IT' => array(
				'site'  => 'amazon.it',
				'code'  => 'IT',
				'categories' => array(

					// Casa e giardino
					'Home' => array(
						'title' => 'Casa e giardino',
						'templates' => array(
							'Flat_File_Home_it-Template.csv',
							'Flat_File_Home_it-Data.csv',	
							'Flat_File_Home_it-Values.csv',								
						),
						'btguides' => array(
							'it_garden_browse_tree_guide.csv',
							'it_kitchen_browse_tree_guide.csv',
						),
					),

					// Computers
					'Computers' => array(
						'title' => 'Computers',
						'templates' => array(
							'Flat_File_Computers_it-Template.csv',
							'Flat_File_Computers_it-Data.csv',	
							'Flat_File_Computers_it-Values.csv',								
						),
						'btguides' => array(
							'it_computers_browse_tree_guide.csv',
						),
					),

					// Elettronica di consumo e accessori
					'CE' => array(
						'title' => 'Elettronica di consumo e accessori',
						'templates' => array(
							'Flat_File_CE_it-Template.csv',
							'Flat_File_CE_it-Data.csv',	
							'Flat_File_CE_it-Values.csv',								
						),
						'btguides' => array(
							'it_electronics_browse_tree_guide.csv',
						),
					),

					// Fai da te
					'Tools' => array(
						'title' => 'Fai da te',
						'templates' => array(
							'Flat_File_Tools_it-Template.csv',
							'Flat_File_Tools_it-Data.csv',	
							'Flat_File_Tools_it-Values.csv',								
						),
						'btguides' => array(
							'it_tools_browse_tree_guide.csv',
						),
					),

					// Gioielli
					'Jewelry' => array(
						'title' => 'Gioielli',
						'templates' => array(
							'Flat_File_Jewelry_it-Template.csv',
							'Flat_File_Jewelry_it-Data.csv',	
							'Flat_File_Jewelry_it-Values.csv',								
						),
						'btguides' => array(
							'it_jewelry_browse_tree_guide.csv',
						),
					),

					// Orologi
					'Watches' => array(
						'title' => 'Orologi',
						'templates' => array(
							'Flat_File_Watches_it-Template.csv',
							'Flat_File_Watches_it-Data.csv',	
							'Flat_File_Watches_it-Values.csv',								
						),
						'btguides' => array(
							'it_watches_browse_tree_guide.csv',
						),
					),

					// Scarpe e borse
					'Shoes' => array(
						'title' => 'Scarpe e borse',
						'templates' => array(
							'Flat_File_Shoes_it-Template.csv',
							'Flat_File_Shoes_it-Data.csv',	
							'Flat_File_Shoes_it-Values.csv',								
						),
						'btguides' => array(
							'it_shoes_browse_tree_guide.csv',
						),
					),


					// ListingLoader
					'ListingLoader' => array(
						'title' => 'ListingLoader',
						'templates' => array(
							'ListingLoader-Template.csv',
							'ListingLoader-DataDefinitions.csv',	
							'ListingLoader-ValidValues.csv',	
						),
						'btguides' => array(
						),
					),


				),
			), // amazon.it

			// amazon.es (ES)
			'ES' => array(
				'site'  => 'amazon.es',
				'code'  => 'ES',
				'categories' => array(

					// Hogar y Cocina
					'Home' => array(
						'title' => 'Hogar y Cocina',
						'templates' => array(
							'Flat_File_Home_es-Template.csv',
							'Flat_File_Home_es-Data.csv',	
							'Flat_File_Home_es-Values.csv',								
						),
						'btguides' => array(
							'es_kitchen_browse_tree_guide.csv',
						),
					),

					// Informática
					'Computers' => array(
						'title' => 'Informática',
						'templates' => array(
							'Flat_File_Computers_es-Template.csv',
							'Flat_File_Computers_es-Data.csv',	
							'Flat_File_Computers_es-Values.csv',								
						),
						'btguides' => array(
							'es_computers_browse_tree_guide.csv',
						),
					),

					// Electrónica y accesorios de electrónica
					'CE' => array(
						'title' => 'Electrónica y accesorios de electrónica',
						'templates' => array(
							'Flat_File_CE_es-Template.csv',
							'Flat_File_CE_es-Data.csv',	
							'Flat_File_CE_es-Values.csv',								
						),
						'btguides' => array(
							'es_electronics_browse_tree_guide.csv',
						),
					),

					// Bricolaje y Herramientas
					'Tools' => array(
						'title' => 'Bricolaje y Herramientas',
						'templates' => array(
							'Flat_File_Tools_es-Template.csv',
							'Flat_File_Tools_es-Data.csv',	
							'Flat_File_Tools_es-Values.csv',								
						),
						'btguides' => array(
							'es_tools_browse_tree_guide.csv',
						),
					),

					// Joyería
					'Jewelry' => array(
						'title' => 'Joyería',
						'templates' => array(
							'Flat_File_Jewelry_es-Template.csv',
							'Flat_File_Jewelry_es-Data.csv',	
							'Flat_File_Jewelry_es-Values.csv',								
						),
						'btguides' => array(
							'es_jewelry_browse_tree_guide.csv',
						),
					),

					// Relojes
					'Watches' => array(
						'title' => 'Relojes',
						'templates' => array(
							'Flat_File_Watches_es-Template.csv',
							'Flat_File_Watches_es-Data.csv',	
							'Flat_File_Watches_es-Values.csv',								
						),
						'btguides' => array(
							'es_watches_browse_tree_guide.csv',
						),
					),


					// ListingLoader
					'ListingLoader' => array(
						'title' => 'ListingLoader',
						'templates' => array(
							'ListingLoader-Template.csv',
							'ListingLoader-DataDefinitions.csv',	
							'ListingLoader-ValidValues.csv',	
						),
						'btguides' => array(
						),
					),

				),
			), // amazon.es

			// amazon.co.jp (JP)
			'JP' => array(
				'site'  => 'amazon.co.jp',
				'code'  => 'JP',
				'categories' => array(

					// Computers
					'Computers' => array(
						'title' => 'Computers',
						'templates' => array(
							'Flat.File.Computers-Template.csv',
							'Flat.File.Computers-DataDefinitions.csv',	
							'Flat.File.Computers-ValidValues.csv',								
						),
						'btguides' => array(
							'electronics_browse_tree_guide.csv',
						),
					),

					// Consumer Electronics
					'ConsumerElectronics' => array(
						'title' => 'Consumer Electronics',
						'templates' => array(
							'Flat.File.ConsumerElectronics-Template.csv',
							'Flat.File.ConsumerElectronics-DataDefinitions.csv',	
							'Flat.File.ConsumerElectronics-ValidValues.csv',								
						),
						'btguides' => array(
							'electronics_browse_tree_guide.csv',
						),
					),

					// Home & Garden
					'Home' => array(
						'title' => 'Home & Garden',
						'templates' => array(
							'Flat.File.Home-Template.csv',
							'Flat.File.Home-DataDefinitions.csv',	
							'Flat.File.Home-ValidValues.csv',								
						),
						'btguides' => array(
							'home-kitchen_browse_tree_guide.csv',
							'garden_browse_tree_guide.csv',
							'arts-and-crafts_browse_tree_guide.csv',
						),
					),

					// Pet Supplies
					'PetSupplies' => array(
						'title' => 'Pet Supplies',
						'templates' => array(
							'Flat.File.PetSupplies-Template.csv',
							'Flat.File.PetSupplies-DataDefinitions.csv',	
							'Flat.File.PetSupplies-ValidValues.csv',								
						),
						'btguides' => array(
							'pet-supplies_browse_tree_guide.csv',
						),
					),

					// ListingLoader
					'ListingLoader' => array(
						'title' => 'ListingLoader',
						'templates' => array(
							'ListingLoader-Template.csv',
							'ListingLoader-DataDefinitions.csv',	
							'ListingLoader-ValidValues.csv',	
						),
						'btguides' => array(
						),
					),

				),
			), // amazon.co.jp

		);

		return $file_index;

	} // get_tpl_index()

} // class WPLA_FeedTemplateIndex
