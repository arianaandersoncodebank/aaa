<?php 
/* Template Name: Iskcon Monthwise Attendance Template
*/ 


// Page code here...

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
#attendance_table td{
	font-size:13px;
	padding:10px;
	border-collapse;
}
.dayname{
	transform: rotate(-70deg);
	width: 21px;
	margin-top: 50px;
	padding-bottom: 10px;
	font-size: 14px;
}
.heading_row1{
	background: lightblue;
}
.heading_row2{
	background: lightskyblue;
}
.present{
	padding-top: 0px!important;
	padding-bottom: 0px!important;
}
.monthwise_div{
	max-height:750px;
	overflow:auto;
}
.heading_row1,.heading_row2{
	position: sticky;
	top:0;
}
.heading_row1{
	min-height:100px;
	height:100px;
	top:0;
}
.heading_row2{
	min-height:50px;
	height:50px;
	top:100px;
}
/*************/

 th:nth-child(1),
 td:nth-child(1) {
    position: sticky;
    left: 0;
    width: 50px;
    min-width: 50px;
}

 th:nth-child(2),
 td:nth-child(2) {
    position: sticky;
    /* 1st cell left/right padding + 1st cell width + 1st cell left/right border width */
    /* 0 + 5 + 150 + 5 + 1 */
    left: 51px;
    width: 200px;
    min-width: 200px;
}

 td:nth-child(1),
 td:nth-child(2) {
    background: beige;
}

 td:nth-child(1),
 td:nth-child(2) {
    z-index: 9999;
}
</style>

<link rel="stylesheet" href="<?php echo get_theme_file_uri();?>/iskcon_template_parts/assets/css/bootstrap-4.6.1.min.css">
<script src="<?php echo get_theme_file_uri();?>/iskcon_template_parts/assets/js/jquery-3.6.0.slim.min.js"></script>
<script src="<?php echo get_theme_file_uri();?>/iskcon_template_parts/assets/js/popper-1.16.1.min.js"></script>
<script src="<?php echo get_theme_file_uri();?>/iskcon_template_parts/assets/js/bootstrap-4.6.1.bundle.min.js"></script>
  
<div class="col-md-12" style="padding-left:15px;padding-right:15px;padding-top:100px;padding-bottom:100px;">
<form method="POST" enctype="multipart/form-data" id="monthwise_attendance">
	<div class="col-md-12">
		<h3  style="padding-bottom:20px;">Monthwise Attendance</h3>
		<div class="form-group d-flex">
			<label class="col-md-2">Devotees</label>
			<select class="form-control col-md-4" name="user_id" required>
				<option value="">All Devotees</option>
				
				<?php if($users_list){
					foreach($users_list as $row){
						echo '<option value="'.$row->id.'">'.$row->name.'</option>';
					}
				}
				?>
				
			</select>
		</div>	
		 <div class="form-group d-flex">
			<label class="col-md-2">Class</label>
			<select class="form-control col-md-4" name="class_id" required>
				<option value="">Select</option>
				<option value="1">Japa Talk</option>
				<option value="2">9 AM Class</option>
				<option value="3">2.45 PM Class</option>
			</select>
		</div> 	
		
		<div class="form-group d-flex">
			<label class="col-md-2">Month</label>
			<select class="form-control col-md-4" name="month" required>
				<option value="">Select</option>
				<option value="01">January</option>
				<option value="02">February</option>
				<option value="03">March</option>
				<option value="04">April</option>
				<option value="05">May</option>
				<option value="06">June</option>
				<option value="07">July</option>
				<option value="08">August</option>
				<option value="09">September</option>
				<option value="10">October</option>
				<option value="11">November</option>
				<option value="12">December</option>
				
			</select>
		</div>		
		<div class="form-group d-flex">
			<label class="col-md-2">Year</label>
			<input type="text" name="year"  class="form-control col-md-4" value="2022" required autocomplete="off" style="pointer-events:none;">
		</div> 
		<div class="form-group">
			 <input type="hidden" name="action" value="monthwise_attendance"> 

		<a href="javascript:void(0)" name="submit" class="btn btn-primary" value="submit">Submit</a>
	</div>
	</div>
</form>



<div id="ajax_content">

</div>
</div>
<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>-->
<link rel="stylesheet" href="<?php echo get_theme_file_uri();?>/iskcon_template_parts/assets/css/jquery-ui-1.13.2.css">
<link rel="stylesheet" href="/resources/demos/style.css">
<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="<?php echo get_theme_file_uri();?>/iskcon_template_parts/assets/js/jquery-ui-1.13.2.js"></script>

<script type="text/javascript">
   // var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
</script>
<script type="text/javascript">
   /* $('#clear').click(function(){
		$('input[type="text"]').val('');
		$('input[name="filter"]').trigger('click');
	});
	
	$('input[name="filter"]').click(function(){
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
		
	});*/
	$(document).ready(function(){
		$('.datepicker').datepicker({ dateFormat: 'dd-mm-yy' });
		//$('.timepicker').timepicker();
	});
</script>
<script type="text/javascript">    
	
	$('a[name="submit"]').click(function(){ //alert("ggg");
		//var class_id = $('select[name="class_id"]').val();
		var month = $('select[name="month"]').val();
		var year = $('input[name="year"]').val();
		var class_id = $('select[name="class_id"]').val();

		//if(class_id=='' || month=='' || year==''){
		if(class_id=='' || year=='' ){
			//if(class_id=='')alert("The field Class is required");
			//else if(month=='')alert("The field Month is required");
			if(class_id=='')alert("The field Class is required");
			else if(year=='')alert("The field Year is required");
			return false;
		}

		var form = $('#monthwise_attendance')[0]; // You need to use standard javascript object here
		var formData = new FormData(form);
		
		$.ajax({
        url: "<?php echo get_theme_file_uri();?>/iskcon_template_parts/ajax_temp.php",
        data: formData,
        type: 'POST',
        contentType: false,
        processData: false,
		success:function(result){ 			
			$('#ajax_content').html(result);
		}
    });
		
	});
	$(document).ready(function(){
		$('.datepicker').datepicker({ dateFormat: 'dd-mm-yy' });
	});
</script>
<?php get_footer();?>