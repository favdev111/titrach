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