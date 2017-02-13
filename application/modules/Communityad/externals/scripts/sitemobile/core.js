/* $Id: core.js 2010-11-04 9:40:21Z SocialEngineAddOns Copyright 2009-2010 BigStep Technologies Pvt. Ltd. $ */

sm4.communityad = {
};

sm4.communityad.do_like = {

  // FUNCTION FOR CREATING A FEEDBACK 
  createLike : function(  ad_id, resource_type, resource_id, owner_id, widgetType, core_like )
  {
		var like_id = $("#" + widgetType + '_likeid_info_'+ ad_id).val();
    $.mobile.showPageLoadingMsg();
    $.ajax({
      url : sm4.core.baseUrl + 'communityad/display/globallikes',
			type: "GET", 
			dataType: "json", 
      data : {
        format : 'json',
				'ad_id' : ad_id,
				'resource_type' : resource_type,	
				'resource_id' : resource_id,
				'owner_id' : owner_id,
				'like_id' : like_id,
				'core_like' : core_like
      }, 
			success: function(responseJSON) {
				$.mobile.hidePageLoadingMsg();
				if(responseJSON.like_id )
				{
					$('#' + widgetType + '_likeid_info_'+ ad_id).val(responseJSON.like_id);
					$('#' + resource_type + '_' + widgetType + '_most_likes_' + ad_id).css('display', 'none');
					$('#' + resource_type + '_' + widgetType + '_unlikes_'+ ad_id).css('display', 'block');
				}
				else
				{
					$('#' + widgetType + '_likeid_info_'+ ad_id).val(0);
					$('#' + resource_type + '_' + widgetType + '_most_likes_' + ad_id).css('display', 'block');
					$('#' + resource_type +'_' + widgetType + '_unlikes_'+ ad_id).css('display', 'none');
				}
 			 sm4.core.runonce.trigger();
       sm4.core.refreshPage();
				
			}
    });
  }
}