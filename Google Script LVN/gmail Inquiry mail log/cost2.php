<?php
//print_r($_REQUEST);
//exit;

# include connection file here
#include("include/connection.php");


$data = array();



//$url=explode("?",$_SERVER['REQUEST_URI']);



function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}
# include pricing calc file here
#include "pricing_calc_function.php";


$email_body = urldecode($_REQUEST['email_body']);
//print_r($email_body);

$property_id = $_REQUEST['property_id'];
$peter_mail_id = $_REQUEST['peter_mail_id'];
$user_mail_id = $_REQUEST['user_mail_id']; 


$Arrival_flag = strstr($email_body, 'Arrival date');

if($Arrival_flag)
{
	
	$start_date_temp = trim(get_string_between($email_body, 'Arrival date:', 'Departure date:'));
	$start_date_temp =  strtotime($start_date_temp);
	if($start_date_temp)
	{	
		$start_date = date("m/d/Y", $start_date_temp);
		
	}else
	{
		
		$data['status'] = "error";
		$data['status_msg'] = "start date not valid";
		echo json_encode($data);exit;
	}	
	




	$stop_date_temp = trim(get_string_between($email_body, 'Departure date:', 'Guests:'));
	$stop_date_temp =  strtotime($stop_date_temp);
	if($stop_date_temp)
	{
		$stop_date = date("m/d/Y", $stop_date_temp);
	}else
	{
		
		$data['status'] = "error";
		$data['status_msg'] = "end date not valid";
		echo json_encode($data);exit;
	}	
	
	
	
	$guest_html_line = trim(get_string_between($email_body, 'Guests:', 'Further info:'));

	$guest_html_line_array = explode(" ",trim($guest_html_line));
	//print_r($guest_html_line_array);exit;
	$guest_html_line_adults = $guest_html_line_array[0];
	$guest_html_line_children = $guest_html_line_array[2];
	$total_guest = $guest_html_line_adults + $guest_html_line_children;
	if($total_guest == 0)
	{
		$total_guest = 1;
	}	

	$t_name = get_string_between($email_body, 'Traveler name:', 'Contact info:');
	$t_name = trim($t_name);
	$t_name_array =  explode(" ",$t_name);
	$fname = $t_name_array[0];
	$lname = $t_name_array[1];
	
	$l_name_array =  explode("--",$lname);
		if($l_name_array)
		$lname = $l_name_array[0];
	
	if($lname == "" && $fname ="")
		{
			$t_name = get_string_between($email_body, 'Traveler name', 'Inquiry from');
			$t_name = trim($t_name);
			$t_name_array =  explode(" ",$t_name);
			$fname = $t_name_array[0];
			$lname = $t_name_array[1];
		}
	
}else
	{
	$parsed = get_string_between($email_body, 'Dates', 'Guests');
	$dates = $parsed;

		$dates = @str_replace('Available','',$dates);
		$dates = str_replace('*','',$dates); //replace * into space
		$dates_comma_sep = explode(",",$dates);

		
		$get_nights_info =  end($dates_comma_sep); 
		$total_night = explode(" ",$get_nights_info);
		$total_number_night = $total_night[1];
		if(count($dates_comma_sep) == 3)
		{
			$year = $dates_comma_sep[1];
		}else if(count($dates_comma_sep) == 4) // if date format contan two year i.e Dec 29, 2016-Jan 2, 2017
		{
		
			$year = $dates_comma_sep[1];
			$year_dash_sep = explode("-",$year);
			$year = $year_dash_sep[0];
		
		}
		// calculate start date and end date 
		$dates_dash_sep = explode("-",$dates_comma_sep[0]);
		$start_date_temp = $dates_dash_sep[0].' '.$year;
		$start_date_temp =  strtotime($start_date_temp);
		$start_date = date("m/d/Y", $start_date_temp);

		//echo $start_date;

		// end date 
		$days = $total_number_night +1 ;
		$stop_date = date('m/d/Y"', strtotime($start_date . ' +'.$total_number_night.' day'));

		
		//print_r($dates_dash_sep);
		$t_name = get_string_between($email_body, 'Traveler name', 'Contact info');
		$t_name = trim($t_name);
		$t_name_array =  explode(" ",$t_name);
		$fname = $t_name_array[0];
		$lname = $t_name_array[1];
		$l_name_array =  explode("--",$lname);
		if($l_name_array)
		$lname = $l_name_array[0];
	
	    if($lname == "" && $fname ="")
		{
			$t_name = get_string_between($email_body, 'Traveler name', 'Inquiry from');
			$t_name = trim($t_name);
			$t_name_array =  explode(" ",$t_name);
			$fname = $t_name_array[0];
			$lname = $t_name_array[1];
		}	
		
		//Guest Count 
		//i.e 20 adults, 0 children 
		$guest_html_line = get_string_between($email_body, 'Guests', 'Traveler name');
		$guest_html_line_array = explode(" ",trim($guest_html_line));
		//print_r($guest_html_line_array);exit;
		$guest_html_line_adults = $guest_html_line_array[0];
		$guest_html_line_children = $guest_html_line_array[2];
		
		$total_guest = $guest_html_line_adults + $guest_html_line_children;
		
	} //else arrival_flag	
	
	
	
	$data['property_number'] = $property_id;
	$data['arrival_date'] =  $start_date;	
	$data['departure_date']  =  $stop_date;
	$data['fname'] 	    =  $fname;
	$data['lname']      =  $lname;
	//$data['total_guest'] = $total_guest;
	//$grandTotal = "0";
	//$data['grandTotal'] = 0; 
	//$data['lvn_property_number'] = "";
	$data['peter_mail_id'] = $peter_mail_id;
    $data['user_mail_id'] = $user_mail_id; 
    $data['status'] = "success";
	


	
//*/
	



//print_r($data);exit;

echo json_encode($data);

?>