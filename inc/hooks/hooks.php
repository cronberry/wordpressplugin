<?php

$userinfo_concent = get_option('cronberry_userinfo_concent');
$order_concent = get_option('cronberry_order_concent');

if ($userinfo_concent > 0) {
    add_action('user_register', 'cronberry_user_register', 10, 1);
}
if ($order_concent > 0) {
    add_action('woocommerce_add_to_cart', 'custome_add_to_cart', 10, 1);
    add_action('woocommerce_thankyou', 'customer_order_placed', 10, 1);
}

function cronberry_user_register($user_id)
{
    global $wpdb;
    $tablename = $wpdb->prefix . 'cronberry_integration';
    $user = get_user_by('ID', $user_id);
    $email =  $user->user_email;
    $name = $user->display_name;
    $mobile = get_user_meta($user, 'user_phone', true);
    $web_fcm_token = $_COOKIE['sentToServer'];
    $wpdb->insert(
        $tablename,
        array(
            'session_id' => $user_id,
            'cart_id' => null,
            'name' => $name,
            'email' => $email,
            'mobile' => $mobile,
            'cart_add_date' => '',
            'productnames' => null,
            'productquantity' => 0,
            'orderid' => null,
            'order_date' => null,
            'orderstatus' => null,
            'city' => null,
            'postcode' => null,
            'web_fcm_token' => $web_fcm_token,
            'add_date' => date('Y-m-d H:i:s', strtotime('now')),
        ),
        array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
    );
}
function customer_order_placed($order_id)
{
    global $woocommerce;
    global $wpdb;

    if (!$order_id)
        return;


    // Allow code execution only once 
    if (!get_post_meta($order_id, '_thankyou_action_done', true)) {

        // Get an instance of the WC_Order object
        $order = wc_get_order($order_id);
        $tablename = $wpdb->prefix . 'cronberry_integration';
        $session_id = $order->get_billing_email();
        $name = $order->get_billing_first_name() . " " . $order->get_billing_last_name();
        $email = $order->get_billing_email();
        $mobile = $order->get_billing_phone();
        $postcode = $order->get_shipping_postcode();
        $amount = $order->get_total();
        $city = $order->get_shipping_city();
        $order_id = $order->get_id();
        $order_status = $order->get_status();
        $order_date = $order->get_date_created()->getTimestamp();
        $web_fcm_token = $_COOKIE['sentToServer'];
        $cart_id = $order->get_cart_hash();
        $product_name = array();
        $quantity = 0;
        foreach ($order->get_items() as $item_id => $item) {

            // Get the product object
            $product = $item->get_product();
            array_push($product_name, $item->get_name());
            $quantity = $quantity + $item->get_quantity();
        }
        $wpdb->insert(
            $tablename,
            array(
                'session_id' => $session_id,
                'cart_id' => $cart_id,
                'name' => $name,
                'email' => $email,
                'mobile' => $mobile,
                'cart_add_date' => $order_date,
                'productnames' => implode(',', $product_name),
                'productquantity' => $quantity,
                'orderid' => $order_id,
                'order_date' => $order_date,
                'orderstatus' => $order_status,
                'city' => $city,
                'postcode' => $postcode,
                'amount'=> $amount,
                'web_fcm_token' => $web_fcm_token,
                'add_date' => date('Y-m-d H:i:s', $order_date),
            ),
            array('%s','%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
        );
        $order->update_meta_data('_thankyou_action_done', true);
        $order->save();
    }
}
function custome_add_to_cart($data1)
{
    global $woocommerce;
    global $wpdb;

    $tablename = $wpdb->prefix . 'cronberry_integration';
    $session_cookie = WC()->session->get_session_cookie();
    $session_id = $session_cookie[3];
    $name = WC()->cart->get_customer()->get_display_name();
    $email = WC()->cart->get_customer()->get_billing_email();
    $mobile = WC()->cart->get_customer()->get_billing_phone();
    $postcode = WC()->cart->get_customer()->get_billing_postcode();
    $amount = WC()->cart->subtotal;
    $city = WC()->cart->get_customer()->get_billing_city();
    $cart_id = WC()->cart->get_cart_hash();
    $web_fcm_token = $_COOKIE['sentToServer'];
    $product_name = array();
    $quantity =  0;
    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        $product =  wc_get_product($cart_item['data']->get_id());
        array_push($product_name, $product->get_title());
        $quantity =  $quantity + $cart_item['quantity'];
    }

    $wpdb->insert(
        $tablename,
        array(
            'session_id' => $session_id,
            'cart_id' => $cart_id,
            'name' => $name,
            'email' => $email,
            'mobile' => $mobile,
            'cart_add_date' => '',
            'productnames' => implode(', ', $product_name),
            'productquantity' => $quantity,
            'orderid' => null,
            'order_date' => null,
            'orderstatus' => null,
            'city' => $city,
            'postcode' => $postcode,
            'amount'=> $amount,
            'web_fcm_token' => $web_fcm_token,
            'add_date' => date('Y-m-d H:i:s', strtotime('now')),
        ),
        array('%s','%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
    );
}
