<?php

if (!empty($_POST) && $_POST['option_page'] == 'cronberry-settings') {

    update_option('cronberry_project_Key', $_POST['cronberry_project_Key']);
    update_option('cronberry_announcement_url', $_POST['cronberry_announcement_url']);
    update_option('cronberry_enable_inapp_notification', $_POST['cronberry_enable_inapp_notification']);
    update_option('cronberry_userinfo_concent', $_POST['cronberry_userinfo_concent']);
    update_option('cronberry_order_concent', $_POST['cronberry_order_concent']);
    createDefaultParams();
}
/**
 * Create Settings Menu
 */
function cronberry_settings_menu()
{

    add_menu_page(
        __('cronberry', 'cronberry'),
        __('Cronberry', 'cronberry'),
        'manage_options',
        'cronberry-settings',
        'cronberry_settings_callback',
        '',
        null
    );
    add_submenu_page(
        'cronberry-settings',
        __('Cronberry Firebase', 'cronberry'),
        __('Firebase', 'cronberry'),
        'manage_options',
        'cronberry-firebase-settings',
        'cronberry_firebase_settings_callback'
    );
}

add_action('admin_menu', 'cronberry_settings_menu');

function check_woocommerce_is_active()
{
    if (!function_exists('is_woocommerce_activated')) {
        if (class_exists('woocommerce')) {
            return true;
        } else {
            return false;
        }
    }
    return false;
}

/**
 * Settings Template Page
 */
function cronberry_settings_callback()
{
    if (!check_woocommerce_is_active()) {
?>
        <div class="notice notice-error">
            <p>
                <strong>
                    <?php
                    echo sprintf(esc_html__('Cronberry requires WooCommerce to be installed and active. You can download %s here.', 'woocommerce-gateway-stripe'), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>');
                    ?>
                </strong>
            </p>
        </div>
    <?php    }
    ?>
    <div class="notice notice-success">


        <p>
            <strong>This is cronberry module for sending cart, order and user info to cronberry dashboard along with firebase config and announcement</strong><br />
            Add following to your cron job scheduler
        <pre>* */10 * * * <?php echo get_site_url() ?>/wp-json/module/cronberryIntegration/cronjob</pre>
        </p>
    </div>
    <div class="notice notice-info">

        <form action="" method="post">
            <?php
            // security field
            settings_fields('cronberry-settings');

            // output settings section here
            do_settings_sections('cronberry-settings');

            // save settings button
            submit_button('Save Settings');
            ?>
        </form>
    </div>
<?php
}



/**
 * Settings Template
 */
function cronberry_settings_init()
{
    add_settings_section(
        'cronberry_settings_section',
        '',
        '',
        'cronberry-settings'
    );
    register_cronberry_project_Key();
    register_cronberry_announcement_url();
    register_cronberry_enable_inapp_notification();
    // register_cronberry_enable_bootstrap();
    register_userinfo_concent();
    register_order_concent();
}

function register_cronberry_project_Key()
{

    register_setting(
        'cronberry-settings',
        'cronberry_project_Key',
        array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => ''
        )
    );
    add_settings_field(
        'cronberry_project_Key',
        __('Cronberry Project Key'),
        'cronberry_project_Key_callback',
        'cronberry-settings',
        'cronberry_settings_section'
    );
}


function register_cronberry_announcement_url()
{

    register_setting(
        'cronberry-settings',
        'cronberry_announcement_url',
        array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => ''
        )
    );
    add_settings_field(
        'cronberry_announcement_url',
        __('Cronberry Announcement URL'),
        'cronberry_announcement_url_callback',
        'cronberry-settings',
        'cronberry_settings_section'
    );
}

function register_cronberry_enable_inapp_notification()
{

    register_setting(
        'cronberry-settings',
        'cronberry_enable_inapp_notification',
        array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 0
        )
    );
    add_settings_field(
        'cronberry_enable_inapp_notification',
        __('Enable InApp Notification'),
        'cronberry_enable_inapp_notification_callback',
        'cronberry-settings',
        'cronberry_settings_section'
    );
}

function register_cronberry_enable_bootstrap()
{

    register_setting(
        'cronberry-settings',
        'register_cronberry_enable_bootstrap',
        array(
            'type' => 'number',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 0
        )
    );
    add_settings_field(
        'register_cronberry_enable_bootstrap',
        __('Enable Bootstrap'),
        'cronberry_enable_bootstrap_callback',
        'cronberry-settings',
        'cronberry_settings_section'
    );
}


function register_userinfo_concent()
{

    register_setting(
        'cronberry-settings',
        'cronberry_userinfo_concent',
        array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => '1'
        )
    );
    add_settings_field(
        'cronberry_userinfo_concent',
        'Do you want to send user info to cronberry?',
        'cronberry_userinfo_concent_callback',
        'cronberry-settings',
        'cronberry_settings_section'
    );
}

function register_order_concent()
{

    register_setting(
        'cronberry-settings',
        'cronberry_order_concent',
        array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => '1'
        )
    );
    add_settings_field(
        'cronberry_order_concent',
        'Do you want to send abondon cart and order data to cronberry?',
        'cronberry_order_concent_callback',
        'cronberry-settings',
        'cronberry_settings_section'
    );
}

add_action('admin_init', 'cronberry_settings_init');


/**
 * txt tempalte
 */
