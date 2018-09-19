<?php

function sld_get_option($key=''){
	
	if($key=='')
		return false;
	
	$data = get_option('sld_option_tree');
	
	if(!is_array($data))
		return false;
	
	if(empty($data))
		return false;
	
	if(array_key_exists($key, $data)){
		return $data[$key];
	}else{
		return false;
	}
	
}

function sld_is_youtube_video($link){
	parse_str( parse_url( $link, PHP_URL_QUERY ), $my_array_of_vars );
	if(isset($my_array_of_vars['v']) && $my_array_of_vars['v']!=''){
		return true;
	}
	return false;
}
function sld_is_vimeo_video($link){
	$urls = parse_url($link);
	if(isset($urls['host']) && $urls['host']=='vimeo.com'){
		return true;
	}
	return false;
}


/*
* Alexa ranking code
*/
function sld_alexaRank($url) {
 $alexaData = @simplexml_load_file("http://data.alexa.com/data?cli=10&url=".$url);
 $alexa['globalRank'] =  isset($alexaData->SD->POPULARITY) ? $alexaData->SD->POPULARITY->attributes()->TEXT : 0 ;
 $alexa['CountryRank'] =  isset($alexaData->SD->COUNTRY) ? $alexaData->SD->COUNTRY->attributes() : 0 ;
 if($alexa['globalRank']==0 && $alexa['CountryRank']==0){
	return array(); 
 }else{
	 return json_decode(json_encode($alexa), TRUE);
 }
 
}

function qc_get_id_by_shortcode($shortcode) {
	global $wpdb;
	$sql = 'SELECT ID
		FROM ' . $wpdb->posts . '
		WHERE
			post_type = "page"
			AND post_status="publish"
			AND post_content LIKE "%' . $shortcode . '%" limit 1';

	$id = $wpdb->get_var($sql);

	return $id;
}

function sld_get_the_user_ip() {
	if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
	//check ip from share internet
	$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
	//to check ip is pass from proxy
	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
	$ip = $_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}

/*
* This function return most voted link items of SLD
*/
function qcopd_get_most_popular_links_wi( $limit = null )
{
	if( $limit == null )
	{
		$limit = 5;
	}

	$arrayOfElements = array();

	$enableUpvoting = sld_get_option( 'sld_enable_widget_upvote' );

	$list_args = array(
		'post_type' => 'sld',
		'orderby' => 'date',
		'order' => 'desc',
		'posts_per_page' => -1,
	);

	$list_query = new WP_Query( $list_args );

	if( $list_query->have_posts() )
	{
		$count = 0;
		
		while ( $list_query->have_posts() ) 
		{
			$list_query->the_post();

			$lists = get_post_meta( get_the_ID(), 'qcopd_list_item01' );
			$lists = sldmodifyupvotes(get_the_ID(), $lists);
			$title = get_the_title();
			$id = get_the_ID();

			foreach( $lists as $list )
			{
				$img = "";
				$newtab = 0;
				$nofollow = 0;
				$votes = 0;

				$showFavicon = (isset($list['qcopd_use_favicon']) && trim($list['qcopd_use_favicon']) != "") ? $list['qcopd_use_favicon'] : "";
				
				$directImgLink = (isset($list['qcopd_item_img_link']) && trim($list['qcopd_item_img_link']) != "") ? $list['qcopd_item_img_link'] : "";
				
				if( $showFavicon == 1 )
				{
					if( $directImgLink != '' )
					{
						$img = trim($directImgLink);
					}else{
						$img = wp_get_attachment_image_src($list['qcopd_item_img']);
					}
				}else{
					$img = wp_get_attachment_image_src($list['qcopd_item_img']);
				}

				if( isset($list['qcopd_item_nofollow']) && $list['qcopd_item_nofollow'] == 1 ) 
				{
					$nofollow = 1;
				}

				if( isset($list['qcopd_item_newtab']) && $list['qcopd_item_newtab'] == 1 ) 
				{
					$newtab = 1;
				}

				if( isset($list['qcopd_upvote_count']) && (int)$list['qcopd_upvote_count'] > 0 )
				{
			  	  $votes = (int)$list['qcopd_upvote_count'];
			    }

				$item['item_title'] = trim($list['qcopd_item_title']);
				$item['item_img'] = $img;
				$item['item_subtitle'] = trim($list['qcopd_item_subtitle']);
				$item['item_link'] = $list['qcopd_item_link'];
				$item['item_nofollow'] = $nofollow;
				$item['item_newtab'] = $newtab;
				$item['item_votes'] = $votes;
				$item['item_parent'] = $title;
				$item['item_parent_id'] = $id;
				$item['item_unique'] = $list['qcopd_timelaps'];

				array_push($arrayOfElements, $item);

			}

			$count++;
		}
		wp_reset_query();
	}
	else
	{
		return __('No list elements was found.', 'qc-sld');
	}

	// Sort the multidimensional array
    usort($arrayOfElements, "custom_sort_by_votes");

    ob_start();

	//echo '<link rel="stylesheet" type="text/css" href="'.QCOPD_ASSETS_URL.'/css/directory-style.css" />';
    $count = 1;
    $listCount = 10111;
    $numberOfItems = count( $arrayOfElements );

    echo '<ul class="widget-sld-list">';
    
    foreach( $arrayOfElements as $item ){
		$mainimg = $item['item_img'];
		$imgurlnew = '';

		if('' != $mainimg && @array_key_exists(0,$mainimg)){
			$imgurlnew = $mainimg[0];
		}else{
			$imgurlnew = $mainimg;
		}
		
		
		
    	?>
    	<li id="item-<?php echo $item['item_parent_id'] ."-". $listCount; ?>">

			<a <?php echo (isset($item['item_nofollow']) && $item['item_nofollow'] == 1) ? 'rel="nofollow"' : ''; ?> <?php echo (isset($item['item_newtab']) && $item['item_newtab'] == 1) ? 'target="_blank"' : ''; ?> href="<?php echo $item['item_link']; ?>">

				<?php if( $imgurlnew != "" ) : ?>
				
					<img class="widget-avatar" src="<?php echo $imgurlnew; ?>" alt="">

				<?php else : ?>

					<img class="widget-avatar" src="<?php echo QCOPD_IMG_URL; ?>/list-image-placeholder.png" alt="">

				<?php endif; ?>
			
					

				<?php echo $item['item_title']; ?>

				<?php if( $enableUpvoting == 'on' ) : ?>

				<div class="widget-vcount">
				
					<div class="upvote-section">
						
						<span data-post-id="<?php echo $item['item_parent_id']; ?>" data-unique="<?php echo $item['item_parent_id'].'_'.$item['item_unique']; ?>" data-item-title="<?php echo $item['item_title']; ?>" data-item-link="<?php echo $item['item_link']; ?>" class="upvote-btn upvote-on">
							<span class="opening-bracket">
								(
							</span>
							<i class="fa fa-thumbs-up"></i>
							<span class="upvote-count">
								<?php echo $item['item_votes']; ?>
							</span>
							<span class="closing-bracket">
								)
							</span>
						</span>	
						
					</div>

				</div>

				<?php endif; ?>

			</a>

		</li>
    	<?php

    	if( $numberOfItems > $limit )
    	{
    		if( $limit == $count )
    		{
    			break;
    		} //if $limit == $count

    	} //if $numberOfItems > $limit

    	$count++;
    	$listCount++;

    } //End Foreach

    echo '</ul>';

    $content = ob_get_clean();

    return $content;

}
//get popular new style

