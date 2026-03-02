<?php

/**
 * Plugin Name: SW - Services CPT
 * Plugin URI: https://www.seniors.com.co
 * Description: Custom Post Type "Services" with native custom fields and optional WPGraphQL support.
 * Version: 1.0.0
 * Author: Seniors
 * Author URI: https://www.seniors.com.co
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: sw-services
 * Requires PHP: 7.4
 * Requires at least: 5.8
 *
 * FEATURES:
 * - Custom Post Type: Services
 * - Custom Taxonomy: Service Category
 * - WPGraphQL support
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants
define('SW_SERVICES_VERSION', '1.0.0');
define('SW_SERVICES_TEXT_DOMAIN', 'sw-services');

/**
 * Register Custom Post Type: Services
 */
function sw_services_register_cpt()
{
    $labels = array(
        'name'                  => _x('Services', 'Post Type General Name', SW_SERVICES_TEXT_DOMAIN),
        'singular_name'         => _x('Service', 'Post Type Singular Name', SW_SERVICES_TEXT_DOMAIN),
        'menu_name'             => __('Services', SW_SERVICES_TEXT_DOMAIN),
        'name_admin_bar'        => __('Service', SW_SERVICES_TEXT_DOMAIN),
        'archives'              => __('Service Archives', SW_SERVICES_TEXT_DOMAIN),
        'attributes'            => __('Service Attributes', SW_SERVICES_TEXT_DOMAIN),
        'all_items'             => __('All Services', SW_SERVICES_TEXT_DOMAIN),
        'add_new_item'          => __('Add New Service', SW_SERVICES_TEXT_DOMAIN),
        'add_new'               => __('Add New', SW_SERVICES_TEXT_DOMAIN),
        'new_item'              => __('New Service', SW_SERVICES_TEXT_DOMAIN),
        'edit_item'             => __('Edit Service', SW_SERVICES_TEXT_DOMAIN),
        'update_item'           => __('Update Service', SW_SERVICES_TEXT_DOMAIN),
        'view_item'             => __('View Service', SW_SERVICES_TEXT_DOMAIN),
        'view_items'            => __('View Services', SW_SERVICES_TEXT_DOMAIN),
        'search_items'          => __('Search Service', SW_SERVICES_TEXT_DOMAIN),
        'not_found'             => __('Not found', SW_SERVICES_TEXT_DOMAIN),
        'not_found_in_trash'    => __('Not found in Trash', SW_SERVICES_TEXT_DOMAIN),
    );

    $args = array(
        'label'                 => __('Service', SW_SERVICES_TEXT_DOMAIN),
        'description'           => __('Services or features of your product/company', SW_SERVICES_TEXT_DOMAIN),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'custom-fields', 'page-attributes'),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-portfolio',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
    );

    // Add GraphQL support if WPGraphQL is active
    if (class_exists('WPGraphQL')) {
        $args['show_in_graphql'] = true;
        $args['graphql_single_name'] = 'swService';
        $args['graphql_plural_name'] = 'swServices';
    }

    register_post_type('service', $args);
}
add_action('init', 'sw_services_register_cpt', 0);

/**
 * Register Taxonomy: Service Category
 */
