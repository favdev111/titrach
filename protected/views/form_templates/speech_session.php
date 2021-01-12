<?php
extract($data);
$base_url = Yii::app()->getBaseUrl('true');
PDF::$base_url = $base_url;

?>
<h2><?php echo $form->getEntityRelatedTitle($entity);?></h2>
<h4 style="text-align: center;">Visit Information</h4>
<table width="600px;" style="width: 800px !important; margin:  0 auto;" class="table table-bordered">
	<tr>
		<td width="50%" align="right" class="text-right"><b>Student</b></td>
		<td width="50%" align="left" class="text-left"><?php $pt = Patient::model()->findByPk($speech_session_student); echo $pt ? $pt->getFullName(true) : '';?></td>
	</tr>
	<tr>
		<td width="50%" align="right" class="text-right"><b>Provider</b></td>
		<td width="50%" align="left" class="text-left"><?php $pr = Provider::model()->findByPk($speech_session_provider); echo $pr ? $pr->getFullName() : ''; ?></td>
	</tr>
	<tr>
		<td width="50%" align="right" class="text-right"><b>Start Date/time</b></td>
		<td width="50%" align="left" class="text-left"><?php echo $speech_session_start_date,' ',$speech_session_start_time ?></td>
	</tr>
	<tr>
		<td width="50%" align="right" class="text-right"><b>End time</b></td>
		<td width="50%" align="left" class="text-left"><?php echo $speech_session_end_time; ?></td>
	</tr>
	<tr>
		<td width="50%" align="right" class="text-right"><b>Language</b></td>
		<td width="50%" align="left" class="text-left"><?php echo ucfirst($speech_session_language); ?></td>
	</tr>
	<tr>
		<td width="50%" align="right" class="text-right"><b>Session Type</b></td>
		<td width="50%" align="left" class="text-left"><?php echo ucfirst($speech_session_session_type); ?></td>
	</tr>
	<tr>
		<td width="50%" align="right" class="text-right"><b>Service Location</b></td>
		<td width="50%" align="left" class="text-left"><?php echo ucfirst($speech_session_service_location); ?></td>
	</tr>
	<tr>
		<td width="50%" align="right" class="text-right"><b>Group Size</b></td>
		<td width="50%" align="left" class="text-left"><?php echo ucfirst($speech_session_group_size); ?></td>
	</tr>	
</table>
<h4 style="text-align: center;">Service Information</h4>
<table width="600px;" style="width: 800px !important; margin:  0 auto;" class="table table-bordered">
	<tr>
		<td width="50%" align="right" class="text-right"><b>Service type</b></td>
		<td width="50%" align="left" class="text-left"><?php echo $speech_session_service_type ?></td>
	</tr>
<?php if($speech_session_service_type!=='Special Education'):?>
	<tr>
		<td width="50%" align="right" class="text-right"><b>Service Description</b></td>
		<td width="50%" align="left" class="text-left"><?php echo implode(',<br />',(array)$speech_session_service_description); ?></td>
	</tr>
<?php else:?>
	<tr>
		<td width="50%" align="right" class="text-right"><b>SETSS SESSION NOTES / Checklist(optional):</b></td>
		<td width="50%" align="left" class="text-left"> <?php PDF::renderCeckboxGrid($formFields[35],$speech_session_optional_note,1,null,false); ?>  </td>
	</tr>

<?php endif;?>
	<tr>
		<td width="50%" align="right" class="text-right"><b>Progress Indicator</b></td>
		<td width="50%" align="left" class="text-left"><?php echo $speech_session_progress ?></td>
	</tr>
	<tr>
		<td width="50%" align="right" class="text-right"><b>Session Notes</b></td>
		<td width="50%" align="left" class="text-left"><?php echo $speech_session_notes; ?></td>
	</tr>
</table>

