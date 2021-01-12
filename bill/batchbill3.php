<html>

	<body>

		<center>
		
			<br />
			<br />

			<h2 style="font-family:Sans-serif; font-size:28;">Milestones Batch Billing Processor</h2>
			Instructions:  Download a CSV report from the vendor portal reports and then upload here.
			<br />
			<br />

			<form action="batchbill3.php" method="post" enctype="multipart/form-data">
				Select CSV File to Upload: 
				<input type="file" name="tsvfile" size="25" />
				<input type="submit" name="submit" value="Upload" />
			</form>

		</center>

	</body>

</html>

<?php
// If file is uploaded (someone hit submit) lets do this!
if( $_FILES['tsvfile']['name'] ) {
	
	// Get temp file
	$filename = $_FILES['tsvfile']['tmp_name'];
	
	// Connect to the database
	$dbhost = 'localhost';
	$dbuser = 'tifrach_learning';
	$dbpass = 'uQl~_FTW8XIu';
	$dbschema = 'tifrach_learning';
	$conn = mysqli_connect( $dbhost, $dbuser, $dbpass );
	mysqli_select_db($conn, $dbschema );
	
	// BUILD FIRST ROW OF OUTPUT TSV FILE
	$output = "SIAP FISCAL YR"."\t";
	$output .= "SIAP BORO CD"."\t";
	$output .= "SIAP DIST CD"."\t";
	$output .= "SIAP_FUND_CD"."\t";
	$output .= "SIAP SCHL ID"."\t";
	$output .= "SIAP PROVIDER TYPE"."\t";
	$output .= "SIAP AGENCY CD"."\t";
	$output .= "SIAP PROVIDER"."\t";
	$output .= "PROVIDER LAST NAME"."\t";
	$output .= "PROVIDER FIRST NAME"."\t";
	$output .= "SIAP ACT PROVIDER"."\t";
	$output .= "SIAP OSIS ID"."\t";
	$output .= "STUD FIRST NAME"."\t";
	$output .= "STUD LAST NAME"."\t";
	$output .= "SIAP SERV SUBTYPE"."\t";
	$output .= "SIAP START DT"."\t";
	$output .= "SIAP END DT"."\t";
	$output .= "SIAP SESSIONS"."\t";
	$output .= "SIAP FREQ TERM"."\t";
	$output .= "SIAP SESS LEN"."\t";
	$output .= "SIAP GROUP SIZE"."\t";
	$output .= "SIAP LANG CD"."\t";
	$output .= "SIAP ASSIGN ID"."\t";
	$output .= "SCIN INVOICE MONTH"."\t";
	$output .= "SCIN INVOICE DAYS"."\t";
	$output .= "SCIN ATTEND CODE"."\t";
	$output .= "SCIN ACT GRP SIZE"."\t";
	$output .= "SCIN START TIME"."\t";
	$output .= "SCIN END TIME"."\t";
	$output .= "SCIN SCHOOL OTHER"."\t"."/"."\t";
	$output .= "SCIN VEND INVOICE"."\t";
	$output .= "SCIN INVOICE AMT"."\t";
	$output .= "SCIN SED PROG ID"."\t/\t/\t/\t/\t/\t\n";

	// OPEN CSV FILE INTO ARRAY FOR EACH ROW
	$rows = explode( "\n", file_get_contents( $filename ) );
	array_shift( $rows );

	//ADDITIONAL CONDITION FOR MANDATE TYPE CHECK
	$mandate_check = array(
		'C1'=>array('service_type'=>33,'type'=>'individual'),
		'S1'=>array('service_type'=>5,'type'=>'individual'),
		'P1'=>array('service_type'=>3,'type'=>'individual'),
		'O1'=>array('service_type'=>4,'type'=>'individual'),
		'TU'=>array('service_type'=>34, 'type'=>'individual'),		
		'CO'=>array('service_type'=>33,'type'=>'group'),
		'SP'=>array('service_type'=>5,'type'=>'group'),
		'OT'=>array('service_type'=>4,'type'=>'group'),
		'PT'=>array('service_type'=>3,'type'=>'group'),
		'TU'=>array('service_type'=>34, 'type'=>'group'),
	);

	// PARSE ROWS OF FILE INTO ARRAYS FOR EACH COLUMN VALUE AND FIND MATCHING DATA IN DATABASE TO POPULATE MISSING DATA
	$counter = 0;
	foreach( $rows as $row => $data ) {

		$row_data = explode( ",", $data );
                
                if (count($row_data) > 1) {
                
		$info[$row]['SRAP FISCAL YR'] = filter_var( $row_data[0], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
		$info[$row]['SRAP BORO CD'] = filter_var( $row_data[1], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
		$info[$row]['SRAP DIST CD'] = filter_var( $row_data[2], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
		$info[$row]['SRAP_FUND_CD'] = filter_var( $row_data[3], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
		$info[$row]['SRAP SCHL ID'] = filter_var( $row_data[4], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
		$info[$row]['SRAP PROVIDER TYPE'] = filter_var( $row_data[5], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
		$info[$row]['SRAP AGENCY CD'] = filter_var( $row_data[6], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
		$info[$row]['SRAP PROVIDER'] = filter_var( $row_data[7], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
		$info[$row]['PROVIDER LAST NAME'] = filter_var( $row_data[8], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
		$info[$row]['PROVIDER FIRST NAME'] = filter_var( $row_data[9], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
		$info[$row]['SRAP ACT PROVIDER'] = filter_var( $row_data[10], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
		$info[$row]['SRAP OSIS ID'] = filter_var( $row_data[11], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
		$info[$row]['STUD FIRST NAME'] = filter_var( $row_data[12], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
		$info[$row]['STUD LAST NAME'] = filter_var( $row_data[13], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
		$info[$row]['SRAP SERV SUBTYPE'] = filter_var( $row_data[14], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
		$info[$row]['SRAP START DT'] = filter_var( $row_data[15], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
		$info[$row]['SRAP END DT'] = filter_var( $row_data[16], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
		$info[$row]['SRAP SESSIONS'] = filter_var( $row_data[17], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
		$info[$row]['SIAP FREQ TERM'] = filter_var( $row_data[18], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
		$info[$row]['SRAP SESS LEN'] = filter_var( $row_data[19], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
		$info[$row]['SRAP GROUP SIZE'] = filter_var( $row_data[20], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
		$info[$row]['SRAP LANG CD'] = filter_var( $row_data[21], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
		$info[$row]['SIAP ASSIGN ID'] = filter_var( $row_data[22], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
		$info[$row]['SCIN INVOICE MONTH'] = filter_var( $row_data[23], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
		$info[$row]['SCIN INVOICE DAYS'] = filter_var( $row_data[24], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
		$info[$row]['SCIN ATTEND CODE'] = filter_var( $row_data[25], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
		$info[$row]['SCIN ACT GRP SIZE'] = filter_var( $row_data[26], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
		$info[$row]['SCIN START TIME'] = filter_var( $row_data[27], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
		$info[$row]['SCIN END TIME'] = filter_var( $row_data[28], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
		$info[$row]['SCIN SCHOOL OTHER'] = filter_var( $row_data[29], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
		$info[$row]['/'] = filter_var( $row_data[30], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
		$info[$row]['SCIN VEND INVOICE'] = filter_var( $row_data[31], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
		$info[$row]['SCIN INVOICE AMT'] = filter_var( $row_data[32], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
		$info[$row]['SCIN SED PROG ID'] = filter_var( $row_data[33], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
		$info[$row]['/2'] = filter_var( $row_data[34], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
		$info[$row]['/3'] = filter_var( $row_data[35], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
		$info[$row]['/4'] = filter_var( $row_data[36], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
		$info[$row]['/5'] = filter_var( $row_data[37], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
		$info[$row]['/6'] = filter_var( $row_data[38], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );



		// GET STUDENT ID FROM DB
		$sidq = "SELECT id FROM patients WHERE replace(student_id, '-', '') = '".$info[$row]['SRAP OSIS ID']."'";
                $getsid = mysqli_query($conn, $sidq );
                $studentid = '';
		while($sid = mysqli_fetch_assoc($getsid)){
			$studentid = $sid['id'];
		}
		
		if( $studentid != '' ) {
                    
			// GET PROVIDER ID FROM DB
			$pqr = "SELECT id FROM provider WHERE last_name LIKE '".trim ($info[$row]['PROVIDER LAST NAME'])."%' AND first_name LIKE '".trim ($info[$row]['PROVIDER FIRST NAME'])."%'";
			
                        $getpid = mysqli_query($conn, $pqr);
                        $providerid  = '';
			while($pid = mysqli_fetch_assoc($getpid)){
				$providerid = $pid['id'];
			}
			
			// FIND MATCHING SESSION
			$searchdate = date( "Y-m-d", strtotime( $info[$row]['SCIN INVOICE DAYS'] ) );
			$sessq = "SELECT start_date_timestamp, end_date_timestamp, event_meta FROM event WHERE start_date_timestamp BETWEEN UNIX_TIMESTAMP('".$searchdate." 00:00:00') AND UNIX_TIMESTAMP('".$searchdate." 23:59:59') AND patient_id = '".$studentid."' AND provider_id = '".$providerid."'";
                        
                        $getsess = mysqli_query($conn, $sessq );
			while($sess = mysqli_fetch_assoc( $getsess ) ) {
                            
				$starttime = gmdate( "H:i:s", $sess['start_date_timestamp'] );
				$endtime = gmdate( "H:i:s", $sess['end_date_timestamp'] );
				$event = $sess['event_meta']; // EVENT ID FOR FINDING OTHER INFO

				//CHECK SESSION FOR CORRESPONDING MANDATE 
				$sql = 'SELECT form_entity_id, pm.service_type, pm.type FROM forms_entities_fields as fef
						INNER JOIN patients_mandates as pm ON pm.id = fef.value
						WHERE fef.form_field_id = 36 AND fef.form_entity_id ='.(int)$event;
				
				if ( ! $result = mysqli_query($conn, $sql ) )
					die( mysqli_error($conn) );

				$m_check = array( 'service_type'=>'','type'=>'' );

				// IF MANDATE FILED, THEN SET INFO FOR TYPE CHECK
				if( $rowSet = mysqli_fetch_assoc( $result ) ) {
					$m_check['service_type'] = $rowSet['service_type'];
					$m_check['type'] = $rowSet['type'];
				} else {
					//If mandate field not filled, then try to detect mandate type via session info
					//Not optimized for speed method, but most easy
					
					//Get session service type
					$sql = 'SELECT fefv.field_value_id as service_type
						FROM `forms_entities_fields` AS fef
						LEFT JOIN forms_entities_fields_values AS fefv ON fefv.fef_id = fef.id
						WHERE form_field_id =18 AND fef.form_entity_id = '.(int)$event;
					if(!$result = mysqli_query($conn, $sql))
						die(mysqli_error($conn));
					if($rowSet = mysqli_fetch_assoc($result))
					{
						$m_check['service_type'] = $rowSet['service_type'];
					}
					
					//Get session group size
					$sql = 'SELECT fv.form_field_value as type
						FROM `forms_entities_fields` AS fef
						LEFT JOIN forms_entities_fields_values AS fefv ON fefv.fef_id = fef.id
							LEFT JOIN fields_values AS fv ON fv.id = fefv.field_value_id 
						WHERE fef.form_field_id =22 AND fef.form_entity_id = '.(int)$event;
					if(!$result = mysqli_query($conn, $sql))
						die(mysqli_error($conn));
					if($rowSet = mysqli_fetch_assoc($result))
					{
						//echo '<br />';
						$m_check['type'] = (int)$rowSet['type'] > 1 ? 'group': 'individual';
					}
				}

				// Get session group size
				$sql = 'SELECT fv.form_field_value as type
						FROM `forms_entities_fields` AS fef
						LEFT JOIN forms_entities_fields_values AS fefv ON fefv.fef_id = fef.id
							LEFT JOIN fields_values AS fv ON fv.id = fefv.field_value_id 
						WHERE fef.form_field_id =22 AND fef.form_entity_id = '.(int)$event;
				if( ! $result = mysqli_query($conn, $sql ) )
						die(mysqli_error($conn));
				$session_group_size = 0;
				if( $rowSet = mysqli_fetch_assoc( $result ) ) {
						if ( isset( $rowSet['type'] ) ) {
							$session_group_size = $rowSet['type'];
						}
				}

				//IMPLEMENT CHECK
				$rowMandate = trim( $info[$row]['SRAP SERV SUBTYPE'] );
				//SKIP ROW IF SESSION FOR OTHER MANDATE TYPE
				if(
					! isset( $mandate_check[ $rowMandate] ) ||
					(
						$mandate_check[$rowMandate]['service_type'] != $m_check['service_type'] || 
						$mandate_check[$rowMandate]['type'] != $m_check['type']
					)
				)
				{
					continue;
				}
				
				
				// CHECK IF IT IS INDIVIDUAL OR GROUP SESSION & HOW MANY IN SESSION (USING SAME START TIME FOR THERAPIST INSTEAD OF GROUP FIELD)
				$sessdatetime = date( "Y-m-d", strtotime( $info[$row]['SCIN INVOICE DAYS'] ) ) . " " . $starttime;
				$unixdate = strtotime( $sessdatetime );
				$cnq = "SELECT start_date_timestamp, end_date_timestamp FROM event WHERE start_date_timestamp = '".$unixdate."' AND provider_id = '".$providerid."'";
				$checknum = mysqli_query($conn, $cnq );
				$numsess = mysqli_num_rows( $checknum );
				

				// GET GROUP SIZE NUMBER
				//$sql_group_size = "SELECT form_field_value from fields_values WHERE form_field_id = 22 AND id IN ( SELECT field_value_id FROM forms_entities_fields_values WHERE fef_id IN( SELECT id FROM forms_entities_fields WHERE form_entity_id IN ( SELECT id FROM forms_entities WHERE patient_id=1 AND timestamp='".$unixdate."' ) ) )";

				
				// CHECK LOCATION OF SESSION AND SET EITHER OFFICE/HOME/SCHOOL
				$gloc = "SELECT field_value_id, form_field_title FROM forms_entities_fields, forms_entities_fields_values, fields_values WHERE form_entity_id = '".$event."' AND forms_entities_fields.form_field_id = '21' AND forms_entities_fields.id = forms_entities_fields_values.fef_id AND field_value_id = fields_values.id";
				//echo $gloc."<br>";
				$getloc = mysqli_query($conn, $gloc);
				while($glc = mysqli_fetch_assoc($getloc)){
					$locid = $glc['field_value_id'];
					$locname = $glc['form_field_title'];
					if($locid == '48'){
						$sessloc = "B";
					} elseif($locid == '46'){
						$sessloc = "H";
					}
					else {
						$sessloc = "S";
					}
					//$info[$row]['MS_SESS_LOCID'] = $locid;
					//$info[$row]['MS_SESS_LOCATION'] = $locname;
					//$info[$row]['MS_SESS_LOC'] = $sessloc;
				}
				
				// SET THE NEEDED FIELDS WITH THE DATA FROM DATABASE
				$info[$row]['SCIN ATTEND CODE'] = ' P';
				//$info[$row]['SCIN ACT GRP SIZE'] = $numsess;
				$info[$row]['SCIN ACT GRP SIZE'] = $session_group_size;
				$info[$row]['SCIN START TIME'] = date("g:i A", strtotime($starttime));
				$info[$row]['SCIN END TIME'] = date("g:i A", strtotime($endtime));
				$info[$row]['SCIN SCHOOL OTHER'] = $sessloc;
			}
		}
		//$info[$row]['SCIN VEND INVOICE'] = '';
		//$info[$row]['SCIN INVOICE AMT'] = '0.00';
		
		// ADD ROW TO OUTPUT TSV FILE
		$info[$row]['SCIN VEND INVOICE'] = "";
		$info[$row]['SCIN INVOICE AMT'] = '0.00';

		$output .=  $info[$row]['SRAP FISCAL YR']."\t".
					$info[$row]['SRAP BORO CD']."\t".
					$info[$row]['SRAP DIST CD']."\t".
					$info[$row]['SRAP_FUND_CD']."\t".
					$info[$row]['SRAP SCHL ID']."\t".
					$info[$row]['SRAP PROVIDER TYPE']."\t".
					$info[$row]['SRAP AGENCY CD']."\t".
					$info[$row]['SRAP PROVIDER']."\t".
					$info[$row]['PROVIDER LAST NAME']."\t".
					$info[$row]['PROVIDER FIRST NAME']."\t".
					$info[$row]['SRAP ACT PROVIDER']."\t".
					$info[$row]['SRAP OSIS ID']."\t".
					$info[$row]['STUD FIRST NAME']."\t".
					$info[$row]['STUD LAST NAME']."\t".
					$info[$row]['SRAP SERV SUBTYPE']."\t".
					$info[$row]['SRAP START DT']."\t".
					$info[$row]['SRAP END DT']."\t".
					$info[$row]['SRAP SESSIONS']."\t".
					$info[$row]['SIAP FREQ TERM']."\t".
					$info[$row]['SRAP SESS LEN']."\t".
					$info[$row]['SRAP GROUP SIZE']."\t".
					$info[$row]['SRAP LANG CD']."\t".
					$info[$row]['SIAP ASSIGN ID']."\t".
					$info[$row]['SCIN INVOICE MONTH']."\t".
					$info[$row]['SCIN INVOICE DAYS']."\t".
					$info[$row]['SCIN ATTEND CODE']."\t".
					$info[$row]['SCIN ACT GRP SIZE']."\t".
					$info[$row]['SCIN START TIME']."\t".
					$info[$row]['SCIN END TIME']."\t".
					$info[$row]['SCIN SCHOOL OTHER']."\t".
					$info[$row]['/']."\t".
					$info[$row]['SCIN VEND INVOICE']."\t".
					$info[$row]['SCIN INVOICE AMT']."\t".
					$info[$row]['SCIN SED PROG ID']."\t\t\t\t\t\t\n";
					
					// UNSET VARIABLES
					unset($info[$row]['SRAP FISCAL YR'],
					$info[$row]['SRAP BORO CD'],
					$info[$row]['SRAP DIST CD'],
					$info[$row]['SRAP_FUND_CD'],
					$info[$row]['SRAP SCHL ID'],
					$info[$row]['SRAP PROVIDER TYPE'],
					$info[$row]['SRAP AGENCY CD'],
					$info[$row]['SRAP PROVIDER'],
					$info[$row]['PROVIDER LAST NAME'],
					$info[$row]['PROVIDER FIRST NAME'],
					$info[$row]['SRAP ACT PROVIDER'],
					$info[$row]['SRAP OSIS ID'],
					$info[$row]['STUD FIRST NAME'],
					$info[$row]['STUD LAST NAME'],
					$info[$row]['SRAP SERV SUBTYPE'],
					$info[$row]['SRAP START DT'],
					$info[$row]['SRAP END DT'],
					$info[$row]['SRAP SESSIONS'],
					$info[$row]['SIAP FREQ TERM'],
					$info[$row]['SRAP SESS LEN'],
					$info[$row]['SRAP GROUP SIZE'],
					$info[$row]['SRAP LANG CD'],
					$info[$row]['SIAP ASSIGN ID'],
					$info[$row]['SCIN INVOICE MONTH'],
					$info[$row]['SCIN INVOICE DAYS'],
					$info[$row]['SCIN ATTEND CODE'],
					$info[$row]['SCIN ACT GRP SIZE'],
					$info[$row]['SCIN START TIME'],
					$info[$row]['SCIN END TIME'],
					$info[$row]['SCIN SCHOOL OTHER'],
					$info[$row]['SCIN SED PROG ID'],
					$studentid,
					$starttime,
					$endtime,
					$event,
					$providerid);
                }		
	}

	// WRITE OUTPUT TO NEW FILE
	$fp = fopen('output.txt', 'w');
	fwrite($fp, $output);
	fclose($fp);
	
//	echo '<pre>';
//		print_r($output);
//		print_r($info);
//	echo '</pre>';
	
	echo "<br><center><a href='output.txt'>Click here to download the processed file</a></center>";
}
?>