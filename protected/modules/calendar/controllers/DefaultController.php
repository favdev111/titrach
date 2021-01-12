<?php

class DefaultController extends Controller
{
	
	public function filters()
	{
		return array(
			'ajaxOnly + getEvents, schedule, fulfilled,delete',
			'accessControl',
		);
	}
        
    public function accessRules()
	{
		return array(
			array('allow', 
				'actions'=>array('index','getEvents','renderPdf','fulfilled','delete'),
				'roles'=>array('doctor'),
			),
			array('allow',
				'actions'=>array('schedule'),
				'roles'=>array(User::ROLE_MANAGER),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}	
	
	public function actionIndex()
	{
		$model = new Event('search');
		if(isset($_POST['Event']))
		{
			$model->unsetAttributes();
			$model->attributes = $_POST['Event'];
			Yii::app()->session['event_provider_id'] = (int)$_POST['Event']['provider_id'];
			Yii::app()->session['event_patient_id'] = (int)$_POST['Event']['patient_id'];
		}else{
			unset(Yii::app()->session['event_provider_id']);
			unset(Yii::app()->session['event_patient_id']);
		}
		
		$this->render('index',array('model'=>$model));
	}
	
	public function actionGetEvents()
	{
		$current = time();
		$sStamp = isset($_GET['start']) ? $_GET['start'] : $current -604800;
		$eStamp = isset($_GET['end']) ? $_GET['end'] : $current + 604800;
		$eStamp   = strtotime("tomorrow", $eStamp) - 1;
		
		Yii::app()->session['event_start_stamp'] = $sStamp;
		Yii::app()->session['event_end_stamp'] = $eStamp;
		
		$condition = 'start_date_timestamp >= :sStamp AND start_date_timestamp <= :eStamp AND event_type = :eType';
		$params = array('sStamp'=>$sStamp,'eStamp'=>$eStamp,'eType'=>Event::EVENT_TYPE_NEW_FORM);
		if(!empty(Yii::app()->session['event_provider_id']))
		{
			$condition .=' AND provider_id = '.Yii::app()->session['event_provider_id'];
		}
		if(!empty(Yii::app()->session['event_patient_id']))
		{
			$condition .=' AND patient_id = '.Yii::app()->session['event_patient_id'];
		}
		
		Patient::disableDefaultScope();
		$events = Event::model()->onlyAllowed()->with('patient','provider')->findAll(array(
								'condition'=>$condition,
								'params'=>$params,
								'order'=>'start_date_timestamp, provider_id'
								));
		$result = array();
		foreach($events as $event)
		{
			$controls = '<span class="event-controls">
								<a title="edit" href="'.$this->createUrl('schedule',array('pid'=>$event->patient_id,'eid'=>$event->id)).'" class="event-edit" onclick="handlers.openModal(this);return false;"><i class="icon-pencil icon-white"></i></a>';
								
			if($event->event_meta || $event->event_type != Event::EVENT_TYPE_NEW_FORM)
				$controls .= '&nbsp;&nbsp;<a title="complete" href="'.$this->createUrl('fulfilled',array('id'=>$event->id)).'" class="event-fulfilled"><i class="icon-check-sign"></i></a>';
			
			$controls .= '&nbsp;&nbsp;<a title="delete" href="'.$this->createUrl('delete',array('id'=>$event->id)).'" class="event-delete"><i class="icon-trash"></i></a>
						</span>';
			
			//Restrict controls for therapist
			if(Yii::app()->user->role == User::ROLE_DOCTOR)
				$controls = '';
			
			if($event->fulfilled)
				$controls = Yii::app()->user->role == User::ROLE_ADMIN ? '<b class="fulfilled">Completed</b>'.$controls :  '<b class="fulfilled">Completed</b>';
			if($event->event_meta)
			{
				if(FormsEntities::checkField((int)$event->event_meta,25))
					$controls .= '<div style="text-align:center; margin: 5px 0;"><span class="badge badge-warning ">Note exist</span></div>';
			}

			//var_dump($event->patient_id);
			$result[] = array(
				'title'=>'<b>'.date('h:i A',$event->start_date_timestamp).' to '.date('h:i A',$event->end_date_timestamp).'</b>  PT '.
							($event->end_date_timestamp - $event->start_date_timestamp)/60 .'m: '.$event->patient->getFullName().'<br> Pr:'.$event->provider->getFullName().$controls
							,
				'start'=>$event->start_date_timestamp,
				'url'=>!empty($event->event_meta) ? $this->createUrl('/patients/form/view',array('eid'=>$event->event_meta,'pid'=>$event->patient_id,'fid'=>2)) : '#',
				'allDay'=>true,
				'id'=>$event->start_date_timestamp
			);
		}
		
		echo CJSON::encode($result);
	}	 	
	
	public function actionRenderPDF()
	{
		$fn = 'Services_Calendar_'.date('Y-m-d_H_i_s').'pdf';
		
		$sStamp = Yii::app()->session['event_start_stamp']+604800;
		$sStamp = mktime(0,0,0,date('n',$sStamp),1,date('Y',$sStamp));
		$eStamp = Yii::app()->session['event_end_stamp'];
		
		$condition = 'start_date_timestamp >= :sStamp AND start_date_timestamp <= :eStamp AND event_type = :eType';
		$params = array('sStamp'=>$sStamp,'eStamp'=>$eStamp,'eType'=>Event::EVENT_TYPE_NEW_FORM);
		if(!empty(Yii::app()->session['event_provider_id']))
		{
			$condition .=' AND provider_id = '.Yii::app()->session['event_provider_id'];
		}else{
			if(Yii::app()->user->getRole() != User::ROLE_ADMIN)
			{
				$condition .=' AND provider_id IN ('.implode(',',Yii::app()->user->getRelatedProviders()).')';
			}
		}
		if(!empty(Yii::app()->session['event_patient_id']))
		{
			$condition .=' AND patient_id = '.Yii::app()->session['event_patient_id'];
		}
		Patient::disableDefaultScope();
		$results = Event::model()->with('patient','provider')->findAll(array(
						'condition'=>$condition,
						'params'=>$params,
						'order'=> 'start_date_timestamp,provider_id '
						));
		$events = array();
		foreach($results as $r)
		{
			$events[date('j', $r->start_date_timestamp)][] = $r;
		}
		$cMonth = date('n',$sStamp);
		$cYear = date('Y',$sStamp);		
		$html = $this->renderPartial('//form_templates/calendar',array('events'=>$events,'cMonth'=>$cMonth,'cYear'=>$cYear),true);
		//echo $html;
		//Yii::app()->end();
		PDF::render($fn,$html,array('outputMode'=>'I','orientation'=>'L'));
	}
	
	/**
	 * ajax call for schedule event
	 * @param int $pid patient's id
	 */
	public function actionSchedule($pid,$eid = false)
	{
		if($eid)
		{
			$model  = $this->loadModel($eid);
		}else{
			$model= new Event();
		}
		$patient = Patient::model()->findByPk($pid);
		if($patient===null)
			throw new CHttpException(404,'The requested page does not exist.');
		
		
		$model->patient_id = (int)$pid;

		$error = false;
		if(isset($_POST['Event']))
		{
			
			$model->attributes = $_POST['Event'];
			if(empty($_POST['event-date']))
			{
				$model->addError('event-date','Event date required field');
			}
			if(empty($_POST['event-time']))
			{
				$model->addError('event-time','Event time required field');
			}
			if(empty($_POST['event-duration']))
			{
				$model->addError('event-duration','Event duration required field');
			}
			if(!$model->hasErrors())
			{
				$model->event_type = Event::EVENT_TYPE_NEW_FORM;
				$time_str = $_POST['event-date'].' '.$_POST['event-time'];
				$time_str = strtotime($time_str);
				$model->start_date_timestamp = $time_str;
				$model->end_date_timestamp = $time_str  + (int)$_POST['event-duration']*60;
			}
			
			
			if(isset($_POST['enable-reccurence']) && $_POST['enable-reccurence']==1)
			{
				$model->reccuring_string = $_POST['rec'];
			}
			
			
			if(!$model->hasErrors())
			{
				if(!$model->save())
				{
					$error = true;
				}else{
					$error = false;
					$this->renderPartial('_ajax_schedule_event_success');
					Yii::app()->end();
				}
				
			}else{
				$error = true;
			}
		}
		$this->renderPartial('_ajax_schedule_event',array('patient'=>$patient, 'error'=>$error,'model'=>$model));
	}
	
	public function actionFulfilled($id)
	{
		$event = $this->loadModel($id);
		$event->fulfilled();
	}
	
	public function actionDelete($id)
	{
		$event = $this->loadModel($id);
		$event->delete();
	}
	
	
	public function loadModel($id,$with = false)
	{
		if($with)
		{
			$model=Event::model()->with($with)->findByPk($id);
		}else
			$model=Event::model()->findByPk($id);	
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
	
}