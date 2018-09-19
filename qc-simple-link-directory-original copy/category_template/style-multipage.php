		<link rel="stylesheet" type="text/css" href="<?php echo OCOPD_TPL_URL . "/style-multipage/style.css"; ?>" />
		<link rel="stylesheet" type="text/css" href="<?php echo OCOPD_TPL_URL . "/style-multipage/responsive.css"; ?>" />
		<div class="qcld_sld_category_list">
			<ul class="<?php echo $temp_style; ?>">
			<?php
				$ci = 0;
				foreach ($cterms as $cterm){
					?>
						<li>
						
							<div class="column-grid3">
								<div class="sld-main-content-area bg-color-0<?php echo (($ci%5)+1); ?>">
									<div class="sld-main-panel">
										<div class="panel-title">
											<h3><?php echo $cterm->name; ?></h3>
										</div>
										<?php $image_id = get_term_meta ( $cterm -> term_id, 'category-image-id', true );
										if($image_id){
										?>
										<div class="feature-image">					
											<?php echo wp_get_attachment_image ( $image_id, 'thumbnail' ); ?>
										</div>
										<?php } ?>
									</div>
									<div class="sld-hover-content">
										<p><?php echo $cterm->description; ?></p>
										<?php 
											if(sld_get_option('sld_lan_visit_page')!=''){
												$visit_page = sld_get_option('sld_lan_visit_page');
											}else{
												$visit_page = __('Visit Page','qco-pd');
											}
										?>
										<a href="<?php echo $current_url; ?>/<?php echo $cterm->slug; ?>" ><?php echo $visit_page; ?></a>
									</div>
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
								<div class="sld_total_lists"><?php echo $total_post; ?> List<?php echo ($total_post>1?'s':'') ?></div>
								<div class="sld_total_items"><?php echo $total_items; ?> Item<?php echo ($total_items>1?'s':'') ?></div>
								</div>
							</div>
						
						</li>
					<?php
					$ci++;					
				}
			?>
			</ul>
		</div>