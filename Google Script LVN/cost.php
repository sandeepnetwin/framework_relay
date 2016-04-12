<?php
//print_r($_REQUEST);
//exit;

# include connection file here
#include("include/connection.php");

$host = "lvnvacation.db.8179672.hostedresource.com";
$database = "lvnvacation";
$db_username = "lvnvacation";
$db_password = "LngVN185Sa";

$data = array();


mysql_connect($host, $db_username, $db_password) or die("Error: Could not connect to the server.");
mysql_select_db($database) or die("Error: Could not select database");
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
	$data['start_date'] =  $start_date;	
	$data['stop_date']  =  $stop_date;
	$data['fname'] 	    =  $fname;
	$data['lname']      =  $lname;
	$data['total_guest'] = $total_guest;
	//$grandTotal = "0";
	$data['grandTotal'] = 0; 
	$data['lvn_property_number'] = "";
    $data['status'] = "success";


	
//*/
/**** INCULDE FILE ****/


//Secured showing pin delete url
//define('SS_DELETE_PIN_URL','http://192.168.43.39/securedshowing/security/delete_cancelorder_access_codes');
define('SS_DELETE_PIN_URL','http://www.securedshowing.com/security/delete_cancelorder_access_codes');


        function dateDifference($startDate,$endDate)
        {
            $startDate = strtotime($startDate);
            $endDate = strtotime($endDate);
            if ($startDate === false || $startDate < 0 || $endDate === false || $endDate < 0 || $startDate > $endDate)
                return false;
               
            $years = date('Y', $endDate) - date('Y', $startDate);
           
            $endMonth = date('m', $endDate);
            $startMonth = date('m', $startDate);
           
            // Calculate months
            $months = $endMonth - $startMonth;
            if ($months <= 0)  {
                $months += 12;
                $years--;
            }
            if ($years < 0)
                return false;
           
            // Calculate the days
                        $offsets = array();
                        if ($years > 0)
                            $offsets[] = $years . (($years == 1) ? ' year' : ' years');
                        if ($months > 0)
                            $offsets[] = $months . (($months == 1) ? ' month' : ' months');
                        $offsets = count($offsets) > 0 ? '+' . implode(' ', $offsets) : 'now';

                        $days = $endDate - strtotime($offsets, $startDate);
                        $days = date('z', $days);   
                       
            return array($years, $months, $days);
        } 


function ninemosreserv($checkin,$checkout,$propertyid)
{
	$get_data_ninemosreserv = mysql_fetch_array(mysql_query("SELECT 9MonthAdvanceRes,9MonthDiscountType,9MonthDiscountRate FROM propertydiscountmst WHERE PropertyID = '".$propertyid."'"));
	
	 $date1 = strtotime ( '+9 month' , strtotime (date('Y-m-d')) ) ;
	
	 $date_after_9mos = date('Y-m-d',$date1);
	 
	 
	
	if($get_data_ninemosreserv['9MonthAdvanceRes'] == 1 && ($checkin > $date_after_9mos))
	{	
		/*if($get_data_ninemosreserv['9MonthDiscountType'] == 1) // discount type in percent
		{
		
		}
		else if($get_data_ninemosreserv['9MonthDiscountType'] == 0) // discount type in amount
		{
		
		}*/
		
		return true;
		
	}
	else
	{
		return false;
	}

}
/*** function addeds by SMW After 06 JUN 13***/

function dateDiff($startDate,$endDate,$day=false){
	$date1 = $startDate;
	$date2 = $endDate;

	$diff = abs(strtotime($date2) - strtotime($date1));

	$years = floor($diff / (365*60*60*24));
	$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
	if($day)
		$days = floor(($diff)/ (60*60*24));//for add checkin date
	else
		$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

	//printf("%d years, %d months, %d days\n", $years, $months, $days);
	return array('years'=>$years, 'months'=> $months, 'days'=>$days);
}

function ninemonthreservation($checkin,$checkout,$propertyid){

	$today = date('Y-m-d');
	$day = date('D', strtotime($chckin));
	$inDateDiff = dateDiff($today,$chckout);
	$isNineMnth=true;

	$get_data_ninemosreserv = mysql_fetch_array(mysql_query("SELECT 9MonthAdvanceRes,9MonthDiscountType,9MonthDiscountRate FROM propertydiscountmst WHERE PropertyID = '".$propertyid."'"));	
	
	if($get_data_ninemosreserv['9MonthAdvanceRes'] == 1 ){
		
	}else{
		$isNineMnth=false;
	}
}

	//Get total price befor discount/premium			
function getSubPrice($dayCount, $propertyNumber){
	$total=0;
	$sql_getrate = "SELECT P.* FROM pricechart AS P LEFT JOIN pricetypemst AS PT ON (P.typeid = PT.typeid) WHERE P.propertynumber = '".$propertyNumber."' ";
	if($dayCount > 14){
		$sql_getrate .= "AND P.typeid = '15' ";				
	}else{
		$sql_getrate .= "AND P.typeid = '".$dayCount."'";
	}
	//echo '<br/>@sql_getrate=>'.$sql_getrate;
	$result_getrate = mysql_query($sql_getrate);
	$cnt_getrate = mysql_num_rows($result_getrate);
	if($cnt_getrate){
		$row_getrate = mysql_fetch_assoc($result_getrate);

		if($row_getrate['typeid'] == 15){
			$total = $dayCount * $row_getrate['amount'];
		}else{
			$total = $row_getrate['amount'];
		}
	}else{
		$sql_getrate = "SELECT P.* FROM default_pricechart AS P LEFT JOIN pricetypemst AS PT ON (P.typeid = PT.typeid) WHERE 1 ";
		if($dayCount > 14){
			$sql_getrate .= "AND P.typeid = '15' ";				
		}else{
			$sql_getrate .= "AND P.typeid = '".$dayCount."'";
		}	
		$result_getrate = mysql_query($sql_getrate);
		$cnt_getrate = mysql_num_rows($result_getrate);	
		if($cnt_getrate){
			$row_getrate = mysql_fetch_assoc($result_getrate);

			if($row_getrate['typeid'] == 15){
				$total = $dayCount * $row_getrate['amount'];
			}else{
				$total = $row_getrate['amount'];
			}		
		}
	}
	return $total;
}

