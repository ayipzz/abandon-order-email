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
		_e('Would you like to complete your purchase for following product ? ','aoe');
	else
		_e('Would you like to complete your purchase for following product ?  ', 'aoe');

	$totals = $order->get_order_item_totals();
?>
<br />
<?php
if ( $order->payment_method !== 'bacs') return;
$bacs_account = new WC_Gateway_BACS();
?>
<p><?php _e( 'Please make payment to the account below:', 'aoe' ); ?></p>
<section class="woocommerce-bacs-bank-details">

	<h2 class="wc-bacs-bank-details-heading"><?php _e( 'Our bank details', 'aoe' ); ?></h2>

	<?php

	foreach ( $bacs_account->account_details as $bacs) { ?>

		<h3 class="wc-bacs-bank-details-account-name"><?php echo $bacs['account_name']; ?>:</h3>

		<ul class="wc-bacs-bank-details order_details bacs_details">
			<li class="bank_name"><?php _e( 'Bank:', 'aoe' ); ?> <strong><?php echo $bacs['bank_name']; ?></strong></li>
			<li class="account_number"><?php _e( 'Account number:', 'aoe' ); ?> <strong><?php echo $bacs['account_number']; ?></strong></li>
		</ul>

	<?php } ?>
</section>

<h2 class="wc-bacs-bank-details-heading"><?php _e( 'Order Details', 'aoe' ); ?></h2>
<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
	<thead>
		<tr>
			<th class="td" scope="col" style="text-align:left; width: 55%"><?php _e( 'Product', 'aoe'); ?></th>
			<th class="td" scope="col" style="text-align:left; width: 5%"><?php _e( 'Quantity', 'aoe'); ?></th>
			<th class="td" scope="col" style="text-align:left; width: 20%"><?php _e( 'Price', 'aoe' ); ?></th>
			<th class="td" scope="col" style="text-align:left; width: 20%"><?php _e( 'Total', 'aoe' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach( $order->get_items() as $item_id => $item ) {
			$product = $order->get_product_from_item( $item ); ?>
			<tr>
				<td>
					<?php
					if ( $_product && ! $_product->is_visible() ) {
						echo $item['name'];
					} else {
						echo sprintf( '<a href="%s">%s</a><br />', get_permalink( $item['product_id'] ), $item['name'] );
					}
					?>
				</td>
				<td style="text-align: center;"><?php echo $item['qty']; ?></td>
				<td style="text-align: right;"><?php echo $item['line_subtotal'] > 0 ? wc_price( $product->get_price() ) : wc_price( 0 ) . '<del> ('. wc_price( $product->get_price() ) . ')</del>'; ?></td>
				<td style="text-align: right;"><?php echo $order->get_formatted_line_subtotal( $item ); ?></td>
			</tr>
		<?php } ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="3"><?php _e( 'Total', 'aoe' ); ?></td>
			<td style="text-align: right;"><strong><?php echo $totals['cart_subtotal']['value']; ?></strong></td>
		</tr>
	</tfoot>
</table>
<br />

<div class="total_payment">
	<p><?php _e( 'Total must be paid:', 'aoe' ); ?></p>
	<h3 class="total"><?php echo $totals['cart_subtotal']['value']; ?></h3>
</div>

<h2 class="wc-bacs-bank-details-heading"><?php _e( 'Complete Order', 'aoe' ); ?></h2>
<p><?php _e( 'Please use this link to complete your order :', 'aoe' ); ?></p>
<p><a href=""><?php echo sprintf(__( 'Complete Purchase at %s', 'aoe' ), get_bloginfo('name')); ?></a></p>


<?php echo apply_filters( 'aoe_footer_text', sprintf( __( 'Thank You for shopping with %s', 'aoe' ), get_bloginfo( 'name' ) ) ); ?>
<?php //do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text ); ?>

<?php do_action( 'woocommerce_email_footer' ); ?>

<style type="text/css">
	table.td thead tr, table.td tfoot {
	    background: #7797b4;
	    color: #fff;
	}
	table.td tr:nth-child(2n+2) {
	    background: #e7eef5;
	}
	.total_payment {
	    background: #eee;
	    padding: 10px 15px 15px;
	    text-align: center;
	    margin-bottom: 20px;
	}
	.total_payment h3 {
	    text-align: center;
	    color: #557da1;
	    font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
	    font-size: 24px;
	    font-weight: bold;
	    margin: 0;
	}
</style>