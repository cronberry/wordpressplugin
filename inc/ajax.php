<?php

add_action('rest_api_init', function () {
    register_rest_route('module/cronberryIntegration', '/inapp', array(
        'methods' => 'POST',
        'callback' => 'cronberry_inapp_api',
    ));

    register_rest_route('module/cronberryIntegration', '/firebase', array(
        'methods' => 'POST',
        'callback' => 'cronberry_firebase_api',
    ));

    register_rest_route('module/cronberryIntegration', '/cronjob', array(
        'methods' => 'GET',
        'callback' => 'cronberry_cron_api',
    ));
});

add_filter('cron_schedules', 'cronberry_cron_api_interval');

function cronberry_cron_api_interval($schedules)
{
    $schedules['10_minutes'] = array(
        'interval' => 600,
        'display' => esc_html__('Every 10 minutes'),
    );
    return $schedules;
}


add_action('cronberry_push_data_hook', 'cronberry_cron_api');


function cronberry_inapp_api()
{
    $templateData = "";
    $project_Key = get_option('cronberry_project_Key');
    $data = getInapp($project_Key, "");

    if (!empty($data)) {
        $dataDecode = json_decode($data);
        if ($dataDecode->status) {
            $templateData = $dataDecode->data->data;
            return array('quickview_html' => inapp_getHtml(
                $templateData
            ));
        }
    }
    return json_encode([
        'Error' => "errro"
    ]);
}
function inapp_getHtml($data)
{


    $content = "<div>";
    if ($data != "" && count($data) > 0) {
        foreach ($data as $item) {
            $content .= '<div class="preview">
            <div class="preview-inner">
                <div class="banner">
                    <img src="' . $item->image . '">
                    
                </div>
                <div class="title-box">
                    <h3>' . $item->title . '</h3>
                </div>
                <div class="description-box">
                    <div class="inner">' . $item->content . '</div>
                    <div class="buttons" >
                    <a class="btn btn-primary" href="' . $item->buttonLink . '" target="" style="background: ' . $item->buttonColor . ';">' . $item->buttonName . '</a>
                    </div>
                </div>
            </div>
        </div>';
        }
    } else {
        $content .= '<p> No notification available. </p>';
    }
    return $content . "</div>";
}
function getInapp($key, $audienceId)
{
    $header = array(
        'Content-Type: application/json',
        'Authorization: Basic Y3JvbmJlcnJ5QHVzZXJuYW1lOmNyb25iZXJyeUBwYXNzd29yZA==',
        'api-key: ' . $key,
    );
    $http = array(
        'method' => 'POST',
        'user_agent' => $_SERVER['SERVER_SOFTWARE'],
        'max_redirects' => 5,
        'timeout' => 5,
    );
    $payload = json_encode(array("audienceId" => $audienceId, "limit" => 50, "page" => 0));

    $ch = curl_init("https://api.cronberry.com/cronberry/api/campaign/fetch-inapp-notifications-list");
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
    return $resp;
}

function get_form_details($project_key, $data)
{
    
    $order_concent = get_option('cronberry_order_concent');
    $paramData['paramList'] = array();

    $userObject =    [
        "projectKey" => $project_key,
        "audienceId" => md5($data['id']),
        "name" => $data['name'],
        "mobile" => $data['mobile'],
        "email" => $data['email'],
        "web_fcm_token" => $data['web_fcm_token'],
    ];

    $cartArray = array();
    $orderArray = array();
   if($order_concent > 0){
    $cartArray = array(
        [
            "paramKey" => "cart_add_date",
            "paramValue" => $data['add_date']
        ],
        [
            "paramKey" => "source",
            "paramValue" => 'wordpress'
        ],
        [
            "paramKey" => "product_names",
            "paramValue" => $data['productnames']
        ],

        [
            "paramKey" => "product_quantity",
            "paramValue" => $data['productquantity']
        ]

    );

    $orderArray =  array(
        [
            "paramKey" => "order_id",
            "paramValue" => $data['orderid']
        ],
        [
            "paramKey" => "order_date",
            "paramValue" => $data['add_date']
        ],
        [
            "paramKey" => "product_names",
            "paramValue" => $data['productnames']
        ],

        [
            "paramKey" => "product_quantity",
            "paramValue" => $data['productquantity']
        ],
        [
            "paramKey" => "order_status",
            "paramValue" => $data['orderstatus']
        ],

        [
            "paramKey" => "amount",
            "paramValue" => $data['amount']
        ],

        [
            "paramKey" => "city",
            "paramValue" => $data['city']
        ],

        [
            "paramKey" => "postcode",
            "paramValue" => $data['postcode']
        ]
    );
   }
    $otherDataArray = array();
    if(!empty($data['otherData'])){
        $otherDataArray = json_decode($data['otherData']);
    }
    $userObject['paramList'] = array_merge($cartArray, $orderArray,$otherDataArray);
    return $userObject;
}

function post_order_details($data)
{
    $url = 'https://api.cronberry.com/cronberry/api/campaign/register-audience-data';

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS,  json_encode($data));
    curl_setopt($curl, CURLOPT_HTTPHEADER,  array(
        'Content-Type: application/json'
    ));
    $response = curl_exec($curl);
    $status_code = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
    if ($status_code == 200) {
        $response = json_decode($response, true);
        return  $response;
    } else {
        return  null;
    }
}
function update_status($id, $status, $message)
{
    global $wpdb;
    $tablename = $wpdb->prefix . 'cronberry_integration';
    $wpdb->query($wpdb->prepare("UPDATE " . $tablename . " SET status = %s, message=%s WHERE id = %s", $status, $message, $id));
}

function cronberry_cron_api()
{
    global $wpdb;
    $project_Key = get_option('cronberry_project_Key');
    $tablename = $wpdb->prefix . 'cronberry_integration';
    $payloads = $wpdb->get_results("SELECT * FROM " . $tablename . " where status=0", ARRAY_A);
    foreach ($payloads as $payload) {
        $post_data = get_form_details($project_Key, $payload);
        $detailObject =   post_order_details($post_data);
        if ($detailObject != null) {
            if ($detailObject['status']) {
                update_status($payload['id'], 1, $detailObject['data']);
            } else {
                update_status($payload['id'], 2, $detailObject['error_msgs']);
            }
        } else {
            update_status($payload['id'], 2, 'somthing went wrong');
        }
    }
}

function cronberry_firebase_api()
{
    global $wpdb;
    $tablename = $wpdb->prefix . 'cronberry_integration';
    $user = wp_get_current_user();
    $email =  $user->user_email;
    $name = $user->display_name;
    $mobile = get_user_meta($user, 'user_phone', true);
    $web_fcm_token = $_COOKIE['sentToServer'];
    $wpdb->insert(
        $tablename,
        array(
            'session_id' => get_current_user_id(),
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
