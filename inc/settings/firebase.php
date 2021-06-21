<?php

if (!empty($_POST) && $_POST['option_page'] == 'cronberry-firebase-settings') {

    update_option('cronberry_push_notification_concent', $_POST['cronberry_push_notification_concent']);
    update_option('firebase_api_key', $_POST['firebase_api_key']);
    update_option('firebase_auth_domain', $_POST['firebase_auth_domain']);
    update_option('firebase_project_id', $_POST['firebase_project_id']);
    update_option('firebase_storage_bucket', $_POST['firebase_storage_bucket']);
    update_option('firebase_messaging_sender_id', $_POST['firebase_messaging_sender_id']);
    update_option('firebase_app_id', $_POST['firebase_app_id']);
    update_option('firebase_measurement_id', $_POST['firebase_measurement_id']);
    cronberry_firebase_create_registration_file();
}

function is_firebase_form_valid()
{
    if (get_option('cronberry_push_notification_concent')) {
        if (
            get_option('firebase_api_key') &&
            get_option('firebase_auth_domain') && get_option('firebase_project_id') &&
            get_option('firebase_storage_bucket') && get_option('firebase_messaging_sender_id') && get_option('firebase_app_id')
        )
            return true;
        else
            return false;
    } else
        return true;
}

function cronberry_firebase_settings_callback()
{

    if (!is_firebase_form_valid()) {
?>
        <div class="notice notice-error">
            <p>
                <strong>
                    Please fill all firebase required setting to send notification
                </strong>
            </p>
        </div>
    <?php    }
    ?>
    <div class="notice notice-info">

        <form action="" method="post">
            <?php
            // security field
            settings_fields('cronberry-firebase-settings');

            // output settings section here
            do_settings_sections('cronberry-firebase-settings');

            // save settings button
            submit_button('Save Settings');

            ?>
        </form>
    </div>
<?php
}

add_action('admin_init', 'cronberry_firebase_settings_init');

function cronberry_firebase_settings_init()
{
    add_settings_section(
        'cronberry_firebase_settings_section',
        '',
        '',
        'cronberry-firebase-settings'
    );
    register_push_notification_concent();
    register_firebase_api_key();
    register_firebase_auth_domain();
    register_firebase_project_id();
    register_firebase_storage_bucket();
    register_firebase_messaging_sender_id();
    register_firebase_app_id();
    register_firebase_measurement_id();
}


function register_firebase_api_key()
{

    register_setting(
        'cronberry-firebase-settings',
        'firebase_api_key',
        array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => ''
        )
    );
    add_settings_field(
        'firebase_api_key',
        __('Firebase API KEY'),
        'firebase_api_key_callback',
        'cronberry-firebase-settings',
        'cronberry_firebase_settings_section'
    );
}

function register_firebase_auth_domain()
{

    register_setting(
        'cronberry-firebase-settings',
        'firebase_auth_domain',
        array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => ''
        )
    );
    add_settings_field(
        'firebase_auth_domain',
        __('Firebase Auth Domain'),
        'firebase_auth_domain_callback',
        'cronberry-firebase-settings',
        'cronberry_firebase_settings_section'
    );
}

function register_firebase_project_id()
{

    register_setting(
        'cronberry-firebase-settings',
        'firebase_project_id',
        array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => ''
        )
    );
    add_settings_field(
        'firebase_project_id',
        __('Firebase Project ID'),
        'firebase_project_id_callback',
        'cronberry-firebase-settings',
        'cronberry_firebase_settings_section'
    );
}
function register_firebase_storage_bucket()
{

    register_setting(
        'cronberry-firebase-settings',
        'firebase_storage_bucket',
        array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => ''
        )
    );
    add_settings_field(
        'firebase_storage_bucket',
        __('Firebase storage bucket'),
        'firebase_storage_bucket_callback',
        'cronberry-firebase-settings',
        'cronberry_firebase_settings_section'
    );
}

function register_firebase_messaging_sender_id()
{

    register_setting(
        'cronberry-firebase-settings',
        'firebase_messaging_sender_id',
        array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => ''
        )
    );
    add_settings_field(
        'firebase_messaging_sender_id',
        __('Firebase Messaging Sender ID'),
        'firebase_messaging_sender_id_callback',
        'cronberry-firebase-settings',
        'cronberry_firebase_settings_section'
    );
}

