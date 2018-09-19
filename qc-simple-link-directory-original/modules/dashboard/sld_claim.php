<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $wp;
$current_url =  home_url( $wp->request );

$ctable = $wpdb->prefix.'sld_claim_purchase';
$cptable = $wpdb->prefix.'sld_claim_configuration';
$current_user = wp_get_current_user();
//Claim listing add handle goes here
if(isset($_POST['submitclaimitem'])){
	
	$date = date('Y-m-d H:i:s');
	
	$wpdb->insert(
		$ctable,
		array(
			'date'  => $date,
			'user_id'   => $current_user->ID,
			'listid'   => $_POST['claim_list'],
			'item'   => $_POST['claim_item'],
			'status'   => 'pending',
			
		)
	);
	$this->sld_claim_notification($current_user->ID, $_POST['claim_item']);
	
}

$claims = $wpdb->get_results("select * from $ctable where 1 and `user_id`=$current_user->ID ");

?>
<h2><?php echo __('Add Claim Listing', 'qc-opd') ?></h2>

<?php if(isset($_GET['er']) && $_GET['er']!=''): ?>
<p style="color:#fff;background:red;padding:10px;text-align:center"><?php echo $_GET['er'] ?></p>
<?php endif; ?>
<?php if(isset($_GET['success']) && $_GET['success']!=''): ?>
<p style="color:#fff;background:green;padding:10px;text-align:center"><?php echo $_GET['success'] ?></p>
<?php endif; ?>

<form action="<?php echo $current_url.'/?action=claim'; ?>" method="POST">
	<ul class="sld_form-style-1 sld_width">

		<li>
			<label><?php echo __('Select List Name','qc-opd') ?></label>
			<select style="" id="sld_claim_list" name="claim_list" required>
				<option value="">None</option>
				<?php 
				$list_args_total = array(
					'post_type' => 'sld',
					'posts_per_page' => -1,
				);
				$list_query = new WP_Query( $list_args_total );
				while ( $list_query->have_posts() )
				{
					$list_query->the_post();
					echo '<option value="'.get_the_ID().'">'.get_the_title().'</option>';
				}
				?>
			</select>
		</li>
		<li>
			<label><?php echo __('Select Item Name','qc-opd') ?></label>
			<select style="" id="sld_list_item" name="claim_item" required>
				<option value="">None</option>
			</select>
		</li>

        <li>
			<input type="submit" name="submitclaimitem" class="sld_submit_style" value="<?php echo __('Submit','qc-opd') ?>" />
		</li>
	</ul>
</form>
<?php
$claimpayment = $wpdb->get_row("select * from $cptable where 1");
?>
<style type="text/css">

	strong {
		font-weight: bold;
	}

	em {
		font-style: italic;
	}

	table {
		background: #f5f5f5;
		border: 1px solid #fff !important;
		box-shadow: inset 0 1px 0 #fff;
		font-size: 12px;
		line-height: 24px;
		margin: 30px auto;
		text-align: left;
		width: 800px;
	}


	td {
		border-right: 1px solid #fff;
		border-left: 1px solid #e8e8e8;
		border-top: 1px solid #fff;
		border-bottom: 1px solid #e8e8e8;
		padding: 10px 15px;
		position: relative;
		transition: all 300ms;
	}



	td:last-child {
		border-right: 1px solid #e8e8e8;
		box-shadow: inset -1px 0 0 #fff;
	}

	tr:last-of-type td {
		box-shadow: inset 0 -1px 0 #fff;
	}

	tr:last-of-type td:first-child {
		box-shadow: inset 1px -1px 0 #fff;
	}

	tr:last-of-type td:last-child {
		box-shadow: inset -1px -1px 0 #fff;
	}
.sld_table_package_head{
	background: #474343;
	color: #fff;
	text-align: center;
}
	.sld_table_package_content{
		font-size:16px;
 }
.qc_sld_cell{
	text-align: center;
}

</style>

<h2><?php echo __('Claim Listing','qc-opd'); ?></h2>





<?php

if(sld_get_option('sld_enable_paypal_test_mode')=='on'){
    $mainurl = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
}else{
    $mainurl = 'https://www.paypal.com/cgi-bin/webscr';
}