function cronberry_project_Key_callback()
{
    $cronberry_project_Key = get_option('cronberry_project_Key');
?>
    <input type="text" name="cronberry_project_Key" class="regular-text" value="<?php echo isset($cronberry_project_Key) ? esc_attr($cronberry_project_Key) : ''; ?>" />
<?php
}




function cronberry_announcement_url_callback()
{
    $cronberry_announcement_url = get_option('cronberry_announcement_url');
?>
    <input type="text" name="cronberry_announcement_url" class="regular-text" value="<?php echo isset($cronberry_announcement_url) ? esc_attr($cronberry_announcement_url) : ''; ?>" />
<?php
}



function cronberry_enable_inapp_notification_callback()
{
    $cronberry_enable_inapp_notification = get_option('cronberry_enable_inapp_notification');
?>
    <label for="1">
        <input type="radio" name="cronberry_enable_inapp_notification" value="1" <?php checked(1, $cronberry_enable_inapp_notification); ?> /> yes
    </label>
    <label for="0">
        <input type="radio" name="cronberry_enable_inapp_notification" value="0" <?php checked(0, $cronberry_enable_inapp_notification); ?> /> No
    </label>
<?php
}

function cronberry_enable_bootstrap_callback()
{
    $register_cronberry_enable_bootstrap = get_option('register_cronberry_enable_bootstrap');
?>
    <label for="1">
        <input type="radio" name="register_cronberry_enable_bootstrap" value="1" <?php checked(1, $register_cronberry_enable_bootstrap); ?> /> yes
    </label>
    <label for="0">
        <input type="radio" name="register_cronberry_enable_bootstrap" value="0" <?php checked(0, $register_cronberry_enable_bootstrap); ?> /> No
    </label>
<?php
}


/**
 * radio field tempalte
 */
function cronberry_userinfo_concent_callback()
{
    $login_hook = get_option('cronberry_userinfo_concent');
?>
    <input type="checkbox" name="cronberry_userinfo_concent" value="1" <?php checked(1, get_option('cronberry_userinfo_concent'), true);  ?> />

<?php
}

/**
 * radio field tempalte
 */
function cronberry_order_concent_callback()
{
    $login_hook = get_option('cronberry_order_concent');
?>
    <input type="checkbox" name="cronberry_order_concent" value="1" <?php checked(1, get_option('cronberry_order_concent'), true);  ?> />

<?php
}

function createDefaultParams()
{
    $cronberry_project_Key = get_option('cronberry_project_Key');
    if ($cronberry_project_Key) {
        $header = array(
            'Content-Type: application/json',
            'Authorization: Basic Y3JvbmJlcnJ5QHVzZXJuYW1lOmNyb25iZXJyeUBwYXNzd29yZA==',
        );
        $defaultParams = array(
            [
                'paramName' => 'Order Date',
                'paramDatatype' => 'Date',
                'paramCategory' => '1',
                'param_key' => 'order_date'
            ],
            [
                'paramName' => 'Cart Add Date',
                'paramDatatype' => 'Date',
                'paramCategory' => '1',
                'param_key' => 'cart_add_date'
            ],
            [
                'paramName' => 'Product Names',
                'paramDatatype' => 'String',
                'paramCategory' => '1',
                'param_key' => 'product_names'
            ],
            [
                'paramName' => 'Source',
                'paramDatatype' => 'String',
                'paramCategory' => '1',
                'param_key' => 'source'
            ],

            [
                'paramName' => 'Product Quantity',
                'paramDatatype' => 'String',
                'paramCategory' => '1',
                'param_key' => 'product_quantity'
            ],
            [
                'paramName' => 'Order Id',
                'paramDatatype' => 'String',
                'paramCategory' => '1',
                'param_key' => 'order_id'
            ],

            [
                'paramName' => 'Order Status',
                'paramDatatype' => 'String',
                'paramCategory' => '1',
                'param_key' => 'order_status'
            ],

            [
                'paramName' => 'City',
                'paramDatatype' => 'String',
                'paramCategory' => '1',
                'param_key' => 'city'
            ],

            [
                'paramName' => 'Postcode',
                'paramDatatype' => 'String',
                'paramCategory' => '1',
                'param_key' => 'postcode'
            ],

            [
                'paramName' => 'Total Amount',
                'paramDatatype' => 'Numeric',
                'paramCategory' => '1',
                'param_key' => 'amount'
            ],
            [
                'paramName' => 'Abondon Cart',
                'paramDatatype' => 'string',
                'paramCategory' => '0',
                'param_key' => 'abandon_cart'
            ],
        );

        $http = array(
            'method' => 'POST',
            'user_agent' => $_SERVER['SERVER_SOFTWARE'],
            'max_redirects' => 5,
            'timeout' => 5,
        );
        $payload = json_encode(array("projectKey" => $cronberry_project_Key, "dynamicParamList" => $defaultParams));

        $url = "https://api.cronberry.com/cronberry/api/plugins/create-dynamic-params";

        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_POST => 1,
            CURLOPT_HTTPHEADER =>  $header,
            CURLOPT_USERAGENT => $http['user_agent'],
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_MAXREDIRS => $http['max_redirects'],
            CURLOPT_TIMEOUT => $http['timeout'],
            CURLOPT_RETURNTRANSFER => 1,
        ));
        $resp = curl_exec($ch);
        curl_close($ch);
    }
}