function qcopd_get_popular_new( $limit = null )
{
	if( $limit == null )
	{
		$limit = 6;
	}

	$arrayOfElements = array();

	$enableUpvoting = sld_get_option( 'sld_enable_widget_upvote' );

	$list_args = array(
		'post_type' => 'sld',
		'orderby' => 'date',
		'order' => 'desc',
		'posts_per_page' => -1,
	);

	$list_query = new WP_Query( $list_args );

	if( $list_query->have_posts() )
	{
		$count = 0;
		
		while ( $list_query->have_posts() ) 
		{
			$list_query->the_post();

			$lists = get_post_meta( get_the_ID(), 'qcopd_list_item01' );
			$lists = sldmodifyupvotes(get_the_ID(), $lists);
			$title = get_the_title();
			$id = get_the_ID();

			foreach( $lists as $list )
			{
				$img = "";
				$newtab = 0;
				$nofollow = 0;
				$votes = 0;

				$showFavicon = (isset($list['qcopd_use_favicon']) && trim($list['qcopd_use_favicon']) != "") ? $list['qcopd_use_favicon'] : "";
				
				$directImgLink = (isset($list['qcopd_item_img_link']) && trim($list['qcopd_item_img_link']) != "") ? $list['qcopd_item_img_link'] : "";
				
				if( $showFavicon == 1 )
				{
					if( $directImgLink != '' )
					{
						$img = trim($directImgLink);
					}else{
						$img = wp_get_attachment_image_src($list['qcopd_item_img']);
					}
				}else{
					$img = wp_get_attachment_image_src($list['qcopd_item_img']);
				}

				if( isset($list['qcopd_item_nofollow']) && $list['qcopd_item_nofollow'] == 1 ) 
				{
					$nofollow = 1;
				}

				if( isset($list['qcopd_item_newtab']) && $list['qcopd_item_newtab'] == 1 ) 
				{
					$newtab = 1;
				}

				if( isset($list['qcopd_upvote_count']) && (int)$list['qcopd_upvote_count'] > 0 )
				{
			  	  $votes = (int)$list['qcopd_upvote_count'];
			    }

				$item['item_title'] = trim($list['qcopd_item_title']);
				$item['item_img'] = $img;
				$item['item_subtitle'] = trim($list['qcopd_item_subtitle']);
				$item['item_link'] = $list['qcopd_item_link'];
				$item['item_nofollow'] = $nofollow;
				$item['item_newtab'] = $newtab;
				$item['item_votes'] = $votes;
				$item['item_parent'] = $title;
				$item['item_parent_id'] = $id;
				$item['item_unique'] = $list['qcopd_timelaps'];

				array_push($arrayOfElements, $item);

			}

			$count++;
		}
		wp_reset_query();
	}
	else
	{
		return __('No list elements was found.', 'qc-sld');
	}

	// Sort the multidimensional array
    usort($arrayOfElements, "custom_sort_by_votes");

    ob_start();

	//echo '<link rel="stylesheet" type="text/css" href="'.QCOPD_ASSETS_URL.'/css/directory-style.css" />';
    $count = 1;
    $listCount = 10111;
    $numberOfItems = count( $arrayOfElements );

    
    
    foreach( $arrayOfElements as $item ){
		$mainimg = $item['item_img'];
		$imgurlnew = '';

		if('' != $mainimg && @array_key_exists(0,$mainimg)){
			$imgurlnew = $mainimg[0];
		}else{
			$imgurlnew = $mainimg;
		}
		
		
		
    	?>
    	<div class="qc-column-4" id="item-<?php echo $item['item_parent_id'] ."-". $listCount; ?>"><!-- qc-column-4 -->
			<!-- Feature Box 1 -->
			<div class="featured-block ">
				<div class="featured-block-img">
					<a <?php echo (isset($item['item_nofollow']) && $item['item_nofollow'] == 1) ? 'rel="nofollow"' : ''; ?> <?php echo (isset($item['item_newtab']) && $item['item_newtab'] == 1) ? 'target="_blank"' : ''; ?> href="<?php echo $item['item_link']; ?>"> 
						<?php if( $imgurlnew != "" ) : ?>
					
						<img src="<?php echo $imgurlnew; ?>" alt="">

						<?php else : ?>

							<img src="<?php echo QCOPD_IMG_URL; ?>/list-image-placeholder.png" alt="">

						<?php endif; ?>
					</a>
				</div>
				<div class="featured-block-info">
					<h4><a <?php echo (isset($item['item_nofollow']) && $item['item_nofollow'] == 1) ? 'rel="nofollow"' : ''; ?> <?php echo (isset($item['item_newtab']) && $item['item_newtab'] == 1) ? 'target="_blank"' : ''; ?> href="<?php echo $item['item_link']; ?>"><?php echo $item['item_title']; ?> </a></h4>

				</div>
				<?php if( $enableUpvoting == 'on' ) : ?>
				<div class="featured-block-upvote upvote-section">
					<span class="upvote-count count"> <?php echo $item['item_votes']; ?> </span>
					<span data-post-id="<?php echo $item['item_parent_id']; ?>" data-item-title="<?php echo $item['item_title']; ?>" data-item-link="<?php echo $item['item_link']; ?>" class="upvote-btn upvote-on"><i class="fa fa-thumbs-up"></i> </span>
				</div>
				<?php endif; ?>
			</div>
		</div><!--/qc-column-4 -->
    	<?php

    	if( $numberOfItems > $limit )
    	{
    		if( $limit == $count )
    		{
    			break;
    		} //if $limit == $count

    	} //if $numberOfItems > $limit

    	$count++;
    	$listCount++;

    } //End Foreach

   

    $content = ob_get_clean();

    return $content;

} //End of get_most_popular_links

