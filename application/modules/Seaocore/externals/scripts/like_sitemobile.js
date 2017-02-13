function seaocore_content_type_likes_sitemobile(resource_id, resource_type) { 
  content_type_undefined = 0;
	var content_type = seaocore_content_type;
	if (seaocore_content_type == '') { 
		content_type_undefined = 1;
		var content_type = resource_type;
	}	if($("#"+content_type + '_like_'+ resource_id)) {
		var like_id = $("#"+content_type + '_like_'+ resource_id).val();
	}
  if($.mobile.activePage.find("#"+content_type+'_most_likes_'+ resource_id).css('display') == 'block'){
    var action = 'like';   
  }else{
    var action = 'unlike';
  }
  resetSeaocoreLikeUnlikeSitemobile(resource_id, content_type,action);
  $.ajax({
		url : sm4.core.baseUrl + 'seaocore/like/like',
		type: "POST", 
		dataType: "json",
		data : {
			format : 'json',
				'resource_id' : resource_id,
				'resource_type' : resource_type,	
				'like_id' : like_id
		}, 
		success: function(response) {
			if (content_type_undefined == 0) {
				if(response.like_id)	{
        if($.mobile.activePage.find("#"+content_type+'_like_'+ resource_id))
					$.mobile.activePage.find("#"+content_type+'_like_'+ resource_id).val(response.like_id);
        if($.mobile.activePage.find("#"+content_type+'_num_of_like_'+ resource_id)) {
						$.mobile.activePage.find("#"+content_type + '_num_of_like_'+ resource_id).html(response.num_of_like);
					}
      }else{
        if($.mobile.activePage.find("#"+content_type+'_num_of_like_'+ resource_id)) {
						$.mobile.activePage.find("#"+content_type + '_num_of_like_'+ resource_id).html(response.num_of_like);
					}
      }
        if (!response.status) {
          resetSeaocoreLikeUnlikeSitemobile(resource_id, resource_type, action =='like' ? 'unlike' : 'like');
        }
			}
			sm4.core.runonce.trigger();
			sm4.core.dloader.refreshPage();	
		}
	});
}

function resetSeaocoreLikeUnlikeSitemobile(resource_id, content_type,action) {
  if(action == 'like')	{
					if($.mobile.activePage.find("#"+content_type+'_most_likes_'+ resource_id))
					$.mobile.activePage.find("#"+content_type+'_most_likes_'+ resource_id).css('display', 'none');
					if($.mobile.activePage.find("#"+content_type+'_unlikes_'+ resource_id))
					$.mobile.activePage.find("#"+content_type+'_unlikes_'+ resource_id).css('display', 'block');
	}	else	{
					if($.mobile.activePage.find("#"+content_type+'_like_'+ resource_id))
					$.mobile.activePage.find("#"+content_type+'_like_'+ resource_id).val(0);
					if($.mobile.activePage.find("#"+content_type+'_most_likes_'+ resource_id))
					$.mobile.activePage.find("#"+content_type+'_most_likes_'+ resource_id).css('display', 'block');
					if($.mobile.activePage.find("#"+content_type+'_unlikes_'+ resource_id))
					$.mobile.activePage.find("#"+content_type+'_unlikes_'+ resource_id).css('display', 'none');
	}
}