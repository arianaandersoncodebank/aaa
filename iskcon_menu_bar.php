
<?php



session_start();
if(isset($_SESSION['seva_user_email']) && $_SESSION['seva_user_email']!='')
	$email = $_SESSION['seva_user_email'];else $email ='';
if(isset($_SESSION['seva_user_password']) && $_SESSION['seva_user_password']!='')
	$password = $_SESSION['seva_user_password'];else $password ='';


if($email!='' && $password!=''){
	$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."iskcon_seva_users where email ='".$email."' and password = '".md5($password)."' and status = 1 and is_delete=1");
//echo $wpdb->last_query;	echo 'hhhh';print_r($result);die;
	if($result)
		header("Location:'attendance-summary'");
	else if($result)
		header("Location:'seva-user-login'");

}else header("Location:'seva-user-login'");


$users_list = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."iskcon_seva_users where status = 1 and is_delete=1 order by name ");

?>


<?php $slug = basename(get_permalink());?>
<style>
/*#iskcon_navbar li.nav-item.active{	
	background:var(--ast-global-color-0);
	border-radius:20px;
}
#iskcon_navbar li.nav-item a{
	color:#fff!important;
}
#iskcon_navbar li.nav-item{
	padding-left: 10px;
	padding-right: 10px;
}*/
.menu-link{
	color : #FF6200;
	font-weight:bold;
}
.ast-container{
	width:100%!important;
	max-width:100%!important;
}
#attendance_table td{
	font-size:13px;
	padding:10px;
	border-collapse;
}
</style>
<!-- <div id="iskcon_navbar" style="width:950px;text-align:center;margin:auto;padding-top:60px;padding-bottom:30px;">
<nav class="navbar navbar-expand-sm bg-light navbar-light" style="background-color:#000!important;border-radius:10px;padding-top:10px;padding-bottom:10px;">
<ul class="navbar-nav">
	<li class="nav-item <?php if($slug == 'attendance-summary') echo 'active';?> ">
		<a class="nav-link" href="<?=site_url();?>/attendance-summary">Attendance Summary </a>
	</li>
	<li class="nav-item <?php if($slug == 'monthwise-attendance') echo 'active';?>">
		<a class="nav-link" href="<?=site_url();?>/monthwise-attendance">Monthwise Attendance</a>
	</li>
	<li class="nav-item <?php if($slug == 'datewise-attendance') echo 'active';?>">
		<a class="nav-link" href="<?=site_url();?>/datewise-attendance">Datewise Attendance</a>
	</li>
	<li class="nav-item <?php if($slug == 'users-list') echo 'active';?>">
		<a class="nav-link" href="<?=site_url();?>/users-list">Users List</a>
	</li>
	<li class="nav-item <?php if($slug == 'logout') echo 'active';?>">
		<a class="nav-link" href="javascript:void(0)" onclick="seva_user_logout()">Logout</a>
	</li>	
</ul>
</nav>
</div> -->

<script>
function seva_user_logout(){
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
}

	</script>