// Define the custom sort function
function custom_sort_by_votes($a, $b) {
    return $a['item_votes'] < $b['item_votes'];
}


/*
* This function return randomly picked link items of SLD
*/
function qcopd_get_random_links_wi( $limit = null )
{
	if( $limit == null )
	{
		$limit = 5;
	}

	$enableUpvoting = sld_get_option( 'sld_enable_widget_upvote' );

	$arrayOfElements = array();

	$list_args = array(
		'post_type' => 'sld',
		'orderby' => 'date',
		'order' => 'desc',
		'posts_per_page' => -1,
	);

	$list_query = new WP_Query( $list_args );

	if( $list_query->have_posts() )
	{
		$count = 0;
		
		while ( $list_query->have_posts() ) 
		{
			$list_query->the_post();

			$lists = get_post_meta( get_the_ID(), 'qcopd_list_item01' );
			$lists = sldmodifyupvotes(get_the_ID(), $lists);
			$title = get_the_title();
			$id = get_the_ID();

			foreach( $lists as $list )
			{
				$img = "";
				$newtab = 0;
				$nofollow = 0;
				$votes = 0;

				$showFavicon = (isset($list['qcopd_use_favicon']) && trim($list['qcopd_use_favicon']) != "") ? $list['qcopd_use_favicon'] : "";
				
				$directImgLink = (isset($list['qcopd_item_img_link']) && trim($list['qcopd_item_img_link']) != "") ? $list['qcopd_item_img_link'] : "";
				
				if( $showFavicon == 1 )
				{
					if( $directImgLink != '' )
					{
						$img = trim($directImgLink);
					}else{
						$img = wp_get_attachment_image_src($list['qcopd_item_img']);
					}
				}else{
					$img = wp_get_attachment_image_src($list['qcopd_item_img']);
				}

				if( isset($list['qcopd_item_nofollow']) && $list['qcopd_item_nofollow'] == 1 ) 
				{
					$nofollow = 1;
				}

				if( isset($list['qcopd_item_newtab']) && $list['qcopd_item_newtab'] == 1 ) 
				{
					$newtab = 1;
				}

				if( isset($list['qcopd_upvote_count']) && (int)$list['qcopd_upvote_count'] > 0 )
				{
			  	  $votes = (int)$list['qcopd_upvote_count'];
			    }

				$item['item_title'] = trim($list['qcopd_item_title']);
				$item['item_img'] = $img;
				$item['item_subtitle'] = trim($list['qcopd_item_subtitle']);
				$item['item_link'] = $list['qcopd_item_link'];
				$item['item_nofollow'] = $nofollow;
				$item['item_newtab'] = $newtab;
				$item['item_votes'] = $votes;
				$item['item_parent'] = $title;
				$item['item_parent_id'] = $id;

				array_push($arrayOfElements, $item);

			}

			$count++;
		}
		wp_reset_query();
	}
	else
	{
		return __('No list elements was found.', 'qc-sld');
	}

	// Sort the multidimensional array
    usort($arrayOfElements, "custom_sort_by_votes");

    shuffle( $arrayOfElements );

    ob_start();
	//echo '<link rel="stylesheet" type="text/css" href="'.QCOPD_ASSETS_URL.'/css/directory-style.css" />';
    $count = 1;
    $listCount = 20111;
    $numberOfItems = count( $arrayOfElements );

    echo '<ul class="widget-sld-list">';
    
    foreach( $arrayOfElements as $item ){
		$mainimg = $item['item_img'];
		$imgurlnew = '';

		if('' != $mainimg && @array_key_exists(0,$mainimg)){
			$imgurlnew = $mainimg[0];
		}else{
			$imgurlnew = $mainimg;
		}
    	?>
    	<li id="item-<?php echo $item['item_parent_id'] ."-". $listCount; ?>">
			<a <?php echo (isset($item['item_nofollow']) && $item['item_nofollow'] == 1) ? 'rel="nofollow"' : ''; ?> <?php echo (isset($item['item_newtab']) && $item['item_newtab'] == 1) ? 'target="_blank"' : ''; ?> href="<?php echo $item['item_link']; ?>">

				<?php if( $imgurlnew != "" ) : ?>
				
					<img class="widget-avatar" src="<?php echo $imgurlnew; ?>" alt="">

				<?php else : ?>

					<img class="widget-avatar" src="<?php echo QCOPD_IMG_URL; ?>/list-image-placeholder.png" alt="">

				<?php endif; ?>

				<?php echo $item['item_title']; ?>

				<?php if( $enableUpvoting == 'on' ) : ?>

				<div class="widget-vcount">
				
					<div class="upvote-section">
						
						<span data-post-id="<?php echo $item['item_parent_id']; ?>" data-item-title="<?php echo $item['item_title']; ?>" data-item-link="<?php echo $item['item_link']; ?>" class="upvote-btn upvote-on">
							<span class="opening-bracket">
								(
							</span>
							<i class="fa fa-thumbs-up"></i>
							<span class="upvote-count">
								<?php echo $item['item_votes']; ?>
							</span>
							<span class="closing-bracket">
								)
							</span>
						</span>	
						
					</div>

				</div>

				<?php endif; ?>

			</a>
		</li>
    	<?php

    	if( $numberOfItems > $limit )
    	{
    		if( $limit == $count )
    		{
    			break;
    		} //if $limit == $count

    	} //if $numberOfItems > $limit

    	$count++;
    	$listCount++;

    } //End Foreach

    echo '</ul>';

    $content = ob_get_clean();

    return $content;

} //End of qcopd_get_random_links_wi

