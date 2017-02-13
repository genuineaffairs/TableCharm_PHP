/* $Id: core.js 2010-08-17 9:40:21Z SocialEngineAddOns Copyright 2009-2010 BigStep Technologies Pvt. Ltd. $ */

//GETTING THE SEARCHED FRIENDS.
function  show_searched_friends (request, event) {
	if (typeof event != 'undefined' && event.keyCode == 13) {
		return false;
	}
	if (request == 0) {
		show_selected = 0;
		document.getElementById('show_all').className = 'selected';
		document.getElementById('selected_friends').className = '';
	}
	else {
		document.getElementById('show_all').className = '';
		document.getElementById('selected_friends').className = 'selected';
	}
	var current_search = trim( document.getElementById(action_module+'_members_search_inputd').value );
	if (show_selected == 1) {
		current_search = '';
		memberSearch = '';
		document.getElementById(action_module+'_members_search_inputd').value = en4.core.language.translate('Search Members');
	}
	var request = new Request.HTML({
		url : url,
		method: 'GET',
		data : {
			format : 'html',
				'task': 'ajax',
				searchs : current_search,
				'selected_checkbox':suggestion_string,
				'show_selected':show_selected,
				'action_id':action_session_id,
				'notification_type':notification_type,
				'entity':entity,
				'item_type':item_type,
				'findFriendFunName':findFriendFunName,
				'notificationType':notificationType,
				'modError':modError,
				'modName':modName,
				'modItemType':modItemType,
				'selected_friend_flag':request,
				'getArray':paginationArray
		},
		'onSuccess' : function(responseTree, responseElements, responseHTML, responseJavaScript)
		{
			document.getElementById('main_box').innerHTML = responseHTML;
			update_html();
			if( document.getElementById('newcheckbox') ) {
				if( paginationArray[memberPage] && (paginationArray[memberPage] == 1) && (dontHaveResult == 1) ) {
					popupFlag = 1;
					document.getElementById('newcheckbox').addClass('selected');
				}else {
					popupFlag = 0;
					document.getElementById('newcheckbox').removeClass('selected');
				}
				document.getElementById('newcheckbox').setProperty('onclick', 'selectAllFriend("' + displayUserStr + '")');
			}
		}
	});
				request.send();
				return false;
}





// When click on "Select All" then we are calling this function which selected all the friend which are showing on page.
function selectAllFriend ( friendStr ) {
	if( popupFlag == 0 ){
		popupFlag = 1;
	}else{
		popupFlag = 0;
	}
	var subcatss = friendStr . split("::");
	for (var i=0; i < subcatss.length; ++i){
		var friend_id = subcatss[i].split("_");
		var check_name_new = 'check_' +  friend_id;
		newmoduleSelect(check_name_new, friend_id);
	}
	
	paginationArray[memberPage] = popupFlag;
}


function newmoduleSelect (check_name, friend_id)
{
	if(document.getElementById(check_name)) {
		// popupFlag: Variable if it value is 0 then "Removed the Selection" and if 1 "Friend Selected".
		if( popupFlag ) {// Check All
      if( !SelectedPopupContent[friend_id] ) {
				moduleSelect(friend_id);
			}
			document.getElementById('newcheckbox').addClass('selected');
		}else {// Uncheck All
      if( SelectedPopupContent[friend_id] ) {
				moduleSelect(friend_id);
			}
			document.getElementById('newcheckbox').removeClass('selected');
		}
	}
}



