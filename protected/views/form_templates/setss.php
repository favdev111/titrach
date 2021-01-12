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

?>
<div style="font-size:10pt">
    <table border="0" width="100%">
        <tr>
            <td rowspan="2" width="20%">
                <img src="<?php echo $base_url?>/img/templates/setss_logo_new.png" width="161"/>
            </td>
            <td align="right" style="text-align: right;" width="80%">
                <span style="color: #777; font-size:10pt;">Version 2/9/14</span>
            </td>
        </tr>
        <tr>
            <td align="left">
                <table border="0" width="100%" cellpadding="1">
                    <tr>
                        <td colspan="4">
                            <h3 style="text-align: center;">
                                <?php
                                if($data['bill_service_type'] == 35){
                                    ?>
                                    Enhanced
                                <?php  }else{ ?>
                                    SETSS INVOICE
                                <?php  } ?>
                            </h3>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="right"><b>Invoice #</b></td>
                        <td><span style="text-decoration: underline;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
                        <td align="right"><b>Invoice Month:</b></td>
                        <td>&nbsp;&nbsp;<?php echo $bill_date?></td>
                    </tr>
                    <tr>
                        <td align="right" valign="top"><b>Agency Name</b></td>
                        <td>&nbsp;&nbsp;<?php echo $agency->name ?> </td>
                        <td align="right"><b>Actual Provider Name</b></td>
                        <td>&nbsp;&nbsp;<?php echo $provider_name; ?></td>
                    </tr>
                    <tr>
                        <td align="right"><b>Agency Tax ID</b></td>
                        <td>&nbsp;&nbsp;<?php echo $agency->tax_id ?></td>
                        <td align="right"><b>Actual Provider SSN</b></td>
                        <td>&nbsp;&nbsp;<?php echo $provider->ss_id; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <br>
    <table width="100%" border="0" cellpadding="0" cellspasing="2">
        <tr>
            <td align="right" width="40%"><b>Agency or Provider Address</b></td>
            <td colspan="3" width="60%" align="left" class="text-left">&nbsp;&nbsp;<?php echo $agency->getFullAddress(); ?></td>
        </tr>
        <tr>
            <td align="right"><b>Agency/Provider Phone Number</b></td>
            <td>&nbsp;&nbsp;<?php echo $agency->phone/*,'/ ',$provider->phone*/;?></td>
            <td align="right"><b>Agency/Provider Email Address</b></td>
            <td  valign="top">&nbsp;<?php echo $agency->email /* ($agency->email ? $agency->email : ($provider->email ? $provider->email : '-')) */;  ?></td>
        </tr>
        <tr>
            <td colspan="4">&nbsp;</td>
        </tr>
        <tr>
            <td align="right"><b>Student Name</b></td>
            <td colspan="3" align="left" class="text-left">&nbsp;&nbsp;<?php echo $student->getFullName(); ?></td>
        </tr>
        <tr>
            <td align="right"><b>Student OSIS #</b></td>
            <td colspan="3" align="left" class="text-left">&nbsp;&nbsp;<?php echo $student->student_id; ?> </td>
        </tr>
        <?php /*	<tr>
		<td align="right"><b>Student Address</b></td>
		<td colspan="3" align="left" class="text-left">&nbsp;&nbsp;<?php echo $student->getFullAddress(); ?></td>
	</tr>
	*/ ?>
        <tr>
            <td align="right"><b>Site (where services were rendered)</b></td>
            <td colspan="3">&nbsp;&nbsp;<?php echo implode(', ',$locations);?></td>
        </tr>
        <tr>
            <td align="right"><b>If other, please indicate</b></td>
            <td colspan="3">&nbsp;&nbsp;<span style="text-decoration: underline;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
        </tr>
    </table>
    <p>&nbsp;</p>
    <div style="font-size:9pt;">
        <table width="100%" border="1">
            <tr>
                <td colspan="6" align="center" style="text-align: center; background-color: #CCC;font-size:14pt;"><b>Service</b></td>
                <td colspan="6" align="center" style="text-align: center; background-color: #CCC;font-size:14pt;"><b>Service</b></td>
            </tr>
            <tr>
                <td style="background-color: #CCC; text-align: center; vertical-align: bottom" width="10%" valign="bottom" align="center"><b>Date</b></td>
                <td style="background-color: #CCC; text-align: center; vertical-align: bottom" width="8%" valign="bottom" align="center"><b>Time In</b></td>
                <td style="background-color: #CCC; text-align: center; vertical-align: bottom" width="8%" valign="bottom" align="center"><b>Time Out</b></td>
                <td style="background-color: #CCC; text-align: center; vertical-align: bottom" width="8%" valign="bottom" align="center"><b>Session Length <em>(in minutes)</em></b></td>
                <td style="background-color: #CCC; text-align: center; vertical-align: bottom" width="8%" valign="bottom" align="center"><b>Group Size</b></td>
                <td style="background-color: #CCC; text-align: center; vertical-align: bottom" width="8%" valign="bottom" align="center"><b>Session Amt</b></td>
                <td style="background-color: #CCC; text-align: center; vertical-align: bottom" width="10%" valign="bottom" align="center"><b>Date</b></td>
                <td style="background-color: #CCC; text-align: center; vertical-align: bottom" width="8%" valign="bottom" align="center"><b>Time In</b></td>
                <td style="background-color: #CCC; text-align: center; vertical-align: bottom" width="8%" valign="bottom" align="center"><b>Time Out</b></td>
                <td style="background-color: #CCC; text-align: center; vertical-align: bottom" width="8%" valign="bottom" align="center"><b>Session Length <em>(in minutes)</em></b></td>
                <td style="background-color: #CCC; text-align: center; vertical-align: bottom" width="8%" valign="bottom" align="center"><b>Group Size</b></td>
                <td style="background-color: #CCC; text-align: center; vertical-align: bottom" width="8%" valign="bottom" align="center"><b>Session Amt</b></td>
            </tr>
            <?php
            $cnt = count($billRows);
            $perCol = $cnt > 20 ? round($cnt/2) : 10;
            $i = 0;
            $mins = 0;
            $total = 0;
            ?>

            <tr>
                <td colspan="6" width="50%">
                    <table width="100%" border="1">
                        <?php 		foreach($billRows as $key=>$row):
                            if($i==$perCol)
                                break;
                            ?>
                            <tr>
                                <td width="20%" align="center" style="text-align: center;"><?php echo date('m/d/Y',$key);?></td>
                                <td width="16%" align="center" style="text-align: center;"><?php echo $row['speech_session_start_time']?></td>
                                <td width="16%" align="center" style="text-align: center;"><?php echo $row['speech_session_end_time']?></td>
                                <td width="16%" align="center" style="text-align: center;  background-color: #CCC;"><?php echo $m = getTimeDiff($row['speech_session_start_time'],$row['speech_session_end_time']); $mins +=$m;?></td>
                                <td width="16%" align="center" style="text-align: center;"><?php echo $row['speech_session_group_size']?></td>
                                <td width="16%" align="center" style="text-align: center; background-color: #CCC;">$<?php  $t = round(($rates[$row['speech_session_group_size']]/$row['speech_session_group_size'])/60 * $m,2); $total +=$t; echo $t;  ?></td>
                            </tr>
                            <?php
                            $i++;
                            unset($billRows[$key]);
                        endforeach;
                        for($i;$i<$perCol;$i++):?>
                            <tr>
                                <td width="20%" align="center" style="text-align: center;">&nbsp;</td>
                                <td width="16%" align="center" style="text-align: center;">&nbsp;</td>
                                <td width="16%" align="center" style="text-align: center;">&nbsp;</td>
                                <td width="16%" align="center" style="text-align: center;background-color: #CCC;">&nbsp;</td>
                                <td width="16%" align="center" style="text-align: center;">&nbsp;</td>
                                <td width="16%" align="center" style="text-align: center; background-color: #CCC;">&nbsp;</td>
                            </tr>
                        <?php
                        endfor;
                        $i = 0;
                        ?>

                    </table>
                </td>
                <td colspan="6" width="50%">
                    <table width="100%" border="1">
                        <?php		foreach($billRows as $key=>$row):
                            if($i==$perCol)
                                break;
                            ?>
                            <tr>
                                <td width="20%" align="center" style="text-align: center;"><?php echo date('m/d/Y',$key);?></td>
                                <td width="16%" align="center" style="text-align: center;"><?php echo $row['speech_session_start_time']?></td>
                                <td width="16%" align="center" style="text-align: center;"><?php echo $row['speech_session_end_time']?></td>
                                <td width="16%" align="center" style="text-align: center;background-color: #CCC;"><?php echo $m = getTimeDiff($row['speech_session_start_time'],$row['speech_session_end_time']); $mins +=$m;?></td>
                                <td width="16%" align="center" style="text-align: center;"><?php echo $row['speech_session_group_size']?></td>
                                <td width="16%" align="center" style="text-align: center; background-color: #CCC;">$<?php  $t = round(($rates[$row['speech_session_group_size']]/$row['speech_session_group_size'])/60 * $m,2); $total +=$t; echo $t;  ?></td>
                            </tr>
                            <?php
                            $i++;
                            unset($billRows[$key]);
                        endforeach;
                        for($i;$i<$perCol;$i++):?>
                            <tr>
                                <td width="20%" align="center" style="text-align: center;">&nbsp;</td>
                                <td width="16%" align="center" style="text-align: center;">&nbsp;</td>
                                <td width="16%" align="center" style="text-align: center;">&nbsp;</td>
                                <td width="16%" align="center" style="text-align: center;background-color: #CCC;">&nbsp;</td>
                                <td width="16%" align="center" style="text-align: center;">&nbsp;</td>
                                <td width="16%" align="center" style="text-align: center; background-color: #CCC;">&nbsp;</td>
                            </tr>
                        <?php
                        endfor;
                        ?>
                    </table>
                </td>
            </tr>

        </table>
    </div>
    <p>&nbsp;</p>
    <table border="0" width="100%">
        <tr>
            <td width="35%"><b>Total Session Length (in hours) :</b></td>
            <td width="15%" align="right"><?php echo convertMinToHrs($mins);?>&nbsp;&nbsp;</td>
            <td width="30%" align="right" style="text-align: right;"><b>Total Amount Due:</b></td>
            <td width="20%"> <?php echo $total;?> </td>
        </tr>
        <tr>
            <td colspan="4">&nbsp;</td>
        </tr>
    </table>
    <table  border="0" width="100%">
        <tr>
            <td colspan="2">
                <?php

                if($data['bill_service_type'] != "35"){
                    ?>
                    <b>SETSS Direct Service Provider:</b>  I  certify that I have provided SETSS services on the dates, times, group size and duration indicated herein.  I understand that when completed and filed, this form must be retained for 6 years and is subject to audit by the Department of Education.
                <?php } ?>
            </td>
        </tr>
        <tr>
            <td width="70%" align="center" style="color: #E7E7E7;"><br/><br/><br/>Actual Provider Signature Required</td>
            <td width="30%" align="left" style="text-align: right;"><br/><br/><br/><b>Date</b>&nbsp; ____________________</td>
        </tr>
    </table>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p style="font-size:9pt;">
        <b>Parent and/or Principal:</b> By my signature, I acknowledge that I have reviewed this billing form and that, to the best of my knowledge, the sessions were provided as indicated
    </p>
    <p>&nbsp;</p>
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
    <p>
        &nbsp;
    </p>
</div>