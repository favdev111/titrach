<?php
   Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/jquery.maskedinput-1.0.js');
?>

<div class="container-fluid fill-form">
    <div class="row-fluid">
		<div class="span9">
<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'forms-pages-form',
	'type'=>'horizontal',
	'enableAjaxValidation'=>false,
));
	echo CHtml::hiddenField('patient_ID',$patient_ID);
	
	$this->widget('bootstrap.widgets.TbTabs', array(
		'type'=>'tabs',
		'placement'=>'left', // 'above', 'right', 'below' or 'left'
		'tabs'=>$this->getTabs($pages,$model,$entity,array('Patient'=>$patient_ID)),
		'htmlOptions'=>array('class'=>'clearfix'),
		'id'=>'tabs',
		'events'=>array(
			'shown'=>'js:handlers.onTabShown',
			'show'=>'js:handlers.onTabShow')
	));

	$this->endWidget();?>
		</div>
		<div class="span3 sidebar">
			<!--sidebar content-->
				<?php
				$this->widget('PatientFilesWidget',array(
					'patient_ID'=>$patient_ID,
					'mode'=>PatientFilesWidget::MODE_FIRST_UPLOAD
				));
				?>		
		</div>
    </div>
</div>
<div id="modal_placeholder"></div>
<script type="text/javascript">
var system = {
	validationInProgress : false,
	steps_count: <?php echo count($pages);?>,
	current_tab: 1,
	form_validate: [],
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
		
<?php
if(count((array)$pages)>=1):?>
		 var page_id = $(sender).parents('fieldset').find('input[name="page_id"]').val();
		 if($.inArray(page_id,system.form_validate) == -1){
			system.form_validate.push(page_id);
		 }
<?php
endif;
?>
		var params =  $(sender).parents('fieldset').find('input,select,textarea').serialize()+'&action=validatePage&SID=<?php echo Yii::app()->session->sessionID ?>';
		$.getJSON('<?php echo $this->createUrl('form/validate');?>',params,function(data){
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
	refreshFilesList:function(){
		tools.showOverlay('.widget.patient-files');
		jQuery('.widget.patient-files .scrollbox ul').load('<?php echo $this->createUrl('files/list',array('pid'=>$patient_ID));?>',{},function(){
			tools.hideOverlay();
		}).error(function(){ alert('Error during get file list'); tools.hideOverlay();});
	}
}

$(document).ready(function(){
	$(".phone input").mask("(999) 999-9999");
	$(".date input").mask("99/99/9999").click(function(){
		tools.setCursor(this,0);
	});
	
   //Workaround for datepicker bug
	$(".date").focusout(function(){
	  $(this).data('date',$('input',this).val());
	  $(this).datepicker('update');
   });
	
   //Disable datepicker/timepicker dialog if input field is readonly
   setTimeout(function(){
	  $('.bootstrap-timepicker input[readonly]').next('.add-on')
		 .click(function(){
			alert('You don\'t have rights to edit time!');
			return false;
		 }).each(function(){
			$(this).off(".timepicker");
		 });
	  $('.date input[readonly]').next('.add-on').off()
		 .click(function(){
			alert('You don\'t have rights to edit date!');
			return false;
		 }); 		 
   },200);
   //End Of Disable


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
		 handlers.validate($(this).find('fieldset button[type="submit"]').first(),function(){
		 form_validated = true;
		 $('#forms-pages-form').submit()
		 })
	  }
	  return false;
   })
<?php
else:?>
   $('#forms-pages-form').submit(function(){
	  if(system.form_validate.length==system.steps_count)
	  {
		 system.form_validate = [];
		 return true;
	  }else if (system.form_validate.length==system.steps_count-1) {
		 handlers.validate($(this).find('fieldset button[type="submit"]:visible').first(),function(){
			 $('#forms-pages-form').submit()
		 }); 
	  }else{
		 alert('You should go over all steps first!');		 
	  }
	  return false;
   });
   
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