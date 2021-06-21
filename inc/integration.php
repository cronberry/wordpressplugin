<?php

add_action('wp_footer', 'cronberry_add_inpp_script_callback');
add_action('wp_footer', 'cronberry_annoument_script_callback');
add_action('wp_footer', 'cronberry_firebase_script_callback');

/*
** in-app notificaiton 
*/
function cronberry_add_inpp_script_callback()
{
    $cronberry_enable_inapp_notification = get_option('cronberry_enable_inapp_notification');
    // $register_cronberry_enable_bootstrap = get_option('register_cronberry_enable_bootstrap');
    $project_Key = get_option('cronberry_project_Key');
    if ($cronberry_enable_inapp_notification > 0) {
        cronberry_inapp_html($project_Key);
        // if ($register_cronberry_enable_bootstrap) {
        //     wp_enqueue_script('boot3-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js', array('jquery'), '', true);
        //     wp_enqueue_style('boot3-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css');
        // }
        wp_enqueue_style('cronberry-css', CRONBERRY_URL . 'css/front.css', '', rand());
        wp_enqueue_script('cronberry-script', CRONBERRY_URL . 'js/front.js', array('jquery'), '', true);
    }
}

function cronberry_annoument_script_callback()
{
    $cronberry_announcement_url = get_option('cronberry_announcement_url');
    if ($cronberry_announcement_url) {
        wp_enqueue_script('cronberry-announcement-script', $cronberry_announcement_url, array('jquery'), rand(), true);
    }
}

function cronberry_inapp_html($project_Key)
{
?>

    <script type="text/javascript">
        var tokencr = "<?php echo $project_Key; ?>";
        // requestPermission();
    </script>';
    <button type="button" id="inappbutton" class="bi bi-bell" data-toggle="modal">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bell" viewBox="0 0 16 16">
            <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2zM8 1.918l-.797.161A4.002 4.002 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4.002 4.002 0 0 0-3.203-3.92L8 1.917zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5.002 5.002 0 0 1 13 6c0 .88.32 4.2 1.22 6z" />
        </svg>
    </button>
    <!-- Modal -->
    <div class="modal fade" style="display: none;" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button id="exampleModalClose" type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span class="close" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="inappbody"></div>

            </div>
        </div>
    </div>
<?php
}

function getFireBaseConfig()
{
    return json_encode(array(
        'apiKey' => get_option('firebase_api_key'),
        'authDomain' => get_option('firebase_auth_domain'),
        'databaseURL' => false,
        'projectId' => get_option('firebase_project_id'),
        'storageBucket' => get_option('firebase_storage_bucket'),
        'messagingSenderId' => get_option('firebase_messaging_sender_id'),
        'appId' => get_option('firebase_app_id'),
        'measurementId' => get_option('firebase_measurement_id'),
    ));
}
function setFirebaseObject()
{
    $firebase_config =  getFireBaseConfig();
?>
    <script>
        var firebaseConfig = <?php echo $firebase_config; ?>;
    </script>
<?php
}


function cronberry_firebase_script_callback()
{
    $cronberry_push_notification_concent = get_option('cronberry_push_notification_concent');
    if ($cronberry_push_notification_concent) {
        setFirebaseObject();
        wp_enqueue_script('firebaseapp', 'https://www.gstatic.com/firebasejs/8.6.2/firebase-app.js', array('jquery'), rand(), true);
        wp_enqueue_script('firebase-analytics', 'https://www.gstatic.com/firebasejs/8.6.2/firebase-analytics.js', array('jquery'), rand(), true);
        wp_enqueue_script('firebase-messaging', 'https://www.gstatic.com/firebasejs/8.6.2/firebase-messaging.js', array('jquery'), rand(), true);
        wp_enqueue_script('firebase-auth', 'https://www.gstatic.com/firebasejs/8.6.2/firebase-auth.js', array('jquery'), rand(), true);
        wp_enqueue_script('firebase-script', CRONBERRY_URL . 'js/cronberry-firebase.js', array('jquery'), rand(), true);
    }
}