// Function call when click on "Friend Div"
function moduleSelect ( friend_id )
{
	// If "friend_id" are not in array, it means that "friend-div" should be selected other vise should not be selected.
	if( SelectedPopupContent[friend_id] ) { // Should not be selected
    friends_count--;
		delete SelectedPopupContent[friend_id];
		suggestion_string = suggestion_string.replace(',' +  friend_id, "");
		// Class: Which are showing friend is selected or not in the popups.
		document.getElementById('check_' + friend_id ).className = "suggestion_pop_friend";
		// "Select All - Checkbox" should be disabled when ever all selected checkbox is unchecked.
		if( friends_count == 0 ) {
			popupFlag = 0;
			document.getElementById('newcheckbox').removeClass('selected');
		}
	}else { // Should be selected
    SelectedPopupContent[friend_id] = 1;
		friends_count++;
		suggestion_string  += ',' + friend_id;
		// Class: Which are showing friend is selected or not in the popups.
		document.getElementById('check_' + friend_id ).className = "suggestion_pop_friend selected";
	}
	if( friends_count < 0 ){
		friends_count = 0;
	}
	
	if(select_text_flag) {
		if (document.getElementById('selected_friends')) {
			document.getElementById('selected_friends').innerHTML = select_text_flag +  ' (' + friends_count + ')';
		}
	}else {
		document.getElementById('selected_friends').innerHTML = en4.core.language.translate('Selected') +  ' (' + friends_count + ')';
	}
}

//SUBMIT THE FORM IF USER HAS SELECTED ATLEAST ONE FRIEND. 
function doCheckAll()
{
	var suggestion_string_1 = suggestion_string.split(',');
	if(suggestion_string_1.length == 1)
	{
		document.getElementById('check_error').innerHTML = '<ul class="form-errors"><li><ul class="errors"><li>' + en4.core.language.translate('Please select at-least one entry above to send suggestion to.') + '</li></ul></li></ul>';
	}
	else
	{
		document.getElementById('hidden_checkbox').innerHTML = "";
		var hidden_checkbox = '';
		for ($i = 1;$i < suggestion_string_1.length; $i++ ) {
			// var checked_id_temp = suggestion_string_1[$i].split('-');
			var checked_id_temp = suggestion_string_1[$i];
			if( checked_id_temp ) {
				delete SelectedPopupContent[1];
				hidden_checkbox = hidden_checkbox + '<input type="hidden" name="check_' + checked_id_temp + '"  value="' + checked_id_temp + '"/>';
			}
		}
		document.getElementById('hidden_checkbox').innerHTML = hidden_checkbox;
		document.suggestion.submit();
	}
}

// close the popup.
var cancelPopup = function ()
{
	parent.Smoothbox.close();
};

en4.sitepageevent = {

  rotate : function(photo_id, angle) {
    request = new Request.JSON({
      url : en4.core.baseUrl + 'sitepageevent/photo/rotate',
      data : {
        format : 'json',
        photo_id : photo_id,
        angle : angle
      },
      onComplete: function(response) {
        // Check status
        if( $type(response) == 'object' &&
          $type(response.status) &&
          response.status == false ) {
          en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
          return;
        } else if( $type(response) != 'object' ||
          !$type(response.status) ) {
          en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
          return;
        }

        // Ok, let's refresh the page I guess
       
        $('media_image').src=response.href;
        $('media_image').style.marginTop="0px";
        if($('canReload'))
          $('canReload').value=1;
        else
          window.location.reload(true);
      }
    });
    request.send();
    return request;
  },

  flip : function(photo_id, direction) {
    request = new Request.JSON({
      url : en4.core.baseUrl + 'sitepageevent/photo/flip',
      data : {
        format : 'json',
        photo_id : photo_id,
        direction : direction
      },
      onComplete: function(response) {
        // Check status
        if( $type(response) == 'object' &&
          $type(response.status) &&
          response.status == false ) {
          en4.core.showError(response.error+ 'An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
          return;
        } else if( $type(response) != 'object' ||
          !$type(response.status) ) {
          en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
          return;
        }

        // Ok, let's refresh the page I guess
        $('media_image').src=response.href;
        $('media_image').style.marginTop="0px";
        if($('canReload'))
          $('canReload').value=1;
        else
          window.location.reload(true);
      }
    });
    request.send();
    return request;
  }

};