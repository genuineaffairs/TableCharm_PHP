
function seaocore_resource_type_follows_sitemobile(resource_id, resource_type) {
  if($.mobile.activePage.find("#"+resource_type + '_follow_'+ resource_id)) {
    var follow_id = $.mobile.activePage.find("#"+resource_type + '_follow_'+ resource_id).val()
  }
  if($.mobile.activePage.find("#"+resource_type+'_most_follows_'+ resource_id).css('display') == 'block'){
    var action = 'follow';   
  }else{
    var action = 'unfollow';
  }
  resetSeaocoreFollowUnfollowSitemobile(resource_id, resource_type,action);

  $.ajax({
    url : sm4.core.baseUrl + 'seaocore/follow/global-follows',
    data : {
      format : 'json',
      'resource_id' : resource_id,
      'resource_type' : resource_type,	
      'follow_id' : follow_id
    },
    success: function(responseJSON){     
      if(responseJSON.follow_id )	{
        $.mobile.activePage.find("#"+resource_type+'_follow_'+ resource_id).val(responseJSON.follow_id);       
        if($.mobile.activePage.find("#"+resource_type+'_num_of_follow_'+ resource_id)) {
          $.mobile.activePage.find("#"+resource_type + '_num_of_follow_'+ resource_id).html(responseJSON.follow_count);
        }
      }	else	{
        $.mobile.activePage.find("#"+resource_type+'_follow_'+ resource_id).val(0);       
        if($.mobile.activePage.find("#"+resource_type+'_num_of_follow_'+ resource_id)) {
          $.mobile.activePage.find("#"+resource_type + '_num_of_follow_'+ resource_id).html(responseJSON.follow_count);
        }
      }
				if (!responseJSON.status) {
          resetSeaocoreLikeUnlikeSitemobile(resource_id, resource_type, action =='follow' ? 'unfollow' : 'follow');
        }	
      sm4.core.runonce.trigger();
      sm4.core.dloader.refreshPage();
    }
  });
}

function resetSeaocoreFollowUnfollowSitemobile(resource_id, resource_type,action) {
  if(action == 'follow')	{
				$.mobile.activePage.find("#"+resource_type+'_most_follows_'+ resource_id).css('display', 'none');
        $.mobile.activePage.find("#"+resource_type+'_unfollows_'+ resource_id).css('display', 'block');
	}	else	{
				$.mobile.activePage.find("#"+resource_type+'_most_follows_'+ resource_id).css('display', 'block');
        $.mobile.activePage.find("#"+resource_type+'_unfollows_'+ resource_id).css('display', 'none');
	}
}