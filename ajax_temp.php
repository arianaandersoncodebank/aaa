
<?php

require_once('../../../../wp-config.php');
global $wpdb;
if(isset($_POST['action']))$action = $_POST['action'] ; else $action = '';

if($action == 'attendance_filter'){
	$filter_arr=[];
	if(isset($_POST['name']) && $_POST['name']!='')
		$filter_arr[] = ' ((login_first_name like "%'.$_POST['name'].'%") or ( login_last_name like "%'.$_POST['name'].'%"))';	

	if(isset($_POST['email']) && $_POST['email']!='')
		$filter_arr[] = ' email like "%'.$_POST['email'].'%" ';

	if(isset($_POST['duration']) && $_POST['duration']!='')
		$filter_arr[] = ' duration like "%'.$_POST['duration'].'%" ';

	if(isset($_POST['time_joined']) && $_POST['time_joined']!='')
		$filter_arr[] = ' time_joined like "%'.$_POST['time_joined'].'%" ';

	if(isset($_POST['time_exited']) && $_POST['time_exited']!='')
		$filter_arr[] = ' time_exited like "%'.$_POST['time_exited'].'%" ';

	if(isset($_POST['date']) && $_POST['date']!='')
		$filter_arr[] = ' date like "'.date("Y-m-d",strtotime($_POST['date'])).'%"';

	$where='';
	if($filter_arr){
		$where = ' where '.implode(' and ',$filter_arr);		
		$result = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."iskcon_user_attendance ".$where);
	}
	else{
		$result = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."iskcon_user_attendance ");	
	}

	$txt='';

	if($result){ 
	$i=1; 
	foreach($result as $row){
	$txt .='<tr>
		<td>'.$i.'</td>
		<td>'.$row->login_first_name.'</td>
		<td>'.$row->login_last_name.'</td>
		<td>'.$row->email.'</td>
		<td>'.$row->duration.'</td>
		<td>'.$row->time_joined.'</td>
		<td>'.$row->time_exited.'</td>
		<td>'.$row->date.'</td>
		</tr>';
	 } } 

	 echo $txt;
 
}
/*****************************************************/
if($action == 'upload_attendance'){ 
	$csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
	$data =[];
	$date = date('Y-m-d',strtotime($_POST['date']));
	$date = '"'.$date.'"';
	$class_id = $_POST['class_id'];

	$result = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."iskcon_user_attendance where date=".$date,ARRAY_A);	
	//echo $wpdb->last_query;die;
	//for update
	$update_data = [];
	if($result){
		foreach($result as $row){
			$update_data[$row['login_first_name'].' '.$row['login_last_name']] = $row;
		}
	}
