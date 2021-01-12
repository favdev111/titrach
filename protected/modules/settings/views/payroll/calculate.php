<?php
$this->breadcrumbs=array(
	'Pay Rates'=>array('index'),
	'Calculate',
);
setlocale(LC_MONETARY, 'en_US');
?>

<h2>Calculate payroll for Provider <?php echo $provider->getFullName(); ?></h2>

<?php
$this->widget('bootstrap.widgets.TbAlert', array(
        'block'=>true, // display a larger alert block?
        'fade'=>true, // use transitions?
        'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
        'alerts'=>array( // configurations per alert type
            'error'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
			'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
        ),
));
?>
<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'action'=>Yii::app()->createUrl($this->route,array('prid'=>$provider->id)),
	'method'=>'get',
	'type'=>'search',

)); ?>
<div class="row">
	<input type="hidden" name="FormsEntities[formsEntitiesFields][14]" value="<?php echo $provider->id; ?>" />
    <?php        
        //HARDCODE: id of date field: 15
        echo $form->datepickerRow($entities,'formsEntitiesFields[15][date_from]',array('class'=>'span2','placeholder'=>'Date from','labelOptions'=>array('label'=>false)));
		echo " ", $form->datepickerRow($entities,'formsEntitiesFields[15][date_to]',array('class'=>'span2','placeholder'=>'Date to','labelOptions'=>array('label'=>false)));
    ?>
    <div style="display:inline-block; float: none; margin-left: 0; vertical-align: top" class="span3">
        <?php   echo $form->select2Row(
                    $entities,
                    'formsEntitiesFields[18]',
                    array(
                        'asDropDownList'=>true,
                        'class'=>'span3',
                        'options'=>array(
                            'placeholder'=>'--Select Service type--',
                        ),
                        'empty'=>'',
                        'multiple'=>'true',
                        'data'=>Forms::getFormFieldValuesAsArray("2","speech_session_service_type",'id'),
                        'labelOptions'=>array('label'=>false)
                    )
                );
        ?>
    </div>
    <div style="display:inline-block; float: none; margin-left: 0; vertical-align: top" class="span3">
        <?php   echo $form->select2Row(
                    $entities,
                    'formsEntitiesFields[20]',
                    array(
                        'asDropDownList'=>true,
                        'class'=>'span3',
                        'options'=>array(
                            'placeholder'=>'--Select Session type--',
                        ),
                        'empty'=>'',
                        'multiple'=>'true',
                        'data'=>Forms::getFormFieldValuesAsArray("2","speech_session_session_type",'id'),
                        'labelOptions'=>array('label'=>false)
                    )
                );
        ?>
    </div>
</div>
<div class="form-actions">
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType'=>'submit',
        'type'=>'primary',
        'size'=>'small',
        'label'=>'Search',
    )); ?>
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType'=>'reset',
        'type'=>'primary',
        'size'=>'small',
        'label'=>'Reset Search',
    )); ?>
	<div class="pull-right" style="margin-right: 15px;">
	<?php
		$this->widget('bootstrap.widgets.TbButton', array(
			'label'=>'Export to excel',
			'type'=>'info',
			'icon'=>'download white',
			'buttonType'=>'link',
			'htmlOptions' => array('onclick'=>"var params = $(this).parents('form.form-search').serialize(); handlers.ExportXLS(params);return false;"),
			'url'=>'/settings/payroll/exportXls',
		));
	?>
	</div>	
</div>
<?php $this->endWidget(); ?>
<?php
if(is_array($data)):
    if(count($data)==0):?>
<div class="alert alert-warning">
    <p style="text-align: center;">
        <b>No records found for this provider!</b>
    </p>
</div>
    
<?php
    else:
?>
<table id="payroll" class="table table-striped">
    <thead>
        <tr>
            <th>Rate</th>
            <th>Interval</th>
            <th>Date</th>
            <th>Student</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
<?php
$total = 0;
foreach($data as $date=>$row):
?>
    <tr>
        <td></td>
        <td></td>
        <td colspan="3"><b><?php $d = new DateTime; $d->setTimestamp($date);
        echo $d->format('l F j Y'); ?></b></td>
    </tr>
<?php
    $services = $row['services'];
    ksort($services,SORT_NUMERIC);
    foreach($services as $service):
        foreach($service as $serv):
            $total +=$serv['rate'];
?>
    <tr>
        <td><?php echo sprintf('%01.2f',$serv['rate']); ?></td>
        <td><?php echo $serv['interval']; ?></td>
        <td><?php echo $serv['begin_date']->format('h:i A'); ?> - <?php echo $serv['end_date']->format('h:i A'); ?></td>
        <td><?php echo $serv['patient']->getFullName(); ?></td>
        <td><?php echo ($serv['g_size'] ? 'Group size:'.$serv['g_size'] : '') ; ?></td>
    </tr>
    <?php
        endforeach;
    endforeach;?>
<?php endforeach;?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="5">
                Total: $<?php echo sprintf('%01.2f',$total);?>
            </th>
        </tr>
    </tfoot>
</table>
<?php
    endif;
endif;
?>
<script type="text/javascript">
var handlers = {
	ExportXLS:function(par){
			$.post('<?php
			$params['prid'] = $provider->id;
			echo $this->createUrl('exportXls',$params);?>',par,function(response){
					if(response.status)
					{
						alert('<?php echo mb_convert_case('Payroll', MB_CASE_TITLE, 'UTF-8');?> has been successfully exported.');
						window.open('/public/export/'+response.filename);
					}
					else
					{
						if("message" in response)
							alert(response.message);
						else
							alert('Some errors were occurred during export.');
					}
					//tools.hideOverlay();
				},'json');
			return false;
		}		
}	
</script>