function getPriceAvailability(){
	if(!session_id()){
		session_start();
	}
	
	$ex_prop = $_SESSION['thirdparty_ex_prop'];
	
	$_SESSION['PRICE_CAL']=array();
	$property_id = $_REQUEST['propertyID'];
	$chckin = date('Y-m-d',strtotime($_REQUEST['check_in']));
	$occupants = $_REQUEST['occupants'];
	$nights = $_REQUEST['nights'];
	$event = $_REQUEST['event'];
	$chckout = date('Y-m-d',(strtotime($chckin) + (86400 * ($nights))));
	$serCheckout = date('Y-m-d',(strtotime($chckout) - (86400)));
	$holidayCheckout = $serCheckout;
	$propert_avail = 1;
	
	//get info of all prop
	$property		= mysql_query("select * from propertymst where PropertyNumber=".$property_id."");
	$get_property	= mysql_fetch_array($property);	
	$message ="";
	$max_occupant = "";
	$grandTotal = 0;
	/* $ipAdd = getIpAddressServer();
	
	$sql_ipch = "SELECT Grade FROM leadgrademst WHERE ClientId In(SELECT ClientId FROM locationclientmst WHERE IpAddress = '".$ipAdd."') ORDER BY Grade DESC LIMIT 1";
	$result_ipch = mysql_query($sql_ipch) or die('ERR: @sql_ipch=>'.mysql_error());
	$cnt_ipch = mysql_num_rows($result_ipch);
	if($cnt_ipch){
		$row_ipch = mysql_fetch_assoc($result_ipch);
		if(in_array($row_ipch['Grade'], array(2,3))){			
			$sql_occu="SELECT isDisplayNevadaUser, verbNevadaUser FROM propertydiscountmst WHERE PropertyID = '".$get_property['PropertyID']."' AND AllowBadTenant='1'";
			//echo '@sql=> '.$sql_occu;die;
			$result_occu = mysql_query($sql_occu);
			$cnt_occu = mysql_num_rows($result_occu);
			if($cnt_occu){
				$row_occu = mysql_fetch_assoc($result_occu);
				if($row_occu['verbNevadaUser']){
					$message = stripslashes($row_occu['verbNevadaUser']);
				}else{
					$message = 'This property requires minimum 30 night renatal.';
				}
			}
		}
	} */

	
	if($chckin != "" && $chckout != "" && $message =="")
	{
		#QUERY TO CHECK AVAILABILTY OF PROPERTY
		$sql_qry="SELECT * FROM reservepropertymst 
		WHERE 
		(('".$chckin."' BETWEEN ArrivalDate AND DepartureDate) 
		OR 
		('".$serCheckout."' BETWEEN ArrivalDate AND DepartureDate) 
		OR 
		('".$chckin."' <= ArrivalDate AND '".$serCheckout."' >= DepartureDate)) AND (ContractStatus != '3') AND 
		(PropertyID = '".$get_property['PropertyNumber']."')";		
		
		$sql_chkavail = mysql_query($sql_qry) 
		or die("err".mysql_error());
		//echo '<br/>@sql=> '.$sql_qry;
		$num_chkavail = mysql_num_rows($sql_chkavail);
		if($get_property['Calendar_Id'] && !$num_chkavail){
			$check_availcal = isDateAvailable($get_property['PropertyNumber'],$chckin,$chckout);
			if($check_availcal['busy']){
				$propert_avail = 0;
				$showadate = date('m/d/Y',strtotime($check_availcal['bsdate']));
				$showddate = date('m/d/Y',strtotime($check_availcal['bedate']));
				$message = "Sorry, this property is not available on ".$showadate." - ".$showddate.". Please call 1 (424) 260-7113 for a comparable properties or change your date range.";
				
				$data['grandTotal'] = $message; 
				 
				if(isset($_REQUEST['isthird_party']) && $_REQUEST['isthird_party']==1){
					$message = "Sorry, this property is not available on ".$showadate." - ".$showddate.". ";
					if($get_property['ThirdPartyPhone']){
						$message .= "Please call ".$get_property['ThirdPartyPhone']." for a comparable properties or change your date range.";
					}
				}
			}

		}
		if($num_chkavail > 0)
		{
			$final_date1 = strtotime($chckout);
			$adate1 = strtotime($chckin);
				while($row_arrival=mysql_fetch_array($sql_chkavail))
				{
					$arrival_date	= strtotime($row_arrival['ArrivalDate']);
					$departure_date	= strtotime($row_arrival['DepartureDate']);
					
					if(($final_date1>=$arrival_date &&  $final_date1<=$departure_date) || ($adate1>=$arrival_date &&  $adate1<=$departure_date) || ($adate1<$arrival_date &&  $final_date1>$departure_date))
					{	
                        if($final_date1>=$arrival_date &&  $final_date1<=$departure_date)
						{
							$timestamp2	= strtotime($row_arrival['ArrivalDate']);
							$timestamp3	= strtotime($row_arrival['DepartureDate']);
							$ndate		= date("m/d/Y",$timestamp2);
							$tdate		= date("m/d/Y",$timestamp3);
							$showadate	= $ndate;
							$showddate	= $tdate;
						}
						
						if($adate1 >= $arrival_date && $adate1 <= $departure_date)
						{
							 $timestamp2	= strtotime($row_arrival['ArrivalDate']);
                             $timestamp3	= strtotime($row_arrival['DepartureDate']);
							$ndate			= date("m/d/Y",$timestamp2);
							$tdate			= date("m/d/Y",$timestamp3);
							$showadate		= $ndate;
							$showddate		= $tdate;
						}
                                                
                         if($adate1 < $arrival_date && $final_date1>$departure_date)
						{
							$timestamp2		= strtotime($row_arrival['ArrivalDate']);
                            $timestamp3		= strtotime($row_arrival['DepartureDate']);
							$ndate			= date("m/d/Y",$timestamp2);
							$tdate			= date("m/d/Y",$timestamp3);
							$showadate		= $ndate;
							$showddate		= $tdate;
						}
					}
				}		
		
			//$message = "Property not available during period '".$showadate."' to '".$showddate."'";
			//$message = "Property Not Available for those exact dates it already booked for '".$showadate."' to '".$showddate."' period, please try for another date";			
			$message = "Sorry, this property is not available on ".$showadate." - ".$showddate.". Please call 1 (424) 260-7113 for a comparable properties or change your date range.";
			$data['grandTotal'] = $message;
			
			if(isset($_REQUEST['isthird_party']) && $_REQUEST['isthird_party']==1){
				$message = "Sorry, this property is not available on ".$showadate." - ".$showddate.". ";
				if($get_property['ThirdPartyPhone']){
					$message .= "Please call ".$get_property['ThirdPartyPhone']." for a comparable properties or change your date range.";
				}
			}
			
			$_SESSION['chckin'] 	= "";
			$_SESSION['chckout'] 	= "";
			$_SESSION['occupants']  = "";
			$_SESSION['event']      = "";
			$_SESSION['propertyid'] = "";

		} // end of if($num_chkavail > 0)
		elseif($propert_avail)
		{
			$_SESSION['chckin'] 	= $chckin;
			$_SESSION['chckout'] 	= $chckout;
			$_SESSION['occupants']  = $occupants;
			$_SESSION['event']      = $event;
			$_SESSION['propertyid'] = $get_property['PropertyID'];
			
			//echo "::".$chckin.", ".$chckout.dateDifference($chckin,$chckout);			
			//$day=array('mon','tue','wed','thu');
			$discountName="";
			$isSatrudayPrem="";
			$isWeekdayDis="";
			$today = date('Y-m-d');
			$day = date('D', strtotime($chckin));
			$inDateDiff = dateDiff($today,$chckin);//diff bet today and checkin in year, month, day
			$countDateDiff = dateDiff($chckin,$chckout,true);//diff bet checkin and checkout in days
			$countCheckDiff = dateDiff($today,$chckin,true);//diff bet today and checkin in days
			
			$bookDayArr=array();
			$isMiniDollar=false;
			$isOccupantUpchrg=false;
			$isWSOP=false;
			//echo '<pre>';
			//print_r(dateDiff($chckin,$chckout));
			//print_r($inDateDiff);
			//echo '</pre>';
			//echo '<br/>@day=>'.date('D' strtotime('+9 month' , strtotime (date('Y-m-d'))));exit;
			
			//check for weekdays discount
			if($countDateDiff['days'] < 5 && !$event){
				$notWeekDay=array('fri','sat','sun');
				$checkintime = strtotime($chckin);
				for($i=0;$i < $countDateDiff['days']; $i++){
					$bookDayArr[$i]=strtolower(date('D',$checkintime));
					$checkintime = $checkintime + 86400;//+ 1 day seconds
				}
				//echo '<br/>@checkin=>'.$chckin.' day is =>'.date('D',strtotime($chckin));print_r($bookDayArr);//print_r($notWeekDay);
				if(!in_array($notWeekDay[0],$bookDayArr) && !in_array($notWeekDay[1],$bookDayArr) && !in_array($notWeekDay[2],$bookDayArr)){
					$discountName = "WeekDayDiscount";
					$isWeekdayDis = "WeekDayDiscount";
				}
			}
			
			//check for 9 month discount
			if(strtolower($day) != "sat" && ($inDateDiff['months'] >= 9 || $inDateDiff['years'] >= 1 ) && !$event){
				//ninemonthreservation($_SESSION['chckin'],$_SESSION['chckout'],$_SESSION['propertyid']);
				$discountName = "9MonthDiscount";
			}

			//check for saturday premium
			if($countDateDiff['days'] >= 7 && strtolower($day) == "sat"){
				$discountName = "SaturdayPremium";
				$isSatrudayPrem = "SaturdayPremium";
			}
			
			//check for chargable night, holidays
			$holidayArr=array();
			$sql_chrg ="SELECT * FROM holidaydiscountmst 
			WHERE ((HolidayStartDate <='".$chckin."'
			AND HolidayEndDate >= '".$holidayCheckout."') OR (HolidayStartDate >='".$chckin."'
			AND HolidayEndDate >= '".$holidayCheckout."' AND HolidayStartDate <= '".$holidayCheckout."') OR (HolidayStartDate <='".$chckin."'
			AND HolidayEndDate <= '".$holidayCheckout."' AND HolidayEndDate >= '".$chckin."') OR (HolidayStartDate >='".$chckin."'
			AND HolidayEndDate <= '".$holidayCheckout."')) AND 
			PropertyID = '".$get_property['PropertyID']."' AND IsRemoved='0' AND IsActive='1'";
			//echo '<br/>@sql_charg=>'.$sql_chrg ;
			$result_chrg = mysql_query($sql_chrg);
			$cnt_chrg = mysql_num_rows($result_chrg);
			if($cnt_chrg){
				$row_chrg = mysql_fetch_assoc($result_chrg);
				$holidayArr['MIN_NIGHT'] = $row_chrg['MinimumNights'];
				if($countDateDiff['days'] > $row_chrg['MinimumNights']){
					$holidayArr['MIN_NIGHT'] = $countDateDiff['days'];
				}				
				/*if($row_chrg['HolidayStartDate'] <= $chckin && $row_chrg['HolidayEndDate'] >= $holidayCheckout){
					$holidayArr['CHARGE_NIGHT'] = ($countDateDiff['days'] > $holidayArr['MIN_NIGHT'])?($countDateDiff['days'] - $holidayArr['MIN_NIGHT']):0;
				
				}else if($row_chrg['HolidayStartDate'] >= $chckin && $row_chrg['HolidayEndDate'] >= $holidayCheckout && $row_chrg['HolidayStartDate'] <= $holidayCheckout ){ //
				
					$stDcho = dateDiff($row_chrg['HolidayStartDate'],$holidayCheckout,true);
					if($stDcho['days'] >= $row_chrg['MinimumNights'])
						$holidayArr['CHARGE_NIGHT'] = $countDateDiff['days'] - $holidayArr['MIN_NIGHT'];
					else				
						$holidayArr['CHARGE_NIGHT'] = $countDateDiff['days'] - $stDcho['days'];
						
				}else if($row_chrg['HolidayStartDate'] <= $chckin && $row_chrg['HolidayEndDate'] <= $holidayCheckout && $row_chrg['HolidayEndDate'] >= $chckin ){
				
					$enDchi = dateDiff($chckin,$row_chrg['HolidayEndDate'],true);
					
					if($enDchi['days'] >= $row_chrg['MinimumNights'])
						$holidayArr['CHARGE_NIGHT'] = $countDateDiff['days'] - $holidayArr['MIN_NIGHT'];
					else				
						$holidayArr['CHARGE_NIGHT'] = $countDateDiff['days'] - $enDchi['days'];
						
				}else if($row_chrg['HolidayStartDate'] >= $chckin && $row_chrg['HolidayEndDate'] <= $holidayCheckout){
					$holidayArr['CHARGE_NIGHT'] = ($countDateDiff['days'] > $holidayArr['MIN_NIGHT'])?($countDateDiff['days'] - $holidayArr['MIN_NIGHT']):0;
				}*/
				//if($countDateDiff['days'] >= $row_chrg['MinimumNights']){
					$discountName = "ChargableNight";
				//}
				//print_r($row_chrg);
			}

			
			//check for minimum dollar analysis
			/*if($countCheckDiff['days'] <= 7){
				$isMiniDollar=true;
			}*/
			//check for occupancy analysis, WSOP
			$WSOPArr=array();
			$WSOPArr['NORMAL_NIGHT'] = 0;
			$sql_occu="SELECT * FROM propertydiscountmst WHERE PropertyID = '".$get_property['PropertyID']."'";
			$result_occu = mysql_query($sql_occu);
			$cnt_occu = mysql_num_rows($result_occu);
			if($cnt_occu){
				$row_occu = mysql_fetch_assoc($result_occu);
				/*if($occupants > $row_occu['MaxOccupants']){
					$isOccupantUpchrg = true;
				}*/
				/*if($row_occu['WSOPStartDate'] <= $chckin && $row_occu['WSOPEndDate'] >= $chckout && $row_occu['WSOPMinNights'] <= $countDateDiff['days'] && $row_occu['WSOPReservationsOnly'] == 1 ){
					$isWSOP = true;
				}*/
				if($row_occu['WSOPReservationsOnly']== 1){
					if($row_occu['WSOPStartDate'] <= $chckin && $row_occu['WSOPEndDate'] >= $chckout){
						if($countDateDiff['days'] >= $row_occu['WSOPMinNights']){
							$WSOPArr['PER_NIGHT'] = $countDateDiff['days'];
							$isWSOP = true;
						}
					}else if($row_occu['WSOPStartDate'] >= $chckin && $row_occu['WSOPEndDate'] >= $chckout && $row_occu['WSOPStartDate'] <= $chckout ){ //
					
						$stDcho = dateDiff($row_occu['WSOPStartDate'],$chckout,true);
						if($stDcho['days'] >= $row_occu['WSOPMinNights']){
							$WSOPArr['PER_NIGHT'] = $stDcho['days'];
							$WSOPArr['NORMAL_NIGHT'] = $countDateDiff['days'] - $stDcho['days'];
							$isWSOP = true;
						}
					}else if($row_occu['WSOPStartDate'] <= $chckin && $row_occu['WSOPEndDate'] <= $chckout && $row_occu['WSOPEndDate'] >= $chckin ){
					
						$enDchi = dateDiff($chckin,$row_occu['WSOPEndDate'],true);
						if($enDchi['days'] >= $row_occu['WSOPMinNights']){
							$WSOPArr['PER_NIGHT'] = $enDchi['days'];
							$WSOPArr['NORMAL_NIGHT'] = $countDateDiff['days'] - $enDchi['days'];
							$isWSOP = true;
						}
					}else if($row_occu['WSOPStartDate'] >= $chckin && $row_occu['WSOPEndDate'] <= $chckout){

						$alDc = dateDiff($row_occu['WSOPStartDate'],$row_occu['WSOPEndDate'],true);
						if($alDc['days'] >= $row_occu['WSOPMinNights']){
							$WSOPArr['PER_NIGHT'] = $alDc['days'];
							$WSOPArr['NORMAL_NIGHT'] = $countDateDiff['days'] - $alDc['days'];
							$isWSOP = true;
						}
					}
				}
			}
			
			/*echo '<br/>@Discount Rate =>'.$discountName;
			echo '@holidayArray/ ChargeNight =><pre>';
			print_r($holidayArr);
			echo '</pre>';*/
			$isPremium=0;
			$isDiscount=0;			
			if($isWSOP){
				//echo '<br/>@WSOP';
				$normalNight = getSubPrice($WSOPArr['NORMAL_NIGHT'], $get_property['PropertyNumber']);
				$total = ($WSOPArr['PER_NIGHT'] * $row_occu['WSOPRatePerNight']) + $normalNight;
			}else if($discountName == 'ChargableNight'){
				//echo '<br/>@ChargableNight';
				$minNight = getSubPrice($holidayArr['MIN_NIGHT'], $get_property['PropertyNumber']);
				//$chargeNight = getSubPrice($holidayArr['CHARGE_NIGHT'], $get_property[PropertyNumber]);
				if($row_chrg['IsPremiumOrDiscount'] == 1 ){//Premium
					if($row_chrg['PremiumOrDiscountType'] ==1){//In precent
						$isPremium = (($row_chrg['PremiumOrDiscountRate']/100) * $minNight);
						$minPrice = $minNight + $isPremium;
					}else if($row_chrg['PremiumOrDiscountType'] == 0){//In $
						$isPremium = $row_chrg['PremiumOrDiscountRate'];
						$minPrice = $minNight + $isPremium;
					}
				}else if($row_chrg['IsPremiumOrDiscount'] == 0){//Discount
					if($row_chrg['PremiumOrDiscountType'] ==1){//In percent
						$isDiscount = (($row_chrg['PremiumOrDiscountRate']/100) * $minNight);
						$minPrice = $minNight - $isDiscount;
					}else if($row_chrg['PremiumOrDiscountType'] == 0){//In $
						$isDiscount = $row_chrg['PremiumOrDiscountRate'];
						$minPrice = $minNight - $isDiscount;					
					}
				}
				if($isSatrudayPrem == 'SaturdayPremium' && $row_occu['SaturdayArrivalReservationAnd7OrMoreNightsPremium'] == 1){
					$isPremium = $isPremium + $row_occu['SaturdayArrivalReservationAnd7OrMoreNightsPremiumRate'];
					$minPrice = $minPrice + $row_occu['SaturdayArrivalReservationAnd7OrMoreNightsPremiumRate'];
				}
				//$total = $minPrice + $chargeNight;
				$total = $minPrice;
			}else if($discountName == 'SaturdayPremium' && $row_occu['SaturdayArrivalReservationAnd7OrMoreNightsPremium'] == 1){//Premium in $
				//echo '<br/>@SaturdayPremium';
				$isPremium = $row_occu['SaturdayArrivalReservationAnd7OrMoreNightsPremiumRate'];
				$total = getSubPrice($countDateDiff['days'], $get_property['PropertyNumber']) + $isPremium;
			}else if($discountName == '9MonthDiscount' && $row_occu['9MonthAdvanceRes'] == 1){//Discount in percent
				if(($isWeekdayDis == 'WeekDayDiscount' && $row_occu['WeekdayDiscount'] == 1) && ($row_occu['WeeknightDiscountRate'] > $row_occu['9MonthDiscountRate'])){
					$subTotal = getSubPrice($countDateDiff['days'], $get_property['PropertyNumber']);
					$isDiscount = (($row_occu['WeeknightDiscountRate']/100) * $subTotal);
					$total = $subTotal - $isDiscount;					
				}else{
					$subTotal = getSubPrice($countDateDiff['days'], $get_property['PropertyNumber']);
					$isDiscount = (($row_occu['9MonthDiscountRate']/100) * $subTotal);
					$total = $subTotal - $isDiscount;
				}
			}else if($discountName == 'WeekDayDiscount' && $row_occu['WeekdayDiscount'] == 1){//Discount in percent
				//echo '<br/>@WeekDayDiscount';
				$subTotal = getSubPrice($countDateDiff['days'], $get_property['PropertyNumber']);
				$isDiscount = (($row_occu['WeeknightDiscountRate']/100) * $subTotal);
				$total = $subTotal - $isDiscount;
			}else{
				//get total price without discount/premium
				//echo '<br/>@Without any discount Premium';
				$total = getSubPrice($countDateDiff['days'], $get_property['PropertyNumber']);				
			}
			$occupantCharge = 0;
			if($occupants > $row_occu['MaxOccupants'] && $occupants <= $row_occu['OccupancyExceedsLimit']){
				//echo '<br/>@occupant EXCEEDS';
				$occupantCharge = ($occupants - $row_occu['MaxOccupants'] ) * $row_occu['PerPersonPremium'];
			}else if($occupants > $row_occu['OccupancyExceedsLimit'] && $row_occu['OccupancyExceedsLimit']){
				//echo '<br/>@occupant LIMIT EXCEEDS';
				$max_occupant = 1;
				//$message = "You can register property with maximum ".$row_occu['OccupancyExceedsLimit']." occupant.";
				$message = "Sorry, this property can hold no more than".$row_occu['OccupancyExceedsLimit']." occupants. Please call us at 1 (424) 260-7113 to arrange special privileges.";
				if(isset($_REQUEST['isthird_party']) && $_REQUEST['isthird_party']==1){
					$message = "Sorry, this property can hold no more than".$row_occu['OccupancyExceedsLimit']." occupants. ";
					if($get_property['ThirdPartyPhone']){
						$message .= "Please call us at ".$get_property['ThirdPartyPhone']." to arrange special privileges.";
					}
				}
			}
			
			//get security deposite, cleaning fees
			$_SESSION['PRICE_CAL']['IS_DISCOUNT'] = $isDiscount;
			$_SESSION['PRICE_CAL']['IS_PREMIUM'] = $isPremium;
			$_SESSION['PRICE_CAL']['OCCUPANT_CHARGE'] = $occupantCharge;
			$_SESSION['PRICE_CAL']['SECURITY_DEPOSITE'] = 250;
			$_SESSION['PRICE_CAL']['UNBRAND_SECURITY_DEPOSITE'] = 250;
			$_SESSION['PRICE_CAL']['CLEANING_FEE'] = 0;
			$sql_security = "SELECT * FROM `po_surcharge` WHERE PropertyID = '".$get_property['PropertyID'] ."'";
			$result_security = mysql_query($sql_security);
			$cnt_security = mysql_num_rows($result_security);
			if($cnt_security){
				$row_security = mysql_fetch_assoc($result_security);
				if($row_security['SecurityDeposit']!=""){
					$_SESSION['PRICE_CAL']['SECURITY_DEPOSITE'] = $row_security['SecurityDeposit'];
				}
				if($row_security['UnbrandSecurityDeposit']!=""){
					$_SESSION['PRICE_CAL']['UNBRAND_SECURITY_DEPOSITE'] = $row_security['UnbrandSecurityDeposit'];
				}				
				/*if($row_security['CleaningFee']!=""){
					$_SESSION['PRICE_CAL']['CLEANING_FEE'] = $row_security['CleaningFee'];
				}else{
					$_SESSION['PRICE_CAL']['CLEANING_FEE'] = 0;
				}*/
			}
			$_SESSION['PRICE_CAL']['SUB_TOTAL'] = $total ;
			$grandTotal = $_SESSION['PRICE_CAL']['SUB_TOTAL'] + $_SESSION['PRICE_CAL']['OCCUPANT_CHARGE'] + $_SESSION['PRICE_CAL']['CLEANING_FEE'];
			
			//check for Minimum Rental < 7
			$miniRentcharge = 0;
			if($countCheckDiff['days'] < 7 && $row_occu['ApplyMinRentalAmtResDtLessThn7DaysFrArr'] == 1){
				$miniRentcharge = $row_occu['MinRentalAmtResDtLessThn7DaysFrArr'];
			}else if($countCheckDiff['days'] >= 7 && $row_occu['ApplyMinRentalAmtResDtGrtThnEq7DaysFrArr'] == 1){
				$miniRentcharge = $row_occu['MinRentalAmtResDtGrtThnEq7DaysFrArr'];
			}
			if($grandTotal < $miniRentcharge){
				$grandTotal = $miniRentcharge;
			}
			$_SESSION['PRICE_CAL']['GRAND_TOTAL'] =$grandTotal;
			$_SESSION['PRICE_CAL']['FIRST_PAY'] = $grandTotal/2;
			$_SESSION['PRICE_CAL']['SECOND_PAY'] = $grandTotal/2;

			$_SESSION['PRICE_CAL']['PROPERTY_ID'] = $get_property['PropertyID'];
			$_SESSION['PRICE_CAL']['PROPERTY_NUMBER'] = $get_property['PropertyNumber'];
			
			$_SESSION['PRICE_CAL']['CHECK_IN'] = $chckin;
			$_SESSION['PRICE_CAL']['CHECK_OUT'] = $chckout;
			$_SESSION['PRICE_CAL']['OCCUPANTS'] = $occupants;
			$_SESSION['PRICE_CAL']['EVENT'] = $event;
			$_SESSION['PRICE_CAL']['DAY_COUNT'] = $countDateDiff['days'];

			$_SESSION['PRICE_CAL']['THIRD_PARTY_COMM'] = 0.00;
			$third_party_ex_arr = explode(',',$ex_prop);
			if(isset($_REQUEST['isthird_party']) && $_REQUEST['isthird_party']==1 && !$message && (!in_array($get_property['PropertyID'],$third_party_ex_arr) || !count($third_party_ex_arr))){
				$start_dt = date('Y-m-d H:i:s',strtotime($chckin));
				$end_dt = date('Y-m-d H:i:s',strtotime($chckout));
				$amounttocom = $grandTotal/$countDateDiff['days'];
				$third_comm = 0;
				$comm_dt_cnt = 0;
				while($start_dt < $end_dt){
					$current_commission = 0 ;
					$response = getCommissionDetail($start_dt,$get_property['PropertyID']);
					if($response['response']=='YES' && $response['commissionrate']){
						$current_commission = $amounttocom * ($response['commissionrate']/100) ;
						$comm_dt_cnt++;
					}
					$third_comm = $third_comm + $current_commission;
					$start_dt = date ("Y-m-d H:i:s", strtotime("+1 day", strtotime($start_dt)));
				}
				if($third_comm && $third_comm > 0 && $comm_dt_cnt ){
					$grandTotal = $grandTotal + $third_comm;
					$_SESSION['PRICE_CAL']['THIRD_PARTY_COMM'] = $third_comm;
					$_SESSION['PRICE_CAL']['FIRST_PAY'] = ($grandTotal)/2;
					$_SESSION['PRICE_CAL']['SECOND_PAY'] = ($grandTotal)/2;
				}else{
					$_SESSION['PRICE_CAL'] = array();
					//$message = "Property Not available for selected period.";
					$message = "Sorry, this property is not available on ".date('m/d/Y',strtotime($chckin))." - ".date('m/d/Y',strtotime($chckout)).". ";
					if($get_property['ThirdPartyPhone']){
						$message .= "Please call ".$get_property['ThirdPartyPhone']." for a comparable properties or change your date range.";
					}
				}
			}
			
			//echo '<br/>@total =>'.$total;
			//echo '<br/>@grandTotal =>'.$grandTotal;
			//exit;
			//echo ninemosreserv($_SESSION['chckin'],$_SESSION['chckout'],$_SESSION['propertyid']);
			//echo '<br/>@message =>'.$message;
			
			
		} // end of else if($num_chkavail > 0)
		
	} // end of if($chckin != "" && $chckout != "")


	if($message==""){
		$message="OK";
	}
	
	return "//***//".json_encode(array('msg' => $message,'grandTotal' => $grandTotal,'max_occupant' => $max_occupant));
}

