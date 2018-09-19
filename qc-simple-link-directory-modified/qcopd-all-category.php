<?php
/**
 * Created by QuantunCloud.
 * Date: 9/14/2017
 * Time: 3:16 PM
 */

defined('ABSPATH') or die("No direct script access!");

add_shortcode('sld-tab', 'qcopd_directory_all_category');
function qcopd_directory_all_category($atts = array()){


	//Defaults & Set Parameters
	extract( shortcode_atts(
		array(
			'orderby' => 'menu_order',
			'order' => 'ASC',
			'mode' => 'all',
			'list_id' => '',
			'column' => '1',
			'style' => 'simple',
			'list_img' => 'true',
			'search' => 'true',
			'category' => "",
			'upvote' => "on",
			'item_count' => "on",
			'top_area' => "on",
			'item_orderby' => "",
			'item_order' => "",
			'mask_url' => "off",
			'tooltip' => 'false',
			'paginate_items' => 'false',
			'per_page' => 5,
			'category_orderby'=>'date',
			'category_order'=>'ASC',
			'category_remove'=>'',
			'list_title_font_size' => '' ,
			'list_title_line_height' => '' ,

			'title_font_size' => '',
			'subtitle_font_size' => '',
			'title_line_height' => '',
			'subtitle_line_height' => '',
			'filter_area' => 'normal',
			'topspacing' => 0,
			'enable_tag_filter'=>'false',
			'main_click'=> ''

		), $atts
	));


    //Category remove array
    if($category_remove != ''){
	    $category_remove = explode(',',$category_remove);
	    $categoryremove = $category_remove;
    }else{
	    $categoryremove = array();
    }

	$cterms = get_terms( 'sld_cat', array(
		'hide_empty' => true,
		'orderby' => $category_orderby,
		'order' => $category_order
	) );



	ob_start();

    if(!empty($cterms)){
?>
		
        <div class="qcld_sld_tab_main"><!--start qcld_sld_tab_main-->
            <div class="qcld_sld_tab">
                <?php
                $ci = 0;
                foreach ($cterms as $cterm){
                    if(!in_array($cterm->term_id,$categoryremove)){
						$image_id = get_term_meta ( $cterm -> term_id, 'category-image-id', true );
                        ?>
                            <button style="<?php echo (!$image_id?'padding-left:22px!important':''); ?>" class="qcld_sld_tablinks <?php echo ($ci==0?'qcld_sld_active':''); ?>" data-cterm="<?php echo $cterm->slug; ?>" ><?php echo $cterm->name; ?>
							<span class="cat_img_top">
							<?php if($image_id) echo wp_get_attachment_image ( $image_id, 'thumbnail' ); ?></span>
							</button>
                        <?php
                        $ci++;
                    }
                }
                ?>
            </div>

	        <?php
	        $ci = 0;
	        foreach ($cterms as $cterm){
		        if(!in_array($cterm->term_id,$categoryremove)){
					//if($ci==1)continue;
			        ?>

                    <div id="<?php echo $cterm->slug; ?>" class="qcld_sld_tabcontent" <?php echo ($ci==0?'style="display:block"':''); ?>>
				        <?php
                            $shortcodeText = '[qcopd-directory category="'.$cterm->slug.'" search="'.$search.'" upvote="'.$upvote.'" item_count="'.$item_count.'" top_area="'.$top_area.'" mask_url="'.$mask_url.'" tooltip="'.$tooltip.'" paginate_items="'.$paginate_items.'" per_page="'.$per_page.'" style="'.$style.'" column="'.$column.'" orderby="'.$orderby.'" order="'.$order.'" list_title_font_size="'.$list_title_font_size.'" item_orderby="'.$item_orderby.'" list_title_line_height="'.$list_title_line_height.'" title_font_size="'.$title_font_size.'" subtitle_font_size="'.$subtitle_font_size.'" title_line_height="'.$title_line_height.'" subtitle_line_height="'.$subtitle_line_height.'" filter_area="'.$filter_area.'" topspacing="'.$topspacing.'" enable_tag_filter="'.$enable_tag_filter.'" main_click="'.$main_click.'" cattabid="'.$ci.'"]';
				        echo do_shortcode($shortcodeText);
				        ?>
                    </div>

			        <?php
			        $ci++;
		        }
	        }
	        ?>



        </div><!--end qcld_sld_tab_main-->

		
		<?php if(sld_get_option('sld_enable_filtering_left')=='on'): ?>
			<script>
				jQuery(document).ready(function ($) {

					var fullwidth = window.innerWidth;
					if (fullwidth < 479) {
						$('.filter-carousel').slick({


							infinite: false,
							speed: 500,
							slidesToShow: 1,


						});
					} else {
						$('.filter-carousel').slick({

							dots: false,
							infinite: false,
							speed: 500,
							slidesToShow: 1,
							centerMode: false,
							variableWidth: true,
							slidesToScroll: 3,

						});
					}

				});
				
			</script>
		<?php endif; ?>
			<script>
				var per_page = <?php echo $per_page; ?>;
			</script>
		
<?php
    }

	$content = ob_get_clean();
	return $content;

}
add_shortcode('sld-multipage-category', 'qcopd_multipage_all_category');
function qcopd_multipage_all_category($atts = array()){
	ob_start();
	sld_show_category();
	$content = ob_get_clean();
	return $content;
}
add_shortcode('qcopd-directory-random', 'qcopd_random_directory');
function qcopd_random_directory($atts = array()){
	ob_start();
	echo '<div class="sld_widget_style"><h2>Random Links</h2>'.qcopd_get_random_links_wi(5).'</div>';
	$content = ob_get_clean();
	return $content;
}
add_shortcode('qcopd-directory-latest', 'qcopd_latest_directory');
function qcopd_latest_directory($atts = array()){
	ob_start();
	echo '<div class="sld_widget_style"><h2>Latest Links</h2>'.qcopd_get_latest_links_wi(5).'</div>';
	$content = ob_get_clean();
	return $content;
}
add_shortcode('qcopd-directory-widget-tab-style', 'qcopd_widget_all_directory');
function qcopd_widget_all_directory($atts = array()){
	ob_start();
	sld_widget_tab_style();
	$content = ob_get_clean();
	return $content;
}



add_shortcode('qcopd-directory-popular', 'qcopd_popular_directory');
function qcopd_popular_directory($atts = array()){
	ob_start();
	echo '<div class="sld_widget_style"><h2>Popular Links</h2>'.qcopd_get_most_popular_links_wi(5).'</div>';
	$content = ob_get_clean();
	return $content;
}