//Random New

function qcopd_get_random_new( $limit = null )
{
	if( $limit == null )
	{
		$limit = 6;
	}

	$enableUpvoting = sld_get_option( 'sld_enable_widget_upvote' );

	$arrayOfElements = array();

	$list_args = array(
		'post_type' => 'sld',
		'orderby' => 'date',
		'order' => 'desc',
		'posts_per_page' => -1,
	);

	$list_query = new WP_Query( $list_args );

	if( $list_query->have_posts() )
	{
		$count = 0;
		
		while ( $list_query->have_posts() ) 
		{
			$list_query->the_post();

			$lists = get_post_meta( get_the_ID(), 'qcopd_list_item01' );
			$lists = sldmodifyupvotes(get_the_ID(), $lists);
			$title = get_the_title();
			$id = get_the_ID();

			foreach( $lists as $list )
			{
				$img = "";
				$newtab = 0;
				$nofollow = 0;
				$votes = 0;

				$showFavicon = (isset($list['qcopd_use_favicon']) && trim($list['qcopd_use_favicon']) != "") ? $list['qcopd_use_favicon'] : "";
				
				$directImgLink = (isset($list['qcopd_item_img_link']) && trim($list['qcopd_item_img_link']) != "") ? $list['qcopd_item_img_link'] : "";
				
				if( $showFavicon == 1 )
				{
					if( $directImgLink != '' )
					{
						$img = trim($directImgLink);
					}else{
						$img = wp_get_attachment_image_src($list['qcopd_item_img']);
					}
				}else{
					$img = wp_get_attachment_image_src($list['qcopd_item_img']);
				}

				if( isset($list['qcopd_item_nofollow']) && $list['qcopd_item_nofollow'] == 1 ) 
				{
					$nofollow = 1;
				}

				if( isset($list['qcopd_item_newtab']) && $list['qcopd_item_newtab'] == 1 ) 
				{
					$newtab = 1;
				}

				if( isset($list['qcopd_upvote_count']) && (int)$list['qcopd_upvote_count'] > 0 )
				{
			  	  $votes = (int)$list['qcopd_upvote_count'];
			    }

				$item['item_title'] = trim($list['qcopd_item_title']);
				$item['item_img'] = $img;
				$item['item_subtitle'] = trim($list['qcopd_item_subtitle']);
				$item['item_link'] = $list['qcopd_item_link'];
				$item['item_nofollow'] = $nofollow;
				$item['item_newtab'] = $newtab;
				$item['item_votes'] = $votes;
				$item['item_parent'] = $title;
				$item['item_parent_id'] = $id;

				array_push($arrayOfElements, $item);

			}

			$count++;
		}
		wp_reset_query();
	}
	else
	{
		return __('No list elements was found.', 'qc-sld');
	}

	// Sort the multidimensional array
    usort($arrayOfElements, "custom_sort_by_votes");

    shuffle( $arrayOfElements );

    ob_start();
	//echo '<link rel="stylesheet" type="text/css" href="'.QCOPD_ASSETS_URL.'/css/directory-style.css" />';
    $count = 1;
    $listCount = 20111;
    $numberOfItems = count( $arrayOfElements );

    
    
    foreach( $arrayOfElements as $item ){
		$mainimg = $item['item_img'];
		$imgurlnew = '';

		if('' != $mainimg && @array_key_exists(0,$mainimg)){
			$imgurlnew = $mainimg[0];
		}else{
			$imgurlnew = $mainimg;
		}
    	?>
    	<div class="qc-column-4" id="item-<?php echo $item['item_parent_id'] ."-". $listCount; ?>"><!-- qc-column-4 -->
			<!-- Feature Box 1 -->
			<div class="featured-block ">
				<div class="featured-block-img">
					<a <?php echo (isset($item['item_nofollow']) && $item['item_nofollow'] == 1) ? 'rel="nofollow"' : ''; ?> <?php echo (isset($item['item_newtab']) && $item['item_newtab'] == 1) ? 'target="_blank"' : ''; ?> href="<?php echo $item['item_link']; ?>"> 
						<?php if( $imgurlnew != "" ) : ?>
					
						<img src="<?php echo $imgurlnew; ?>" alt="">

						<?php else : ?>

							<img src="<?php echo QCOPD_IMG_URL; ?>/list-image-placeholder.png" alt="">

						<?php endif; ?>
					</a>
				</div>
				<div class="featured-block-info">
					<h4><a <?php echo (isset($item['item_nofollow']) && $item['item_nofollow'] == 1) ? 'rel="nofollow"' : ''; ?> <?php echo (isset($item['item_newtab']) && $item['item_newtab'] == 1) ? 'target="_blank"' : ''; ?> href="<?php echo $item['item_link']; ?>"><?php echo $item['item_title']; ?> </a></h4>

				</div>
				<?php if( $enableUpvoting == 'on' ) : ?>
				<div class="featured-block-upvote upvote-section">
					<span class="upvote-count count"> <?php echo $item['item_votes']; ?> </span>
					<span data-post-id="<?php echo $item['item_parent_id']; ?>" data-item-title="<?php echo $item['item_title']; ?>" data-item-link="<?php echo $item['item_link']; ?>" class="upvote-btn upvote-on"><i class="fa fa-thumbs-up"></i> </span>
				</div>
				<?php endif; ?>
			</div>
		</div><!--/qc-column-4 -->
    	<?php

    	if( $numberOfItems > $limit )
    	{
    		if( $limit == $count )
    		{
    			break;
    		} //if $limit == $count

    	} //if $numberOfItems > $limit

    	$count++;
    	$listCount++;

    } //End Foreach

    

    $content = ob_get_clean();

    return $content;

}