if(!empty($claims)){
	?>
<?php 
if(isset($claimpayment->enable) and $claimpayment->enable==1){
	$paymentAmount = $claimpayment->Amount;
}else{
	$paymentAmount = 0;
}

/*
if($paymentAmount==$claim->paid_amount && $claim->status=='pending'):
echo __('<p>  Your claim list item is waiting for admin approval. After it is approved you will find your claim list item in "Your Links" Tab for editing.</p>','qc-opd').'<br>';
endif;
*/
?>
    <div class="qc_sld_table_area">
        <div class="qc_sld_table">

            <div class="qc_sld_row header">

                <div class="qc_sld_cell qc_sld_table_head">
					<?php _e( 'List Name', 'qc-opd' ) ?>
                </div>

                <div class="qc_sld_cell qc_sld_table_head">
					<?php _e( 'Item Name', 'qc-opd' ); ?>
                </div>
                <div class="qc_sld_cell qc_sld_table_head">
					<?php _e( 'Payable Amount', 'qc-opd' ); ?>
                </div>
				<div class="qc_sld_cell qc_sld_table_head">
					<?php _e( 'Paid Amount', 'qc-opd' ); ?>
                </div>

                <div class="qc_sld_cell qc_sld_table_head">
					<?php _e( 'Status', 'qc-opd' ); ?>
                </div>
				
				<?php if(!empty($claimpayment) and $claimpayment->enable==1 and $claim->paid_amount==0): ?>
				<div class="qc_sld_cell qc_sld_table_head">
					<?php _e( 'Payment', 'qc-opd' ); ?>
				</div>
				<?php endif; ?>
                
            </div>

			<?php 
			$c=0;
			foreach($claims as $claim): 
			$c++;
			?>
                <div class="qc_sld_row">



                    <div class="qc_sld_cell">
                        <div class="sld_responsive_head"><?php echo __('List Name', 'qc-opd') ?></div>
						<?php echo get_the_title($claim->listid); ?>
                    </div>


                    <div class="qc_sld_cell">
                        <div class="sld_responsive_head"><?php echo __('Item Name', 'qc-opd') ?></div>
						<?php echo $claim->item; ?>
                    </div>

                    <div class="qc_sld_cell">
                        <div class="sld_responsive_head"><?php echo __('Payable Amount', 'qc-opd') ?></div>
						<?php 
							if(!empty($claimpayment) and $claimpayment->enable==1){
								echo $claimpayment->Amount.' '.$claimpayment->currency;
							}else{
								echo 0;
							}
						?>
                    </div>
					<div class="qc_sld_cell">
                        <div class="sld_responsive_head"><?php echo __('Paid Amount', 'qc-opd') ?></div>
						<?php echo $claim->paid_amount.' '.$claimpayment->currency; ?>
                    </div>

                    <div class="qc_sld_cell">
                        <div class="sld_responsive_head"><?php echo __('Status', 'qc-opd') ?></div>
						<?php echo ucfirst($claim->status); ?>
                    </div>
					<?php if(!empty($claimpayment) and $claimpayment->enable==1 and $claim->paid_amount==0): ?>
                    <div class="qc_sld_cell">
                        <div class="sld_responsive_head"><?php echo __('Payment', 'qc-opd') ?></div>
						<?php if(sld_get_option('sld_enable_paypal_payment')!='off'): ?>
						
                        <form action="<?php echo $mainurl; ?>" method="post" id="paypalProcessor">
                            <input type="hidden" name="cmd" value="_xclick" />

                            <input type="hidden" name="business" value="<?php echo sld_get_option('sld_paypal_email'); ?>">
                            <input type="hidden" name="currency_code" value="<?php echo $claimpayment->currency; ?>" />
                            <input type="hidden" name="no_note" value="1"/>
                            <input type="hidden" name="no_shipping" value="1" />
                            <input type="hidden" name="charset" value="utf-8" />

                            <input type="hidden" name="notify_url" value="<?php echo esc_url( add_query_arg( array('payment'=> 'claim-paypal', 'pkg'=> $claim->id), $url ) ) ?>" />

                            <input type="hidden" name="return" value="<?php echo esc_url( add_query_arg( 'payment', 'success', $url ) ) ?>" />

                            <input type="hidden" name="cancel_return" value="<?php echo esc_url( add_query_arg( 'payment', 'cancel', $url ) ) ?>">
                            <input type="hidden" name="item_name" value="Claim Listing payment for <?php echo $claim->item; ?>">
                            <input type="hidden" name="amount" value="<?php echo (isset($claimpayment->Amount)&&$claimpayment->Amount!=''?$claimpayment->Amount:'0'); ?>">

                            <input type="hidden" name="quantity" value="1">
                            <input type="hidden" name="receiver_email" value="<?php echo sld_get_option('sld_paypal_email'); ?>">
                            <input type="image" name="submit" border="0"  src="<?php echo QCOPD_IMG_URL.'/btn_buynow_LG.gif'; ?>" alt="PayPal - The safer, easier way to pay online">
							<span style="display:block;margin:0;padding:0;">Paypal</span>
                        </form>
						<?php endif; ?>
						
						
						<?php if(sld_get_option('sld_enable_stripe_payment')=="on"): ?>
							
							<form action="<?php echo esc_url( add_query_arg( array('payment'=> 'claim-stripe', 'pkg'=> $claim->id), $url ) ) ?>" method="post">
								<script src="https://checkout.stripe.com/checkout.js" class="stripe-button"
									  data-key="<?php echo sld_get_option('sld_stripe_public_key'); ?>"
									  data-description="Claim Listing Payment for <?php echo $claim->item; ?>"
									  data-amount="<?php echo (isset($claimpayment->Amount)&&$claimpayment->Amount!=''?($claimpayment->Amount*100):'0'); ?>"
									  data-locale="auto"						  
									  ></script>
									 <span style="display:block;margin:0;padding:0;">Stripe</span>
							</form>
						
						<?php endif; ?>

						<?php if(sld_get_option('sld_enable_2checkout_payment')=="on"): ?>
						<button class="sld_2co_btn" data-mfp-src="#sld_package_2co<?php echo $c; ?>" style="margin-top: 6px;">Pay with Card</button>
						<p style="margin: 0px 0px;padding: 0px;color: #000;font-size: 14px;">2Checkout</p>
						<div id="sld_package_2co<?php echo $c; ?>" class="white-popup mfp-hide">
							<div class="sld_2co_form">
								<p class="sld_2co_head">2Checkout Payment</p>
								<form id="sld_2co_package_form<?php echo $c; ?>" action="<?php echo esc_url( add_query_arg( array('payment'=> 'claim-2co', 'pkg'=>$claim->id), $url ) ) ?>" method="post">
									<input name="token" type="hidden" value="">
									<input name="amount" type="hidden" value="<?php echo (isset($claimpayment->Amount)&&$claimpayment->Amount!=''?($claimpayment->Amount):'0'); ?>">
									<input type="text" name="name" placeholder="Name" required />
									<input type="text" name="address" placeholder="Street Address" required />
									<input type="text" name="city" placeholder="City" required />
									<input type="text" name="state" placeholder="State" required />
									<input type="text" name="zipcode" placeholder="Zipcode" required />
									<input type="text" name="country" placeholder="Country" required />
									<input type="text" name="phone" placeholder="Phone" required />
									<input id="sld_package_ccNo<?php echo $c; ?>" type="text" size="20" value="" placeholder="Card No" autocomplete="off" required />
									<div class="sld_2co_expire_date">
										<input type="text" size="2" id="sld_package_expMonth<?php echo $c; ?>" maxlength="2" placeholder="MM" required /> / <input type="text" placeholder="YYYY" size="2" maxlength="4" id="sld_package_expYear<?php echo $c; ?>" required />
									</div>
									<input id="sld_package_cvv<?php echo $c; ?>" type="text" placeholder="CVV" value="" autocomplete="off" required />
									<input type="submit" class="sld_2co_submit" id="sld_2co_package_submit<?php echo $c; ?>" value="Pay $<?php echo (isset($claimpayment->Amount)&&$claimpayment->Amount!=''?($claimpayment->Amount):'0'); ?>" />
								</form>
							<script>
								// Called when token created successfully.
								var successCallback<?php echo $c; ?> = function(data) {
									var myForm = document.getElementById('sld_2co_package_form<?php echo $c; ?>');
									
									// Set the token as the value for the token input
									myForm.token.value = data.response.token.token;

									// IMPORTANT: Here we call `submit()` on the form element directly instead of using jQuery to prevent and infinite token request loop.
									myForm.submit();
								};

								// Called when token creation fails.
								var errorCallback<?php echo $c; ?> = function(data) {
									if (data.errorCode === 200) {
										tokenRequest<?php echo $c; ?>();
									} else {
										alert(data.errorMsg);
									}
								};

								var tokenRequest<?php echo $c; ?> = function() {
									// Setup token request arguments
									
									var args = {
										sellerId: "<?php echo sld_get_option('sld_2checkout_seller_id'); ?>",
										publishableKey: "<?php echo sld_get_option('sld_2checkout_public_key'); ?>",
										ccNo: document.getElementById('sld_package_ccNo<?php echo $c; ?>').value,
										cvv: document.getElementById('sld_package_cvv<?php echo $c; ?>').value,
										expMonth: document.getElementById('sld_package_expMonth<?php echo $c; ?>').value,
										expYear: document.getElementById('sld_package_expYear<?php echo $c; ?>').value
									};

									// Make the token request
									TCO.requestToken(successCallback<?php echo $c; ?>, errorCallback<?php echo $c; ?>, args);
								};

								jQuery(function() {
									// Pull in the public encryption key for our environment
									<?php if(sld_get_option('sld_enable_2checkout_sandbox')=="on"): ?>
									TCO.loadPubKey('sandbox');
									<?php endif; ?>

									jQuery("#sld_2co_package_form<?php echo $c; ?>").submit(function(e) {
										// Call our token request function
										tokenRequest<?php echo $c; ?>();

										// Prevent form from submitting
										return false;
									});
								});
							</script>
							</div>
						</div>
						<?php endif; ?>


                    </div>
					<?php endif; ?>

                </div>
				<?php endforeach; ?>


        </div>

    </div>
	<?php
}else{
?>
    <p><?php echo __('No Claim Listing', 'qc-opd') ?></p>
<?php
}
?>