function sw_services_register_taxonomy()
{
    $labels = array(
        'name'                       => _x('Service Categories', 'Taxonomy General Name', SW_SERVICES_TEXT_DOMAIN),
        'singular_name'              => _x('Service Category', 'Taxonomy Singular Name', SW_SERVICES_TEXT_DOMAIN),
        'menu_name'                  => __('Categories', SW_SERVICES_TEXT_DOMAIN),
        'all_items'                  => __('All Categories', SW_SERVICES_TEXT_DOMAIN),
        'new_item_name'              => __('New Category Name', SW_SERVICES_TEXT_DOMAIN),
        'add_new_item'               => __('Add New Category', SW_SERVICES_TEXT_DOMAIN),
        'edit_item'                  => __('Edit Category', SW_SERVICES_TEXT_DOMAIN),
        'update_item'                => __('Update Category', SW_SERVICES_TEXT_DOMAIN),
        'view_item'                  => __('View Category', SW_SERVICES_TEXT_DOMAIN),
        'search_items'               => __('Search Categories', SW_SERVICES_TEXT_DOMAIN),
        'not_found'                  => __('Not Found', SW_SERVICES_TEXT_DOMAIN),
    );

    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => false,
        'show_in_rest'               => true,
    );

    // Add GraphQL support if WPGraphQL is active
    if (class_exists('WPGraphQL')) {
        $args['show_in_graphql'] = true;
        $args['graphql_single_name'] = 'swServiceCategory';
        $args['graphql_plural_name'] = 'swServiceCategories';
    }

    register_taxonomy('service_category', array('service'), $args);
}
add_action('init', 'sw_services_register_taxonomy', 0);

/**
 * Add custom meta box for Service fields
 */
