<?php 
/* Template Name: Iskcon Datewise Attendance Template
*/ 


?>
<?php

global $wpdb;

get_header();

include('iskcon_menu_bar.php');

$users_list = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."iskcon_disciple_users where status = 1 and is_delete=1 order by name ");

?>
<style>
.ast-container{
	width:100%!important;
	max-width:100%!important;
}
#filter_table ,#filter_table tr th, #filter_table tr td{
	border:none!important;
}
#filter_div{
	display:none;
}
#attendance_table td{
	font-size:13px;
	padding:10px;
	border-collapse;
}
.present{
	background: #28a745;
	padding: 0 10px;
	color: #fff;
	width: fit-content;
}
.absent{
	background: antiquewhite;
	padding: 0 10px;
	width: fit-content;
}
.time_duration_table{
	margin-top: 5px;
	display: none;
}

.on_off_time_info input[type=checkbox]{
	height: 0;
	width: 0;
	visibility: hidden;
}

.on_off_time_info label {
	cursor: pointer;
	text-indent: -9999px;
	width: 90px;
	height: 45px;
	background: grey;
	display: block;
	border-radius: 60px;
	position: relative;
}

.on_off_time_info label:after {
	content: '';
	position: absolute;
	top: 2px;
	left: 5px;
	width: 40px;
	height: 40px;
	background: #fff;
	border-radius: 50%;
	transition: 0.3s;
}

.on_off_time_info input:checked + label {
	background: #bada55;
}

.on_off_time_info input:checked + label:after {
	left: calc(100% - 5px);
	transform: translateX(-100%);
}

.on_off_time_info label:active:after {
	width: 45px;
}

.time_duration_table{
	background: lavender;
}

</style>
<link rel="stylesheet" href="<?php echo get_theme_file_uri();?>/iskcon_template_parts/assets/css/bootstrap-4.6.1.min.css">
<script src="<?php echo get_theme_file_uri();?>/iskcon_template_parts/assets/js/jquery-3.6.0.slim.min.js"></script>
<script src="<?php echo get_theme_file_uri();?>/iskcon_template_parts/assets/js/popper-1.16.1.min.js"></script>
<script src="<?php echo get_theme_file_uri();?>/iskcon_template_parts/assets/js/bootstrap-4.6.1.bundle.min.js"></script>
  
<div class="col-md-12" style="padding-left:15px;padding-right:15px;padding-top:40px;">
<form method="POST" enctype="multipart/form-data" id="datewise_attendance">
	<div class="col-md-12">
		<h3  style="padding-bottom:20px;">Datewise attendance</h3>		
		<div class="form-group d-flex">
			<label class="col-md-2">User</label>
			<select class="form-control col-md-4" name="user_id" required>
				<option value="">All users</option>				
				<?php if($users_list){
					foreach($users_list as $row){
						echo '<option value="'.$row->id.'">'.$row->name.'</option>';
					}
				}
				?>
				
			</select>
		</div>				
		<div class="form-group d-flex">
			<label class="col-md-2">Date</label>
			<input type="text" name="date" class="form-control col-md-4 datepicker" autocomplete="off">			
		</div> 
		<div class="form-group">
			<input type="hidden" name="action" value="datewise_attendance">
		<a href="javascript:void(0)" name="submit" class="btn btn-primary" value="submit">Submit</a>
	</div>
	</div>
</form>

<div id="filter_div">
	<h3> Attendance List </h3>
	<table class="table" id="filter_table">
		<tr><th>Show/Hide Time Info</th></tr>
		<tr><td>
			<div class="on_off_time_info" style="margin-top:-25px;">
				<input type="checkbox" id="switch" name="show_hide_time_info" /><label for="switch">Toggle</label>
			</div>
		</td>
		</tr>
	</table>
</div>
<div id="ajax_content"></div>

</div>

<link rel="stylesheet" href="<?php echo get_theme_file_uri();?>/iskcon_template_parts/assets/css/jquery-ui-1.13.2.css">
<script src="<?php echo get_theme_file_uri();?>/iskcon_template_parts/assets/js/jquery-3.6.0.js"></script>
<script src="<?php echo get_theme_file_uri();?>/iskcon_template_parts/assets/js/jquery-ui-1.13.2.js"></script>

<script type="text/javascript">
    $('#clear').click(function(){
		$('input[type="text"]').val('');
		$('input[name="filter"]').trigger('click');
	});
	
	$('input[name="filter"]').click(function(){//alert("gggg");
		$.ajax({
        url: "<?php echo site_url()?>/wp-content/plugins/attendance_manager/ajax.php",
        data: {
            first_name: $('input[name="first_name"]').val(),
            last_name: $('input[name="last_name"]').val(),
            email: $('input[name="email"]').val(),
            duration: $('input[name="duration"]').val(),
            time_joined: $('input[name="time_joined"]').val(),
            time_exited: $('input[name="time_exited"]').val(),
            date: $('input[name="date"]').val(),
			action:'attendance_filter'
			
        },
        type: 'POST',
		success:function(result){
			//alert(result);
			$('#attendance_tbody').html(result);
		}
    });
		
	});
	
</script>
<script type="text/javascript">    
	
	$('a[name="submit"]').click(function(){ //alert("ggg");
		
		var date = $('input[name="date"]').val();
		
		if(date=='' ){			
			alert("The field Date is required");
			return false;
		}

		var form = $('#datewise_attendance')[0]; // You need to use standard javascript object here
		var formData = new FormData(form);
		
		$.ajax({
        url: "<?php echo get_theme_file_uri();?>/iskcon_template_parts/ajax_temp.php",
        data: formData,
        type: 'POST',
        contentType: false,
        processData: false,
		success:function(result){
			
			$('#filter_div').show();
			$('input[name="show_hide_time_info"]').prop('checked',false);
			$('#ajax_content').html(result);
		}
    });
		
	});
	//$(document).ready(function(){
		$('.datepicker').datepicker({ 
			dateFormat: 'dd-mm-yy',
			maxDate : "0" ,
			endDate : "0" 
		});
	//});

	$('input[name="show_hide_time_info"]').click(function(){
		$('.time_duration_table').toggle();
	})
	$(document).ready(function(){
		$('input[name="show_hide_time_info"]').prop('checked',false);
	});
</script>
<?php get_footer();?>