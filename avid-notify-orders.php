<?php
/*
Plugin Name: 04 AVID Notify Orders to Admins
Description: Notifies woocommerce orders in processing more visibly. The alert is located on top of the dashboard.
Requires Plugins: woocommerce
Version: 1.0
Author: AVID-MIS
Author URI: www.avid.com.ph
*/

function order_is_shop_manager() {
    $user = wp_get_current_user();
    return isset($user->roles[0]) && $user->roles[0] == 'shop_manager';
}

function order_is_admin_not_dashboard() {
    $user = wp_get_current_user();
    return isset($user->roles[0]) && $user->roles[0] == 'administrator';
}

function check_unviewed_orders() {
    if (order_is_shop_manager() || order_is_admin_not_dashboard()) {
        $unviewed_orders = wc_get_orders(array(
            'status' => 'processing',
            'limit' => -1,
        ));

        $unviewed_count = count($unviewed_orders);

        if ($unviewed_count > 0) {
            echo '<div class="admin_notif_container"><h1 class="new-order-alert">ALERT!!! ORDER FOR PROCESSING</h1>';
            $order_page_url = admin_url('edit.php?post_type=shop_order');
            echo '<h3 class="new-order-alert">Please Check <a href="' . esc_url($order_page_url) . '" class="link-to-orders">Orders</a></h3></div>';
        }
    }
}
add_action('admin_notices', 'check_unviewed_orders');

function admin_order_alert_styles() {
    wp_enqueue_style('admin-custom-styles', get_template_directory_uri() . '/admin-styles.css');
    $inline_css = "
    .admin_notif_container {
        background: #edff00;
        padding: 40px 20px 20px 20px;
        margin: 40px 20px 0 0;
        position: sticky;
        top: 0;
        z-index: 999;
    }
    .new-order-alert {
        margin: 0;
    }
    ";
    wp_add_inline_style('admin-custom-styles', $inline_css);
}
add_action('admin_enqueue_scripts', 'admin_order_alert_styles');
