<?PHP

add_action( 'admin_enqueue_scripts', 'abandon_order_email_scripts' );
function abandon_order_email_scripts() {
	wp_enqueue_style( 'aoe-admin-style', PLUGIN_AOE_PATH . 'assets/css/admin.css', array(), false );
	wp_enqueue_script( 'aoe-admin-js', PLUGIN_AOE_PATH . 'assets/js/admin.js' , array( 'jquery' ), '', true );
}

add_action( 'admin_menu', 'aoe_menu_page' );
function aoe_menu_page() {
	add_management_page( __( 'Abandon Order Email' ), __( 'Abandon Order Email' ), 'manage_options', 'abandon_order_email', 'aoe_page_handler' );
}

function aoe_page_handler() {
	global $wpdb, $pagenow;
	
	$page           = isset( $_GET['page'] ) ? $_GET['page'] : 'abandon_order_email';
	$paged          = isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 1;
	$per_page       = apply_filters( 'aoe_order_per_page', 10 );
	$offset         = apply_filters( 'aoe_order_offset', ($per_page * $paged) - $per_page );
	
	$customer_orders= $wpdb->get_results( $wpdb->prepare( "
		SELECT ID, post_date, post_type, post_status
		FROM {$wpdb->prefix}posts 
		WHERE post_type = %s
		AND post_status <> %s
		ORDER BY post_date DESC
		LIMIT $offset, $per_page
	", array( 'shop_order', 'trash' ) ) );
	$total_customer_orders = $wpdb->get_var( $wpdb->prepare( "
		SELECT COUNT(*) AS total
		FROM {$wpdb->prefix}posts 
		WHERE post_type = %s
		AND post_status <> %s
	", array( 'shop_order', 'trash' ) ) );
	$max_pages       = max( 1, ceil( $total_customer_orders / $per_page ) );
	?>
	<div id="background-worker" class="wrap">
		<h1><?php _e( 'Abandon Order Email', 'aoe' ); ?></h1>
		
		<div class="list-aoe">
			<table class="bordered-table">
				<thead>
					<tr>
						<th rowspan="2"><?php _e( 'Date', 'aoe' ); ?></th>
						<th rowspan="2"><?php _e( 'Order ID', 'aoe' ); ?></th>
						<th rowspan="2"><?php _e( 'Username', 'aoe' ); ?></th>
						<th rowspan="2"><?php _e( 'Total Value', 'aoe' ); ?></th>
						<th rowspan="2"><?php _e( 'Abandoned', 'aoe' ); ?></th>
						<th rowspan="2"><?php _e( 'Email Sent', 'aoe' ); ?></th>
						<th rowspan="2"><?php _e( 'Recover', 'aoe' ); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php
					if ( $customer_orders ) {

						foreach ($customer_orders as $order) { 

							$customer 		= get_post_meta( $order->ID, '_customer_user', true );
							$abandoned 		= get_post_meta( $order->ID, 'aoe_abandoned', true );
							$email_sent 	= get_post_meta( $order->ID, 'aoe_abandoned_emailed', true );
							$total_order 	= get_post_meta( $order->ID, '_order_total', true );
							$user 			= get_user_by( 'ID', $customer );

							?>

							<tr>
								<td class="text-center"><?php echo $order->post_date;?></td>
								<td class="text-center"><a href="<?php echo admin_url( 'post.php?post=' . $order->ID . '&action=edit' ); ?>">#<?php echo $order->ID;?></a></td>
								<td class="text-center"><a href="<?php echo admin_url( 'user-edit.php?user_id=' . $user->ID ); ?>"><?php echo $user->user_login; ?></a></td>
								<td class="text-right"><?php echo wc_price( $total_order ); ?></td>
								<td class="text-center"><?php echo $abandoned == 1 ? '<span class="status_ok">✔</span>' : '-'; ?></td>
								<td class="text-center"><?php echo $email_sent == 1 ? '<span class="status_ok">✔</span>' : '-'; ?></td>
								<td class="text-center"><?php echo ( ( $email_sent == 1 ) && ( $order->post_status == 'wc-completed' ) ) ? '<span class="status_ok">✔</span>' : '-'; ?></td>
							</tr>
						<?php
						}
					} else { ?>
						<tr>
							<td class="text-center" colspan="7"><b><?php echo apply_filters( 'aoe_order_no_results',__( 'No Orders found', 'aoe')); ?></b></td>
						</tr>
					<?php
					}
				?>
				</tbody>
			</table>
			<div class="pagination text-right">
				<?php
					echo paginate_links(
						array(
							'base'      => add_query_arg( 'paged', '%#%' ),
							'format'    => '',
							'prev_text' => __( '&laquo;' ),
							'next_text' => __( '&raquo;' ),
							'total'     => $max_pages,
							'current'   => $paged,
						)
					);
				?>
			</div>
		</div>
	</div>
	<?php
}