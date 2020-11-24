<?php

return [
	'removed' => [
		// 'date_created','date_modified'
	],
	'limit'   => 2,
	'paginate'   => 50,
	'type_product' => [
		'ALL_PRODUCT',
		'SPECIFIC_COLLECTIONS'
	],
	'include_variant' => [
		'FIRST_VARIANT',
		'ALL_VARIANT'
	],
	'woocommerce_custom_fields' => [
		'_visibility', '_sku', '_price', '_regular_price', '_sale_price', '_sale_price_dates_from', '_sale_price_dates_to', 'total_sales', '_tax_status', '_tax_class', '_manage_stock', '_stock', '_stock_status', '_backorders', '_low_stock_amount', '_sold_individually', '_weight', '_length', '_width', '_height', '_upsell_ids', '_crosssell_ids', '_purchase_note', '_default_attributes', '_product_attributes', '_virtual', '_downloadable', '_download_limit', '_download_expiry', '_featured', '_downloadable_files', '_wc_rating_count', '_wc_average_rating', '_wc_review_count', '_variation_description', '_thumbnail_id', '_file_paths', '_product_image_gallery', '_product_version', '_wp_old_slug', '_edit_last', '_edit_lock'
	],
	'mapping' => [
		'product_id'			=> 'id',
		'product_title' 		=> 'name',
		'product_description' 	=> 'description',
		'product_vendor' 		=> null,
		'product_product_type' 	=> 'product_type',
		'product_product_url'  	=> 'product_url',
		'variant_variant_url'  	=> 'product_url',
		'product_image_url'    	=> 'image_url',
		'variant_image_url'    	=> 'image_url',
		'variant_inventory_management' => 'manage_stock',
		'variant_id' 		 	=> 'id',
		'variant_parent_id'     => 'parent_id',
		'variant_title' 		=> 'name',
		'variant_price' 		=> 'regular_price',
		'variant_compare_at_price' => 'sale_price',
		'variant_shipping_weight'  => 'weight',
		'variant_shipping_weight_unit' => null,
		'variant_additional_image_url' => 'gallery_image_ids',
		'variant_sku' 					=> 'sku',
		'variant_barcode'				=> null,
		'variant_inventory_quantity' 	=> 'stock_quantity',
		'variant_size' 					=> 'attributes.activity',
		'variant_color' 				=> 'attributes.pattern',
		'variant_material' 				=> 'attributes.material',
		'variant_style'					=> 'attributes.strap',
		'variant_inventory_policy' 		=> null,
		'product_currency' 				=> null,
	]
];