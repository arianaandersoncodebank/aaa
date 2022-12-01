<?php 
/* Template Name: Iskcon Rolewise Login Template
*/ 

?>
<?php
session_start();
if(isset($_SESSION['seva_user_email']) && $_SESSION['seva_user_email']!='')
    $email = $_SESSION['seva_user_email'];else $email ='';
if(isset($_SESSION['seva_user_password']) && $_SESSION['seva_user_password']!='')
    $password = $_SESSION['seva_user_password'];else $password ='';

if($email!='' && $password!='')header("Location:'attendance-summary'");


global $wpdb;

get_header();

$users_list = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."iskcon_disciple_users where status = 1 and is_delete=1 order by name ");


?>
<style>

label.error{
	color:red;
}
</style>
<link rel="stylesheet" href="<?php echo get_theme_file_uri();?>/iskcon_template_parts/assets/css/bootstrap-4.6.1.min.css">
  <script src="<?php echo get_theme_file_uri();?>/iskcon_template_parts/assets/js/jquery-3.6.1.min.js"></script> 
<script src="<?php echo get_theme_file_uri();?>/iskcon_template_parts/assets/js/popper-1.16.1.min.js"></script>
<script src="<?php echo get_theme_file_uri();?>/iskcon_template_parts/assets/js/bootstrap-4.6.1.bundle.min.js"></script>
  
<div class="col-md-12" style="padding-left:15px;padding-right:15px;padding-top:100px;padding-bottom:100px;">
  <form method="POST" enctype="multipart/form-data" id="login_form">
    <div class="d-flex justify-content-center">
      <div class="" style="width:600px;padding:50px;border:1px solid #ddd;">
        <h3 style="padding-bottom:20px;">Login</h3>
        <div class="form-group">
          <label for="email">Email address:</label>
          <input type="text" class="form-control" placeholder="Enter email" name="email" required>
        </div>
        <div class="form-group">
          <label for="pwd">Password:</label>
          <input type="password" class="form-control" placeholder="Enter password" name="password" required>
        </div>
        <div class="form-group form-check22 err_msg">
          <!-- <label class="form-check-label"><input class="form-check-input" type="checkbox"> Remember me
		    </label> -->
        </div>
        <div class="form-group">
          <input type="hidden" name="action" value="rolewise_login">
          <input type="submit" name="submit" class="btn btn-primary" id="submit_btn" value="Submit">
        </div>
      </div>
    </div>
  </form>
  <div id="ajax_content"></div>
</div>


<script type="text/javascript">   
var ajaxurl = "<?=admin_url('admin-ajax.php'); ?>";	
$(function() {
    $("#login_form").validate({
        rules: {
            email: {
                required: true,
                email: true,
                minlength: 5
            },
            password: {
                required: true,
                //minlength: 5
            }
        },
        // Specify validation error messages
        messages: {
            //real_name: "Please enter your firstname",
            /*lastname: "Please enter your lastname",
            password: {
              required: "Please provide a password",
              minlength: "Your password must be at least 5 characters long"
            },
            email: "Please enter a valid email address"*/
        },

        submitHandler: function(form) {
            event.preventDefault();
            //form.submit();
            /*var form = $('#login_form')[0]; // You need to use standard javascript object here
            var formData = new FormData(form);

            $.ajax({
                url: "<?php echo get_theme_file_uri();?>/iskcon_template_parts/ajax_temp.php",
                data: formData,
                type: 'POST',
                contentType: false,
                processData: false,
                success: function(result) {
                    if (result == 1) {
                        window.location = "attendance-summary";
                    } else {
                        //$('.err_msg').html("<span style='color:red'>Either email or password is not correct.</span>");
                        Swal.fire({
                            title: 'Error',
                            text: "Either email or password is not correct",
                            icon: 'warning',

                        });
                    }


                }
            });*/
            /************/
            var data = {
                'action': 'my_ajax_request',
                //'post_type': 'POST',
                'name': 'My First AJAX Request',
                'email': $('input[name="email"]').val(),
                'password' : $('input[name="password"]').val()
              };

              jQuery.post(ajaxurl, data, function(response) { 
                //alert(response);
                console.log( response );
                    if (response == 1) {
                        window.location = "attendance-summary";
                    } else {
                        //$('.err_msg').html("<span style='color:red'>Either email or password is not correct.</span>");
                        Swal.fire({
                            title: 'Error',
                            text: "Either email or password is not correct",
                            icon: 'warning',

                        });
                    }
              });
                    }
    });
});
</script>
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<?php get_footer();?>