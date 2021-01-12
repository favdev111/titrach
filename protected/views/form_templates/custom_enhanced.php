<?php extract($data);
$bill_date = DateTime::createFromFormat('m/d/Y',$bill_date);
$bill_date = $bill_date ? $bill_date->format('F') : '-';
$agency = Agency::model()->findByPk($bill_agency);

$provider = Provider::model()->findByPk($bill_provider);
if($provider)
    $provider_name = $provider->getFullName();

$student = Patient::model()->findByPk($bill_patient);

$base_url = Yii::app()->getBaseUrl('true');
PDF::$base_url = $base_url;

//Rates
$rates = array(
    ''=>0,
    '0'=>0,
    '1'=>41.98,
    '2'=>62.97,
    '3'=>83.96,
    '4'=>94.45,
    '5'=>104.95
);
$total_session_length = 0;
$total_Amount = 0;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<div >

    <table border="0" width="100%">
        <tr>
            <td width="20%">
                <img src="<?php echo $base_url?>/img/templates/cse_logo.jpg" width="100"/>
            </td>
            <td width="15%"></td>
            <td width="30%" align="center" style="text-align: center">
                <div>Division of Financial Operations</div>
                <div>Non Public School Payables</div>
                <div>65 Court Street, Room 1001</div>
                <div>Brooklyn, NY 11201</div>
            </td>
            <td width="25%"></td>
            <td width="10%" align="center" >
                <div>Enhanced</div>
                <div>Rate</div>
                <div>SETSS</div>
            </td>
        </tr>
    </table>
    <p></p>
    <table border="0" style="text-align: right;float: right">
        <tr  style="text-align: right">
            <td> </td>
            <td></td>
            <td> Month:  <?php echo date("F") ?> </td>
            <td> Year:  <?php echo date("Y") ?> </td>
        </tr>
    </table>
    <p></p>

    <table width="100%"  style="border: 1px solid black">
        <tr style="border-bottom: 1px solid black">
            <td colspan="4" align="center" style="text-align: center;font-size:14pt;border-bottom: 1px solid black"><b>Student Information</b></td>
        </tr>
        <tr><td></td></tr>
        <tr >
            <td  style="text-align: left; vertical-align: bottom" width="33.333%" valign="bottom" align="left"><b>Student name: <?php echo $student->getFullName(); ?></b></td>
            <td  style=" text-align: left; vertical-align: bottom" width="33.6%" valign="bottom" align="left"><b>Date of birth: <?php echo $student->dob; ?></b></td>
            <td  colspan="2" style=" text-align: left; vertical-align: bottom" width="33%.333" valign="bottom" align="left"><b>NYC #ID: <?php echo $student->student_id; ?></b></td>
        </tr>
        <tr >
            <td  style="border-left: 1px solid black; text-align: left; vertical-align: bottom" width="25%" valign="bottom" align="left"><b>Service District: <?php echo $student->service_district; ?></b></td>
            <td  style="border-left: 1px solid white; text-align: left; vertical-align: bottom" width="25%" valign="bottom" align="left"><b>Frequency:</b></td>
            <td  style="border-left: 1px solid white; text-align: left; vertical-align: bottom" width="25%" valign="bottom" align="left"><b>Duration:</b></td>
            <td  style="border-left: 1px solid white; text-align: left; vertical-align: bottom" width="25%" valign="bottom" align="left"><b>Hourly rate:</b></td>
        </tr>
        <tr><td></td></tr>
    </table>
    <p>&nbsp;</p>
    <table width="100%"  style="border: 1px solid black">
        <tr style="border-bottom: 1px solid black">
            <td colspan="4" align="center" style="text-align: center;font-size:14pt;border-bottom: 1px solid black"><b>Agency Information</b></td>
        </tr>
        <tr><td></td></tr>
        <tr >
            <td  style="text-align: left; vertical-align: bottom" width="50%" valign="bottom" align="left"><b>Agency Name: <?php echo $agency->name ?></b></td>
            <td  style=" text-align: left; border-right: 1px solid black; vertical-align: bottom" width="50%" valign="bottom" align="left"><b>Federal Tax ID#: <?php echo $agency->tax_id ?> </b></td>
        </tr>
        <tr >
            <td colspan="2"  style="text-align: left; vertical-align: bottom;border-right: 1px solid black;" width="100%" valign="bottom" align="left"><b>Address: <?php echo $agency->address /* ($agency->email ? $agency->email : ($provider->email ? $provider->email : '-')) */;  ?> </b></td>
        </tr>
        <tr >
            <td  style="text-align: left; vertical-align: bottom" width="50%" valign="bottom" align="left"><b>Telephone#: <?php echo $agency->phone/*,'/ ',$provider->phone*/;?> </b></td>
            <td  style="border-right: 1px solid black; text-align: left; vertical-align: bottom" width="50%" valign="bottom" align="left"><b>E-Mail Address: <?php echo $agency->email /* ($agency->email ? $agency->email : ($provider->email ? $provider->email : '-')) */;  ?></b></td>
        </tr>
        <tr><td></td></tr>
    </table>
    <p>&nbsp;</p>
    <table width="100%"  style="border: 1px solid black">
        <tr style="border-bottom: 1px solid black">
            <td colspan="4" align="center" style="text-align: center;font-size:14pt;border-bottom: 1px solid black"><b>Provider Information</b></td>
        </tr>
        <tr><td></td></tr>
        <tr >
            <td  style="text-align: left; vertical-align: bottom;border-right: 1px solid black;" width="100%" valign="bottom" align="left"><b>Providers Name: <?php echo $provider_name; ?></b></td>
        </tr>
        <tr >
            <td  style="text-align: left; vertical-align: bottom;border-right: 1px solid black" width="100%" valign="bottom" align="left"><b>Provider Number:  <?php echo $provider->phone/*,'/ ',$provider->phone*/;?></b></td>
        </tr>
        <tr><td></td></tr>
    </table>
    <p>&nbsp;</p>
    <table  border="0" width="100%">
        <tr>
            <td colspan="2">
                <?php if($data['bill_service_type'] != "35"){?><b>SETSS Direct Service Provider:</b>  I  certify that I have provided SETSS services on the dates, times, group size and duration indicated herein.  I understand that when completed and filed, this form must be retained for 6 years and is subject to audit by the Department of Education.<?php } ?>
            </td>
        </tr>
        <tr>
            <td width="70%" align="center" style="color: #E7E7E7;"><br/><br/><br/>Actual Provider Signature Required</td>
            <td width="30%" align="left" style="text-align: right;"><br/><br/><br/><b>Date</b>&nbsp; ____________________</td>
        </tr>
    </table>
    <p></p>
    <p style="font-size:9pt;">
        <b>Parent and/or Principal:</b> By my signature, I acknowledge that I have reviewed this billing form and that, to the best of my knowledge, the sessions were provided as indicated
    </p>
    <p></p>
    <table border="0" width="100%">
        <tr>
            <td width="50%"><b>Parent (for services other than at school)</b></td>
            <td width="35%" align="center" style="color: #E7E7E7;">Signature Required</td>
            <td width="15%"><b>Date</b> ___________</td>
        </tr>
        <tr><td colspan="3" style="font-size:8pt;">&nbsp;</td></tr>
        <tr>
            <td width="50%"><b>Parent's Printed Name (for services other than at school)</b></td>
            <td width="35%"align="center" style="color: #E7E7E7;">Printed Name Required</td>
            <td width="15%"><b>Date</b> ___________</td>
        </tr>
        <tr><td colspan="3" style="font-size:8pt;">&nbsp;</td></tr>
        <tr>
            <td width="50%"><b>Principal's Signature (for services at school)</b></td>
            <td width="35%" align="center" style="color: #E7E7E7;">Signature Required</td>
            <td width="15%"><b>Date</b> ___________</td>
        </tr>
        <tr><td colspan="3" style="font-size:8pt;">&nbsp;</td></tr>
        <tr>
            <td width="50%"><b>Principal's Printed Name (for services at school)</b></td>
            <td width="35%"align="center" style="color: #E7E7E7;">Printed Name Required</td>
            <td width="15%"><b>Date</b> ___________</td>
        </tr>
    </table>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>

    <div style="font-size:12pt;">
        <table width="100%" class="service-provision" border="1">
            <tr>
                <td width="100%" align="center" style="text-align: center; font-size: 14pt;"><b>Service Provision</b></td>
            </tr>
            <tr class="service-provision-header">
                <td style="background-color: #CCC; text-align: center; vertical-align: bottom" width="12%" valign="bottom" align="center"><b>Date</b></td>
                <td style="background-color: #CCC; text-align: center; vertical-align: bottom" width="13%" valign="bottom" align="center"><b>Start Time</b></td>
                <td style="background-color: #CCC; text-align: center; vertical-align: bottom" width="13%" valign="bottom" align="center"><b>End Time</b></td>
                <td style="background-color: #CCC; text-align: center; vertical-align: bottom" width="12%" valign="bottom" align="center"><b>Length</b></td>
                <td style="background-color: #CCC; text-align: center; vertical-align: bottom" width="12%" valign="bottom" align="center"><b>Date</b></td>
                <td style="background-color: #CCC; text-align: center; vertical-align: bottom" width="13%" valign="bottom" align="center"><b>Start Time</b></td>
                <td style="background-color: #CCC; text-align: center; vertical-align: bottom" width="13%" valign="bottom" align="center"><b>End Time</b></td>
                <td style="background-color: #CCC; text-align: center; vertical-align: bottom" width="12%" valign="bottom" align="center"><b>Length</b></td>
            </tr>
            <?php for ($i = 1; $i <= 16; $i++): ?>
                <?php $is_existed_left = false; ?>
                <tr>
                    <?php  foreach($billRows as $key=>$row): ?>
                            <?php 
                                $session_date = intval(date("d", $key)); 
                                $session_start_time = $row['speech_session_start_time'] ? $row['speech_session_start_time']:  '';
                                $session_end_time = $row['speech_session_end_time'] ? $row['speech_session_end_time']: '';
                                $session_length = getTimeDiff($row['speech_session_start_time'],$row['speech_session_end_time'])/60;
                                $session_amt = round(($rates[$row['speech_session_group_size']]/$row['speech_session_group_size']) * $session_length,2);
                                $is_existed_right = false;
                            ?>
                            <?php if($session_date == $i): ?>
                                <?php $total_session_length += $session_length; ?>
                                <?php $total_Amount += $session_amt; ?>
                                <?php $is_existed_left = true; ?>
                                <td width="12%" style="text-align: center; vertical-align: bottom"> <?= $session_date ?> </td>
                                <td width="13%" style="text-align: center; vertical-align: bottom"> <?= $session_start_time ?> </td>
                                <td width="13%" style="text-align: center; vertical-align: bottom"> <?= $session_end_time ?> </td>
                                <td width="12%" style="text-align: center; vertical-align: bottom"> <?= $session_length ?> </td>
                                <td width="12%" style="text-align: center; vertical-align: bottom"> <?= $i+16 ?> </td>
                                <?php  foreach($billRows as $key_r=>$row_r): ?>
                                    <?php 
                                        $session_date_r = intval(date("d", $key_r)); 
                                        $session_start_time_r = $row_r['speech_session_start_time'] ? $row_r['speech_session_start_time']:  '';
                                        $session_end_time_r = $row_r['speech_session_end_time'] ? $row_r['speech_session_end_time']: '';
                                        $session_length_r = getTimeDiff($row_r['speech_session_start_time'],$row_r['speech_session_end_time'])/60;
                                        $session_amt_r = round(($rates[$row_r['speech_session_group_size']]/$row_r['speech_session_group_size']) * $session_length_r,2);
                                    ?>
                                    <?php if($session_date_r == $i+16): ?>
                                        <?php $total_session_length += $session_length_r; ?>
                                        <?php $total_Amount += $session_amt_r; ?>
                                        <?php $is_existed_right = true; ?>
                                        <td width="13%" style="text-align: center; vertical-align: bottom"> <?= $session_start_time_r ?></td>
                                        <td width="13%" style="text-align: center; vertical-align: bottom"> <?= $session_end_time_r ?></td>
                                        <td width="12%" style="text-align: center; vertical-align: bottom"> <?= $session_length_r ?></td>
                                    <?php endif;?>
                                <?php endforeach; ?>
                                <?php if(!$is_existed_right): ?>
                                    <td width="13%" style="text-align: center; vertical-align: bottom"></td>
                                    <td width="13%" style="text-align: center; vertical-align: bottom"></td>
                                    <td width="12%" style="text-align: center; vertical-align: bottom"></td>
                                <?php endif; ?>
                            <?php endif; ?>
                    <?php endforeach; ?>
                   
                   <?php if(!$is_existed_left): ?>
                        <td width="12%" style="text-align: center; vertical-align: bottom"><?= $i ?></td>
                        <td width="13%" style="text-align: center; vertical-align: bottom"></td>
                        <td width="13%" style="text-align: center; vertical-align: bottom"></td>
                        <td width="12%" style="text-align: center; vertical-align: bottom"></td>
                        <td width="12%" style="text-align: center; vertical-align: bottom"> <?= $i+16 ?> </td>
                        <td width="13%" style="text-align: center; vertical-align: bottom"></td>
                        <td width="13%" style="text-align: center; vertical-align: bottom"></td>
                        <td width="12%" style="text-align: center; vertical-align: bottom"></td>
                    <?php endif; ?>
                </tr>
            <?php endfor; ?>
        </table>
    </div>
    <table border="0" width="100%">
        <tr>
            <td width="25%" align="right"><b>Total Session Length :</b></td>
            <td width="10%" align="right"><?= $total_session_length ?>&nbsp;&nbsp;</td>
            <td width="15%" align="right"><b>Rate :</b></td>
            <td width="15%" align="right"></td>
            <td width="25%" align="right" style="text-align: right;"><b>Total Amount Due:</b></td>
            <td width="10%"> <?php echo $total_Amount;?> </td>
        </tr>
    </table>
    


</div>
</body>
<style>
    body{
        color: #181818;
    }
    .service-sub tr td {
        border: 1px solid black;
    }
</style>
</html>