/*
* This function return the most recent link items of SLD
*/
function qcopd_get_latest_links_wi( $limit = null )
{
	if( $limit == null )
	{
		$limit = 5;
	}

	$enableUpvoting = sld_get_option( 'sld_enable_widget_upvote' );

	$arrayOfElements = array();

	$list_args = array(
		'post_type' => 'sld',
		'orderby' => 'date',
		'order' => 'desc',
		'posts_per_page' => -1,
	);

	$list_query = new WP_Query( $list_args );

	if( $list_query->have_posts() )
	{
		$count = 0;
		
		while ( $list_query->have_posts() ) 
		{
			$list_query->the_post();

			$lists = get_post_meta( get_the_ID(), 'qcopd_list_item01' );
			$lists = sldmodifyupvotes(get_the_ID(), $lists);
			
			$title = get_the_title();
			$id = get_the_ID();

			foreach( $lists as $list )
			{
				
				$img = "";
				$newtab = 0;
				$nofollow = 0;
				$votes = 0;

				$showFavicon = (isset($list['qcopd_use_favicon']) && trim($list['qcopd_use_favicon']) != "") ? $list['qcopd_use_favicon'] : "";
				
				$directImgLink = (isset($list['qcopd_item_img_link']) && trim($list['qcopd_item_img_link']) != "") ? $list['qcopd_item_img_link'] : "";
				
				if( $showFavicon == 1 )
				{
					if( $directImgLink != '' )
					{
						$img = trim($directImgLink);
					}else{
						$img = wp_get_attachment_image_src($list['qcopd_item_img']);
					}
				}else{
					$img = wp_get_attachment_image_src($list['qcopd_item_img']);
				}

				if( isset($list['qcopd_item_nofollow']) && $list['qcopd_item_nofollow'] == 1 ) 
				{
					$nofollow = 1;
				}

				if( isset($list['qcopd_item_newtab']) && $list['qcopd_item_newtab'] == 1 ) 
				{
					$newtab = 1;
				}

				if( isset($list['qcopd_upvote_count']) && (int)$list['qcopd_upvote_count'] > 0 )
				{
			  	  $votes = (int)$list['qcopd_upvote_count'];
			    }

				$item['item_title'] = trim($list['qcopd_item_title']);
				$item['item_img'] = $img;
				$item['item_subtitle'] = trim($list['qcopd_item_subtitle']);
				$item['item_link'] = $list['qcopd_item_link'];
				$item['item_nofollow'] = $nofollow;
				$item['item_newtab'] = $newtab;
				$item['item_votes'] = $votes;
				$item['item_parent'] = $title;
				$item['item_parent_id'] = $id;

				$item['item_time'] = '0';

				if( isset($list['qcopd_timelaps']) && $list['qcopd_timelaps'] != "" )
				{
					$item['item_time'] = $list['qcopd_timelaps'];
				}

				array_push($arrayOfElements, $item);

			}

			$count++;
		}
		wp_reset_query();
	}
	else
	{
		return __('No list elements was found.', 'qc-sld');
	}
	
	// Sort the multidimensional array
    usort($arrayOfElements, "custom_sort_by_entry_time");

    ob_start();
	//echo '<link rel="stylesheet" type="text/css" href="'.QCOPD_ASSETS_URL.'/css/directory-style.css" />';
    $count = 1;
    $listCount = 30111;
    $numberOfItems = count( $arrayOfElements );

    echo '<ul class="widget-sld-list">';
    
    foreach( $arrayOfElements as $item ){
		
		$mainimg = $item['item_img'];
		$imgurlnew = '';

		if('' != $mainimg && @array_key_exists(0,$mainimg)){
			$imgurlnew = $mainimg[0];
		}else{
			$imgurlnew = $mainimg;
		}
		
    	?>
    	<li id="item-<?php echo $item['item_parent_id'] ."-". $listCount; ?>">
			<a <?php echo (isset($item['item_nofollow']) && $item['item_nofollow'] == 1) ? 'rel="nofollow"' : ''; ?> <?php echo (isset($item['item_newtab']) && $item['item_newtab'] == 1) ? 'target="_blank"' : ''; ?> href="<?php echo $item['item_link']; ?>">

				<?php if( $imgurlnew != "" ) : ?>
				
					<img class="widget-avatar" src="<?php echo $imgurlnew; ?>" alt="">

				<?php else : ?>

					<img class="widget-avatar" src="<?php echo QCOPD_IMG_URL; ?>/list-image-placeholder.png" alt="">

				<?php endif; ?>

				<?php echo $item['item_title']; ?>

				<?php if( $enableUpvoting == 'on' ) : ?>

				<div class="widget-vcount">
				
					<div class="upvote-section">
						
						<span data-post-id="<?php echo $item['item_parent_id']; ?>" data-item-title="<?php echo $item['item_title']; ?>" data-item-link="<?php echo $item['item_link']; ?>" class="upvote-btn upvote-on">
							<span class="opening-bracket">
								(
							</span>
							<i class="fa fa-thumbs-up"></i>
							<span class="upvote-count">
								<?php echo $item['item_votes']; ?>
							</span>
							<span class="closing-bracket">
								)
							</span>
						</span>	
						
					</div>

				</div>

				<?php endif; ?>

			</a>
		</li>
    	<?php

    	if( $numberOfItems > $limit )
    	{
    		if( $limit == $count )
    		{
    			break;
    		} //if $limit == $count

    	} //if $numberOfItems > $limit

    	$count++;
    	$listCount++;

    } //End Foreach

    echo '</ul>';

    $content = ob_get_clean();

    return $content;

} //End of qcopd_get_latest_links_wi

