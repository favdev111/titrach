<?php
   Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/jquery.maskedinput-1.0.js');
?>

<div class="container-fluid fill-form">
    <div class="row-fluid">
		<div class="span12">
<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'forms-pages-form',
	'type'=>'horizontal',
	'enableAjaxValidation'=>false,
));
	
	$this->widget('bootstrap.widgets.TbTabs', array(
		'type'=>'tabs',
		'placement'=>'above', // 'above', 'right', 'below' or 'left'
		'tabs'=>$this->getTabs($pages,$model,$entity),
		'htmlOptions'=>array('class'=>'clearfix'),
		'id'=>'tabs',
		'events'=>array(
			'shown'=>'js:handlers.onTabShown',
			'show'=>'js:handlers.onTabShow')
	));
	?>
	
<h4>Services Session</h4>
<?php $this->actionGrid();?>
<div class="form-actions">
<?php
	$buttons = array();
	/*if($current>1)
	{
		$buttons[] = array(
					'label'=>'Back',
					'icon'=>'arrow-left',
					'htmlOptions'=>array(
						'onclick'=>'handlers.prevStep(this);return false;'
					));
	}
	if($entity || $current == $steps_count)
	{		$buttons[] = array(
					'label'=>'Save',
					'icon'=>'ok',
					'buttonType'=>'submit',
					'htmlOptions'=>array(
						//'onclick'=>'handlers.saveChanges(this);return false;'
					));
	}
    if($current==1 || $current < $steps_count)
	{
		$buttons[] = array(
					'label'=>'Next',
					'icon'=>'arrow-right',
					'htmlOptions'=>array(
						'onclick'=>'handlers.nextStep(this);return false;'
					));
		
	}
	
	*/
		$buttons[] = array(
					'label'=>'Fill Rows',
					'icon'=>'align-justify',
					'htmlOptions'=>array(
						'onclick'=>'handlers.fillRelatedRows(this);return false;'
					));	
	
		$buttons[] = array(
					'label'=>'Save',
					'icon'=>'ok',
					'buttonType'=>'submit',
					'disabled'=>true,
					'active'=>false,
					'htmlOptions'=>array(
						//'onclick'=>'handlers.saveChanges(this);return false;'
					));	
?>

<?php $this->widget('bootstrap.widgets.TbButtonGroup', array(
    'buttons'=>$buttons,
)); ?>

				</div><!-- //.form_actions -->	
	
<?php 	$this->endWidget();?>
		</div>
    </div>
</div>
<div id="modal_placeholder"></div>
<script type="text/javascript">
var system = {
	validationInProgress : false,
	steps_count: <?php echo count($pages);?>,
	current_tab: 1
}
var handlers ={
	nextStep:function(sender){
		var $sender = $(sender);
		handlers.validate($sender,function(){
			tools.enableTab('#tabs',system.current_tab);
			tools.activateTab('#tabs',system.current_tab);
		})
	},
	prevStep:function(sender){
		var $sender = $(sender);
			tools.activateTab('#tabs',system.current_tab-2);
		},	
	validate:function(sender,callback){
		if(system.validationInProgress) return false;
			system.validationInProgress = true;
		$("div.control-group.error").removeClass('error').find(".help-inline").fadeOut(function(){
			$(this).remove();   
		});
		sender = $(sender);
		tools.showOverlay("#tabs");
		var params =  $(sender).find('input,select,textarea').serialize()+'&action=validatePage&SID=<?php echo Yii::app()->session->sessionID ?>';
		$.getJSON('<?php echo $this->createUrl('/patients/form/validate');?>',params,function(data){
			if(!('errors' in data)){
				callback();
				tools.hideOverlay();                    
			}else{
				for(i in data.errors){
					$('#id_'+i).parents('div.controls').append('<span class="help-inline">'+data.errors[i]+'</span>').parents('.control-group').addClass('error');
				}
				tools.hideOverlay();
			}
			system.validationInProgress = false;
		}).error(function() {
			tools.hideOverlay();
			system.validationInProgress = false;
			alert("Error during validation!");
		});
		return true;
	},
	
	onTabShown:function(e){
		system.current_tab = $(e.target).data('id');	
	},
	openModal: function(sender){
		$sender = $(sender);
		tools.showOverlay('#page');
		$('#modal_placeholder').load($sender.attr('href'),{},function(){
			tools.hideOverlay();
		}).error(function(){ alert('Error during request'); tools.hideOverlay();});
	},
	
	
	fillRelatedRows: function(sender)
	{
		var params = {
			is_search: 1,
			patient_id : $('#id_bill_patient').val(),
			provider_id:$('#id_bill_provider').val(),
			agency_id:$('#id_bill_agency').val(),
			service_type: $('#id_bill_service_type :selected').val(),
			mandate_id: $('#id_bill_mandate').val()
		}
		$.fn.yiiGridView.update('bill-rows-grid', {
			data: $.param(params)
		});

		return false;
	}
}