/********Function Get Grand total Start HERE******/

function getGrandTotal($get_propertys,$param){
	
	//echo '@getprop=> <pre>';print_r($get_propertys);echo '</pre>';
	//echo '@param=> <pre>';print_r($param);echo '</pre>';exit;
	$chckin = date('Y-m-d',strtotime($param['chckin']));
	$chckout = date('Y-m-d',strtotime($param['chckout']));
	$serCheckout = date('Y-m-d',(strtotime($param['chckout']) - (86400)));
	$holidayCheckout = $serCheckout;
	$occupants = $param['occupants'];
	$event = $param['event'];
	$grandTotal = 0;
	$doNotDisplay ="";
	
	$num_chkavail = 0;
	
	if(!$num_chkavail)
	{
		$_SESSION['QUOTE_PROP'][$get_propertys['PropertyID']]['chckin'] 	= $chckin;
		$_SESSION['QUOTE_PROP'][$get_propertys['PropertyID']]['chckout'] 	= $chckout;
		$_SESSION['QUOTE_PROP'][$get_propertys['PropertyID']]['occupants']  = $occupants;
		$_SESSION['QUOTE_PROP'][$get_propertys['PropertyID']]['event']      = $event;
		$_SESSION['QUOTE_PROP'][$get_propertys['PropertyID']]['propertyid'] = $get_propertys['PropertyID'];
		$_SESSION['QUOTE_PROP'][$get_propertys['PropertyID']]['propertynumber'] = $get_propertys['PropertyNumber'];
		$_SESSION['QUOTE_PROP'][$get_propertys['PropertyID']]['propertyname'] = $get_propertys['PropertyName'];

		$_SESSION['QUOTE_PROP'][$get_propertys['PropertyID']]['bedrooms'] = $get_propertys['Bedrooms'];
		$_SESSION['QUOTE_PROP'][$get_propertys['PropertyID']]['baths'] = $get_propertys['Baths'];
		$_SESSION['QUOTE_PROP'][$get_propertys['PropertyID']]['sleeps'] = $get_propertys['SleepCount'];
		
		$aka='';
		if($get_propertys['OtherProperty'])
			$aka = ' AKA '.str_replace(',',', ',$get_propertys['OtherProperty']);											
		$_SESSION['QUOTE_PROP'][$get_propertys['PropertyID']]['aka'] = $aka;
		
		$discountName="";
		$isSatrudayPrem="";
		$isWeekdayDis="";
		$preDis="";
		$today = date('Y-m-d');
		$day = date('D', strtotime($chckin));
		$inDateDiff = dateDiff($today,$chckin);//diff bet today and checkin in year, month, day
		$countDateDiff = dateDiff($chckin,$chckout,true);//diff bet checkin and checkout in days
		$countCheckDiff = dateDiff($today,$chckin,true);//diff bet today and checkin in days
		
		$bookDayArr=array();
		$isMiniDollar=false;
		$isOccupantUpchrg=false;
		$isWSOP=false;

		//check for weekdays discount
		if($countDateDiff['days'] < 5 && !$event){
			$notWeekDay=array('fri','sat','sun');
			$checkintime = strtotime($chckin);
			for($i=0;$i < $countDateDiff['days']; $i++){
				$bookDayArr[$i]=strtolower(date('D',$checkintime));
				$checkintime = $checkintime + 86400;//+ 1 day seconds
			}

			if(!in_array($notWeekDay[0],$bookDayArr) && !in_array($notWeekDay[1],$bookDayArr) && !in_array($notWeekDay[2],$bookDayArr)){
				$discountName = "WeekDayDiscount";
				$isWeekdayDis = "WeekDayDiscount";
			}
		}
		
		//check for 9 month discount
		if(strtolower($day) != "sat" && ($inDateDiff['months'] >= 9 || $inDateDiff['years'] >= 1 ) && !$event){
			$discountName = "9MonthDiscount";
		}

		//check for saturday premium
		if($countDateDiff['days'] >= 7 && strtolower($day) == "sat"){
			$discountName = "SaturdayPremium";
			$isSatrudayPrem = "SaturdayPremium";
		}
		
		//check for chargable night, holidays
		$holidayArr=array();
		$sql_chrg ="SELECT * FROM holidaydiscountmst 
		WHERE ((HolidayStartDate <='".$chckin."'
		AND HolidayEndDate >= '".$holidayCheckout."') OR (HolidayStartDate >='".$chckin."'
		AND HolidayEndDate >= '".$holidayCheckout."' AND HolidayStartDate <= '".$holidayCheckout."') OR (HolidayStartDate <='".$chckin."'
		AND HolidayEndDate <= '".$holidayCheckout."' AND HolidayEndDate >= '".$chckin."') OR (HolidayStartDate >='".$chckin."'
		AND HolidayEndDate <= '".$holidayCheckout."')) AND 
		PropertyID = '".$get_propertys[PropertyID]."' AND IsRemoved='0' AND IsActive='1'";
		//echo '<br/>@sql_charg=>'.$sql_chrg ;
		$result_chrg = mysql_query($sql_chrg);
		$cnt_chrg = mysql_num_rows($result_chrg);
		if($cnt_chrg){
			$row_chrg = mysql_fetch_assoc($result_chrg);
			$holidayArr['MIN_NIGHT'] = $row_chrg['MinimumNights'];
			if($countDateDiff['days'] > $row_chrg['MinimumNights']){
				$holidayArr['MIN_NIGHT'] = $countDateDiff['days'];
			}

				$discountName = "ChargableNight";
		}

		$row_occu = "";
		//check for occupancy analysis, WSOP
		$WSOPArr=array();
		$WSOPArr['NORMAL_NIGHT'] = 0;
		$sql_occu="SELECT * FROM propertydiscountmst WHERE PropertyID = '".$get_propertys[PropertyID]."'";
		$result_occu = mysql_query($sql_occu);
		$cnt_occu = mysql_num_rows($result_occu);
		if($cnt_occu){
			$row_occu = mysql_fetch_assoc($result_occu);
			if($row_occu['WSOPReservationsOnly']== 1){
				if($row_occu['WSOPStartDate'] <= $chckin && $row_occu['WSOPEndDate'] >= $chckout){
					if($countDateDiff['days'] >= $row_occu['WSOPMinNights']){
						$WSOPArr['PER_NIGHT'] = $countDateDiff['days'];
						$isWSOP = true;
					}else{
						$doNotDisplay = 'OUTWSOP';
					}
				}else if($row_occu['WSOPStartDate'] >= $chckin && $row_occu['WSOPEndDate'] >= $chckout && $row_occu['WSOPStartDate'] <= $chckout ){ //
				
					$stDcho = dateDiff($row_occu['WSOPStartDate'],$chckout,true);
					if($stDcho['days'] >= $row_occu['WSOPMinNights']){
						$WSOPArr['PER_NIGHT'] = $stDcho['days'];
						$WSOPArr['NORMAL_NIGHT'] = $countDateDiff['days'] - $stDcho['days'];
						$isWSOP = true;
					}else{
						$doNotDisplay = 'OUTWSOP';
					}
				}else if($row_occu['WSOPStartDate'] <= $chckin && $row_occu['WSOPEndDate'] <= $chckout && $row_occu['WSOPEndDate'] >= $chckin ){
				
					$enDchi = dateDiff($chckin,$row_occu['WSOPEndDate'],true);
					if($enDchi['days'] >= $row_occu['WSOPMinNights']){
						$WSOPArr['PER_NIGHT'] = $enDchi['days'];
						$WSOPArr['NORMAL_NIGHT'] = $countDateDiff['days'] - $enDchi['days'];
						$isWSOP = true;
					}else{
						$doNotDisplay = 'OUTWSOP';
					}
				}else if($row_occu['WSOPStartDate'] >= $chckin && $row_occu['WSOPEndDate'] <= $chckout){

					$alDc = dateDiff($row_occu['WSOPStartDate'],$row_occu['WSOPEndDate'],true);
					if($alDc['days'] >= $row_occu['WSOPMinNights']){
						$WSOPArr['PER_NIGHT'] = $alDc['days'];
						$WSOPArr['NORMAL_NIGHT'] = $countDateDiff['days'] - $alDc['days'];
						$isWSOP = true;
					}else{
						$doNotDisplay = 'OUTWSOP';
					}
				}
			}
			/*if($row_occu['WSOPStartDate'] <= $chckin && $row_occu['WSOPEndDate'] >= $chckout && $row_occu['WSOPReservationsOnly'] == 1 ){
				if($row_occu['WSOPMinNights'] <= $countDateDiff['days']){
					$isWSOP = true;
				}else{
					$doNotDisplay = 'OUTWSOP';
				}
			}*/
		}		
		
		$isPremium=0;
		$isDiscount=0;
		if($isWSOP){
			$preDis .= '<br/>@WSOP';
			$normalNight = getSubPrice($WSOPArr['NORMAL_NIGHT'], $get_propertys[PropertyNumber]);
			$total = ($WSOPArr['PER_NIGHT'] * $row_occu['WSOPRatePerNight']) + $normalNight;
		}else if($discountName == 'ChargableNight'){
			$preDis .= '<br/>@ChargableNight';
			$minNight = getSubPrice($holidayArr['MIN_NIGHT'], $get_propertys[PropertyNumber]);

			if($row_chrg['IsPremiumOrDiscount'] == 1 ){//Premium
				if($row_chrg['PremiumOrDiscountType'] ==1){//In precent
					$isPremium = (($row_chrg['PremiumOrDiscountRate']/100) * $minNight);
					$minPrice = $minNight + $isPremium;
				}else if($row_chrg['PremiumOrDiscountType'] == 0){//In $
					$isPremium = $row_chrg['PremiumOrDiscountRate'];
					$minPrice = $minNight + $isPremium;
				}
			}else if($row_chrg['IsPremiumOrDiscount'] == 0){//Discount
				if($row_chrg['PremiumOrDiscountType'] ==1){//In percent
					$isDiscount = (($row_chrg['PremiumOrDiscountRate']/100) * $minNight);
					$minPrice = $minNight - $isDiscount;
				}else if($row_chrg['PremiumOrDiscountType'] == 0){//In $
					$isDiscount = $row_chrg['PremiumOrDiscountRate'];
					$minPrice = $minNight - $isDiscount;					
				}
			}
			if($isSatrudayPrem == 'SaturdayPremium' && $row_occu['SaturdayArrivalReservationAnd7OrMoreNightsPremium'] == 1){
				$preDis .= '<br/>@SaturdayPremium';
				$isPremium = $isPremium + $row_occu['SaturdayArrivalReservationAnd7OrMoreNightsPremiumRate'];
				$minPrice = $minPrice + $row_occu['SaturdayArrivalReservationAnd7OrMoreNightsPremiumRate'];
			}					
			//$total = $minPrice + $chargeNight;
			$total = $minPrice;
		}else if($discountName == 'SaturdayPremium' && $row_occu['SaturdayArrivalReservationAnd7OrMoreNightsPremium'] == 1){//Premium in $
			$preDis .= '<br/>@SaturdayPremium';
			$isPremium = $row_occu['SaturdayArrivalReservationAnd7OrMoreNightsPremiumRate'];
			$total = getSubPrice($countDateDiff['days'], $get_propertys[PropertyNumber]) + $isPremium;
		}else if($discountName == '9MonthDiscount' && $row_occu['9MonthAdvanceRes'] == 1){//Discount in percent
			if(($isWeekdayDis == 'WeekDayDiscount' && $row_occu['WeekdayDiscount'] == 1) && ($row_occu['WeeknightDiscountRate'] > $row_occu['9MonthDiscountRate'])){
				$preDis .= '<br/>@WeekDayDiscount';
				$subTotal = getSubPrice($countDateDiff['days'], $get_propertys[PropertyNumber]);
				$isDiscount = (($row_occu['WeeknightDiscountRate']/100) * $subTotal);
				$total = $subTotal - $isDiscount;					
			}else{
				$preDis .= '<br/>@9MonthDiscount';
				$subTotal = getSubPrice($countDateDiff['days'], $get_propertys[PropertyNumber]);
				$isDiscount = (($row_occu['9MonthDiscountRate']/100) * $subTotal);
				$total = $subTotal - $isDiscount;
			}
		}else if($discountName == 'WeekDayDiscount' && $row_occu['WeekdayDiscount'] == 1){//Discount in percent
			$preDis .= '<br/>@WeekDayDiscount';
			$subTotal = getSubPrice($countDateDiff['days'], $get_propertys[PropertyNumber]);
			$isDiscount = (($row_occu['WeeknightDiscountRate']/100) * $subTotal);
			$total = $subTotal - $isDiscount;
		}else{
			//get total price without discount/premium
			$preDis .= '<br/>@Without any discount Premium';
			$total = getSubPrice($countDateDiff['days'], $get_propertys[PropertyNumber]);				
		}
		$occupantCharge = 0;
		if($occupants > $row_occu['MaxOccupants'] && $occupants <= $row_occu['OccupancyExceedsLimit']){
			$preDis .= '<br/>@occupant EXCEEDS';
			$occupantCharge = ($occupants - $row_occu['MaxOccupants'] ) * $row_occu['PerPersonPremium'];
		}else if($occupants > $row_occu['OccupancyExceedsLimit'] && $row_occu['OccupancyExceedsLimit']){
			$preDis .= '<br/>@occupant LIMIT EXCEEDS';
			$message = "You can register property with maximum ".$row_occu['OccupancyExceedsLimit']." occupant.";
		}
		
		//get security deposite, cleaning fees
		$_SESSION['QUOTE_PROP'][$get_propertys['PropertyID']]['PRICE_CAL']['IS_DISCOUNT'] = $isDiscount;
		$_SESSION['QUOTE_PROP'][$get_propertys['PropertyID']]['PRICE_CAL']['IS_PREMIUM'] = $isPremium;				
		$_SESSION['QUOTE_PROP'][$get_propertys['PropertyID']]['PRICE_CAL']['OCCUPANT_CHARGE'] = $occupantCharge;
		$_SESSION['QUOTE_PROP'][$get_propertys['PropertyID']]['PRICE_CAL']['SECURITY_DEPOSITE'] = 250;
		$_SESSION['QUOTE_PROP'][$get_propertys['PropertyID']]['PRICE_CAL']['CLEANING_FEE'] = 0;
		$sql_security = "SELECT * FROM `po_surcharge` WHERE PropertyID = '".$get_propertys[PropertyID] ."'";
		$result_security = mysql_query($sql_security);
		$cnt_security = mysql_num_rows($result_security);
		if($cnt_security){
			$row_security = mysql_fetch_assoc($result_security);
			if($row_security['SecurityDeposit']!=""){
				$_SESSION['QUOTE_PROP'][$get_propertys['PropertyID']]['PRICE_CAL']['SECURITY_DEPOSITE'] = $row_security['SecurityDeposit'];
			}else{
				$_SESSION['QUOTE_PROP'][$get_propertys['PropertyID']]['PRICE_CAL']['SECURITY_DEPOSITE'] = 250;
			}
		}
		$_SESSION['QUOTE_PROP'][$get_propertys['PropertyID']]['PRICE_CAL']['SUB_TOTAL'] = $total ;
		$grandTotal = $_SESSION['QUOTE_PROP'][$get_propertys['PropertyID']]['PRICE_CAL']['SUB_TOTAL'] + $_SESSION['QUOTE_PROP'][$get_propertys['PropertyID']]['PRICE_CAL']['OCCUPANT_CHARGE'] + $_SESSION['QUOTE_PROP'][$get_propertys['PropertyID']]['PRICE_CAL']['CLEANING_FEE'];
		
		//check for Minimum Rental < 7
		$miniRentcharge = 0;
		if($countCheckDiff['days'] < 7 && $row_occu['ApplyMinRentalAmtResDtLessThn7DaysFrArr'] == 1){
			$miniRentcharge = $row_occu['MinRentalAmtResDtLessThn7DaysFrArr'];
		}else if($countCheckDiff['days'] >= 7 && $row_occu['ApplyMinRentalAmtResDtGrtThnEq7DaysFrArr'] == 1){
			$miniRentcharge = $row_occu['MinRentalAmtResDtGrtThnEq7DaysFrArr'];
		}
		if($grandTotal < $miniRentcharge){
			$grandTotal = $miniRentcharge;
		}
		$_SESSION['QUOTE_PROP'][$get_propertys['PropertyID']]['PRICE_CAL']['GRAND_TOTAL'] =$grandTotal;
		$_SESSION['QUOTE_PROP'][$get_propertys['PropertyID']]['PRICE_CAL']['FIRST_PAY'] = $grandTotal/2;
		$_SESSION['QUOTE_PROP'][$get_propertys['PropertyID']]['PRICE_CAL']['SECOND_PAY'] = $grandTotal/2;

		$_SESSION['QUOTE_PROP'][$get_propertys['PropertyID']]['PRICE_CAL']['PROPERTY_ID'] = $get_propertys[PropertyID];
		$_SESSION['QUOTE_PROP'][$get_propertys['PropertyID']]['PRICE_CAL']['PROPERTY_NUMBER'] = $get_propertys[PropertyNumber];
		
		$_SESSION['QUOTE_PROP'][$get_propertys['PropertyID']]['PRICE_CAL']['CHECK_IN'] = $chckin;
		$_SESSION['QUOTE_PROP'][$get_propertys['PropertyID']]['PRICE_CAL']['CHECK_OUT'] = $chckout;
		$_SESSION['QUOTE_PROP'][$get_propertys['PropertyID']]['PRICE_CAL']['OCCUPANTS'] = $occupants;
		$_SESSION['QUOTE_PROP'][$get_propertys['PropertyID']]['PRICE_CAL']['EVENT'] = $event;
		$_SESSION['QUOTE_PROP'][$get_propertys['PropertyID']]['PRICE_CAL']['DAY_COUNT'] = $countDateDiff['days'];
		$preDis .= '<br/>@total =>'.$total;
		$preDis .= '<br/>@grandTotal =>'.$grandTotal;
		$_SESSION['QUOTE_PROP'][$get_propertys['PropertyID']]['subtotal'] 	= $total;
		$_SESSION['QUOTE_PROP'][$get_propertys['PropertyID']]['grandtotal'] 	= $grandTotal;
		
		//echo $preDis;echo '@row_occu=> <pre>'; print_r($row_occu); echo '</pre>';
	} // end of else if($num_chkavail > 0)
	if($doNotDisplay == 'OUTWSOP' && isset($param['WSOP']) && $param['WSOP']==1)
		return 'OUTWSOP';
	return $grandTotal;
}
/********Function Get Grand total End HERE******/

