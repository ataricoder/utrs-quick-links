		<link rel="stylesheet" type="text/css" href="<?php echo OCOPD_TPL_URL . "/style-10-multipage/template.css"; ?>" />
		<style type="text/css">
		.qc-sld-single-item-11 .qc-sld-main {
			border-bottom:1px solid #eee
		}
		</style>
		<div class="qc-feature-container qc-sld-single-item-11">
			<ul class="<?php echo $temp_style; ?> portfolio-listing">
			<?php
				$ci = 0;
				foreach ($cterms as $cterm){
					?>
						<li class="list-style-seven listy-style-seven-01 sld-style10-column3">
						
							<a href="<?php echo $current_url; ?>/<?php echo $cterm->slug; ?>">
							
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
							  
							 <div class="list-inner-part-seven">

                                <!-- Image, If Present -->
								<?php $image_id = get_term_meta ( $cterm -> term_id, 'category-image-id', true );
										if($image_id){
										?>
											<span class="ca-icon list-img-1" style="background-image: url(<?php echo wp_get_attachment_image_url ( $image_id, 'full' ); ?>"></span>		
											
										
										<?php }else{ ?>
								
										<span class="ca-icon list-img-1" style="background-image: url(<?php echo QCOPD_IMG_URL; ?>/list-image-placeholder.png);"></span>
									<?php } ?>
								<div class="sld_total_lists"><?php echo $total_post; ?> List<?php echo ($total_post>1?'s':'') ?></div>
								<div class="sld_total_items"><?php echo $total_items; ?> Item<?php echo ($total_items>1?'s':'') ?></div>

                                <div class="effect-style-seven">
                                    <h3>
                                    	<?php echo $cterm->name; ?>
                                    </h3>
                                    
                                    <p>
                                    	<?php echo $cterm->description; ?>
                                    </p>
                                    

                                </div>
                            </div>
							  
							  
							  
							  
						  </a>
						  
					  </li>
					<?php
					$ci++;					
				}
			?>
			</ul>
		</div>