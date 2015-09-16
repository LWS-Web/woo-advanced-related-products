<?php
/*
Plugin Name: Woo Advanced Related Products
Plugin URI: -
Description: Replaces the default related products in single product view. You can change the order, products per page and number of columns.
Author: Mo
Version: 1.0.1
Author URI: -
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: woo-arp
Domain Path: /lang
*/

/**
 * Exit if accessed directly
 **/
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Load plugin textdomain
 **/
add_action( 'plugins_loaded', 'woo_arp_load_textdomain' );
function woo_arp_load_textdomain() {
  load_plugin_textdomain( 'woo-arp', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' ); 
}

/**
 * Check if WC is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

/**
 * Create "Settings" link for plugin
 **/
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'woo_arp_plugin_links' );
function woo_arp_plugin_links( $links ) {
   $links[] = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=wc-settings&tab=products&section=woo_arp') ) .'">'.__('Settings', 'woo-arp').'</a>';
   return $links;
}// END: "function woo_arp_plugin_links"


/**
 * Create a new section tab, in "WooCommerce > Settings > Products > Advanced Related Products" 
 **/
add_filter( 'woocommerce_get_sections_products', 'woo_arp_wc_section' );
function woo_arp_wc_section( $sections ) {
	
	$sections['woo_arp'] = __( 'Advanced Related Products', 'woo-arp' );
	return $sections;
	
}// END: "function woo_arp_wc_section"


/**
 * Add settings to the new "Related Products Advanced" tab
 */
add_filter( 'woocommerce_get_settings_products', 'woo_arp_wc_settings', 10, 2 );
function woo_arp_wc_settings( $settings, $current_section ) {

	// Check if the current section is what we want
	if ( $current_section == 'woo_arp' ) {
		$settings_woo_arp = array();

		// Add a title to the settings tab
		$settings_woo_arp[] = array(
			'id'	=> 'woo_arp',
			'type'	=> 'title',
			'name'	=> __( 'Advanced Related Products', 'woo-arp' )
		);

		// "Activate Advanced Related Products" checkbox
		$settings_woo_arp[] = array(
			'id'	=> 'woo_arp_active',
			'type'  => 'checkbox',
			'name'  => __( 'Activate Advanced Related Products', 'woo-arp' ),
			'desc'  => __( 'This will automatically hide the default related products, and instead show the new Advanced Related Products container.', 'woo-arp' )
		);

		// "Hide Up-Sells" checkbox
		$settings_woo_arp[] = array(
			'id'   	=> 'woo_arp_upsells',
			'type'  => 'checkbox',
			'name'  => __( 'Hide Up-Sells', 'woo-arp' ),
			'desc'  => __( 'Activate to hide the Up-Sells.', 'woo-arp' )
		);

		// "Taxonomy" select
		$settings_woo_arp[] = array(
			'id'       	=> 'woo_arp_taxonomy',
			'type'     	=> 'select',
			'options'	=> array(
				'product_cat'	=> __( 'Product Categories', 'woo-arp' ),
      			'Product_tag'	=> __( 'Product Tags', 'woo-arp' )
			),
			'class'		=> 'wc-enhanced-select',
			'std'     	=> 'product_cat', // WooCommerce < 2.0
    		'default' 	=> 'product_cat', // WooCommerce >= 2.0
			'name'     	=> __( 'Taxonomy', 'woo-arp' ),
			'desc'     	=> __( 'Select the taxonomy which generates the related products. Default: Product Categories', 'woo-arp' )
		);

		// "Order by" select
		$settings_woo_arp[] = array(
			'id'       	=> 'woo_arp_orderby',
			'type'     	=> 'select',
			'options'	=> array(
				'rand'			=> __( 'Random', 'woo-arp' ),
				'ID'			=> __( 'Post ID', 'woo-arp' ),
				'author'		=> __( 'Post author', 'woo-arp' ),
				'title'			=> __( 'Post title', 'woo-arp' ),
				'name'			=> __( 'Post slug', 'woo-arp' ),
				'date'			=> __( 'Post date', 'woo-arp' ),
      			'menu_order'	=> __( 'Menu order', 'woo-arp' )
			),
			'class'		=> 'wc-enhanced-select',
			'std'     	=> 'rand', // WooCommerce < 2.0
    		'default' 	=> 'rand', // WooCommerce >= 2.0
			'name'     	=> __( 'Order by', 'woo-arp' ),
			'desc'     	=> __( 'Select the general order of the related products. Default: Random', 'woo-arp' )
		);

		// "Order" select
		$settings_woo_arp[] = array(
			'id'       	=> 'woo_arp_order',
			'type'     	=> 'select',
			'options'	=> array(
				'DESC'	=> __( 'Descending', 'woo-arp' ),
      			'ASC'	=> __( 'Ascending', 'woo-arp' )
			),
			'class'		=> 'wc-enhanced-select',
			'std'     	=> 'DESC', // WooCommerce < 2.0
    		'default' 	=> 'DESC', // WooCommerce >= 2.0
			'name'     	=> __( 'Order', 'woo-arp' ),
			'desc'     	=> __( 'Select the sorting order of the related products. Default: Descending', 'woo-arp' )
		);

		// "Product Columns" select
		$settings_woo_arp[] = array(
			'id'       	=> 'woo_arp_columns',
			'type'     	=> 'select',
			'options'	=> array(
				'1'	=> '1',
				'2'	=> '2',
				'3'	=> '3',
				'4'	=> '4',
				'5'	=> '5',
				'6'	=> '6',
				'7'	=> '7',
      			'8'	=> '8',
			),
			'class'		=> 'wc-enhanced-select',
			'css'		=> 'max-width:100px;',
			'std'     	=> '5', // WooCommerce < 2.0
    		'default' 	=> '5', // WooCommerce >= 2.0
			'name'     	=> __( 'Product Columns', 'woo-arp' ),
			'desc'     	=> __( 'The number of columns that products are arranged in. Default: 5', 'woo-arp' )
		);

		// "Products per page" textfield
		$settings_woo_arp[] = array(
			'id'       	=> 'woo_arp_limit',
			'type'     	=> 'text',
			'css'		=> 'max-width:100px;',
			'std'     	=> '5', // WooCommerce < 2.0
    		'default' 	=> '5', // WooCommerce >= 2.0
			'name'     	=> __( 'Products per page', 'woo-arp' ),
			'desc'     	=> __( 'The number of products shown on one page. Default: 5', 'woo-arp' )
		);
		
		$settings_woo_arp[] = array( 
			'type'	=> 'sectionend', 
			'id' 	=> 'woo_arp' 
		);
		return $settings_woo_arp;
	
	// If not, return the standard settings, "General"
	} else {
		return $settings;
	}

}// END: "function woo_arp_wc_settings"


