<?php
if(isset($data['bill_type']))
{
	$template = '//form_templates/_undefined';
	switch($data['bill_type']){
		case BilledForms::BILL_TYPE_CSE :
			$template = '//form_templates/_cse';
			break;
		case BilledForms::BILL_TYPE_CPSE :
			$template = '//form_templates/_cpse';
			break;
		case BilledForms::BILL_TYPE_SETSS :
			$template = '//form_templates/_setss';
			break;
	}
	
	//Get all forms, that attached to this bill
	$billRows = BilledForms::model()->findAll(array('condition'=>'bill_id = :billId','params'=>array(':billId'=>$entity->id)));
	
	//proceed rows, get rows enities
	$rows = array();
	$services = array();
	$lngs = array();
	$locations = array();
	$size = array();
	foreach($billRows as $row)
	{
		$billRowsData  = $row->entity->getDataAsArray();
		$date = DateTime::createFromFormat('m/d/Y h:i A',$billRowsData['speech_session_start_date'].' '.$billRowsData['speech_session_start_time']);
		if($date)
			$rows[$date->getTimestamp()] = $billRowsData;
		if(isset($billRowsData['speech_session_language']))
			$lngs[$billRowsData['speech_session_language']] = $billRowsData['speech_session_language'];
		//if(isset($billRowsData['speech_session_service_type']))
		//	$services[$billRowsData['speech_session_service_type']] = $billRowsData['speech_session_service_type'];
		if(isset($billRowsData['speech_session_service_location']))
			$locations[$billRowsData['speech_session_service_location']] = $billRowsData['speech_session_service_location'];
		if(isset($billRowsData['speech_session_group_size']))
			$size[$billRowsData['speech_session_group_size']] = $billRowsData['speech_session_group_size'];
	}
	
	//If mandate selected during filling rows, then we will use group size from mandate
	$mandate = false;
	if(!empty($data['bill_mandate']))
	{
		$mandate = PatientsMandates::model()->findByPk($data['bill_mandate']);
		if($mandate)
			$size = $mandate->type == PatientsMandates::TYPE_INDIVIDUAL ?  array(1) : array($mandate->recommended_count);
	}
	
	//Read service from bill
	$f_s_t = FieldsValues::model()->findByPk($data['bill_service_type']);
	$services[$f_s_t->form_field_title] = $f_s_t->form_field_title;
	
	ksort($rows);
	$this->renderPartial($template,array('data'=>$data,'form'=>$form,'entity'=>$entity,'formFields'=>$formFields,'billRows'=>$rows,
										 'lngs'=>$lngs,'services'=>$services,'locations'=>$locations,'size'=>$size,'mandate'=>$mandate));
}

?>