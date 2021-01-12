<?php
class CalendarWidget extends CWidget{
	
	public $eventsFeedUrl = '';
	public $fullcalendar = array();
	public $id = 'ServicesCal';

	public function init()
    {
        // этот метод будет вызван внутри CBaseController::beginWidget()
		$this->registerClientScript();
		parent::init();
    }
	
	public function run()
	{
		$this->render('calendar', array('id' => $this->id));			
	}
	
	public function registerClientScript()
	{
		$cs = Yii::app()->getClientScript();
        $scriptUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.widgets.assets.js'));
        $cs->registerCssFile($scriptUrl . '/fullcalendar/fullcalendar.css');
        $cs->registerCoreScript('jquery');
        $cs->registerScriptFile($scriptUrl . '/fullcalendar/fullcalendar.min.js');
        $cs->registerScriptFile($scriptUrl . '/servicescal.js');      
		$param['id'] = $this->id;
		$param['feed_url'] = $this->eventsFeedUrl;
        $param['fullcalendar'] = $this->fullcalendar;
        $param = CJavaScript::encode($param);
        $js = "jQuery().servicescal($param);";
        $cs->registerScript(__CLASS__ . '#'.$this->id, $js);
	}

	
}
?>