<style>
#full_present_table,#half_present_table,#absent_table{
	/*background: beige;*/
}
#full_present_table td,#half_present_table td,#absent_table td,
#full_present_table th,#half_present_table th,#absent_table th{
	border: 1px solid #ddd;
}
#user_attendance_list h3{
	padding: 30px 0px 10px 0px;
}
#heading_table th, #heading_table td{
	border: 1px solid #ddd;
}
</style>

<?php
require_once('../../../../wp-config.php');
global $wpdb;

	$date = date('Y-m-d',strtotime($_POST['date']));
	$class_id = $_POST['class_id'];
   
	$result = $wpdb->get_results("SELECT u.login_first_name,u.login_last_name,u.name,u.mobile_no,a.email,
		a.duration".$class_id.",a.time_joined".$class_id.",a.time_exited".$class_id.",		
		u.id as user_id,a.date FROM ".$wpdb->prefix."iskcon_disciple_users as u  left join ".$wpdb->prefix."iskcon_user_attendance as a on (u.login_first_name=a.login_first_name and u.login_last_name=a.login_last_name and date='".$date."') order by u.name ASC");
//echo $wpdb->last_query;die;
	
	$half_present_arr = $present_arr = $absent_arr = [];
	if($result){
		foreach($result as $row){
			if($class_id==1)$duration = $row->duration1;
			else if($class_id==2)$duration = $row->duration2;
			else if($class_id==3)$duration = $row->duration3;


			/*if(strpos($duration,'hr')==false)
				$du = '0 hrs '.$duration;
			else $du = $duration;*/
			/*$du = str_replace(' hrs ','.',$du);
			$du = str_replace(' hr ','.',$du);
			$du = str_replace('mins','',$du);
			$du = str_replace('min','',$du);*/

			$hh = explode(' ',$duration);
			$du=0;
			if(count($hh)==4){
				$du = $hh[0] + $hh[2]/100;
			}
			else if(count($hh)==2){
				if($hh[1]=='hrs' || $hh[1] == 'hr')
					$du = $hh[0];
				else if($hh[1]=='mins' || $hh[1] == 'min')
					$du = $hh[0]/100;
			}


			/*$du = str_replace('hrs ','hr',$du);
			$du = str_replace('hr','+',$du);

			$du = str_replace('mins','min',$du);
			$du = str_replace('min','/100',$du);*/
			
			$row->du = $du;

			if($du >= 0.3)$present_arr[] = $row;
			else if($du < 0.3 && $du > 0)$half_present_arr[] = $row;
			else $absent_arr[] = $row;

		}
	}
	//include_once(ABSPATH."wp-content/plugins/attendance_manager/attendance_summary_ajax_temp.php");
	//include(plugins_dir_url(__FILE__).'attendance_summary_ajax.php');
 
 //$arr = array('present_arr'=>$present_arr,'half_present_arr'=>$half_present_arr,'absent_arr'=>$absent_arr);
 //echo json_encode($arr);
	//echo '<pre>';print_r($present_arr);die;

?>


<?php $class_name = array(1=>'Japa Class',2=>'9 AM Class',3=>'2:45 PM Class');?>

<h2>Google Meet Attendance Tracking Report</h2>
<table class="table" id="heading_table" style="width:600px;">
	<tr><th width="200">Meeting Name</th><td width="100"><?=$class_name[$class_id]?></td></tr>
	<tr><th>Meeting Date</th><td><?=$_POST['date']?></td></tr>
	<tr><th>Total No. of Devotees</th><td><?=count($result)?></td></tr>
	<tr><th>Total No. of Devotees Full Present</th><td><?=count($present_arr)?></td></tr>
	<tr><th>Total No. of Devotees Half Present</th><td><?=count($half_present_arr)?></td></tr>
	<tr><th>Total No. of Devotees Absent</th><td><?=count($absent_arr)?></td></tr>
	

</table>

<h3>Detailed Attendance Report</h3>
<h4>No. of Devotees Full Present ( >= 30 mins) </h4>
<?php if($present_arr){ $i=1;?>
<table class="table" id="full_present_table">
	<tr>
		<th>S.No</th>
		<th>Name</th>
		<th>Login Name</th>
		<th>Mobile</th>
		<th>Email</th>
		<th width="150">Duration</th>	
		<th width="200">Time Period</th>		
	</tr>
<?php foreach($present_arr as $row){ 
	if($class_id==1){
		$time_joined = $row->time_joined1; $time_exited = $row->time_exited1; $duration = $row->duration1; 
	}
	else if($class_id==2){
		$time_joined = $row->time_joined2; $time_exited = $row->time_exited2; $duration = $row->duration2;
	}
	else if($class_id==3){
		$time_joined = $row->time_joined3; $time_exited = $row->time_exited3;$duration = $row->duration3; 
	}


	echo '<tr>
			<td>'.$i.'</td>
			<td>'.$row->name.'</td>
			<td>'.$row->login_first_name.' '.$row->login_last_name.'</td>
			<td>'.$row->mobile_no.'</td>
			<td>'.$row->email.'</td>
			<td>'.$duration.'</td>	
			<td>'.date('H:i a',strtotime($time_joined)).' - '.date('H:i a',strtotime($time_exited)).'</td>			
	</tr>';
	$i++;
} ?>
</table>
<?php } ?>

<!------------------->

<h4>No. of Devotees Half Present ( < 30 mins) </h4>
<?php if($half_present_arr){ $i=1;?>
<table class="table" id="half_present_table">
	<tr>
		<th>S.No</th>
		<th>Name</th>
		<th>Login Name</th>
		<th>Mobile</th>
		<th>Email</th>
		<th  width="150">Duration</th>		
		<th  width="200">Time Period</th>		
	</tr>
<?php foreach($half_present_arr as $row){ 
	if($class_id==1){
		$time_joined = $row->time_joined1; $time_exited = $row->time_exited1; $duration = $row->duration1; 
	}
	else if($class_id==2){
		$time_joined = $row->time_joined2; $time_exited = $row->time_exited2; $duration = $row->duration2;
	}
	else if($class_id==3){
		$time_joined = $row->time_joined3; $time_exited = $row->time_exited3; $duration = $row->duration3; 
	}

	echo '<tr>
			<td>'.$i.'</td>
			<td>'.$row->name.'</td>
			<td>'.$row->login_first_name.' '.$row->login_last_name.'</td>
			<td>'.$row->mobile_no.'</td>
			<td>'.$row->email.'</td>
			<td>'.$duration.'</td>				
			<td>'.date('H:i a',strtotime($time_joined)).' - '.date('H:i a',strtotime($time_exited)).'</td>				
	</tr>';
	$i++;
} ?>
</table>
<?php } ?>

<!------------------->

<div class="d-flex justify-content-between" style="margin-top:20px;margin-bottom:20px;">
	<h4>No. of Devotees Absent </h4>
	<form method="POST" action="<?php echo get_theme_file_uri();?>/iskon_template_parts/ajax_temp.php">
		<input type="hidden" name="class_id" value="<?=$class_id?>">
		<input type="hidden" name="date" value="<?=$date?>">
		<input type="hidden" name="action" value="export_absentes_datewise">
		<input type="submit" name="submit" value="Export" class="btn btn-success">
		<!-- <a href="javascript:void(0)" class="btn btn-success btn-sm">Export</a> -->
	</form>
</div>
<?php if($absent_arr){ $i=1;?>
<table class="table" id="absent_table" style="">
	<tr>
		<th width="50">S.No</th>
		<th width="25%">Name</th> 
		<th width="25%">Login Name</th>
		<th width="15%">Mobile</th>
		<th>Email</th> 
			
	</tr>
<?php foreach($absent_arr as $row){
	echo '<tr>
			<td>'.$i.'</td>
			<td>'.$row->name.'</td>
			<td>'.$row->login_first_name.' '.$row->login_last_name.'</td>
			<td>'.$row->mobile_no.'</td>
			<td>'.$row->email.'</td>
							
	</tr>';
	$i++;
} ?>
</table>
<?php } ?>