function insertGoogleCalendarEvent($resrvation_id){
	if($resrvation_id){
		try{
			$resrvation_details = getReservationDetailFromId($resrvation_id);
			$calendar_id = getCalendarIdFromProperty($resrvation_details['PropertyID']);
			$row_credential = getGoogleCredential();
			$eventstartdate = date('Y-m-d',strtotime($resrvation_details['ArrivalDate']));
			$eventenddate = date('Y-m-d',(strtotime($resrvation_details['DepartureDate']) + 86400));
			
			$esd = $eventstartdate.' 00:00:00';
			$eed = $eventenddate.' 10:00:00';
			
			$pst = new DateTimeZone('America/Los_Angeles');
			$gmt = new DateTimeZone('GMT');

			$dates = new DateTime($esd, $pst);
			$dates->setTimezone($gmt);
			$eventstartdate = $dates->format('Y-m-d').'T'.$dates->format('H:i:s').'Z';
			
			$datee = new DateTime($eed, $pst);
			$datee->setTimezone($gmt);			
			$eventenddate = $datee->format('Y-m-d').'T'.$datee->format('H:i:s').'Z';
			
			//change as per client requirement to display full period from checkin to checkout on google calender
			//$eventenddate = date('Y-m-d',(strtotime($resrvation_details['DepartureDate']) + 172800)); // 86400 122400
			$tenantname = stripslashes($resrvation_details['FirstName']);
			if($resrvation_details['MiddleName'])
				$tenantname .= ' '.stripslashes($resrvation_details['MiddleName']);
			$tenantname .= ' '.stripslashes($resrvation_details['LastName']);
			$tenantemail = stripslashes($resrvation_details['Email']);
			$tenantphone = stripslashes($resrvation_details['TelephoneNumber']);
			$tenantdayphone = stripslashes($resrvation_details['DaytimePhone']);
			$tenantstaypurpose = stripslashes($resrvation_details['StayPurpose']);
			
			//Event Summery i.e. Event Title
			$summery = 'Reservation Period - '.$tenantname;
			
			//Event Description
			$description = "Tenant Details:\n\r Name: {$tenantname}\n\r Email: {$tenantemail}\n\r Phone: {$tenantphone}\n\r Day Phone: {$tenantdayphone}\n\r Stay Purpose: {$tenantstaypurpose}";

			include_once 'src/Google_Client.php';
			include_once 'src/contrib/Google_CalendarService.php';
			$client = new Google_Client();
			$client->setClientId($row_credential['ClientId']);
			$client->setClientSecret($row_credential['ClientSecret']);
			$client->setRedirectUri($row_credential['RedirectUri']);
			$client->setDeveloperKey($row_credential['DeveloperKey']);
			
			$cal = new Google_CalendarService($client);
			
			$client->setAccessToken($row_credential['AccessToken']);
			if($client->getAccessToken()) {
				$eventendtime = new Google_EventDateTime();
				$eventendtime->setDateTime($eventenddate);
				
				$eventstarttime = new Google_EventDateTime();
				$eventstarttime->setDateTime($eventstartdate);	
				
				$addevent = new Google_Event();
				$addevent->setEnd($eventendtime);
				$addevent->setStart($eventstarttime);
				$addevent->setDescription($description);
				$addevent->setSummary($summery);
				
				$eventadresp = $cal->events->insert($calendar_id,$addevent);
				updateEventId($resrvation_id, $eventadresp['id']);
			}
		}catch(Exception $e){
			/* echo '<pre>';
			print_r($e);
			echo '</pre>';
			exit; */
			//log error for debbug
			$response = json_encode($e);
			$insert_errsql = "INSERT INTO debug_calendarerr SET Response='".mysql_real_escape_string($response)."', ReservePropertyId='".$resrvation_id."'";
			$result_errsql = mysql_query($insert_errsql);
		}
	}
}

