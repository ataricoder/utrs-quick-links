jQuery(document).ready(function($){
	
	$('#sld_reset_upvote').on('click', function(e){
		e.preventDefault();
		$( "input[name*='qcopd_upvote_count']" ).each(function(){
			$(this).val(0);
		})
		$('#sld_show_msg').html('Upvote has been reset successfully. please click Update button.');
	})
	
	$('#sld_reset_all_upvotes').on('click', function(e){		
		e.preventDefault();
		$.post(ajaxurl, {
			action: 'show_qcsld_upvote_reset', 
			},
			function(data) {

				$('#wpwrap').append(data);

			}
		);
		
	})
	$(document).on( 'click', '.modal-content .close', function(){
        $(this).parent().parent().remove();
    })
	$(document).on( 'change', '#sld_list', function(){
        var currentVal = $(this).val();
		if(currentVal!=='all'){
			$.post(ajaxurl, {
				action: 'show_qcsld_list_items',
				listid: currentVal
				},
				function(data) {

					$('.sld_reset_child_item').append(data);
					
				}
			);
		}else{
			$('.sld_reset_child_item').html('');
		}
    })
	
	$(document).on('click','#sld_reset_votes', function(e){
		e.preventDefault();
		
		var list = $('#sld_list').val();
		var item = $('#sld_list_item').val();
		if(typeof(item)=='undefined'){
			item = '';
		}
		$.post(ajaxurl, {
			action: 'qcopd_reset_all_upvotes',
			list: list,
			item: item
			},
			function(data) {

				$('.sld_reset_child_item').append(data);
				
			}
		);
		
		
	})
	
	$('#tab_frontend').on('click',function(e){
		e.preventDefault();
		$('#sld_page_check').html('<p class="sld_page_loading">Loading...</p>');
		var datarr = ['sld_login', 'sld_registration', 'sld_dashboard', 'sld_restore'];
		for(var i=0;i<4;i++){
			
			$.post(ajaxurl, {
				action: 'qcopd_search_sld_page', 
				shortcode: datarr[i],

				},
				
				function(data) {
					$('#sld_page_check .sld_page_loading').hide();
					
					if(data!=='' && !data.match(/not/g)){
						$('#sld_page_check').append('<p style="color:green">'+data+'</p>');
					}else{
						$('#sld_page_check').append('<p style="color:red">'+data+'</p>');
					}
					
					
					
				});
			
			
		}
		//
	})

	$('#sld_flash_button').on('click',function(e){
		e.preventDefault();
		$('#sld_flash_msg').html('<p class="sld_page_loading">Loading...</p>');

			$.post(ajaxurl, {
				action: 'qcopd_flash_rewrite_rules', 
				},
				
				function(data) {

					$('#sld_flash_msg').html('<p class="sld_page_loading" style="color:green;">Rewrite has been Flushed Successfully!</p>');

				}
			);
	})
	
	$(document).on('click', '.sld_collapse', function(e){
		e.preventDefault();
		
		var elem = $(this);
		elem.closest('.field-item').addClass('sld_section_collapse');
		elem.removeClass('sld_collapse').addClass('sld_expend');
		elem.text('Expand');
		
	})
	$(document).on('click', '.sld_expend', function(e){
		e.preventDefault();
		
		var elem = $(this);
		elem.closest('.field-item').removeClass('sld_section_collapse');
		elem.removeClass('sld_expend').addClass('sld_collapse');
		elem.text('Collapse');
		
	})
	
	$(document).on('click','.sld_ctm_btn1', function(e){
		e.preventDefault();
		var elem = $(this);
		$( ".sld_collapse" ).each(function( index ) {
		  $( this ).click();
		});
		
		elem.removeClass('sld_ctm_btn1').addClass('sld_ctm_btn1_e');
		elem.text('Expand All');
		
		
	})
	$(document).on('click','.sld_ctm_btn1_e', function(e){
		e.preventDefault();
		var elem = $(this);
		$( ".sld_expend" ).each(function( index ) {
		  $( this ).click();
		});
		
		elem.removeClass('sld_ctm_btn1_e').addClass('sld_ctm_btn1');
		elem.text('Collapse All');
		
		
	})
	$(document).on('click','.sld_ctm_btn2', function(e){
		e.preventDefault();
		 $('html, body').animate({
			scrollTop: $("#text-ad-block").offset().top - 100
		}, 2000);
	})
	
		$(document).on('click', '#qcopd_tags input', function(e){
		var elem = $(this);
		var elemid = this.id;
		$.post(ajaxurl, {
			action: 'qcopd_tag_pd_page', 
			
			},
			
		function(data) {
			
			$('#wpwrap').append(data);
			$('#sldtagvalue').val(elem.val());
			$('#sld-tags').attr('data', elemid);
			
			//console.log($(data).find('.fa-field-modal-title').text());

			$('#sld-tags').tagInput();
			$('.labelinput').focus();
			$.post(ajaxurl, {
			action: 'qcopd_search_pd_tags', 
			},
			
			function(data) {
				console.log(data);
				
				$('.labelinput').autocomplete({
					  source: data.split(','),
					  
				});					
				
			});
				

			
		});
		
	})
	
	$(document).on( "autocompleteselect",'.labelinput', function( event, ui ) {
		//event.preventDefault();
		if( event.keyCode == 13 ){
			event.preventDefault();
		}
	});
	
	$(document).on('click','.closelabel',function(e){
		e.preventDefault();
	})
	
	$( document ).on( 'click','.fa-field-modal-close', function() {
		
		$('#fa-field-modal-tag').remove();

	});
	$(document).on('click','#sld_tag_select', function(){
		$('#'+$('#sld-tags').attr('data')).val($('#sldtagvalue').val());
		$('#fa-field-modal-tag').remove();
	})
	
	$(document).on('click', "input[name*='[qcopd_image_from_link]']", function(e){
		var objc = $(this);
		
		
		if($(this).is(':checked')) {
			if(objc.closest('.cmb_metabox').find('#qcopd_item_link input').val()!='' && objc.closest('.cmb_metabox').find('#qcopd_item_img input').val()==''){
				html = "<div id='sld_ajax_preloader'><div class='sld_ajax_loader'></div></div>";
				$('#wpwrap').append(html);
				$.post(ajaxurl, {
				action: 'qcopd_img_download', 
				url: objc.closest('.cmb_metabox').find('#qcopd_item_link input').val(),

				},
				function(data) {
					data = JSON.parse(data);
					console.log(data);
					$('#sld_ajax_preloader').remove();

					
					if(data.imgurl.match(/.jpg/g) || data.imgurl!==null){
						objc.closest('.cmb_metabox').find('#qcopd_item_img input').val(data.attachmentid);
						objc.closest('.cmb_metabox').find('#qcopd_item_img .cmb-file-holder').show();
						objc.closest('.cmb_metabox').find('#qcopd_item_img .cmb-remove-file').removeClass('hidden').show();
						objc.closest('.cmb_metabox').find('#qcopd_item_img .cmb-file-upload').addClass('hidden').hide();
						objc.closest('.cmb_metabox').find('#qcopd_item_img .cmb-file-holder').append('<img src="'+data.imgurl+'" width="150" height="150" />');
					}else{
						alert('Could not generate image. Please check URL and try again.');
					}
					
					
				});
				
			}
			
			//alert(objc.closest('.cmb_metabox').find('#qcpd_item_link input').val() + objc.closest('.cmb_metabox').find('#qcpd_item_img input').val());
		}
	})
	
	
	$(document).on('click', "input[name*='[qcopd_generate_title]']", function(e){
		var objc = $(this);
		if($(this).is(':checked')) {
			
			if(objc.closest('.cmb_metabox').find('#qcopd_item_link input').val()!=''){
				html = "<div id='sld_ajax_preloader'><div class='sld_ajax_loader'></div></div>";
				$('#wpwrap').append(html);
				$.post(ajaxurl, {
				action: 'qcopd_generate_text', 
				url: objc.closest('.cmb_metabox').find('#qcopd_item_link input').val(),
				},
				function(data) {
					
					$('#sld_ajax_preloader').remove();
					data = JSON.parse(data);
					objc.closest('.cmb_metabox').find('#qcopd_item_title input').val(data.title)
					objc.closest('.cmb_metabox').find('#qcopd_item_subtitle input').val(data.description)
					objc.prop('checked', false);
				});
				
			}else{
				alert('Item link field cannot left empty!');
			}
			
		}
		
	})
	
	$('#sld_shortcode_generator_meta').on('click', function(e){
		 $('#sld_shortcode_generator_meta').prop('disabled', true);
		$.post(
			ajaxurl,
			{
				action : 'show_qcsld_shortcodes'
				
			},
			function(data){
				 $('#sld_shortcode_generator_meta').prop('disabled', false);
				$('#wpwrap').append(data);
			}
		)
	})
	
	
	
});