$(document).ready(function(){
	$(".phone input").mask("(999) 999-9999");
	$(".date input").mask("99/99/9999").click(function(){
		tools.setCursor(this,0);
	});
	
	$(".date").focusout(function(){
	  $(this).data('date',$('input',this).val());
	  $(this).datepicker('update');
   });	
	
<?php
// If page have only one page, then add validation on submit
if(count((array)$pages)<=1):?>

   var form_validated = false;
   $('#forms-pages-form').submit(function(){
	  if(form_validated)
	  {
		 form_validate = false;
		 return true;
	  }else{
		 handlers.validate($(this).find('fieldset').first(),function(){
		 form_validated = true;
		 $('#forms-pages-form').submit()
		 })
	  }
	  return false;
   })
<?php
endif;

?>	
});

//Convert select
    $('.convert-to-related-checkbox select').each(function(){
        var $initial = $(this), name = $initial.attr('name'), elements_html ='';
        $initial.hide();        
        $initial.attr('name','converted_'+name);
        var select_html = '<select id="'+name+'_converted"><option value="--">-Select Diagnoses-</option>';
        $('optgroup',$initial).each(function(index){
            select_html += '<option value="'+index+'">'+($(this).attr('label'))+'</option>';
            elements_html += '<div class="elements-wrap" style="display:none" id="elements_for_'+index+'">'
            $('option',$(this)).each(function(){
                elements_html +='<label class="checkbox"><input type="checkbox" name="'+name+'[]" value="'+$(this).val()+'" '+($(this).attr('selected')=='selected' ? 'checked="checked"':'')+'/>'+$(this).text()+'</label>' 
            })
            elements_html +='</div>'
        });
        select_html +='</select>';
        var $select = $(select_html);
        var $wrap = $('<div class="wrap-for-converted-select"></div>').append($select).insertAfter($initial);
        $wrap.append(elements_html);
        $select.change(function(){
            $('.elements-wrap:visible',$wrap).hide();
            $('#elements_for_'+$(this).val(),$wrap).show();
        });
        
    });
    
//Convert related textarea
    $('.convert-to-checkbox-related').each(function(){
        var $initial = $('textarea',this);
        var $val =$initial.val();
        var $label = $initial.parent('div').prev('label').text();
        var html  = '<label class="checkbox"><input type="checkbox" >'+$label+'</label><br/>';
        var $checkbox=  $(html).insertBefore($initial).find('input');
        if($.trim($val)!=''){
            console.log($checkbox);
            $checkbox.attr("checked","checked");
        }else{
            $initial.attr("disabled","disabled");
        }
        $checkbox.change(function(){
            if($(this).filter(':checked').length > 0){
                $initial.removeAttr('disabled');
            }else{
                $initial.attr('disabled',true);
            }
        });
        
    });

</script>