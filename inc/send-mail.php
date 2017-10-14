<?php

function aoe_do_send_email( $order = false , $test = false, $custom_customer_email = false )
{
	// Send to Admin First
    $abandoned_email_template = apply_filters('aoe_abandon_email_template','templates/emails/email-abandoned.php');
	$order_shipped_email = wc_locate_template( $abandoned_email_template, false, PLUGIN_AOE_PATH );

	$mailer = WC()->mailer();
	$email_heading = __( 'Your order has been shipped', PLUGIN_AOE );
	$sent_to_admin = true;
	$plain_text = false;

	// get the preview email content
	ob_start();
	include $order_shipped_email;
	$admin_email = ob_get_clean();

	// create a new email
	$email_admin = new WC_Email();

	// wrap the content with the email template and then add styles
	$admin_email = $email_admin->style_inline(  $admin_email  );

	// Send to customer
	$mailer = WC()->mailer();

	$sent_to_admin = false;
	// get the preview email content

	ob_start();
	include $order_shipped_email;
	$customer_email = ob_get_clean();

	// create a new email
	$email_customer = new WC_Email();

	// wrap the content with the email template and then add styles
	$customer_email = $email_customer->style_inline(  $customer_email  );

	// custom customer email or not
	if($custom_customer_email) {
		$billing_email = $custom_customer_email;
	} else {
		$billing_email = $order->billing_email == false ? get_userdata( $order->post->post_author ) : $order->billing_email;
	}

	if(! $test)
	{
		add_filter( 'wp_mail_content_type', 'aoe_set_content_type' );

		$email_customer->send(
			$billing_email,
			__( 'Order shipping information' , PLUGIN_AOE ),
			$customer_email, // + _billing_email
			apply_filters( 'woocommerce_email_headers', '', 'shipped' ),
			array()
		);

		$options = get_option('aoe_options');

		// also send email to admin or not
		// if(isset($options['email_to_admin']) && $options['email_to_admin'] == 'yes') {
		// 	$email_customer->send(
		// 		get_option('admin_email'),
		// 		__( 'Order shipping information' , aoe_PATH ),
		// 		$admin_email,
		// 		apply_filters( 'woocommerce_email_headers', '', 'shipped' ),
		// 		array()
		// 	);
		// }
	}
}

function aoe_set_content_type( $content_type ) {
	return 'text/html';
}
