<?php
/**
 * TbDatepicker class file.
 * @author Sam Stenvall <sam.stenvall@arcada.fi>
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2012-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package bootstrap.widgets
 * @since 0.10.0
 */

/**
 * Bootstrap datepicker widget.
 */
class TbDatepicker extends CInputWidget
{
	const TYPE_INLINE = 'attached';
	const TYPE_COMPONENT = 'component';
	
	
	/**
	 * @var array the options for the Bootstrap JavaScript plugin.
	 */
	public $options = array();
	/**
	 * @var string[] the JavaScript event handlers.
	 */
	
	public $type = self::TYPE_INLINE;
	
	public $events = array();
	/**
	 * @var array the HTML attributes for the widget container.
	 */
	public $htmlOptions = array();

	/**
	 * Initializes the widget.
	 */
	public function init()
	{
		if (!isset($this->htmlOptions['id']))
			$this->htmlOptions['id'] = $this->getId();

		$this->htmlOptions['type'] = 'text';

		if (!isset($this->options['format']))
			$this->options['format'] = 'mm/dd/yyyy';

		if (!isset($this->options['autoclose']))
			$this->options['autoclose'] = 'true';

		if (!isset($this->options['weekStart']))
			$this->options['weekStart'] = 0; // Sunday

		Yii::app()->bootstrap->registerDatepicker();
	}

	/**
	 * Runs the widget.
	 */
	public function run()
	{
		list($name, $id) = $this->resolveNameID();
		$html = '';
		if ($this->hasModel())
			$html .= CHtml::activeTextField($this->model, $this->attribute, $this->htmlOptions);
		else
			$html .=  CHtml::textField($name, $this->value, $this->htmlOptions);
		
		$html = CHtml::openTag('div',array('id'=>'date_component_'.$id,'class'=>'input-append date')).$html.'<span class="add-on"><i class="icon-calendar"></i></span></div>';
		
		echo $html;
		
		$options = !empty($this->options) ? CJavaScript::encode($this->options) : '';

		ob_start();

		echo "jQuery('#date_component_{$id}').datepicker({$options})";
		foreach ($this->events as $event => $handler)
			echo ".on('{$event}', ".CJavaScript::encode($handler).")";

		Yii::app()->getClientScript()->registerScript(__CLASS__.'#date_component_'.$id, ob_get_clean().';');
	}
}