function getReservationDetailFromId($resrvation_id){
	$sql = "SELECT * FROM reservepropertymst WHERE ReservePropertyId='".$resrvation_id."'";
	$result = mysql_query($sql);
	$row = mysql_fetch_assoc($result);
	return $row;
}

function getGoogleCredential(){
	$sql = "SELECT * FROM calendar_credential WHERE CalendarCredId='1'";
	$result = mysql_query($sql);
	$row = mysql_fetch_assoc($result);
	return $row;
}

function getCalendarIdFromProperty($propertyid){
	$sql = "SELECT Calendar_Id FROM propertymst WHERE PropertyNumber='".$propertyid."'";
	$result = mysql_query($sql);
	$row = mysql_fetch_assoc($result);
	return $row['Calendar_Id'];
}

function updateEventId($resrvation_id, $eventid){
	if($eventid && $resrvation_id){
		$sql = "UPDATE reservepropertymst SET GoogleEventId='".$eventid."' WHERE ReservePropertyId ='".$resrvation_id."'";
		$result = mysql_query($sql);	
	}
}

function isDateAvailable($propertyid,$adate,$fdate){
	$isbusy = 0;
	$bsdate = '';
	$bedate = '';
	try{
		$calendar_id = getCalendarIdFromProperty($propertyid);
		$stdatecp = strtotime($adate);
		$arrivaldate = date('Y-m-d',$stdatecp);
		$departuredate = date('Y-m-d',(strtotime($fdate) - 86400));
		$row_credential = getGoogleCredential();
		
		include_once 'src/Google_Client.php';
		include_once 'src/contrib/Google_CalendarService.php';
		$client = new Google_Client();
		$client->setClientId($row_credential['ClientId']);
		$client->setClientSecret($row_credential['ClientSecret']);
		$client->setRedirectUri($row_credential['RedirectUri']);
		$client->setDeveloperKey($row_credential['DeveloperKey']);
		
		$cal = new Google_CalendarService($client);
		
		$client->setAccessToken($row_credential['AccessToken']);
		if($client->getAccessToken()) {	
			$freebusy = new Google_FreeBusyRequest();
			$freebusy->setCalendarExpansionMax(1);
			$freebusy->setGroupExpansionMax(1);
			$freebusy->setItems(array(array('id'=>$calendar_id)));
			$freebusy->setTimeMax($departuredate.'T23:59:59Z');
			$freebusy->setTimeMin($arrivaldate.'T00:00:00Z');
			$freebusy->setTimeZone('');  
			$fcalList = $cal->freebusy->query($freebusy);
			$freebusylist = $fcalList['calendars'][$calendar_id]['busy'];
			if(count($freebusylist)){
				/* $isbusy = 1;
				$bsdate = $freebusylist[0]['start'];
				$bedate = $freebusylist[0]['end']; */
				$response = $freebusylist;
				/* foreach($response as $respd){
					$startdc = $respd['start'];
					$enddc = $respd['end'];
					$startt = strtotime($respd['start']); 
					$endtt = strtotime($respd['end']); //1413712800
					$gcsdc = date('Y-m-d',$startt); //google calendar start date compare
					$gcedc = date('Y-m-d',$endtt); //google calendar end date compare
					$gcstt = strtotime($gcsdc);
					$gcett = strtotime($gcedc); //strtotime of Y-m-d formate 
					if(($stdatecp != $endtt && $stdatecp != $gcett) || ($stdatecp == $gcett && $stdatecp == $endtt) || ($gcstt == $gcett && $startt == $endtt) ){ //It is not end/checkout date
						$isbusy = 1;
						$bsdate = $respd['start'];
						$bedate = $respd['end'];
					}
				} */
				foreach($response as $respd){
					$startdc = $respd['start'];
					$enddc = $respd['end'];
					$start_tis = strtotime($respd['start']); 
					$end_tis = strtotime($respd['end']); //1413712800
					$gc_bsd = date('Y-m-d',$start_tis); //google calendar start date compare
					$gc_bed = date('Y-m-d',$end_tis); //google calendar end date compare
					$gc_sdtis = strtotime($gc_bsd);
					$gc_edtis = strtotime($gc_bed); //strtotime of Y-m-d formate 
					
					$gcdecmp = $gc_bed.'T23:59:59Z';
					$gcsdcmp = strstr($startdc,'T');
					$gc_etstp = strstr($enddc,'T');
					$flag = 0;
					if($stdatecp != $end_tis && $stdatecp != $gc_edtis && $gcsdcmp == 'T00:00:00Z' && $gc_etstp == 'T23:59:59Z'){
						$flag = 1;
					}elseif($stdatecp == $gc_edtis && $stdatecp == $end_tis ){
						$flag = 2;
					}elseif($gc_sdtis == $gc_edtis && $start_tis == $end_tis ){
						$flag = 3;
					}elseif($gc_sdtis <= $gc_edtis && $enddc ==$gcdecmp ){
						$gd_ddswc = dateDiff($startdc,$enddc,true);
						if($gd_ddswc['days'] || (!$gd_ddswc['days'] && $gcsdcmp == 'T00:00:00Z')){
							$flag = 4;
						}elseif(!$gd_ddswc['days'] && ($gcsdcmp == 'T08:00:00Z' || $gcsdcmp == 'T07:00:00Z') && $gc_etstp == 'T23:59:59Z'){ // T07:00:00Z for PDT
							$flag = 6;
						}
					}elseif($gc_sdtis <= $gc_edtis && $gcsdcmp == $gc_etstp && $gcsdcmp != 'T00:00:00Z' && $gc_etstp != 'T00:00:00Z' && $gcsdcmp != 'T23:59:59Z' && $gc_etstp != 'T23:59:59Z' ){
						$flag = 5;
					}
					
					if($flag){
						$isbusy = 1;
						$bsdate = $respd['start'];
						$bedate = $respd['end'];
					}
				}				
			}
		}
	}catch(Exception $e){
		/* echo '<pre>';
		print_r($e);
		echo '</pre>';
		exit; */
	}
	return array('busy'=>$isbusy,'bsdate'=>$bsdate,'bedate'=>$bedate);
}

