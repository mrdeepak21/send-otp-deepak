<?php
/**
 * @package: send-otp-deepak
 * @version: 1.0
 * Plugin name: Send OTP and SMS by Deepak
 * Description: cURL Based Plugin
 * Author: Deepak Kumar
 * Author URI: https://www.linkedin.com/in/deepak01/
 * Plugin URI: https://www.linkedin.com/in/deepak01/
 * Version: 1.0
 */

 if(!defined('ABSPATH')) exit;

 /**
 * Register WooCommerce registration form Name and phone field.
 */
add_action('woocommerce_register_form', function(){ ?>
<p class="form-row form-row-first">
        <label for="billing_first_name"><?php _e('First name', 'text_domain'); ?><span class="required">*</span></label>
        <input type="text" class="input-text" name="billing_first_name" id="billing_first_name" value="<?php if (!empty($_POST['billing_first_name'])) esc_attr_e($_POST['billing_first_name']); ?>" />
    </p>
    <p class="form-row form-row-last">
        <label for="billing_last_name"><?php _e('Last name', 'text_domain'); ?><span class="required">*</span></label>
        <input type="text" class="input-text" name="billing_last_name" id="billing_last_name" value="<?php if (!empty($_POST['billing_last_name'])) esc_attr_e($_POST['billing_last_name']); ?>" />
    </p>

    <p class="form-row">
        <label for="billing_phone"><?php _e('Mobile number', 'woocommerce'); ?><span class="required">*</span></label>
        <input type="tel" class="input-text" name="billing_phone" maxlength="10" id="billing_phone" value="<?php if (!empty($_POST['billing_phone'])) esc_attr_e($_POST['billing_phone']); ?>" />
    </p>
    <div class="clear" id="msg-otp"></div>
    <?php
});

/**
 * To validate WooCommerce registration form custom fields.
 */


add_action('woocommerce_register_post', function ($username, $email, $validation_errors) {
    if (isset($_POST['billing_first_name']) && empty($_POST['billing_first_name'])) {
        $validation_errors->add('billing_first_name_error', __('<strong>Error</strong>: First name is required!', 'text_domain'));
    }

    if (isset($_POST['billing_last_name']) && empty($_POST['billing_last_name'])) {
        $validation_errors->add('billing_last_name_error', __('<strong>Error</strong>: Last name is required!.', 'text_domain'));
    }

    if (isset($_POST['billing_phone']) && (empty($_POST['billing_phone']) || strlen($_POST['billing_phone'])!=10)) {
        $validation_errors->add('billing_phone_error', __('<strong>Error</strong>: Mobile number is required and 10 Digit required!.', 'text_domain'));
    }

    return $validation_errors;
}, 10, 3);

/**
 * To save WooCommerce registration form custom fields.
 */
add_action('woocommerce_created_customer', function ($customer_id) {
    //First name field
    if (isset($_POST['billing_first_name'])) {
        update_user_meta($customer_id, 'first_name', sanitize_text_field($_POST['billing_first_name']));
        update_user_meta($customer_id, 'billing_first_name', sanitize_text_field($_POST['billing_first_name']));
    }
    //Last name field
    if (isset($_POST['billing_last_name'])) {
        update_user_meta($customer_id, 'last_name', sanitize_text_field($_POST['billing_last_name']));
        update_user_meta($customer_id, 'billing_last_name', sanitize_text_field($_POST['billing_last_name']));
    }
    //Phone Number
    $mobile = '';
    if (isset($_POST['billing_phone'])) {
        $mobile = sanitize_text_field($_POST['billing_phone']);
        update_user_meta($customer_id, 'billing_phone', $mobile);
        update_user_meta($customer_id, 'shipping_phone', $mobile);
    }

     ##-------------Send SMS Registration Succesfull -------------------------------------
     $mobileNumber = '91'.$mobile;

     $senderId = "QMOMOM";
     
     $message = urlencode("Dear ".sanitize_text_field($_POST['billing_first_name']). ' '.sanitize_text_field($_POST['billing_last_name'])." Welcome to QMomo. Order from qmomo.in for additional discounts.
     For any help, give a missed call to 8779282540.
     Thank you - QMOMO");
     
     $route = 4;
     
     //Prepare you post parameters
     $postData = array(
		'authkey'	=> "380344Ayao1UEhI62e8c136P1",
		'mobiles'	=> $mobileNumber,
		'message'	=> $message,
		'sender'	=> $senderId,
		'route'		=> 4,
		'DLT_TE_ID'	=> 1107165849010500587
	);
     
     $url="http://api.msg91.com/api/v2/sendsms";
     
     
     $curl = curl_init();
     curl_setopt_array($curl, array(
         CURLOPT_URL => "$url",
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_CUSTOMREQUEST => "POST",
         CURLOPT_POSTFIELDS => $postData,
         CURLOPT_HTTPHEADER => array(
             "content-type: multipart/form-data"
         ),
     ));
     
      $response = curl_exec($curl);
     
    //  $err = curl_error($curl);
     

    //  curl_close($curl);
     
    //  if ($err) {
    //      echo "cURL Error #:" . $err;
    //  } else {
    //      echo $response;
    //  }
});