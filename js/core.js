//Different helpers
var tools = {
	showOverlay:function(container){
		$('#content_overlay').remove();
		$container = $(container).append('<div id="content_overlay"></div>');
	},

	hideOverlay:function(container){
		$('#content_overlay').remove();
	},

	disableTab:function(tab_obj,i){
		$('li', $(tab_obj)).eq(i).addClass('disabled').find('a').bind('click',false);
	},

	enableTab:function(tab_obj,i){
		$('li', $(tab_obj)).eq(i).filter('.disabled').removeClass('disabled').find('a').unbind('click',false);
	},

	activateTab:function(tab_obj,i){
		$('li', tab_obj).eq(i).find('a').tab('show');
	},
	reloadCurrentTab:function(tab_obj){
		loadPageContent({target:$('.nav-tabs li.active a',$(tab_obj))[0],preventDefault:function(){}});
	},
	
	setCursor:function(node,pos){
		var node = (typeof node == "string" || node instanceof String) ? document.getElementById(node) : node;
		if(!node){
			return false;
		}else if(node.createTextRange){
			var textRange = node.createTextRange();
			textRange.collapse(true);
			textRange.moveEnd(pos);
			textRange.moveStart(pos);
			textRange.select();
			return true;
		}else if(node.setSelectionRange){
			node.setSelectionRange(pos,pos);
			return true;
		}
		return false;
	}
}

var tpl = {
	parse:function(str,values){
		var result = str;
		for(i in values){
			result = str.replace(new RegExp('\{\{'+i+'\}\}','g'),values[i]);
		}
		return result;
	},	
}

var counters = {}