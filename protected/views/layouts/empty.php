<?php /* @var $this Controller */ ?>
<?php $this->beginContent('//layouts/skeleton'); ?>

<div id="container">
	<?php echo $content; ?>
	<div id="space"></div>
</div>
<footer>
	Copyright &copy; <?php echo date('Y'); ?> by <?php echo CHtml::encode(Yii::app()->name);?><br/>
	All Rights Reserved.<br/>
</footer>
<?php $this->endContent(); ?>
