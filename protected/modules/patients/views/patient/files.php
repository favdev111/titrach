<?php
$this->breadcrumbs=array(
	'Students'=>array('/patients/patient/index'),
	$patient->getFullName()=>'/patients/patient/history/pid/'.$patient->id,
	'Files'
);
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
<div class="page-patient-files">
<?php
$this->widget('PatientFilesWidget',array(
	'patient_ID'=>$patient->id,
	'mode'=>PatientFilesWidget::MODE_FIRST_UPLOAD,
	'title'=>$patient->getFullName().' files',
));
?>
</div>
<div id="modal_placeholder"></div>
<script type="text/javascript">
	var handlers={
		openModal: function(sender){
			$sender = $(sender);
			tools.showOverlay('#page');
			$('#modal_placeholder').load($sender.attr('href'),{},function(){
				tools.hideOverlay();
			}).error(function(){ alert('Error during request'); tools.hideOverlay();});
		},
		refreshFilesList:function(){
			tools.showOverlay('.widget.patient-files');
			jQuery('.widget.patient-files .scrollbox ul').load('<?php echo $this->createUrl('files/list',array('pid'=>$patient->id));?>',{},function(){
				tools.hideOverlay();
			}).error(function(){ alert('Error during get file list'); tools.hideOverlay();});
		}		
	}
	jQuery(function($){
		$('ul.file-list').on('click','li.file .delete-file', function(e){
			var href = $(this).attr('href');
			if (!confirm('Are you sure want to delete this file?')) {
				return false;
			}
			tools.showOverlay('.widget.patient-files');
			$.post(href,function(data){
				if (!data) {
					handlers.refreshFilesList();
				}else{
					alert(data);
				}
			},'text').error(function(){ alert('Error during server request!');});
			return false;
		});
	});
</script>