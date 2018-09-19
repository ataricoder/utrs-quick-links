# utrs-quick-links

qc-simple-link-directory>qc-opd-ajax-stuffs.php
Line 1141

Replace 		$blueprint['imgurl'] = @wp_get_attachment_image_src($attach_id,'thumbnail')[0];
With        $tempvar = @wp_get_attachment_image_src($attach_id,'thumbnail');
            $blueprint['imgurl'] = $tempvar[0];
            
            
       
       
       
qc-simple-link-directory>qc-opd-ajax-stuffs.php>modules>dashboard>sld_entry.php
Line 35

Replace	    $custom_name = strtolower(explode('.',$_FILES['sld_link_image']['name'])[0]);
With        $tempvar = strtolower(explode('.',$_FILES['sld_link_image']['name']));
		        $custom_name = ($tempvar[0]);



