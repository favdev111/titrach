<?php
$this->widget('bootstrap.widgets.TbAlert', array(
    'block'=>true,
    'fade'=>true, 
    'closeText'=>'&times;',
    'alerts'=>array( 
        'error'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'),
        'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), 
    ),
));

?>

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
    'id'=>'pay-rate-rules-form',
    'enableAjaxValidation'=>false,
    'type'=>'horizontal',
    'action'=>$this->createUrl('/settings/payroll/rules',array('model'=>$model->id))
)); ?>
<?php echo $form->errorSummary($rateRule); ?>
<div class="in-row clearfix">
    <?php echo $form->hiddenField($rateRule,'pay_rate_id'); ?>
    <?php echo $form->dropDownListRow($rateRule,'form_field_id',array('--Select field--'),array('id'=>'rate_fields','class'=>'span3')); ?>
    <?php echo $form->dropDownListRow($rateRule,'form_field_value_id',array('--Select field first --'),array('id'=>'rate_values','class'=>'span3')); ?>
    <?php echo $form->dropDownListRow($rateRule,'rule',PayRateRules::getRateRules(),array('class'=>'span1')); ?>
    <?php  echo $form->textFieldRow($rateRule,'additional_rate',array('class'=>'span1 decimal','maxlength'=>6,'prepend'=>'$')); ?>
    <?php  echo $form->textFieldRow($rateRule,'sort_order',array('class'=>'span1','maxlength'=>2)); ?>
</div>
<div class="form-actions">
        <?php $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType'=>'submit',
            'type'=>'primary',
            'label'=>'Add',
            'htmlOptions'=>array(
                'onclick' => 'handlers.submitRule(this);return false;',
            ),
        )); ?>
    </div>
<?php $this->endWidget(); ?>
<?php $this->widget('bootstrap.widgets.TbGridView',array(
    'id'=>'provider-grid',
    'dataProvider'=>new CArrayDataProvider($model->rateRules, array(
        'id'=>'id',
        'sort'=>array(
            'attributes'=>array(
                 'order',
                ),
        ),
        'pagination'=>false,
    )),
    'type' => 'striped bordered condensed',
    'columns'=>array(
        array('name'=>'id','header'=>'ID'),
        array(
            'name'=>'form_field_id',
            'header'=>'Field',
            'value'=>function($data)
            {
                $field = FormFields::model()->findByPk($data['form_field_id']);
                if($field)
                    return $field->title;
                return 'UNDEFINED';
            }
        ),
        array(
            'name'=>'form_field_value_id',
            'header'=>'Value',
            'value'=>function($data)
            {
                $field = FieldsValues::model()->findByPk($data['form_field_value_id']);
                if($field)
                    return $field->form_field_title;
                return 'UNDEFINED';
            }
        ),
        array(
            'name'=>'rule',
            'header'=>'Rule',
            'value'=>'Common::getStringById(PayRateRules::getRateRules(),$data["rule"])'
              ),
        array('name'=>'additional_rate','header'=>'Additional Rate'),
        array('name'=>'order','header'=>'Order'),
        array(
            'class'=>'bootstrap.widgets.TbButtonColumn',
            'template'=>'{delete}',//{update}
            'updateButtonUrl'=>'Yii::app()->createUrl("/settings/payroll/editRule",array("id"=>$data->id))',
            'deleteButtonUrl'=>'Yii::app()->createUrl("/settings/payroll/deleteRule",array("id"=>$data->id))'
        ),
    ),
)); ?>
<script type="text/javascript">
jQuery(function($){
    handlers.selected.field = <?php echo $rateRule->form_field_id ? $rateRule->form_field_id : 'null' ?>;
    handlers.selected.value = <?php echo $rateRule->form_field_value_id ? $rateRule->form_field_id : 'null'  ?>;
    handlers.fillFields();
    handlers.fillValues();
    $('#rate_fields').change(function(){
        handlers.fillValues();
    });
})    
</script>