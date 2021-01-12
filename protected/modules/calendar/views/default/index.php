<?php
$this->breadcrumbs=array(
	'Services Calendar'
);
$this->widget('bootstrap.widgets.TbAlert', array(
        'block'=>true, 
        'fade'=>true,
        'closeText'=>'&times;', 
        'alerts'=>array( 
            'error'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), 
			'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'),
			'info'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), 
        ),
));
?>
<h1><?php
$this->widget('bootstrap.widgets.TbButton', array(
		'label'=>'Download PDF',
		'type'=>'success',
		'icon'=>'download',
		'size'=>'small',
		'url'=>$this->createUrl('renderPdf'),
		'htmlOptions'=>array(
			'target'=>'_blank',
			'class'=>'pull-right',
			'style'=>'margin-top:10px;'
		),
	)); ?>	
Services Calendar</h1>
<div class="search-form compacted one-row" style="display:block">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->
<?php
$this->widget('CalendarWidget',array(
	'eventsFeedUrl'=>$this->createUrl('getEvents'),
	)
);
?>
<div id="modal_placeholder"></div>
<script type="text/javascript">
	jQuery(function($){
		$('body').delegate('.event-fulfilled','click',function(){
			var sender = $(this);
			if(confirm('Mark this event as completed?'))
			{
				tools.showOverlay($('#ServicesCal'));
				$.post(sender.attr('href'),function(){
					tools.hideOverlay($('#ServicesCal'));
					$('#ServicesCal').fullCalendar( 'refetchEvents' );		
				}).error(function(){
					alert('Error during fulfilled  request')
				})				
			}
			return false;

		});
		$('body').delegate ('.event-delete','click',function(){
			var sender = $(this);
			if(confirm('Are you sure you want do delete this event?')){
				tools.showOverlay($('#ServicesCal'));
				$.post(sender.attr('href'),function(){
					tools.hideOverlay($('#ServicesCal'));
					$('#ServicesCal').fullCalendar( 'refetchEvents' );	
				}).error(function(){
					alert('Error during delete  request')
				})		
				
			}
			return false;
		});
	})
	var handlers = {
		openModal: function(sender){
			$sender = $(sender);
			tools.showOverlay('#page');
			$('#modal_placeholder').load($sender.attr('href'),{},function(){
				tools.hideOverlay();
			}).error(function(){ alert('Error during request'); tools.hideOverlay();});
		},
		updateReccurence: function(ev){
			var time = ev.date;
			var day_of_month = time.getDate();
			var weekday = $.fn.datepicker.dates.en.days[time.getDay()];
			$('#every_week_day').text(weekday).parents('label').find('input').val('next '+weekday);
			$('#every_day_in_month').text(day_of_month).parents('label').find('input');
			$("#every_month_day").text($.fn.datepicker.dates.en.monthsShort[time.getMonth()] + " " + day_of_month ).parents('label').find('input');
		},
		submitEvent:function(sender){
			var $sender = $(sender);
			var $modal = $sender.parents("#modal_dlg").find('.modal-body');
			var $form  = $modal.find('form');
			var param = $form.serialize();
			tools.showOverlay($modal);
			$.post($form.attr('action'),param,function(data){
				$modal.html(data)
			},'html').error(function(){alert("Error during scheduling event!"); tools.hideOverlay()});			
		},
		
	}
	
</script>