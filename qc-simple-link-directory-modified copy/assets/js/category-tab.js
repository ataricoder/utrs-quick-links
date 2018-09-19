jQuery(document).ready(function($){
	
	
	$('.qcld_sld_tablinks').on('click', function(evt){
		
		var qcld_sld_event = $(this).attr('data-cterm')
		var i, qcld_sld_tabcontent, qcld_sld_tablinks;
		qcld_sld_tabcontent = document.getElementsByClassName("qcld_sld_tabcontent");
		for (i = 0; i < qcld_sld_tabcontent.length; i++) {
			qcld_sld_tabcontent[i].style.display = "none";
		}
		qcld_sld_tablinks = document.getElementsByClassName("qcld_sld_tablinks");
		for (i = 0; i < qcld_sld_tablinks.length; i++) {
			qcld_sld_tablinks[i].className = qcld_sld_tablinks[i].className.replace(" qcld_sld_active", "");
		}
		document.getElementById(qcld_sld_event).style.display = "block";
		evt.currentTarget.className += " qcld_sld_active";
		
		jQuery('#'+qcld_sld_event +' .qcopd-single-list').each(function(e){
			
			if($(this).find('.sldp-holder').length > 0 && $(this).find('.sldp-holder > .jp-current').length==0){

				var containerId = $(this).find('.sldp-holder').attr('id');
				var containerList = $(this).find('ul').attr('id');
				console.log(containerList);
				$("#"+$(this).find('.sldp-holder').attr('id')).jPages({
					containerID : containerList,
					perPage : per_page,
				});
				
			}
			
		})
		//$('#'+qcld_sld_event).find(".filter-btn[data-filter='all']").click();
		jQuery('.qc-grid').packery({
			itemSelector: '.qc-grid-item',
			gutter: 10
		});
		/*jQuery( '.filter-btn[data-filter="all"]' ).trigger( "click" );*/
		
	})
	
})