function getCommissionDetail($datetocmp,$propid){
	$response = 'NA';
	$commisionrate= '0%';
	$sql_check = "SELECT * FROM thirdpartycomm_mst WHERE PropertyId='".$propid."' AND CommissionDate='".$datetocmp."'";
	$result_check = mysql_query($sql_check);
	$cnt_check = mysql_num_rows($result_check);
	if($cnt_check){
		$row_check = mysql_fetch_assoc($result_check);
		$response = 'NO';
		if($row_check['Commissionable']){
			$response = 'YES';
		}
		$commisionrate= $row_check['CommissionRate'];
	}
	return array('response'=>$response,'commissionrate'=>$commisionrate);
}

function deleteGoogleCalendarEvent($resrvation_id){
	if($resrvation_id){
		try{
			$resrvation_details = getReservationDetailFromId($resrvation_id);
			
			//Delete Generated Pins on secured showing.
			//deletePinsFromSS($resrvation_details);
			
			$calendar_id = getCalendarIdFromProperty($resrvation_details['PropertyID']);
			$row_credential = getGoogleCredential();
			if($resrvation_details['GoogleEventId']){
				include_once 'src/Google_Client.php';
				include_once 'src/contrib/Google_CalendarService.php';
				$client = new Google_Client();
				$client->setClientId($row_credential['ClientId']);
				$client->setClientSecret($row_credential['ClientSecret']);
				$client->setRedirectUri($row_credential['RedirectUri']);
				$client->setDeveloperKey($row_credential['DeveloperKey']);
				
				$cal = new Google_CalendarService($client);
				
				$client->setAccessToken($row_credential['AccessToken']);
				if($client->getAccessToken()) {
					$eventdelresp = $cal->events->delete($calendar_id,$resrvation_details['GoogleEventId']);
					updateEventId($resrvation_id, '');
				}
			}
		}catch(Exception $e){
			/* echo '<pre>';
			print_r($e);
			echo '</pre>';
			exit; */
		}
	}
}

/***** Call Delete Pin API From Secured Showing for cancel reservation.
*
*@purpose call delete pins api for cancel reservations
*@param $res_det reservation details
*
*****/
function deletePinsFromSS($res_det){
	$SS_url = SS_DELETE_PIN_URL;
	$data = array(
		'ReservePropertyId' => $res_det['ReservePropertyId'], 
		'FirstName' => $res_det['FirstName'], 
		'MiddleName' => $res_det['MiddleName'], 
		'LastName' => $res_det['LastName'], 
		'TelephoneNumber' => $res_det['TelephoneNumber'], 
		'DaytimePhone' => $res_det['DaytimePhone'], 
		'TelephoneCountryCode' => $res_det['TelephoneCountryCode'], 
		'DaytimeCountryCode' => $res_det['DaytimeCountryCode'], 
		'PropertyID' => $res_det['PropertyID'], 
		'ArrivalDate' => $res_det['ArrivalDate'], 
		'ArrivalTime' => $res_det['ArrivalTime'], 
		'DepartureDate' => $res_det['DepartureDate'], 
		'NightCount' => $res_det['NightCount'], 
		'StayPurpose' => $res_det['StayPurpose'], 
		'Email' => $res_det['Email'], 
		'accessDoorCode' => $res_det['accessDoorCode'], 
		'garageCode' => $res_det['garageCode'], 
		'alarmCode' => $res_det['alarmCode'], 
		'gateCode' => $res_det['gateCode'], 
		'ContractStatus' => $res_det['ContractStatus']
	);
	$fields = http_build_query($data);
	
	$ch = curl_init($SS_url);
	curl_setopt($ch, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
	curl_setopt($ch, CURLOPT_POSTFIELDS, $fields); // use HTTP POST to send form data
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // uncomment this line if you get no gateway response. ###
	$resp = curl_exec($ch); //execute post and get results
	curl_close ($ch);
	
	/* echo SS_DELETE_PIN_URL;
	echo '<pre>';
	print_r($resp);
	echo '</pre>';die; */
}

/**/
function getIpAddressServer(){
	$ip = $_SERVER['REMOTE_ADDR'];

	if(!empty($_SERVER['HTTP_CLIENT_IP'])){
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	}else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}

	return $ip;
	//return '166.137.209.19'; //for bad tenant test
}

if($_REQUEST['action']){
	switch($_REQUEST['action']){
		case 'getPriceAvailability' :
			echo getPriceAvailability();exit;
		break;
	}
}



/**** END INCULDE FILE ******/ 

