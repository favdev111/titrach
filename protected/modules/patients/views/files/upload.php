<?php
	$heading = $patient->isNewRecord ? 'Upload files for new student' : 'Upload files for'.$patient->getFullName(true);
?>
<div id="modal_dlg" class="modal fade wide">
	<div class="modal-header">
		<a class="close" data-dismiss="modal">&times;</a>
		<h4><?php echo $heading ?></h4>
	</div>
	<div class="modal-body">
	<p>You can upload one or multiple additional files that will be saved in the student's folder</p>
    <br>
    <?php
		$this->widget('xupload.XUpload', array(
			'url' => Yii::app()->createUrl("patients/files/uploadFile"),
			'model' => $upload_form,
			'attribute' => 'file',
			'multiple' => true,
		));
		?>
		
		
	</div>
	<div class="modal-footer">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'label'=>'Close',
			'url'=>'#',
			'htmlOptions'=>array('data-dismiss'=>'modal'),
		)); ?>
	</div>
</div>


<script type="text/javascript">
	var $dlg = jQuery('#modal_dlg');
	$dlg.modal({show:true});
	$dlg.on('hidden',function(){
		jQuery('#modal_dlg').remove();
		handlers.refreshFilesList();
		delete $dlg;
	});
	jQuery('a[rel="tooltip"]',$dlg).tooltip();
	jQuery('#XUploadForm-form').fileupload({'url':'<?php echo Yii::app()->createUrl("patients/files/uploadFile");?>'});
</script> 