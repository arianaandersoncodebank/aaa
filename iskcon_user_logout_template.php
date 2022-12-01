<?php 
/* Template Name: Iskcon User Logout Template
*/ 

?>
<?php

global $wpdb;

get_header();

//include('iskcon_menu_bar.php');

session_start(); print_r($_SESSION);
unset($_SESSION['seva_user_email']);
unset($_SESSION['seva_user_password']);
//echo 1;	
wp_redirect( site_url().'/seva_user_login' );
//header('Location: seva_user_login');
// or die();
//exit();
echo site_url().'/seva_user_login';
?>
khjkhjkhjkhjkghjkhjkhjkhjk
lk;jlk;kl;kl
llk;l;kl

<script>
	$(document).ready(function(){alert("ggggg");
		window.location="seva-user-login";

	});
$(document).ready(function(){
	
	$.ajax({
        url: "<?php echo get_theme_file_uri();?>/iskcon_template_parts/ajax_temp.php",
        data: {action:"seva_user_logout"},
        type: 'POST',
        //contentType: false,
        //processData: false,
		success:function(result){			
			if(result==1){
				window.location="seva-user-login";
			}
		}
	});


})
</script>