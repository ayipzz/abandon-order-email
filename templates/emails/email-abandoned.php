<?php
/**
 * Admin new order email
 *
 * @author WooThemes
 * @package WooCommerce/Templates/Emails/HTML
 * @version 2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<?php do_action( 'woocommerce_email_header', $email_heading ); ?>

<h2><a class="link" href="<?php echo admin_url( 'post.php?post=' . $order->id . '&action=edit' ); ?>"><?php printf( __( 'Order #%s', 'woocommerce'), $order->get_order_number() ); ?></a> (<?php printf( '<time datetime="%s">%s</time>', date_i18n( 'c', strtotime( $order->order_date ) ), date_i18n( wc_date_format(), strtotime( $order->order_date ) ) ); ?>)</h2>
<?php
	if( $sent_to_admin )
		_e('Hi there. The shipping detail for your order is as follows: ',PLUGIN_RESI);
	else
		_e('Hi there. Your order has been shipped. The shipping detail for your order is as follows:  ',PLUGIN_RESI);
?>

<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
	<thead>
		<tr>
			<th class="td" scope="col" style="text-align:left;"><?php _e( 'Shipping Courier', PLUGIN_RESI); ?></th>
			<th class="td" scope="col" style="text-align:left;"><?php _e( 'Website', PLUGIN_RESI); ?></th>
			<th class="td" scope="col" style="text-align:left;"><?php _e( 'Tracking Number', PLUGIN_RESI ); ?></th>
		</tr>
	</thead>
	<tbody>
	<tr>
		<?php
			$name = get_post_meta($order->id,'plugin_resi_ekspedisi_nama',true);
			$url = get_post_meta( $order->id,'plugin_resi_ekspedisi_url',true);
			$resi = get_post_meta( $order->id,'plugin_resi_nomor_resi',true);

			$name = $name != '' ? $name : 'Example Express';
			$url = $url != '' ? $url : 'http://www.example.com';
			$resi = $resi != '' ? $resi : '1234567890';
		?>

		<td class="td" scope="col" ><?php echo $name ?></td>
		<td class="td" scope="col" >
			<a href="<?php echo $url ?>"><?php echo $url ?></a>
		</td>
		<td class="td" scope="col" ><?php echo $resi ?></td>
	</tr>
	</tbody>
</table>
<?php do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text ); ?>

<?php do_action( 'woocommerce_email_footer' ); ?>