//echo '<pre>';print_r($update_data);die;
	 // Validate whether selected file is a CSV file
    //if(!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'], $csvMimes)){
	if(!empty($_FILES['file']['name'])){
		$ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
	if( $ext=='csv'){
        
        // If the file is uploaded
        if(is_uploaded_file($_FILES['file']['tmp_name'])){
            
            // Open uploaded CSV file with read-only mode
            $csvFile = fopen($_FILES['file']['tmp_name'], 'r');
            
            // Skip the first line
            fgetcsv($csvFile);
            
            // Parse data from CSV file line by line
            while(($line = fgetcsv($csvFile)) !== FALSE){
                // Get row data
                $first_name   = '"'.$line[0].'"';//first_name
                $last_name  = '"'.$line[1].'"';//last_name
                $email  = '"'.$line[2].'"';
                $duration = '"'.$line[3].'"';
                $time_joined = '"'.$line[4].'"';
                $time_exited = '"'.$line[5].'"';

                $data[] = array($first_name,$last_name,$email,$duration,$time_joined,$time_exited,$date);
                //echo $line[0],$line[1];
            }            
            // Close opened CSV file
            fclose($csvFile);
            
            $status = 'success';
            $msg = 'File Uploded successfully';
        }else{
            $status = 'error';
            $msg = 'Some error came.';
        }
    }else{
        $status = 'invalid_file';
        $msg = 'Invalid File';
    }
   // echo '<pre>';print_r($data);echo '</pre>';die;
	if(!empty($data) && $status == 'success'){
		$insertQuery = "INSERT INTO ".$wpdb->prefix."iskcon_user_attendance(login_first_name,login_last_name,email,duration".$class_id.",time_joined".$class_id.",time_exited".$class_id.",date) VALUES";
		
		//$insertQuery = "INSERT INTO ".$wpdb->prefix."iskcon_user_attendance(login_first_name,login_last_name,email,duration,time_joined,time_exited,date,class_id) VALUES";

		$insertQueryValues = array();$updateQueryValues = array();
/*foreach($data as $value) {
	//echo '<pre>';print_r($value);
	echo $value[3].'<br>';
}die;*/
		
		foreach($data as $value) {
			 $login_name = $value[0].' '.$value[1]; 
			 $login_name = str_replace('"','',$login_name); 
			if (array_key_exists($login_name,$update_data))
			  {
			 	//$value = implode(',',$value);
			 	$updateQuery = "UPDATE  ".$wpdb->prefix."iskcon_user_attendance set duration".$class_id." = ".$value[3].",time_joined".$class_id." = ".$value[4] .", time_exited".$class_id." = ".$value[5]." where id=".$update_data[$login_name]['id'];
			 	array_push( $updateQueryValues, $updateQuery );
			  }
			  else{
			  	$value = implode(',',$value);
		  		array_push( $insertQueryValues, "(" . $value . ")" );
			  }
			
		}
		//echo '<pre>';print_r($updateQueryValues);
		//die;

		if($insertQueryValues){
			$insertQuery .= implode( ",", $insertQueryValues );
			//echo $insertQuery;
			$wpdb->query( $insertQuery );
		}
		if($updateQueryValues){
			//$updateQuery = implode( ";", $updateQueryValues );
			//echo '<br><br>'.$updateQuery;
			//$wpdb->query( $updateQuery );
			//$wpdb->show_errors();
			foreach($updateQueryValues  as $q){
				$wpdb->query( $q );
			}
		}
		/*$insertQuery .= implode( ",", $insertQueryValues );
		//echo $insertQuery;die;
		$wpdb->query( $insertQuery );*/
		
	}
	//echo $status;
	echo $msg;

}
}
/**********************************/
if($action == 'monthwise_attendance'){
	$year = $_POST['year'];
	$month = $_POST['month'];
	$user_id = $_POST['user_id'];
	$class_id = $_POST['class_id'];
	if($user_id)$user_id = ' and u.id='.$user_id;
	//if($month)$month_txt = ' and MONTH(date)='.$month;else $month_txt = '';

	$txt = '<h4 style="padding-top:20px;padding-bottom:10px;"> Attendance List </h4>';

	if($month=='')$start=1;
	else $start = $month;


	if($year == date('Y')){
		if($month=='')$count=date('m');
		else $count=$month;
	}
	else if($year < date('Y')){
		if($month=='')$count=12;
		else $count=$month;
	}

	//echo $start.'=='.$count;die;
	if($class_id==1)$duration_txt = " and a.duration1!='' ";
	else if($class_id==2)$duration_txt = " and a.duration2!='' ";
	else if($class_id==3)$duration_txt = " and a.duration3!='' ";

	for($z=$start;$z<=$count;$z++){
		$month = $z;
		if($month)$month_txt = ' and MONTH(a.date)='.$month;else $month_txt = '';
   
	/*$result = $wpdb->get_results("SELECT u.id,u.login_first_name,u.login_last_name,u.name,u.email,u.mobile_no,a.date,
		a.duration1,a.time_joined1,a.time_exited1,
		a.duration2,a.time_joined2,a.time_exited2,
		a.duration3,a.time_joined3,a.time_exited3,
		u.id as user_id FROM ".$wpdb->prefix."iskcon_disciple_users as u  
		left join ".$wpdb->prefix."iskcon_user_attendance as a on  (u.login_first_name=a.login_first_name and u.login_last_name = a.login_last_name ".$month_txt."  and YEAR(a.date)=".$year.") where u.is_delete=1  ".$user_id.' group by u.login_first_name,u.login_last_name order by u.name');*/

	$result = $wpdb->get_results("SELECT * from ".$wpdb->prefix."iskcon_disciple_users as u left join ".$wpdb->prefix."iskcon_user_attendance as a on (u.login_first_name = a.login_first_name and u.login_last_name = a.login_last_name) where YEAR(a.date)=".$year. $user_id .$month_txt ." order by u.login_first_name ASC", );

	//echo $wpdb->last_query;die;

	//echo '<pre>';print_r($result);die;	
	$arr=[];
	if($result){
	foreach($result as $row){
		if($row->date=='' )continue;		
		$arr[$row->name][date('d',strtotime($row->date))] = $row;
		
	}
	}
	//echo '<pre>';print_r($arr);die;
	$today_date = date('d-m-Y');
	
    $total_days=cal_days_in_month(CAL_GREGORIAN,$month,$year);
    $month_name = array(1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December');

    $txt .= '
    <div class="table-responsive monthwise_div" style="margin-bottom:50px;">
    	<table class="table table-bordered" border="1" id="attendance_table">
		<tr  class="heading_row1">
			<td></td>
			<td>Month<br>('.$month_name[(int)$month].')</td>
			<td>Year<br>('.$year.')</td>';
			for($i=1;$i<=($total_days);$i++){
				$txt .= '<td><div class="dayname">'.date('l',strtotime($i.'-'.$month.'-'.$year)).'</div></td>';	
			 } 
			 $txt .= '<td>Full Present</td><td>Half Present</td><td>Absent</td>';
		$txt .='</tr>
				<tr class="heading_row2">
				<td>S.No</td>
					<td>Devotee Name</td>
					<td>Mobile No.</td>';
			 for($i=1;$i<=($total_days);$i++){
				$txt .='<td>'.$i.'</td>';		
			} 
			$txt .='<td></td><td></td><td></td>';
		$txt .='</tr>';
		if($result){ $m=1; $showed=[];
			if($year==date('Y') && $month == date('m')){
				$spent_days=date('d');
			}else {
				$spent_days=$total_days;
			}
		foreach($result as $row){ 
			if(in_array(addslashes($row->name),$showed))continue;
				else $showed[] = addslashes($row->name);
		$txt .='<tr>
				<td>'.$m.'</td>
				<td>'.$row->name.'</td>
				<td>'.$row->mobile_no.'</td>'; $absent = 0;
			    for($i=1;$i<=($total_days);$i++){
			    	$day = date('d',strtotime($row->date));
			    	$txt .='<td>';
			    	$k = str_pad($i,2,0,STR_PAD_LEFT );//eg 01,02 date

			    	$duration ='';
			    	if(isset($arr[$row->name][$k])){
			    		if($class_id==1)$duration = $arr[$row->name][$k]->duration1;
						else if($class_id==2)$duration = $arr[$row->name][$k]->duration2;
						else if($class_id==3)$duration = $arr[$row->name][$k]->duration3;
			    	}

			    	

			    	if(isset($arr[$row->name][$k]) && $duration!='')
			    		$txt .='<div class="present btn btn-warning">P</div>';
			    	else if(strtotime($today_date) < strtotime($i.'-'.$month.'-'.$year)) 
			    		$txt .='<div class="att_not_added btntt btn-danger44">-</div>';
			    	else {
			    		$txt .='<div class="absent btntt btn-danger44">A</div>';
			    		$absent++;
			    	}
			    	$txt .='</td>';		
		 		}
		 		$att_count = present_absent_userwise_count($arr[$row->name],$total_days,$class_id,$spent_days);
		 		$txt .='<td>'.$att_count['p_count'].'</td><td>'.$att_count['h_count'].'</td><td>'.$absent.'</td>';
		 		//$txt .='<td></td><td></td><td></td>';
				$txt .='</tr>';
				$m++;
		 } 
		}else{
			$txt .='<tr><td align="center" colspan="'.($total_days+5).'">No data available</td></tr>';
		}

		  $txt .='</table></div>';
		}

		echo $txt;
}
function present_absent_userwise_count($arr,$total_days,$class_id,$spent_days){
	$p_count = $h_count = $a_count = 0;
	for($i=1;$i<=($spent_days);$i++){ 

		$k = str_pad($i,2,0,STR_PAD_LEFT );//eg 01,02 date   	

    	if(isset($arr[$k])){
			$x = attendance_count($arr[$k],$class_id);
			$p_count = $p_count + $x['p_count'];
			$h_count = $h_count + $x['h_count'];
			$a_count = $a_count + $x['a_count'];		

    	}
    			
	}
return array('p_count'=>$p_count,'h_count'=>$h_count,'a_count'=>$a_count);

}

function attendance_count($row,$class_id){
	if($class_id==1)$duration = $row->duration1;
	else if($class_id==2)$duration = $row->duration2;
	else if($class_id==3)$duration = $row->duration3;

	$p_count = $h_count = $a_count = 0;

	/*if(strpos($duration,'hr')==false)
		$du = '0 hrs '.$duration;
	else $du = $duration;
	$du = str_replace(' hrs ','.',$du);
	$du = str_replace(' hr ','.',$du);
	$du = str_replace('mins','',$du);
	$du = str_replace('min','',$du);
	
	//$row->du = $du;
*/
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
	$row->du = $du;

	if($du >= 0.3)$p_count = 1;
	else if($du < 0.3 && $du > 0)$h_count = 1;
	else $a_count = 1;

	return array('p_count'=>$p_count,'h_count'=>$h_count,'a_count'=>$a_count);
}

/***********************************/
if($action == 'upload_attendance2222'){ 
	$csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
	$data =[];
	$date = date('Y-m-d',strtotime($_POST['date']));
	$date = '"'.$date.'"';
	$class_id = $_POST['class_id'];


	 // Validate whether selected file is a CSV file
    //if(!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'], $csvMimes)){
	if(!empty($_FILES['file']['name'])){
		$ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
	if( $ext=='csv'){
        
        // If the file is uploaded
        if(is_uploaded_file($_FILES['file']['tmp_name'])){
            
            // Open uploaded CSV file with read-only mode
            $csvFile = fopen($_FILES['file']['tmp_name'], 'r');
            
            // Skip the first line
            fgetcsv($csvFile);
            
            // Parse data from CSV file line by line
            while(($line = fgetcsv($csvFile)) !== FALSE){
                // Get row data
                $x= explode(' ',$line[0]);
                $first_name   = '"'.$x[0].'"';//first_name
                if(isset($x[1]))$last_name  = '"'.$x[1].'"';else $last_name="''";
                $mobile_no  = '"'.$line[1].'"';
                $name  = '"'.$line[0].'"';
                
                
                $data[] = array($first_name,$last_name,$mobile_no,$name);
                //echo $line[0],$line[1];
            }            
            // Close opened CSV file
            fclose($csvFile);
            
            $status = 'success';
            $msg = 'File Uploded successfully';
        }else{
            $status = 'error';
            $msg = 'Some error came.';
        }
    }else{
        $status = 'invalid_file';
        $msg = 'Invalid File';
    }
   // echo '<pre>';print_r($data);echo '</pre>';die;
	if(!empty($data) && $status == 'success'){
		
		$insertQuery = "INSERT INTO ".$wpdb->prefix."iskcon_disciple_users(login_first_name,login_last_name,mobile_no,name) VALUES";
		$insertQueryValues = array();
		foreach($data as $value) {
			$value = implode(',',$value);
		  array_push( $insertQueryValues, "(" . $value . ")" );
		}
		$insertQuery .= implode( ",", $insertQueryValues );
		//echo $insertQuery;die;
		$wpdb->query( $insertQuery );
		
	}
	//echo $status;
	echo $msg;

}
}
/**********************************/
if($action == 'datewise_attendance'){
	$date = date("Y-m-d",strtotime($_POST['date']));	
	
	$user_id = $_POST['user_id'];
	if($user_id)$user_id = ' and u.id='.$user_id;
    
	$result = $wpdb->get_results("SELECT u.id,u.login_first_name,u.login_last_name,
		u.name,a.email,u.mobile_no,a.date,
		a.duration1,a.time_joined1,a.time_exited1,
		a.duration2,a.time_joined2,a.time_exited2,
		a.duration3,a.time_joined3,a.time_exited3,
		a.class_id,u.id as user_id 
		FROM ".$wpdb->prefix."iskcon_disciple_users as u  left join ".$wpdb->prefix."iskcon_user_attendance as a on  (u.login_first_name=a.login_first_name and u.login_last_name=a.login_last_name and date='".$date."') where u.is_delete=1 ".$user_id." order by u.name");
	//echo '<pre>';print_r($result);die;
	$arr=[];
	foreach($result as $row){
		
		if(!isset($arr[$row->name]['japa_class']))
			$arr[$row->name]['japa_class'] = '<div class="absent">Absent</div>';
		if(!isset($arr[$row->name]['morning_class']))
			$arr[$row->name]['morning_class'] = '<div class="absent">Absent</div>';
		if(!isset($arr[$row->name]['noon_class']))
			$arr[$row->name]['noon_class'] = '<div class="absent">Absent</div>';

		$pr_txt1 = '<div class="present">Present</div>
		<table class="table22 time_duration_table">
		<tr><td width="100">Time Joined</td><td width="100">Time Exited</td><td>Duration</td>
		</tr>
		<tr><td>'.date('h:i A',strtotime($row->time_joined1)).'</td>
			<td>'.date('h:i A',strtotime($row->time_exited1)).'</td>
			<td>'.$row->duration1.'</td>
		</tr></table>';
		$pr_txt2 = '<div class="present">Present</div>
		<table class="table22 time_duration_table">
		<tr><td width="100">Time Joined</td><td width="100">Time Exited</td><td>Duration</td>
		</tr>
		<tr><td>'.date('h:i A',strtotime($row->time_joined2)).'</td>
			<td>'.date('h:i A',strtotime($row->time_exited2)).'</td>
			<td>'.$row->duration2.'</td>
		</tr></table>';
		$pr_txt3 = '<div class="present">Present</div>
		<table class="table22 time_duration_table">
		<tr><td width="100">Time Joined</td><td width="100">Time Exited</td><td>Duration</td>
		</tr>
		<tr><td>'.date('h:i A',strtotime($row->time_joined3)).'</td>
			<td>'.date('h:i A',strtotime($row->time_exited3)).'</td>
			<td>'.$row->duration3.'</td>
		</tr></table>';

		/*$pr_txt1 = '<div class="present">Present</div>
		<div class="time_duration_table">'.$row->duration1.' ('.date('h:i A',strtotime($row->time_joined1)).' - 
			'.date('h:i A',strtotime($row->time_exited1)).')
		</div>';*/
		/*$pr_txt2 = '<div class="present">Present</div>
		<div class="time_duration_table">'.$row->duration2.' ('.date('h:i A',strtotime($row->time_joined2)).' - 
			'.date('h:i A',strtotime($row->time_exited2)).')
		</div>';*/
		/*$pr_txt3 = '<div class="present">Present</div>
		<div class="time_duration_table">'.$row->duration3.' ('.date('h:i A',strtotime($row->time_joined3)).' - 
			'.date('h:i A',strtotime($row->time_exited3)).')
		</div>';*/

		if($row->duration1!='')$arr[$row->name]['japa_class'] = $pr_txt1;
		if($row->duration2!='')$arr[$row->name]['morning_class'] = $pr_txt2;
		if($row->duration3!='')$arr[$row->name]['noon_class'] = $pr_txt3;

		//$arr[$row->name]=$row;
		$arr[$row->name]['name']=$row->name;
		$arr[$row->name]['login_first_name']=$row->login_first_name;
		$arr[$row->name]['login_last_name']=$row->login_last_name;
		$arr[$row->name]['email']=$row->email;
		$arr[$row->name]['mobile_no']=$row->mobile_no;
		$arr[$row->name]['date']=$row->date;

		/*$arr[$row->name]['duration1']=$row->duration1;
		$arr[$row->name]['time_joined1']=$row->time_joined1;
		$arr[$row->name]['time_exited1']=$row->time_exited1;

		$arr[$row->name]['duration2']=$row->duration2;
		$arr[$row->name]['time_joined2']=$row->time_joined2;
		$arr[$row->name]['time_exited2']=$row->time_exited2;

		$arr[$row->name]['duration3']=$row->duration3;
		$arr[$row->name]['time_joined3']=$row->time_joined3;
		$arr[$row->name]['time_exited3']=$row->time_exited3;*/
	}
	
	$today_date = date('d-m-Y');
	
    $txt ='<div class="table-responsive">
		<table class="table table-bordered" border="1" id="attendance_table">
		<tr>
	    	<th width="50">S.No</th>
			<th>Real Name</th>
			<th>Login Name</th>
			<th>Email</th>
			<th>Mobile</th>
			<th>Japa Class</th>
			<th>9 AM Class</th>
			<th>2:45 PM Class</th> 
		</tr>';

    
		if($arr){ $i=1;
		foreach($arr as $row){
			
		$txt .='<tr>
				<td>'.$i.'</td>
				<td>'.$row['name'].'</td>
				<td>'.$row['login_first_name'].' '.$row['login_last_name'].'</td>
				<td>'.$row['email'].'</td>
				<td>'.$row['mobile_no'].'</td>
				<td>'.$row['japa_class'].'</td>
				<td>'.$row['morning_class'].'</td>
				<td>'.$row['noon_class'].'</td>';
			   
				$txt .='</tr>';
				$i++;
		 } 
		}else{
			$txt .='<tr><td align="center" colspan="7">No data available</td></tr>';
		}

		 echo $txt.'</table></div>';

}

/**********************************/
if($action == 'get_user_detail'){
	/*$real_name 		= $_POST['real_name'];
	$mobile_no 		= $_POST['mobile_no'];
	$email 			= $_POST['email'];
	$city 			= $_POST['city'];*/
	$id 			= $_POST['id'];
	
    //echo '<pre>';print_r($_POST);die;

	$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."iskcon_disciple_users where is_delete=1 and id=".$id,ARRAY_A);
	//echo '<pre>';print_r($result);die;
	if($result)echo json_encode($result);
	

}


/***********************************/
if($action == 'update_user_detail'){
	$real_name 		= $_POST['real_name'];
	$mobile_no 		= $_POST['mobile_no'];
	$email 			= $_POST['email'];
	$city 			= $_POST['city'];
	$user_id 		= $_POST['user_id'];
	
    //echo '<pre>';print_r($_POST);die;

	//$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."iskcon_disciple_users where is_delete=1 and id=".$id,ARRAY_A);
	$table_name = $wpdb->prefix.'iskcon_disciple_users';
 	$data_update = array('name' => $real_name ,'mobile_no' => $mobile_no,'email' => $email,'city' => $city);
 	$data_where = array('id' => $user_id);
 	$wpdb->update($table_name , $data_update, $data_where);
//echo $wpdb->last_query;
echo 'User updated successfully';
	//echo '<pre>';print_r($result);die;
	//if($result)echo json_encode($result);
	

}
/**********************************/
if($action == 'absentees'){
	$date = $_POST['date'];
	$class_id = $_POST['class_id'];
	
	$date = "2022-10-29";
	$class_id = 1;
   
	$result = $wpdb->get_results("SELECT u.*,a.*,u.id as user_id FROM ".$wpdb->prefix."iskcon_disciple_users as u  left join ".$wpdb->prefix."iskcon_user_attendance as a where u.login_first_name=a.login_first_name and u.login_last_name=a.login_last_name and class_id=".$class_id." and date='".$date."' order by u.name");
	
	$arr=[];
	foreach($result as $row){
		$arr[$row->name] = $row->duration;
	}

	echo '<pre>';print_r($result);die;
		
	$today_date = date('d-m-Y');
	
    $total_days=cal_days_in_month(CAL_GREGORIAN,$month,$year);
    $month_name = array(1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December');

    $txt = '
		<tr  class="heading_row1">
			<td></td>
			<td>Month<br>('.$month_name[$month].')</td>
			<td>Year<br>(2022)</td>';
			for($i=1;$i<=($total_days);$i++){
				$txt .= '<td><div class="dayname">'.date('l',strtotime($i.'-'.$month.'-'.$year)).'</div></td>';	
			 } 
		$txt .='</tr>
				<tr class="heading_row2">
				<td>S.No</td>
					<td>Employee Name</td>
					<td>Mobile No.</td>';
			 for($i=1;$i<=($total_days);$i++){
				$txt .='<td>'.$i.'</td>';		
			} 
		$txt .='</tr>';
		if($result){ $m=1;
		foreach($result as $row){
			//if($row->duration!=NULL)
		$txt .='<tr>
				<td>'.$m.'</td>
				<td>'.$row->name.'</td>
				<td>'.$row->mobile_no.'</td>';
			    for($i=1;$i<=($total_days);$i++){
			    	$day = date('d',strtotime($row->date));
			    	$txt .='<td>';
			    	if(isset($arr[$row->name][$i]))$txt .='<div class="present btn btn-warning">P</div>';
			    	else if(strtotime($today_date) > strtotime($i.'-'.$month.'-'.$year)) $txt .='<div class="att_not_added btntt btn-danger44">-</div>';
			    	else $txt .='<div class="absent btntt btn-danger44">A</div></td>';		
		 		} 
				$txt .='</tr>';
				$m++;
		 } 
		}else{
			$txt .='<tr><td align="center" colspan="'.($total_days+2).'">No data available</td></tr>';
		}

		 echo $txt;

}


/**********************************/
if($action == 'attendance_summary'){

	$date = $_POST['date'];
	$class_id = $_POST['class_id'];

	/*$result = $wpdb->get_row("SELECT count(*) FROM ".$wpdb->prefix."iskcon_user_attendance where date='".$date."'");
	echo '<pre>';print_r($result);die;

	if(empty($result)){
	 $arr = array('present_arr'=>'','half_present_arr'=>'','absent_arr'=>'','error'=>'Attendance of date '.$_POST['date'].' is not uploded');
	 echo json_encode($arr);
	 exit;
	}*/
   
	$result = $wpdb->get_results("SELECT u.login_first_name,u.login_last_name,u.name,u.mobile_no,a.email,a.duration,a.time_joined,a.time_exited,u.id as user_id,a.date FROM ".$wpdb->prefix."iskcon_disciple_users as u  left join ".$wpdb->prefix."iskcon_user_attendance as a on (u.login_first_name=a.login_first_name and u.login_last_name=a.login_last_name and class_id=".$class_id." and date='".$date."') where u.is_delete=1 and u.status =1 order by u.name ASC");

	
	$half_present_arr = $present_arr = $absent_arr = [];
	if($result){
		foreach($result as $row){
			if(strpos($row->duration,'hr')==false)
				$du = '0 hrs '.$row->duration;
			else $du = $row->duration;
			/*$du = str_replace(' hrs ','.',$du);
			$du = str_replace(' hr ','.',$du);
			$du = str_replace('mins','',$du);
			$du = str_replace('min','',$du);*/

			/*$du = str_replace('hrs ','hr',$du);
			$du = str_replace('hr','+',$du);

			$du = str_replace('mins','min',$du);
			$du = str_replace('min','/100',$du);*/
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
			
			$row->du = $du;

			if($du >= 0.3)$present_arr[] = $row;
			else if($du < 0.3 && $du > 0)$half_present_arr[] = $row;
			else $absent_arr[] = $row;

		}
	}
	//include_once(ABSPATH."wp-content/plugins/attendance_manager/attendance_summary_ajax.php");
	//include(plugins_dir_url(__FILE__).'attendance_summary_ajax.php');


	
 
 $arr = array('present_arr'=>$present_arr,'half_present_arr'=>$half_present_arr,'absent_arr'=>$absent_arr);
 //echo '<pre>';print_r($present_arr);die;
 echo json_encode($arr);

}
/********************************************/
if($action == 'check_new_user_and_upload'){
	
    
	$result = $wpdb->get_results("SELECT * from ".$wpdb->prefix."iskcon_disciple_users where status=1 and is_delete=1 order by login_first_name asc",ARRAY_A);

	$disc_table_users = [];

	foreach($result as $row){
		$disc_table_users[strtolower($row['login_first_name']).' '.strtolower($row['login_last_name'])] = $row;
	}
	$result = $wpdb->get_results("SELECT * from ".$wpdb->prefix."iskcon_user_attendance order by login_first_name asc",ARRAY_A);

	$att_table_users = []; $ins=[];

	foreach($result as $row){
		//$att_table_users[$row['login_first_name'].' '.$row['login_last_name']] = $row;
		$key = strtolower($row['login_first_name']).' '.strtolower($row['login_last_name']);
		if(array_key_exists($key,$disc_table_users))continue;
		else $ins[] = $row;
	}
	//echo '<pre>';print_r($ins);
	$insertQuery = "INSERT INTO ".$wpdb->prefix."iskcon_disciple_users(login_first_name,login_last_name,email,name) VALUES";
		$insertQueryValues = array();
		foreach($ins as $value) {
			//$value = implode(',',$value);
			$a = array(
				"'".$value['login_first_name']."'",
				"'".$value['login_last_name']."'",
				"'".$value['email']."'",
				"'".$value['login_first_name'].' '.$value['login_last_name']."'"
			);
			$value = implode(',',$a);
		  array_push( $insertQueryValues, "(" . $value . ")" );
		}
		$insertQuery .= implode( ",", $insertQueryValues );
		//echo $insertQuery;die;
		$wpdb->query( $insertQuery );
		echo 'Uploded successfully';

}
/********************************************/
if($action == 'users_list'){
	$limit=$_REQUEST['length']; if($limit=='')$limit=10;
	$start = $_REQUEST['start']; if($start=='')$start=0;
	$result = $wpdb->get_row("SELECT count(*) as total_records FROM ".$wpdb->prefix."iskcon_disciple_users where status = 1 and is_delete=1 ");
	$totalRecords = $result->total_records;
	$search=$_REQUEST['search']['value'];
	if($search)$search =" and (login_first_name like '%".$search."%' or  login_last_name like '%".$search."%' or dob like '%".$search."%')";

	$result = $wpdb->get_row("SELECT count(*) as total_records FROM ".$wpdb->prefix."iskcon_disciple_users where status = 1 and is_delete=1 ".$search);
	//echo $wpdb->last_query;die;
	$recordsFiltered = $result->total_records;
	//$recordsFiltered =10;

	$users_list = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."iskcon_disciple_users where status = 1 and is_delete=1 ".$search." order by name limit ".$start .",".$limit);
	//echo $wpdb->last_query;
	$arr = [];
	if($users_list){ $i=$start+1;
			foreach($users_list as $row){
				if($row->status ==1)$status_chk = 'checked';
				else $status_chk = '';

				$status = '<div class="user_status_toggle">
								<label class="switch">
				  					<input type="checkbox" value="1" user_id="'.$user_id.'" class="user_status" '.$status_chk.'>
				  					<span class="slider round"></span>
								</label>
							</div>';
				
			$arr[]=	array(
					$i,
					$row->name,
					$row->login_first_name.' '.$row->login_last_name,
					$row->dob,
					//'<input type="text" name="dob['.$row->id.']" value="'.$row->dob.'">', 
					$row->mobile_no,
					$row->email	,				
					$row->city,
					(!empty($row->joining_date) && $row->joining_date!="0000-00-00") ? date('d-m-Y',$row->joining_date) : '',
					$status,
					'<a href="javascript:void(0)" onclick="edit_user('.$row->id.')">Edit</a>&nbsp;&nbsp;'

					//<a href="javascript:void(0)" onclick="delete_user('.$row->id.')">Delete</a>'
					);
					$i++;
			 }
		}
		$a = array(
			'data'=>$arr,
			'recordsTotal' => $totalRecords,
			'recordsFiltered' => $recordsFiltered
		);
		echo json_encode($a);
}

/****************************Export************/
function array2csv(array &$array)
{
   if (count($array) == 0) {
     return null;
   }
   ob_start();
   $df = fopen("php://output", 'w');
   fputcsv($df, array_keys(reset($array)));
   foreach ($array as $row) {
      fputcsv($df, $row);
   }
   fclose($df);
   return ob_get_clean();
}


function download_send_headers($filename) {
    // disable caching
    $now = gmdate("D, d M Y H:i:s");
    header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
    header("Last-Modified: {$now} GMT");

    // force download  
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");

    // disposition / encoding on response body
    header("Content-Disposition: attachment;filename={$filename}");
    header("Content-Transfer-Encoding: binary");
}

if($action == 'export_absentes_datewise'){

	$date = date('Y-m-d',strtotime($_POST['date']));
	$class_id = $_POST['class_id'];
   
	$result = $wpdb->get_results("SELECT u.login_first_name,u.login_last_name,u.name,u.mobile_no,a.email,
		a.duration".$class_id.",a.time_joined".$class_id.",a.time_exited".$class_id.",		
		u.id as user_id,a.date FROM ".$wpdb->prefix."iskcon_disciple_users as u  left join ".$wpdb->prefix."iskcon_user_attendance as a on (u.login_first_name=a.login_first_name and u.login_last_name=a.login_last_name and date='".$date."') order by u.name ASC");

	
	 $absent_arr = [];
	if($result){
		foreach($result as $row){
			if($class_id==1)$duration = $row->duration1;
			else if($class_id==2)$duration = $row->duration2;
			else if($class_id==3)$duration = $row->duration3;

			if(!empty($duration)) $absent_arr[] = $row;

		}
	}
	$data_arr = [];
	if($absent_arr){
		$data_arr[] = array('S.No','Login Name', 'Mobile');
		$i=1;
		foreach($absent_arr as $row){
			$data_arr[] = array($i,$row->login_first_name.' '.$row->login_last_name, $row->mobile_no);
			$i++;
		}
	}

	$date = date('d-m-Y',strtotime($date));
	download_send_headers("absentees_list_" . $date . ".csv");
	
	echo array2csv($data_arr);
	die();

}


/**********************************/
if($action == 'rolewise_login'){
	
	$email 		= $_POST['email'];
	$password 	= md5($_POST['password']);
	$role_id = 1;


	
    //echo '<pre>';print_r($_POST);die;

	$result = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."iskcon_seva_login where email ='".$email."' and password = '".$password."' and role_id = ".$role_id,ARRAY_A);
	//echo $wpdb->last_query;
	//echo '<pre>';print_r($result);die;
	if($result){ 
		session_start();
		$_SESSION['seva_user_email'] = $email;
		$_SESSION['seva_user_password'] = $password;
		echo 1;		
	}
	

}
if($ction == 'change_user_status'){
	$status = $_POST['status'];
	$user_id = $_POST['user_id'];

	
	$table_name = $wpdb->prefix.'iskcon_disciple_users';
 	$data_update = array('status' => $status);
 	$data_where  = array('id' => $user_id);
 	$wpdb->update($table_name , $data_update, $data_where);
	//echo $wpdb->last_query;
	echo 'Devotee status updated successfully';

}

if($action == 'seva_user_logout'){
	
		session_start();
		unset($_SESSION['seva_user_email']);
		unset($_SESSION['seva_user_password']);
		session_destroy();
		echo 1;	
	

}
if($action == 'save_dob'){ //echo 'kkkkkk';
//session_start();
//$_SESSION['ddddd'] = $_POST;

$table_name = $wpdb->prefix.'iskcon_disciple_users';

foreach($_POST['dob'] as $key=>$value){
	if($value=='')continue;
	$data_update = array('dob' => $value ,);
 	$data_where = array('id' => $key);
 	$wpdb->update($table_name , $data_update, $data_where);
}

	
 	/*$data_update = array('name' => $real_name ,'mobile_no' => $mobile_no,'email' => $email,'city' => $city);
 	$data_where = array('id' => $user_id);
 	$wpdb->update($table_name , $data_update, $data_where);*/
//echo $wpdb->last_query;
echo 'User updated successfully';


	}

?>