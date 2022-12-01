<?php 
/* Template Name: Iskcon Users List Template
*/ 

?>
<?php

global $wpdb;

get_header();

include('iskcon_menu_bar.php');

$users_list = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."iskcon_disciple_users where status = 1 and is_delete=1 order by name ");
//session_start();
//$dd = array_filter($_SESSION['ddddd']);
//echo '<pre>';print_r($dd);
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
label.error{
	color:red;
}
</style>
<style>
.user_status_toggle .switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.user_status_toggle .switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.user_status_toggle .slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.user_status_toggle .slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

.user_status_toggle input:checked + .slider {
  background-color: #2196F3;
}

.user_status_toggle input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

.user_status_toggle input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.user_status_toggle .slider.round {
  border-radius: 34px;
}

.user_status_toggle .slider.round:before {
  border-radius: 50%;
}
</style>
<link rel="stylesheet" href="<?php echo get_theme_file_uri();?>/iskcon_template_parts/assets/css/bootstrap-4.6.1.min.css">
<script src="<?php echo get_theme_file_uri();?>/iskcon_template_parts/assets/js/jquery-3.6.0.slim.min.js"></script>
<script src="<?php echo get_theme_file_uri();?>/iskcon_template_parts/assets/js/popper-1.16.1.min.js"></script>
<script src="<?php echo get_theme_file_uri();?>/iskcon_template_parts/assets/js/bootstrap-4.6.1.bundle.min.js"></script>
  
<div class="col-md-12" style="padding-left:15px;padding-right:15px;padding-top:100px;padding-bottom:10px;">
<h3> Users List </h3>

<form method="POST" enctype="multipart/form-data" id="dob_form">
	<!--<button type="submit" name="submit" id="submit_btn">Submit</button>
	 <input type="hidden" name="action" value="save_dob"> -->
<div class="table-responsive" style="width:99%;">
<table class="table table-bordered" border="1" id="attendance_table" style="width:100%;">
<thead>
	<tr>
		<th width="40">S.No</th>
		<th>Real Name</th>
		<th>Login Name</th>
		<th>Dob</th>
		<th>Mobile</th>
		<th>Email</th>		
		<th>City</th>
		<th>Joining Date</th>
		<th>Status</th> 	 
		<th>Action</th> 	 
	</tr>
</thead>
	<tbody></tbody>
</table>
</div>
</form>
</div>
<link rel="stylesheet" href="<?php echo get_theme_file_uri();?>/iskcon_template_parts/assets/css/jquery-ui-1.13.2.css">
<script src="<?php echo get_theme_file_uri();?>/iskcon_template_parts/assets/js/jquery-3.6.0.js"></script>

<script src="<?php echo get_theme_file_uri();?>/iskcon_template_parts/assets/js/jquery-ui-1.13.2.js"></script>

<link rel="stylesheet" href="<?php echo get_theme_file_uri();?>/iskcon_template_parts/assets/css/jquery.dataTables-1.13.1.min.css">
<link rel="stylesheet" href="<?php echo get_theme_file_uri();?>/iskcon_template_parts/assets/css/buttons.dataTables-2.3.2.min.css">

<link rel="stylesheet" href="<?php echo get_theme_file_uri();?>/iskcon_template_parts/assets/css/sweetalert2.min.css">

<script src="<?php echo get_theme_file_uri();?>/iskcon_template_parts/assets/js/jquery.dataTables-1.13.1.min.js"></script> 


<script type="text/javascript">    
	var table;
// Wait for the DOM to be ready
$(function() {  
  $("#edit_user_form").validate({    
    rules: {      
      real_name: "required",
      /*lastname: "required",
      email: {
        required: true,        
        email: true
      },
      password: {
        required: true,
        minlength: 5
      }*/
    },
    // Specify validation error messages
    messages: {
      real_name: "Please enter your firstname",
      /*lastname: "Please enter your lastname",
      password: {
        required: "Please provide a password",
        minlength: "Your password must be at least 5 characters long"
      },
      email: "Please enter a valid email address"*/
    },
   
    submitHandler: function(form) {
    	event.preventDefault();//alert("dfsdfsdfsdf");
       //form.submit();
    	var form = $('#edit_user_form')[0]; // You need to use standard javascript object here
		var formData = new FormData(form);
		
		$.ajax({
	        url: "<?php echo site_url()?>/wp-content/plugins/attendance_manager/ajax.php",
	        data: formData,
	        type: 'POST',
	        contentType: false,
	        processData: false,
			success:function(result){
				
			Swal.fire({
				  title: 'Success',
				  text: "Devotee details updated successfully",
				  icon: 'success',
				  
				});
			//$('#editUserModal').modal('hide');	
			$('#editUserModal').find('.close').trigger('click');
			attendance_table();	
				//$('#edit_user_btn').modal('hide');				
			}
    	});
		/************/
    }
  });
});


