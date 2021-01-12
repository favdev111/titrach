<?php 


$this->widget('bootstrap.widgets.TbTabs', array(
	'type'=>'tabs',
	'placement'=>'above', // 'above', 'right', 'below' or 'left',
	'id'=>'formTabs',
	'tabs'=>array(
		array('label'=>'Form Settings', 'content' => $this->renderPartial('_form_tab', array('model' => $model), true), 'id' => 'form-tab', 'active' => true),
		array('label'=>'Form Page(s)', 'id' => 'page-tab',
				'content'=> $model->isNewRecord  ?
									'<div class="well"><p>You should to save form before you can add page</p></div>'
									: $this->renderPartial('_pages_tab', array('model' => $model), true),),
	),
));
?>
<div id="modal_placeholder"></div>
 <script type="text/javascript">
	var system   = {
		form_id: <?php echo $model->id?>,
		need_page_refresh:false
	};
	var loadPageContent = function(e) {
		var tabId = e.target.getAttribute("href");
	
		var ctUrl = '';
		
		pageId = tabId.replace('#page-','');
		if(pageId == 0)
		{
			return true
		}
		tools.showOverlay('#page-tab .tab-content');
		ctUrl = '/form/pages/update/id/'+pageId;
		
		if(ctUrl != '') {
		    $.ajax({
			url      : ctUrl,
			type     : 'POST',
			dataType : 'html',
			cache    : false,
			data:{},
			success  : function(html)
			{
			    jQuery(tabId).html(html);
				tools.hideOverlay($(tabId));
			},
			error:function(){
			    alert('Request failed');
				tools.hideOverlay($(tabId));
			}
		    });
		}

		e.preventDefault();
		return false;
	}
	var handlers = {
		formPageSubmit: function(sender){
			var $sender = $(sender);
			var $form  = $sender.parents('form');
			var param = $form.serialize();
			tools.showOverlay("#page-tab");
			$.post($sender.attr('formaction'),param,function(data){
				if('status' in data)
				{
					switch(data.status){
						case 'success':
							tools.hideOverlay();
							if('mode' in data){
								$('#page-tab .tab-pane.active').html(data.html);
							}else{
								handlers.loadPageTabContent();								
							}
							break;
						case 'invalid':
							$('#page-tab .tab-pane.active').html(data.html);
							tools.hideOverlay();
							break;
						default:
							alert('Undefined state');
							tools.hideOverlay();
					}
				}
			},"json").error(function(){alert("Error during request!"); tools.hideOverlay()});
		},
		formFieldSubmit:function(sender){
			var $sender = $(sender);
			var $modal = $sender.parents("#modal_dlg").find('.modal-body');
			var $form  = $modal.find('form');
			var param = $form.serialize();
			tools.showOverlay($modal);
			$.post($form.attr('action'),param,function(data){
				switch (data.status){
					case 'success':
						$modal.next('.modal-footer').find('button').text('Save');
						system.need_page_refresh = true;
						break;
					case 'error':
						break;
				}
				
				$modal.html(data.html)
			},'json').error(function(){alert("Error during saving field data!"); tools.hideOverlay()});			
		},
	
		loadPageTabContent : function(){
			tools.showOverlay('#page-tab');
			$("#page-tab").load('/form/forms/pagesTab/id/'+system.form_id,{},function(){
				tools.hideOverlay();
				$('.nav-tabs a',$(this)).live('click',function(e){
					loadPageContent(e);
				})
			}).error(function(){ tools.hideOverlay()});
		},
		openModal: function(sender){
			$sender = $(sender);
			tools.showOverlay('#page-tab');
			$('#modal_placeholder').load($sender.attr('href'),{},function(){
				tools.hideOverlay();
			}).error(function(){ alert('Error during request'); tools.hideOverlay();});
		},
		addValueRow:function(sender,tpl_id,counter){
			counters[counter]++;
			var row = $(sender).parents('table').find('tbody').append(tpl.parse(tpl[tpl_id],{index:counters[counter]}));
			return false;
		},
		deleteValueRow:function(sender,counter){
			$(sender).parents('tr').remove();
			return false;
		}
		
	}

	jQuery(document).on('click','#fields-grid a.delete',function() {
		if(!confirm('Are you sure you want to delete this item? All field values will be deleted too.'))
			return false;
		$.post($(this).attr('href'),{},function(data){
			tools.reloadCurrentTab('#pages-tab');
		},'json');
		return false;
	});	
</script>