//get latest new style
function qcopd_get_latest_new( $limit = null )
{
	if( $limit == null )
	{
		$limit = 6;
	}

	$enableUpvoting = sld_get_option( 'sld_enable_widget_upvote' );

	$arrayOfElements = array();

	$list_args = array(
		'post_type' => 'sld',
		'orderby' => 'date',
		'order' => 'desc',
		'posts_per_page' => -1,
	);

	$list_query = new WP_Query( $list_args );

	if( $list_query->have_posts() )
	{
		$count = 0;
		
		while ( $list_query->have_posts() ) 
		{
			$list_query->the_post();

			$lists = get_post_meta( get_the_ID(), 'qcopd_list_item01' );
			$lists = sldmodifyupvotes(get_the_ID(), $lists);
			
			$title = get_the_title();
			$id = get_the_ID();

			foreach( $lists as $list )
			{
				
				$img = "";
				$newtab = 0;
				$nofollow = 0;
				$votes = 0;

				$showFavicon = (isset($list['qcopd_use_favicon']) && trim($list['qcopd_use_favicon']) != "") ? $list['qcopd_use_favicon'] : "";
				
				$directImgLink = (isset($list['qcopd_item_img_link']) && trim($list['qcopd_item_img_link']) != "") ? $list['qcopd_item_img_link'] : "";
				
				if( $showFavicon == 1 )
				{
					if( $directImgLink != '' )
					{
						$img = trim($directImgLink);
					}else{
						$img = wp_get_attachment_image_src($list['qcopd_item_img']);
					}
				}else{
					$img = wp_get_attachment_image_src($list['qcopd_item_img']);
				}

				if( isset($list['qcopd_item_nofollow']) && $list['qcopd_item_nofollow'] == 1 ) 
				{
					$nofollow = 1;
				}

				if( isset($list['qcopd_item_newtab']) && $list['qcopd_item_newtab'] == 1 ) 
				{
					$newtab = 1;
				}

				if( isset($list['qcopd_upvote_count']) && (int)$list['qcopd_upvote_count'] > 0 )
				{
			  	  $votes = (int)$list['qcopd_upvote_count'];
			    }

				$item['item_title'] = trim($list['qcopd_item_title']);
				$item['item_img'] = $img;
				$item['item_subtitle'] = trim($list['qcopd_item_subtitle']);
				$item['item_link'] = $list['qcopd_item_link'];
				$item['item_nofollow'] = $nofollow;
				$item['item_newtab'] = $newtab;
				$item['item_votes'] = $votes;
				$item['item_parent'] = $title;
				$item['item_parent_id'] = $id;

				$item['item_time'] = '0';

				if( isset($list['qcopd_timelaps']) && $list['qcopd_timelaps'] != "" )
				{
					$item['item_time'] = $list['qcopd_timelaps'];
				}

				array_push($arrayOfElements, $item);

			}

			$count++;
		}
		wp_reset_query();
	}
	else
	{
		return __('No list elements was found.', 'qc-sld');
	}
	
	// Sort the multidimensional array
    usort($arrayOfElements, "custom_sort_by_entry_time");

    ob_start();
	//echo '<link rel="stylesheet" type="text/css" href="'.QCOPD_ASSETS_URL.'/css/directory-style.css" />';
    $count = 1;
    $listCount = 30111;
    $numberOfItems = count( $arrayOfElements );

    
    
    foreach( $arrayOfElements as $item ){
		
		$mainimg = $item['item_img'];
		$imgurlnew = '';

		if('' != $mainimg && @array_key_exists(0,$mainimg)){
			$imgurlnew = $mainimg[0];
		}else{
			$imgurlnew = $mainimg;
		}
		
    	?>
		
		
		<div class="qc-column-4" id="item-<?php echo $item['item_parent_id'] ."-". $listCount; ?>"><!-- qc-column-4 -->
			<!-- Feature Box 1 -->
			<div class="featured-block ">
				<div class="featured-block-img">
					<a <?php echo (isset($item['item_nofollow']) && $item['item_nofollow'] == 1) ? 'rel="nofollow"' : ''; ?> <?php echo (isset($item['item_newtab']) && $item['item_newtab'] == 1) ? 'target="_blank"' : ''; ?> href="<?php echo $item['item_link']; ?>"> 
						<?php if( $imgurlnew != "" ) : ?>
					
						<img src="<?php echo $imgurlnew; ?>" alt="">

						<?php else : ?>

							<img src="<?php echo QCOPD_IMG_URL; ?>/list-image-placeholder.png" alt="">

						<?php endif; ?>
					</a>
				</div>
				<div class="featured-block-info">
					<h4><a <?php echo (isset($item['item_nofollow']) && $item['item_nofollow'] == 1) ? 'rel="nofollow"' : ''; ?> <?php echo (isset($item['item_newtab']) && $item['item_newtab'] == 1) ? 'target="_blank"' : ''; ?> href="<?php echo $item['item_link']; ?>"><?php echo $item['item_title']; ?> </a></h4>

				</div>
				<?php if( $enableUpvoting == 'on' ) : ?>
				<div class="featured-block-upvote upvote-section">
					<span class="upvote-count count"> <?php echo $item['item_votes']; ?> </span>
					<span data-post-id="<?php echo $item['item_parent_id']; ?>" data-item-title="<?php echo $item['item_title']; ?>" data-item-link="<?php echo $item['item_link']; ?>" class="upvote-btn upvote-on"><i class="fa fa-thumbs-up"></i> </span>
				</div>
				<?php endif; ?>
			</div>
		</div><!--/qc-column-4 -->
		
    	<?php

    	if( $numberOfItems > $limit )
    	{
    		if( $limit == $count )
    		{
    			break;
    		} //if $limit == $count

    	} //if $numberOfItems > $limit

    	$count++;
    	$listCount++;

    } //End Foreach

    

    $content = ob_get_clean();

    return $content;

}