function edit_user(id){
		$('#edit_user_form input[name="user_id"]').val(id);
	$.ajax({
        url: "<?php echo site_url()?>/wp-content/plugins/attendance_manager/ajax.php",
        data: {action:'get_user_detail',id:id},
        type: 'POST',
        dataType:'json',
       //contentType: false,
        //processData: false,
		success:function(result){ //$('#editUserModal').modal('show');
			//alert(result.name);
			$('#edit_user_form input[name="real_name"]').val(result.name);
			$('#edit_user_form input[name="mobile_no"]').val(result.mobile_no);
			$('#edit_user_form input[name="email"]').val(result.email);
			$('#edit_user_form input[name="city"]').val(result.city);
			$('#edit_user_btn').trigger('click');
			//$('#editUserModal').modal('show');
			//$('input[name="file"],input[name="date"],select[name="class_id"]').val('');
			//$('#aaa').html(result);
			//$('#attendance_tbody').html(result);
			//$('#attendance_table').html(result);
		}
    });
}   


$(document).ready(function(){
	attendance_table();

});
function attendance_table(){
	//table.destroy();
    table = $('#attendance_table').DataTable({
        "processing": true,
        "serverSide": true,
        "pageLength":50,
        "start" :0,
        "stateSave":true,
        "destroy":true,
        ajax: {
            url: '<?php echo get_theme_file_uri();?>/iskcon_template_parts/ajax_temp.php',
            type: 'POST',
            data:{action:'users_list'}
        },
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
        //"ajax": "<?php echo site_url()?>/wp-content/plugins/attendance_manager/ajax.php"
    });
}



$(document).on('click','.user_status_toggle .user_status',function(){
	var status = $(this).prop('checked');
	if($(this).prop('checked')==true)var status = 1;
	else var status =0;
	var user_id = $(this).attr('user_id');

	Swal.fire({
			title: 'Are you sure?',
			text: "You want to change user status",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes'
		}).then((result) => {
		if (result.isConfirmed) {
			$.ajax({
		        url: "<?php echo site_url()?>/wp-content/plugins/attendance_manager/ajax.php",
		        //data: formData,
		        data:{user_id:user_id,status:status,action:"change_user_status"},
		        type: 'POST',
		       // contentType: false,
		        //processData: false,
				success:function(result_txt){
					
				Swal.fire({
					  title: 'Success',
					  text: "Devotee status updated successfully",
					  icon: 'success',
					  
					});
			//*/$('#editUserModal').modal('hide');	
			//$('#editUserModal').find('.close').trigger('click');
			//attendance_table();*/	
				//$('#edit_user_btn').modal('hide');				
			}
    	});
		/************/
			/*Swal.fire(
			  'Deleted!',
			  'Your file has been deleted.',
			  'success'
			)*/
		}
	})
})
</script>
<script>
$('#submit_btn').click(function(){
		

		var form = $('#dob_form')[0]; // You need to use standard javascript object here
		var formData = new FormData(form);
		
		$.ajax({
        url: "<?php echo get_theme_file_uri();?>/iskcon_template_parts/ajax_temp.php",
        data: formData,
        type: 'POST',
        contentType: false,
        processData: false,
		success:function(result){	
		//alert("hhhhh");		
			/*if(result==1){
				window.location="attendance-summary";
			}
			else {
				$('.err_msg').html("<span style='color:red'>Either email or password is not correct.</span>");
			}*/
		}
    });
		
	});

	</script>



<button type="button" class="btn btn-default btn-md" id="edit_user_btn" data-toggle="modal" data-target="#editUserModal" style="display:none;">User Info</button>

<div class="modal fade" id="editUserModal">
  <div class="modal-dialog" style="max-width:800px;">
    <div class="modal-content">      
	      <div class="modal-header">
	        <h4 class="modal-title">Edit User</h4>
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	      </div>

      <!-- Modal body -->
      <form method="POST" enctype="multipart/form-data" id="edit_user_form">
      <div class="modal-body">        
			<div class="col-md-12">
				<div class="form-group d-flex">
					<label class="col-md-5">Real Name</label>
					<div class="col-md-7">
						<input type="text" name="real_name" class="form-control">		
					</div>	
				</div>
				<div class="form-group d-flex">
					<label class="col-md-5">Mobile No.</label>
					<div class="col-md-7">
						<input type="text" name="mobile_no" class="form-control mobile_no">
					</div>			
				</div>
				<div class="form-group d-flex">
					<label class="col-md-5">Email</label>
					<div class="col-md-7">
						<input type="email" name="email" class="form-control">	
					</div>		
				</div> 
				<div class="form-group d-flex">
					<label class="col-md-5">City</label>
					<div class="col-md-7">
						<input type="text" name="city" class="form-control ">	
					</div>		
				</div> 	
				<input type="hidden" name="user_id" value="">
				<input type="hidden" name="action" value="update_user_detail">				
				 
				
	</div>

      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
      	<!-- <a href="javascript:void(0)" name="submit" class="btn btn-primary" value="submit" onclick="update_user()">Submit</a> -->
      	<button type="submit" class="btn btn-primary" >Submit</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
      </form>

    </div>
  </div>
</div>



<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.print.min.js"></script>
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<script src="<?php echo get_theme_file_uri();?>/iskcon_template_parts/assets/js/sweetalert2.min.js"></script>
<?php get_footer();?>