/**
 * Hide the default WC Cross-Sells and Up-Sells on single products
 */
add_action( 'init', 'woo_arp_hide_defaults');
function woo_arp_hide_defaults() {

	// Check if "Activate Advanced Related Products" admin option is activated, and remove default "Cross-Sells"
    if ( get_option( 'woo_arp_active', 'no' ) == 'yes' ) {
        remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
    	remove_action( 'woocommerce_after_single_product_summary', 'woo_wc_related_products', 20);
    }

    // Check if "Hide Up-Sells" admin option is activated, and remove default "Up-Sells"
    if ( get_option( 'woo_arp_upsells', 'no' ) == 'yes' ) {
    	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
    	remove_action( 'woocommerce_after_single_product_summary', 'woo_wc_upsell_display', 15 );
    }

}// END: "function woo_arp_hide_defaults"


/**
 * The custom related products Query
 */
add_filter( 'woocommerce_after_single_product_summary', 'woo_arp_advanced_related', 30 );
function woo_arp_advanced_related() {

	// Get the current post/product ID
	$id = get_the_ID();

	// Get the admin options
	$taxonomy 		= get_option( 'woo_arp_taxonomy', 'product_cat');
	$orderby 		= get_option( 'woo_arp_orderby', 'rand');
	$order 			= get_option( 'woo_arp_order', 'DESC');
	$columns 		= get_option( 'woo_arp_columns', '5');
	$post_per_page 	= get_option( 'woo_arp_limit', '5');

	// Check if "Advanced Related Products" admin option is activated
	if ( get_option( 'woo_arp_active', 'no' ) == 'yes' ) {

		/* Here we try to get the last term.
		 * If a product has an parent term and child-term assigned, we try to get only the last child-term.
		 * Watch out: If a product is assigned in multiple parent-categories, you will get the last child-terms of all parents.
		 * See: https://snipt.net/ivan747/get-last-child-cetegory-id/
		 */

		//Get all terms associated with post in taxonomy
		$terms = get_the_terms( $id, $taxonomy );

		//Get an array of their IDs
		$term_ids = wp_list_pluck( $terms, 'term_id' );

		//Get array of parents - 0 is not a parent
		$parents = array_filter( wp_list_pluck ( $terms, 'parent' ) );

		//Get array of IDs of terms which are not parents.
		$term_ids_not_parents = array_diff( $term_ids,  $parents );

		//Get corresponding term objects
		$terms_not_parents = array_intersect_key( $terms,  $term_ids_not_parents );

		//Create an empty array
		$elements = array();

		foreach ($terms_not_parents as $t_id) {
			//Get the term_id of this term
			$last_term = $t_id->term_id;
			//Add the term_id to the elements array
			$elements[] = $last_term;
		}

		// $elements is now an array of term ID´s


		//The Query arguments
		$args = array(
			'post_type' 			=> 'product',
			'post_status' 			=> 'publish',
			'ignore_sticky_posts'  	=> 1,
			'posts_per_page'		=> $post_per_page,
			'orderby' 				=> $orderby,
			'order'					=> $order,
			'post__not_in' 			=> array( $id ),
			'tax_query' 			=> array(
				array(
					'taxonomy'	=> $taxonomy,
					'field'		=> 'term_id',
					'terms'		=> $elements,
				),
			),
		);

		// The Query
		$related = new WP_Query( $args );

		// Get the WC columns
		global $woocommerce_loop;
		$woocommerce_loop['columns'] = $columns;

		// The Loop
		if ( $related->have_posts() ) {

			echo '<div class="related products advanced"><h2>'.__( 'Related Products', 'woocommerce' ).'</h2>';

			// Creates default WC product list start-tag
			woocommerce_product_loop_start();

				while ( $related->have_posts() ) : $related->the_post();

					// Get the default WC product display
					wc_get_template_part( 'content', 'product' );

				endwhile;

			// Creates default WC product list end-tag
			woocommerce_product_loop_end();

			echo '</div>';

			wp_reset_postdata();
		
		}// END: if "$related->have_posts"

	}// END: Check if "Advanced Related Products" admin option is active

}// END: "function woo_arp_advanced_related"


/**
 * END: Check if WC is active
 **/
}