function custom_sort_by_entry_time($a, $b) {
    return ( $a['item_time'] ) < ( $b['item_time'] );
}

function sld_new_expired($id){
	//$results = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE post_id = ".get_the_ID()." AND meta_key = 'qcopd_list_item01' order by `meta_id` ASC");
	$metas = get_post_meta($id, 'qcopd_list_item01');
	$expire = sld_get_option('sld_new_expire_after');
	$expire_date = date('Y-m-d H:i:s',strtotime("-$expire days"));
	
	foreach($metas as $meta){

		
		if(isset($meta['qcopd_entry_time']) && strtotime($meta['qcopd_entry_time']) < strtotime($expire_date)){
			if(isset($meta['qcopd_new'])){
				unset($meta['qcopd_new']);
			}
		}
	}
	delete_post_meta($id, 'qcopd_list_item01');
	foreach($metas as $meta){
		add_post_meta( $id, 'qcopd_list_item01', $meta );
	}
	
	
}


function sldmodifyupvotes($id, $lists){
	global $wpdb;
	$utable = $wpdb->prefix.'sld_ip_table';
	$expire = sld_get_option('sld_upvote_expire_after');
	
	if($expire!='' && (int)$expire>0){
		
		$expire_date = date('Y-m-d H:i:s',strtotime("-$expire days"));
		$newArray = array();
		
		foreach($lists as $list){
			
			$subArray = array();
			
			foreach($list as $k=>$v){
				
				if($k=='qcopd_upvote_count'){
					
					$item_id = $id.'_'.$list['qcopd_timelaps'];
					
					$rowcount = $wpdb->get_var("SELECT COUNT(*) FROM $utable WHERE item_id = '$item_id' and time > '$expire_date'");
					$subArray[$k] = $rowcount;
					
				}else{
					$subArray[$k] = $v;
				}
			}
			$newArray[] = $subArray;
		}
		return $newArray;
		
	}else{
		return $lists;
	}
	
}