function sw_services_add_meta_box()
{
    add_meta_box(
        'service_details',
        __('Service Details', SW_SERVICES_TEXT_DOMAIN),
        'sw_services_meta_box_callback',
        'service',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'sw_services_add_meta_box');

/**
 * Meta box callback
 */
function sw_services_meta_box_callback($post)
{
    // Add nonce for security
    wp_nonce_field('sw_services_save_meta', 'sw_services_meta_nonce');

    // Get current values
    $icon_class = get_post_meta($post->ID, '_service_icon_class', true);

?>
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="service_icon_class">
                    <?php _e('Icon Class', SW_SERVICES_TEXT_DOMAIN); ?>
                </label>
            </th>
            <td>
                <input
                    type="text"
                    id="service_icon_class"
                    name="service_icon_class"
                    value="<?php echo esc_attr($icon_class); ?>"
                    class="regular-text"
                    placeholder="fa-solid fa-music" />
                <p class="description">
                    <?php _e('Icon CSS class. Works with any framework:', SW_SERVICES_TEXT_DOMAIN); ?>
                    <br>
                    • Font Awesome: <code>fa-solid fa-music</code>
                    <br>
                    • Material Icons: <code>material-icons music_note</code>
                    <br>
                    • Bootstrap Icons: <code>bi bi-music-note</code>
                    <br>
                    • Iconify: <code>mdi:music</code>
                </p>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <p class="description">
                    <?php _e('<strong>Note:</strong> To set the display order, use the "Order" field in the Page Attributes panel on the right side of the editor.', SW_SERVICES_TEXT_DOMAIN); ?>
                </p>
            </td>
        </tr>
    </table>
<?php
}

/**
 * Save meta box data
 */
function sw_services_save_meta($post_id)
{
    // Check nonce
    if (!isset($_POST['sw_services_meta_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['sw_services_meta_nonce'], 'sw_services_save_meta')) {
        return;
    }

    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save icon_class
    if (isset($_POST['service_icon_class'])) {
        update_post_meta(
            $post_id,
            '_service_icon_class',
            sanitize_text_field($_POST['service_icon_class'])
        );
    }

    // Note: Order is now handled by WordPress natively via menu_order (page-attributes)
}
add_action('save_post_service', 'sw_services_save_meta');

/**
 * Add custom columns to Services admin table
 */
function sw_services_add_admin_columns($columns)
{
    // Insert after title column
    $new_columns = [];
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'title') {
            $new_columns['service_icon'] = __('Icon', SW_SERVICES_TEXT_DOMAIN);
            $new_columns['service_order'] = __('Order', SW_SERVICES_TEXT_DOMAIN);
        }
    }
    return $new_columns;
}
add_filter('manage_service_posts_columns', 'sw_services_add_admin_columns');

/**
 * Display custom column content
 */
function sw_services_display_admin_columns($column, $post_id)
{
    switch ($column) {
        case 'service_icon':
            $icon_class = get_post_meta($post_id, '_service_icon_class', true);
            if ($icon_class) {
                echo '<code>' . esc_html($icon_class) . '</code>';
            } else {
                echo '—';
            }
            break;

        case 'service_order':
            $post = get_post($post_id);
            echo $post->menu_order;
            break;
    }
}
add_action('manage_service_posts_custom_column', 'sw_services_display_admin_columns', 10, 2);

/**
 * Make Order column sortable
 */
function sw_services_sortable_columns($columns)
{
    $columns['service_order'] = 'service_order';
    return $columns;
}
add_filter('manage_edit-service_sortable_columns', 'sw_services_sortable_columns');

/**
 * Sort by menu_order field
 */
function sw_services_orderby($query)
{
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    $orderby = $query->get('orderby');
    if ($orderby === 'service_order') {
        $query->set('orderby', 'menu_order');
    }
}
add_action('pre_get_posts', 'sw_services_orderby');

/**
 * Register custom fields in WPGraphQL (only if WPGraphQL is active)
 */
function sw_services_register_graphql_fields()
{
    // Only register if WPGraphQL is active
    if (!class_exists('WPGraphQL')) {
        return;
    }

    // Register swServiceFields object type
    register_graphql_object_type('SwServiceFields', [
        'description' => __('Service custom fields', SW_SERVICES_TEXT_DOMAIN),
        'fields' => [
            'iconClass' => [
                'type' => 'String',
                'description' => __('Icon CSS class', SW_SERVICES_TEXT_DOMAIN),
            ],
            'order' => [
                'type' => 'Integer',
                'description' => __('Display order', SW_SERVICES_TEXT_DOMAIN),
            ],
        ],
    ]);

    // Register field on SwService post type
    register_graphql_field('SwService', 'swServiceFields', [
        'type' => 'SwServiceFields',
        'description' => __('Service custom fields', SW_SERVICES_TEXT_DOMAIN),
        'resolve' => function ($post) {
            $icon_class = get_post_meta($post->ID, '_service_icon_class', true);

            // Get menu_order from the post object
            $post_object = get_post($post->ID);
            $order = $post_object ? $post_object->menu_order : 0;

            return [
                'iconClass' => $icon_class ?: '',
                'order' => absint($order),
            ];
        },
    ]);
}
add_action('graphql_register_types', 'sw_services_register_graphql_fields');

/**
 * Add custom categorySlug filter to WPGraphQL
 */
function sw_services_register_graphql_category_filter()
{
    // Only register if WPGraphQL is active
    if (!class_exists('WPGraphQL')) {
        return;
    }

    // Register custom where arg for category slug
    add_filter('graphql_input_fields', function ($fields, $type_name) {
        if ($type_name === 'RootQueryToSwServiceConnectionWhereArgs') {
            $fields['categorySlug'] = [
                'type' => 'String',
                'description' => __('Filter by service category slug', SW_SERVICES_TEXT_DOMAIN),
            ];
        }
        return $fields;
    }, 10, 2);

    // Apply the category filter to the query
    add_filter('graphql_post_object_connection_query_args', function ($query_args, $source, $args, $context, $info) {
        // Check if we're querying swServices and categorySlug is provided
        if ($info->fieldName === 'swServices' && isset($args['where']['categorySlug'])) {
            $category_slug = sanitize_text_field($args['where']['categorySlug']);

            // Add tax_query to filter by category
            if (!isset($query_args['tax_query'])) {
                $query_args['tax_query'] = [];
            }

            $query_args['tax_query'][] = [
                'taxonomy' => 'service_category',
                'field'    => 'slug',
                'terms'    => $category_slug,
            ];
        }
        return $query_args;
    }, 10, 5);
}
add_action('graphql_register_types', 'sw_services_register_graphql_category_filter');

/**
 * Flush rewrite rules on activation
 */
function sw_services_activate()
{
    sw_services_register_cpt();
    sw_services_register_taxonomy();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'sw_services_activate');

/**
 * Flush rewrite rules on deactivation
 */
function sw_services_deactivate()
{
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'sw_services_deactivate');
