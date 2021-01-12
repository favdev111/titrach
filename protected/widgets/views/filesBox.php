<div class="widget patient-files">
	<h4 class="widget-title"><?php echo $title;?></h4>
	<div class="scrollbox">
		<ul class="file-list">
			<?php
				if(count($files) == 0):?>
					<li class="no-files">No files</li>
			<?php
				endif;
				foreach($files as $file): ?>
				<li class="file"><a class="delete-file" href="<?php echo $file['deleteUrl']?>"><i class="icon-trash"></i></a><a href="<?php echo $file['url']?>" class="file-icon <?php echo $file['ext'];?>"><span>
				<?php echo $file['filename']?></span></a></li>
			<?php	
				endforeach;
			?>
		</ul>
	</div>
	<div class="buttons">
		<?php
		$this->widget('bootstrap.widgets.TbButton', array(
				'label'=>'Upload',
				'type'=>'info', 
				'url'=>'/patients/files/upload/pid/'.$this->patient_ID,
				'icon'=>'icon-plus icon-white',
				'htmlOptions'=>array(
					'onClick'=>'handlers.openModal(this); return false;',
					'class'=>'pull-right'
				),
			)); ?>		
	</div>
</div>