function register_firebase_app_id()
{

    register_setting(
        'cronberry-firebase-settings',
        'firebase_app_id',
        array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => ''
        )
    );
    add_settings_field(
        'firebase_app_id',
        __('Firebase App Id'),
        'firebase_app_id_callback',
        'cronberry-firebase-settings',
        'cronberry_firebase_settings_section'
    );
}

function register_firebase_measurement_id()
{

    register_setting(
        'cronberry-firebase-settings',
        'firebase_measurement_id',
        array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => ''
        )
    );
    add_settings_field(
        'firebase_measurement_id',
        __('Firebase Measurement Id (optional)'),
        'firebase_measurement_id_callback',
        'cronberry-firebase-settings',
        'cronberry_firebase_settings_section'
    );
}

function register_push_notification_concent()
{

    register_setting(
        'cronberry-firebase-settings',
        'cronberry_push_notification_concent',
        array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => ''
        )
    );
    add_settings_field(
        'cronberry_push_notification_concent',
        'Do you want to send push notification?',
        'cronberry_push_notification_concent_callback',
        'cronberry-firebase-settings',
        'cronberry_firebase_settings_section'
    );
}

function firebase_api_key_callback()
{
    $firebase_api_key = get_option('firebase_api_key');
?>
    <input type="text" name="firebase_api_key" class="regular-text" value="<?php echo isset($firebase_api_key) ? esc_attr($firebase_api_key) : ''; ?>" />
<?php
}

function firebase_auth_domain_callback()
{
    $firebase_auth_domain = get_option('firebase_auth_domain');
?>
    <input type="text" name="firebase_auth_domain" class="regular-text" value="<?php echo isset($firebase_auth_domain) ? esc_attr($firebase_auth_domain) : ''; ?>" />
<?php
}

function firebase_project_id_callback()
{
    $firebase_project_id = get_option('firebase_project_id');
?>
    <input type="text" name="firebase_project_id" class="regular-text" value="<?php echo isset($firebase_project_id) ? esc_attr($firebase_project_id) : ''; ?>" />
<?php
}


function firebase_storage_bucket_callback()
{
    $firebase_storage_bucket = get_option('firebase_storage_bucket');
?>
    <input type="text" name="firebase_storage_bucket" class="regular-text" value="<?php echo isset($firebase_storage_bucket) ? esc_attr($firebase_storage_bucket) : ''; ?>" />
<?php
}


function firebase_messaging_sender_id_callback()
{
    $firebase_messaging_sender_id = get_option('firebase_messaging_sender_id');
?>
    <input type="text" name="firebase_messaging_sender_id" class="regular-text" value="<?php echo isset($firebase_messaging_sender_id) ? esc_attr($firebase_messaging_sender_id) : ''; ?>" />
<?php
}


function firebase_app_id_callback()
{
    $firebase_app_id = get_option('firebase_app_id');
?>
    <input type="text" name="firebase_app_id" class="regular-text" value="<?php echo isset($firebase_app_id) ? esc_attr($firebase_app_id) : ''; ?>" />
<?php
}


function firebase_measurement_id_callback()
{
    $firebase_measurement_id = get_option('firebase_measurement_id');
?>
    <input type="text" name="firebase_measurement_id" class="regular-text" value="<?php echo isset($firebase_measurement_id) ? esc_attr($firebase_measurement_id) : ''; ?>" />
<?php
}

function cronberry_push_notification_concent_callback()
{
    $login_hook = get_option('cronberry_push_notification_concent');
?>
    <input type="checkbox" name="cronberry_push_notification_concent" value="1" <?php checked(1, get_option('cronberry_push_notification_concent'), true);  ?> />

<?php
}


function cronberry_firebase_create_registration_file()
{
    $cronberry_push_notification_concent = get_option('cronberry_push_notification_concent');
    if ($cronberry_push_notification_concent) {
        $message = 'importScripts("https://www.gstatic.com/firebasejs/8.6.2/firebase-app.js");
    importScripts("https://www.gstatic.com/firebasejs/8.6.2/firebase-messaging.js");
    var firebaseConfig = ' . getFireBaseConfig() . ';
    firebase.initializeApp(firebaseConfig);
    const messaging = firebase.messaging();';
        $fh = fopen(CRONBERRY_PATH . "/../../../firebase-messaging-sw.js", "w");
        fwrite($fh, $message);
        fclose($fh);
    }
}
