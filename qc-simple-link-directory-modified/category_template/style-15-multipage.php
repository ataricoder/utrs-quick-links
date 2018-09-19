		<link rel="stylesheet" type="text/css" href="<?php echo OCOPD_TPL_URL . "/style-15-multipage/style.css"; ?>" />
		<style type="text/css">
		.qc-sld-single-item-11 .qc-sld-main {
			border-bottom:1px solid #eee
		}
		</style>
		<div class="qc-feature-container qc-sld-single-item-11">
			<ul class="<?php echo $temp_style; ?>">
			<?php
				$ci = 0;
				foreach ($cterms as $cterm){
					?>
						<li class="opt-column-03">
							<a href="<?php echo $current_url; ?>/<?php echo $cterm->slug; ?>">
							<div class="qc-sld-main">
							  <div class="qc-feature-media image">

							  <?php
							  
								$args = array(
									'numberposts' => -1,
									'post_type'   => 'sld',
								);
								$taxArray = array(
									array(
										'taxonomy' => 'sld_cat',
										'field'    => 'id',
										'terms'    => $cterm -> term_id,
									),
								);
								$args = array_merge($args, array( 'tax_query' => $taxArray ));								
								$listItems = get_posts( $args );
								$total_post = 0;
								if(!empty($listItems))
									$total_post = count($listItems);
								$total_items = 0;
								foreach ($listItems as $item){
									
									$total_items += count(get_post_meta( $item->ID, 'qcopd_list_item01' ));
									
								}
								
							  ?>
							  
								<?php $image_id = get_term_meta ( $cterm -> term_id, 'category-image-id', true );
										if($image_id){
										?>
														
											<?php echo wp_get_attachment_image ( $image_id, 'full' ); ?>
										
										<?php } ?>
									<div class="sld_total_lists"><?php echo $total_post; ?> List<?php echo ($total_post>1?'s':'') ?></div>
									<div class="sld_total_items"><?php echo $total_items; ?> Item<?php echo ($total_items>1?'s':'') ?></div>
							  </div>
							  <div class="qc-sld-content">
								<h4 class="sld-title"><?php echo $cterm->name; ?></h4>
								
								<p class="sub-title"><?php echo $cterm->description; ?></p>
							  </div>
							  
							  <div class="clear"></div>
							  </div>
						  </a>
						  
					  </li>
					<?php
					$ci++;					
				}
			?>
			</ul>
		</div>