function sld_featured_at_top($lists){
	
	$featured = array();
	foreach($lists as $k=>$v){
		if(isset($v['qcopd_featured']) and $v['qcopd_featured']==1){
			unset($lists[$k]);
			$featured[] = $v;
		}
	}
	return array_merge($featured,$lists);
}


function sld2_get_web_page( $url )
{
    $options = array(
        CURLOPT_RETURNTRANSFER => true,     // return web page
        CURLOPT_HEADER         => false,    // don't return headers
        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
        CURLOPT_ENCODING       => "",       // handle all encodings
        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
        CURLOPT_TIMEOUT        => 120,      // timeout on response
        CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
        CURLOPT_SSL_VERIFYPEER => false     // Disabled SSL Cert checks
    );
    $ch      = curl_init( $url );
    curl_setopt_array( $ch, $options );
    $content = curl_exec( $ch );
    $err     = curl_errno( $ch );
    $errmsg  = curl_error( $ch );
    $header  = curl_getinfo( $ch );
    curl_close( $ch );
    $header['errno']   = $err;
    $header['errmsg']  = $errmsg;
    $header['content'] = $content;
    return $content;
}
function sld_show_tag_filter($category=''){
	
	$args = array(
		'numberposts' => -1,
		'post_type'   => 'sld',
	);
	
	if($category!=''){
		$taxArray = array(
			array(
				'taxonomy' => 'sld_cat',
				'field'    => 'slug',
				'terms'    => $category,
			),
		);

		$args = array_merge($args, array( 'tax_query' => $taxArray ));
	}
	
	$listItems = get_posts( $args );
	$tags = array();
	foreach($listItems as $item){
		$configs = get_post_meta( $item->ID, 'qcopd_list_item01' );
		foreach($configs as $config){
			if(isset($config['qcopd_tags']) && $config['qcopd_tags']!=''){				
				foreach(explode(',',$config['qcopd_tags']) as $itm){
					if(!in_array($itm, $tags)){
						array_push($tags, $itm);
					}
				}				
			}
		}
	}
	
	?>
	<div class="sld-tag-filter-area">
		<?php echo (sld_get_option('sld_lan_tags')!=''?sld_get_option('sld_lan_tags'):__('Tags', 'qco-pd')); ?>: 
		<a class="sld_tag_filter" data-tag=""><?php echo (sld_get_option('sld_lan_all')!=''?sld_get_option('sld_lan_all'):__('All', 'qco-pd')); ?></a>
		<?php foreach($tags as $tag): ?>
		<a class="sld_tag_filter" data-tag="<?php echo $tag; ?>"><?php echo $tag; ?></a>
		<?php endforeach; ?>
	
	</div>
	<?php
	
}

function sld_widget_tab_style(){
	wp_enqueue_style('sld-style_widget_tab-css', QCOPD_ASSETS_URL . "/css/style_widget_tab.css" );
?>

<div class="qc_tab_container">
	
    <div class="qc_tab_style clearfix-div">
      <button class="qc_tablinks active" onclick="qcTab(event, 'qc_tab_01')">Latest</button>
      <button class="qc_tablinks" onclick="qcTab(event, 'qc_tab_02')">Popular </button>
      <button class="qc_tablinks" onclick="qcTab(event, 'qc_tab_03')">Random</button>
    </div>
    
    <div id="qc_tab_01" class="qc_tabcontent clearfix-div" style="display:block">
    	<div class="qc-row widget-sld-list">
		
           <?php echo qcopd_get_latest_new(); ?>

        </div>
        <!--qc row-->
    </div>
    
    <div id="qc_tab_02" class="qc_tabcontent clearfix-div" style="display:none">
		<?php echo qcopd_get_popular_new(); ?>
    </div>
    
    <div id="qc_tab_03" class="qc_tabcontent clearfix-div" style="display:none">
    	 <?php echo qcopd_get_random_new(); ?>
    </div>

</div>

<script>
function qcTab(evt, qc_id) {
    var i, qc_tabcontent, qc_tablinks;
    qc_tabcontent = document.getElementsByClassName("qc_tabcontent");
    for (i = 0; i < qc_tabcontent.length; i++) {
        qc_tabcontent[i].style.display = "none";
    }
    qc_tablinks = document.getElementsByClassName("qc_tablinks");
    for (i = 0; i < qc_tablinks.length; i++) {
        qc_tablinks[i].className = qc_tablinks[i].className.replace(" active", "");
    }
    document.getElementById(qc_id).style.display = "block";
    evt.currentTarget.className += " active";
}
</script>
<?php
}