/********************* Calculate Total amount ************************************/

		$check_in = $start_date; //"03/10/2016";
		$stop_date = @str_replace('"','',$stop_date);
		 $check_out = trim($stop_date); //"03/14/2016";
		 $property_numbre =  $property_id; // 3709;
		 $occupants_count =  $total_guest; //2;
		 $event_flag = 0;  //means no event 
		
		/*echo $check_in = $start_date; //"03/10/2016";
		$stop_date = @str_replace('"','',$stop_date);
		echo $check_out = trim($stop_date); //"03/14/2016";
		echo $property_numbre =  $property_id; // 3709;
		echo $occupants_count =  $total_guest; //2;
		echo $event_flag = 0;  //means no event 
		*/
		/*
		echo $check_in =  "07/05/2016";
		echo $check_out = "07/07/2016";
		echo $property_numbre =   475252;
		echo $occupants_count =  2;
		echo $event_flag = 0;  //means no event 
		*/
		
		$property		= mysql_query("select * from propertymst where PropertyNumber=".$property_numbre." OR OtherProperty like '%".$property_numbre."%'");
		
		$get_property	= mysql_fetch_array($property);
	  if($get_property)
	{	
		$data['lvn_property_number'] = $get_property[PropertyNumber]; 
		
		$chckin 	= date('Y-m-d', strtotime($check_in));
		$chckout 	= date('Y-m-d', strtotime($check_out));
		$serCheckout = date('Y-m-d',(strtotime($check_out) - (86400)));
		$holidayCheckout = $serCheckout;
		$occupants 	= $occupants_count;
		$event 		= $event_flag;
		$propert_avail = 1;
		$message ="";

		if($chckin != "" && $chckout != "" ) //&& $message ==""
		{
			
			#QUERY TO CHECK AVAILABILTY OF PROPERTY
			
			
			$sql_qry="SELECT * FROM reservepropertymst 
			WHERE 
			(('".$chckin."' BETWEEN ArrivalDate AND DepartureDate) 
			OR 
			('".$serCheckout."' BETWEEN ArrivalDate AND DepartureDate) 
			OR 
			('".$chckin."' <= ArrivalDate AND '".$serCheckout."' >= DepartureDate)) AND (ContractStatus != '3') AND 
			(PropertyID = '".$get_property[PropertyNumber]."')";			
			$sql_chkavail = mysql_query($sql_qry) 
			or die("err".mysql_error());
			//echo '<br/>@sql=> '.$sql_qry;
			$num_chkavail = mysql_num_rows($sql_chkavail);
			if($get_property['Calendar_Id'] && !$num_chkavail){
				$check_availcal = isDateAvailable($get_property['PropertyNumber'],$chckin,$chckout);
				if($check_availcal['busy']){
					$propert_avail = 0;
					$showadate = date('m/d/Y',strtotime($check_availcal['bsdate']));
					$showddate = date('m/d/Y',strtotime($check_availcal['bedate']));
					$message = "Sorry, this property is not available on ".$showadate." - ".$showddate.". Please call 1 (424) 260-7113 for a comparable properties <br/>or change your date range.";
					$data['grandTotal'] = $message;
				}

			}
			
			if($num_chkavail > 0)
			{
				
				$final_date1 = strtotime($check_out);
				$adate1 = strtotime($check_in);
					while($row_arrival=mysql_fetch_array($sql_chkavail))
					{
						$arrival_date	= strtotime($row_arrival['ArrivalDate']);
						$departure_date	= strtotime($row_arrival['DepartureDate']);
						
						if(($final_date1>=$arrival_date &&  $final_date1<=$departure_date) || ($adate1>=$arrival_date &&  $adate1<=$departure_date) || ($adate1<$arrival_date &&  $final_date1>$departure_date))
						{	
							if($final_date1>=$arrival_date &&  $final_date1<=$departure_date)
							{
								$timestamp2	= strtotime($row_arrival['ArrivalDate']);
								$timestamp3	= strtotime($row_arrival['DepartureDate']);
								$ndate		= date("m/d/Y",$timestamp2);
								$tdate		= date("m/d/Y",$timestamp3);
								$showadate	= $ndate;
								$showddate	= $tdate;
							}
							
							if($adate1 >= $arrival_date && $adate1 <= $departure_date)
							{
								 $timestamp2	= strtotime($row_arrival['ArrivalDate']);
								 $timestamp3	= strtotime($row_arrival['DepartureDate']);
								$ndate			= date("m/d/Y",$timestamp2);
								$tdate			= date("m/d/Y",$timestamp3);
								$showadate		= $ndate;
								$showddate		= $tdate;
							}
													
							 if($adate1 < $arrival_date && $final_date1>$departure_date)
							{
								$timestamp2		= strtotime($row_arrival['ArrivalDate']);
								$timestamp3		= strtotime($row_arrival['DepartureDate']);
								$ndate			= date("m/d/Y",$timestamp2);
								$tdate			= date("m/d/Y",$timestamp3);
								$showadate		= $ndate;
								$showddate		= $tdate;
							}
						}
					}		
			
				//$message = "Property not available during period '".$showadate."' to '".$showddate."'";
				//$message = "Property Not Available for those exact dates it already booked for '".$showadate."' to '".$showddate."' period, please try for another date";
				//echo $message = "Sorry, this property is not available on ".$showadate." - ".$showddate.". Please call 1 (424) 260-7113 for a comparable properties <br/>or change your date range.";
				
				$data['grandTotal'] = "Sorry, this property is not available on ".$showadate." - ".$showddate.". Please call 1 (424) 260-7113 for a comparable properties <br/>or change your date range.";
				//print_r($data['grandTotal']);
				//echo "sdfsd"exit;
				
			} // end of if($num_chkavail > 0)
			elseif($propert_avail)
			{
				
				
				
				
				//echo "::".$chckin.", ".$chckout.dateDifference($chckin,$chckout);			
				//$day=array('mon','tue','wed','thu');
				$discountName="";
				$isSatrudayPrem="";
				$isWeekdayDis="";
				$preDis="";
				$today = date('Y-m-d');
				$day = date('D', strtotime($chckin));
				$inDateDiff = dateDiff($today,$chckin);//diff bet today and checkin in year, month, day
				$countDateDiff = dateDiff($chckin,$chckout,true);//diff bet checkin and checkout in days
				$countCheckDiff = dateDiff($today,$chckin,true);//diff bet today and checkin in days
				
				$bookDayArr=array();
				$isMiniDollar=false;
				$isOccupantUpchrg=false;
				$isWSOP=false;
				
				
				//check for weekdays discount
				if($countDateDiff['days'] < 5 && !$event){
					$notWeekDay=array('fri','sat','sun');
					$checkintime = strtotime($chckin);
					for($i=0;$i < $countDateDiff['days']; $i++){
						$bookDayArr[$i]=strtolower(date('D',$checkintime));
						$checkintime = $checkintime + 86400;//+ 1 day seconds
					}
					//echo '<br/>@checkin=>'.$chckin.' day is =>'.date('D',strtotime($chckin));print_r($bookDayArr);//print_r($notWeekDay);
					if(!in_array($notWeekDay[0],$bookDayArr) && !in_array($notWeekDay[1],$bookDayArr) && !in_array($notWeekDay[2],$bookDayArr)){
						$discountName = "WeekDayDiscount";
						$isWeekdayDis = "WeekDayDiscount";
					}
				}
				
				//check for 9 month discount
				if(strtolower($day) != "sat" && ($inDateDiff['months'] >= 9 || $inDateDiff['years'] >= 1 ) && !$event){
					//ninemonthreservation($_SESSION['chckin'],$_SESSION['chckout'],$_SESSION['propertyid']);
					$discountName = "9MonthDiscount";
				}

				//check for saturday premium
				if($countDateDiff['days'] >= 7 && strtolower($day) == "sat"){
					$discountName = "SaturdayPremium";
					$isSatrudayPrem = "SaturdayPremium";
				}
				
				//check for chargable night, holidays
				$holidayArr=array();
				$sql_chrg ="SELECT * FROM holidaydiscountmst 
				WHERE ((HolidayStartDate <='".$chckin."'
				AND HolidayEndDate >= '".$holidayCheckout."') OR (HolidayStartDate >='".$chckin."'
				AND HolidayEndDate >= '".$holidayCheckout."' AND HolidayStartDate <= '".$holidayCheckout."') OR (HolidayStartDate <='".$chckin."'
				AND HolidayEndDate <= '".$holidayCheckout."' AND HolidayEndDate >= '".$chckin."') OR (HolidayStartDate >='".$chckin."'
				AND HolidayEndDate <= '".$holidayCheckout."')) AND 
				PropertyID = '".$get_property[PropertyID]."' AND IsRemoved='0' AND IsActive='1'";
				//echo '<br/>@sql_charg=>'.$sql_chrg ;
				$result_chrg = mysql_query($sql_chrg);
				$cnt_chrg = mysql_num_rows($result_chrg);
				if($cnt_chrg){
					$row_chrg = mysql_fetch_assoc($result_chrg);
					$holidayArr['MIN_NIGHT'] = $row_chrg['MinimumNights'];
					if($countDateDiff['days'] > $row_chrg['MinimumNights']){
						$holidayArr['MIN_NIGHT'] = $countDateDiff['days'];
					}
					/*if($row_chrg['HolidayStartDate'] <= $chckin && $row_chrg['HolidayEndDate'] >= $holidayCheckout){
						$holidayArr['CHARGE_NIGHT'] = ($countDateDiff['days'] > $holidayArr['MIN_NIGHT'])?($countDateDiff['days'] - $holidayArr['MIN_NIGHT']):0;
					
					}else if($row_chrg['HolidayStartDate'] >= $chckin && $row_chrg['HolidayEndDate'] >= $holidayCheckout && $row_chrg['HolidayStartDate'] <= $holidayCheckout ){ //
					
						$stDcho = dateDiff($row_chrg['HolidayStartDate'],$holidayCheckout,true);
						if($stDcho['days'] >= $row_chrg['MinimumNights'])
							$holidayArr['CHARGE_NIGHT'] = $countDateDiff['days'] - $holidayArr['MIN_NIGHT'];
						else				
							$holidayArr['CHARGE_NIGHT'] = $countDateDiff['days'] - $stDcho['days'];
							
					}else if($row_chrg['HolidayStartDate'] <= $chckin && $row_chrg['HolidayEndDate'] <= $holidayCheckout && $row_chrg['HolidayEndDate'] >= $chckin ){
					
						$enDchi = dateDiff($chckin,$row_chrg['HolidayEndDate'],true);
						
						if($enDchi['days'] >= $row_chrg['MinimumNights'])
							$holidayArr['CHARGE_NIGHT'] = $countDateDiff['days'] - $holidayArr['MIN_NIGHT'];
						else				
							$holidayArr['CHARGE_NIGHT'] = $countDateDiff['days'] - $enDchi['days'];
							
					}else if($row_chrg['HolidayStartDate'] >= $chckin && $row_chrg['HolidayEndDate'] <= $holidayCheckout){
						$holidayArr['CHARGE_NIGHT'] = ($countDateDiff['days'] > $holidayArr['MIN_NIGHT'])?($countDateDiff['days'] - $holidayArr['MIN_NIGHT']):0;
					}*/
					//if($countDateDiff['days'] >= $row_chrg['MinimumNights']){
						$discountName = "ChargableNight";
					//}
					//print_r($row_chrg);
				}

				
				//check for minimum dollar analysis
				/*if($countCheckDiff['days'] <= 7){
					$isMiniDollar=true;
				}*/
				//check for occupancy analysis, WSOP
				$WSOPArr=array();
				$WSOPArr['NORMAL_NIGHT'] = 0;
				$sql_occu="SELECT * FROM propertydiscountmst WHERE PropertyID = '".$get_property[PropertyID]."'";
				$result_occu = mysql_query($sql_occu);
				$cnt_occu = mysql_num_rows($result_occu);
				if($cnt_occu){
					$row_occu = mysql_fetch_assoc($result_occu);
					/*if($occupants > $row_occu['MaxOccupants']){
						$isOccupantUpchrg = true;
					}*/
					/*if($row_occu['WSOPStartDate'] <= $chckin && $row_occu['WSOPEndDate'] >= $chckout && $row_occu['WSOPMinNights'] <= $countDateDiff['days'] && $row_occu['WSOPReservationsOnly'] == 1 ){
						$isWSOP = true;
					}*/
					$varw="";
					if($row_occu['WSOPReservationsOnly']== 1){
						if($row_occu['WSOPStartDate'] <= $chckin && $row_occu['WSOPEndDate'] >= $chckout){
							if($countDateDiff['days'] >= $row_occu['WSOPMinNights']){
								$WSOPArr['PER_NIGHT'] = $countDateDiff['days'];
								$isWSOP = true;
							}
							$varw .= '<br/>@LINE:589 <br/>';
						}else if($row_occu['WSOPStartDate'] >= $chckin && $row_occu['WSOPEndDate'] >= $chckout && $row_occu['WSOPStartDate'] <= $chckout ){ //
						
							$stDcho = dateDiff($row_occu['WSOPStartDate'],$chckout,true);
							if($stDcho['days'] >= $row_occu['WSOPMinNights']){
								$WSOPArr['PER_NIGHT'] = $stDcho['days'];
								$WSOPArr['NORMAL_NIGHT'] = $countDateDiff['days'] - $stDcho['days'];
								$isWSOP = true;
								$varw .= '<br/>@LINE:597 <br/>';
							}
						}else if($row_occu['WSOPStartDate'] <= $chckin && $row_occu['WSOPEndDate'] <= $chckout && $row_occu['WSOPEndDate'] >= $chckin ){
						
							$enDchi = dateDiff($chckin,$row_occu['WSOPEndDate'],true);
							if($enDchi['days'] >= $row_occu['WSOPMinNights']){
								$WSOPArr['PER_NIGHT'] = $enDchi['days'];
								$WSOPArr['NORMAL_NIGHT'] = $countDateDiff['days'] - $enDchi['days'];
								$isWSOP = true;
								$varw .= '<br/>@LINE:606 <br/>';
							}
						}else if($row_occu['WSOPStartDate'] >= $chckin && $row_occu['WSOPEndDate'] <= $chckout){

							$alDc = dateDiff($row_occu['WSOPStartDate'],$row_occu['WSOPEndDate'],true);
							if($alDc['days'] >= $row_occu['WSOPMinNights']){
								$WSOPArr['PER_NIGHT'] = $alDc['days'];
								$WSOPArr['NORMAL_NIGHT'] = $countDateDiff['days'] - $alDc['days'];
								$isWSOP = true;
								$varw .= '<br/>@LINE:615 <br/>';
							}
						}
					}
					//echo '<br/>@WSOPStartDate=> '.$row_occu['WSOPStartDate'];
					//echo '<br/>@WSOPEndDate=> '.$row_occu['WSOPEndDate'];
					//echo '<br/>@chckin=> '.$chckin;
					//echo '<br/>@holidayCheckout=> '.$holidayCheckout;
					//echo '<br/>@chckout=> '.$chckout;
					//echo $varw ;
				}		
				
				/*echo '<br/>@Discount Rate =>'.$discountName;
				echo '@holidayArray/ ChargeNight =><pre>';
				print_r($holidayArr);
				echo '</pre>';*/
				$isPremium=0;
				$isDiscount=0;
				if($isWSOP){
					$preDis .= '<br/>@WSOP';
					$normalNight = getSubPrice($WSOPArr['NORMAL_NIGHT'], $get_property[PropertyNumber]);
					$total = ($WSOPArr['PER_NIGHT'] * $row_occu['WSOPRatePerNight']) + $normalNight;
				}else if($discountName == 'ChargableNight'){
					$preDis .= '<br/>@ChargableNight';
					$minNight = getSubPrice($holidayArr['MIN_NIGHT'], $get_property[PropertyNumber]);
					//$chargeNight = getSubPrice($holidayArr['CHARGE_NIGHT'], $get_property[PropertyNumber]);
					if($row_chrg['IsPremiumOrDiscount'] == 1 ){//Premium
						if($row_chrg['PremiumOrDiscountType'] ==1){//In precent
							$isPremium = (($row_chrg['PremiumOrDiscountRate']/100) * $minNight);
							$minPrice = $minNight + $isPremium;
						}else if($row_chrg['PremiumOrDiscountType'] == 0){//In $
							$isPremium = $row_chrg['PremiumOrDiscountRate'];
							$minPrice = $minNight + $isPremium;
						}
					}else if($row_chrg['IsPremiumOrDiscount'] == 0){//Discount
						if($row_chrg['PremiumOrDiscountType'] ==1){//In percent
							$isDiscount = (($row_chrg['PremiumOrDiscountRate']/100) * $minNight);
							$minPrice = $minNight - $isDiscount;
						}else if($row_chrg['PremiumOrDiscountType'] == 0){//In $
							$isDiscount = $row_chrg['PremiumOrDiscountRate'];
							$minPrice = $minNight - $isDiscount;					
						}
					}
					if($isSatrudayPrem == 'SaturdayPremium' && $row_occu['SaturdayArrivalReservationAnd7OrMoreNightsPremium'] == 1){
						$preDis .= '<br/>@SaturdayPremium';
						$isPremium = $isPremium + $row_occu['SaturdayArrivalReservationAnd7OrMoreNightsPremiumRate'];
						$minPrice = $minPrice + $row_occu['SaturdayArrivalReservationAnd7OrMoreNightsPremiumRate'];
					}					
					//$total = $minPrice + $chargeNight;
					$total = $minPrice;
				}else if($discountName == 'SaturdayPremium' && $row_occu['SaturdayArrivalReservationAnd7OrMoreNightsPremium'] == 1){//Premium in $
					$preDis .= '<br/>@SaturdayPremium';
					$isPremium = $row_occu['SaturdayArrivalReservationAnd7OrMoreNightsPremiumRate'];
					$total = getSubPrice($countDateDiff['days'], $get_property[PropertyNumber]) + $isPremium;
				}else if($discountName == '9MonthDiscount' && $row_occu['9MonthAdvanceRes'] == 1){//Discount in percent
					if(($isWeekdayDis == 'WeekDayDiscount' && $row_occu['WeekdayDiscount'] == 1) && ($row_occu['WeeknightDiscountRate'] > $row_occu['9MonthDiscountRate'])){
						$preDis .= '<br/>@WeekDayDiscount';
						$subTotal = getSubPrice($countDateDiff['days'], $get_property[PropertyNumber]);
						$isDiscount = (($row_occu['WeeknightDiscountRate']/100) * $subTotal);
						$total = $subTotal - $isDiscount;					
					}else{
						$preDis .= '<br/>@9MonthDiscount';
						$subTotal = getSubPrice($countDateDiff['days'], $get_property[PropertyNumber]);
						$isDiscount = (($row_occu['9MonthDiscountRate']/100) * $subTotal);
						$total = $subTotal - $isDiscount;
					}
				}else if($discountName == 'WeekDayDiscount' && $row_occu['WeekdayDiscount'] == 1){//Discount in percent
					$preDis .= '<br/>@WeekDayDiscount';
					$subTotal = getSubPrice($countDateDiff['days'], $get_property[PropertyNumber]);
					$isDiscount = (($row_occu['WeeknightDiscountRate']/100) * $subTotal);
					$total = $subTotal - $isDiscount;
				}else{
					//get total price without discount/premium
					$preDis .= '<br/>@Without any discount Premium';
					$total = getSubPrice($countDateDiff['days'], $get_property[PropertyNumber]);				
				}
				$occupantCharge = 0;
				if($occupants > $row_occu['MaxOccupants'] && $occupants <= $row_occu['OccupancyExceedsLimit']){
					$preDis .= '<br/>@occupant EXCEEDS';
					$occupantCharge = ($occupants - $row_occu['MaxOccupants'] ) * $row_occu['PerPersonPremium'];
				}else if($occupants > $row_occu['OccupancyExceedsLimit'] && $row_occu['OccupancyExceedsLimit']){
					$preDis .= '<br/>@occupant LIMIT EXCEEDS';
					$message = "You can register property with maximum ".$row_occu['OccupancyExceedsLimit']." occupant.";
				}
				
				//get security deposite, cleaning fees
				
				/* comment by abhijit 
				$_SESSION['PRICE_CAL']['IS_DISCOUNT'] = $isDiscount;
				$_SESSION['PRICE_CAL']['IS_PREMIUM'] = $isPremium;				
				$_SESSION['PRICE_CAL']['OCCUPANT_CHARGE'] = $occupantCharge;
				$_SESSION['PRICE_CAL']['SECURITY_DEPOSITE'] = 250;
				$_SESSION['PRICE_CAL']['CLEANING_FEE'] = 0;
				*/
				
				$IS_DISCOUNT = $isDiscount;
				$IS_PREMIUM = $isPremium;
				$OCCUPANT_CHARGE = $occupantCharge;
				$SECURITY_DEPOSITE = 250;
				$CLEANING_FEE = 0;
				
				
				$sql_security = "SELECT * FROM `po_surcharge` WHERE PropertyID = '".$get_property[PropertyID] ."'";
				$result_security = mysql_query($sql_security);
				$cnt_security = mysql_num_rows($result_security);
				if($cnt_security){
					$row_security = mysql_fetch_assoc($result_security);
					if($row_security['SecurityDeposit']!=""){
						// comment $_SESSION['PRICE_CAL']['SECURITY_DEPOSITE'] = $row_security['SecurityDeposit'];
						$SECURITY_DEPOSITE = $row_security['SecurityDeposit'];
					}else{
						// comment $_SESSION['PRICE_CAL']['SECURITY_DEPOSITE'] = 250;
						$SECURITY_DEPOSITE = 250;
					}
					/*if($row_security['CleaningFee']!=""){
						$_SESSION['PRICE_CAL']['CLEANING_FEE'] = $row_security['CleaningFee'];
					}else{
						$_SESSION['PRICE_CAL']['CLEANING_FEE'] = 0;
					}*/
				}
				// comment $_SESSION['PRICE_CAL']['SUB_TOTAL'] = $total ;
				$SUB_TOTAL = $total ; 
				// comment $grandTotal = $_SESSION['PRICE_CAL']['SUB_TOTAL'] + $_SESSION['PRICE_CAL']['OCCUPANT_CHARGE'] + $_SESSION['PRICE_CAL']['CLEANING_FEE'];
				
				$grandTotal = $SUB_TOTAL + $OCCUPANT_CHARGE + $CLEANING_FEE;
				
				//check for Minimum Rental < 7
				$miniRentcharge = 0;
				if($countCheckDiff['days'] < 7 && $row_occu['ApplyMinRentalAmtResDtLessThn7DaysFrArr'] == 1){
					$miniRentcharge = $row_occu['MinRentalAmtResDtLessThn7DaysFrArr'];
				}else if($countCheckDiff['days'] >= 7 && $row_occu['ApplyMinRentalAmtResDtGrtThnEq7DaysFrArr'] == 1){
					$miniRentcharge = $row_occu['MinRentalAmtResDtGrtThnEq7DaysFrArr'];
				}
				if($grandTotal < $miniRentcharge){
					$grandTotal = $miniRentcharge;
				}
				
				/* comment by AB
				$_SESSION['PRICE_CAL']['GRAND_TOTAL'] =$grandTotal;
				$_SESSION['PRICE_CAL']['FIRST_PAY'] = $grandTotal/2;
				$_SESSION['PRICE_CAL']['SECOND_PAY'] = $grandTotal/2;

				$_SESSION['PRICE_CAL']['PROPERTY_ID'] = $get_property[PropertyID];
				$_SESSION['PRICE_CAL']['PROPERTY_NUMBER'] = $get_property[PropertyNumber];
				
				$_SESSION['PRICE_CAL']['CHECK_IN'] = $chckin;
				$_SESSION['PRICE_CAL']['CHECK_OUT'] = $chckout;
				$_SESSION['PRICE_CAL']['OCCUPANTS'] = $occupants;
				$_SESSION['PRICE_CAL']['EVENT'] = $event;
				$_SESSION['PRICE_CAL']['DAY_COUNT'] = $countDateDiff['days'];
				
				
				$preDis .= '<br/>@total =>'.$total;
				$preDis .= '<br/>@grandTotal =>'.$grandTotal;
				
				if(isset($_REQUEST['test'])){ echo $preDis;echo '<pre>';print_r($_SESSION['PRICE_CAL']);print_r($WSOPArr);echo '</pre>';}
				*/
				
				$GRAND_TOTAL =$grandTotal;
				$FIRST_PAY = $grandTotal/2;
				$SECOND_PAY = $grandTotal/2;

				$PROPERTY_ID = $get_property[PropertyID];
				$PROPERTY_NUMBER = $get_property[PropertyNumber];
				
				$CHECK_IN = $chckin;
				$CHECK_OUT = $chckout;
				$OCCUPANTS = $occupants;
				$EVENT = $event;
				$DAY_COUNT = $countDateDiff['days'];
				
				
				$preDis .= '<br/>@total =>'.$total;
				$preDis .= '<br/>@grandTotal =>'.$grandTotal;
				
				$data['grandTotal'] = $grandTotal; 
				//print_r($preDis);
				//echo "----------";
				
				
				
			} // end of else if($num_chkavail > 0)
			
		} // end of if($chckin != "" && $chckout != "")
	}	



//print_r($data);exit;

echo json_encode($data);

?>