/* $Id: core.js 2011-05-05 9:40:21Z SocialEngineAddOns Copyright 2010-2011 BigStep Technologies Pvt. Ltd. $ */
en4.sitepagenote = {

  rotate : function(photo_id, angle) {
    request = new Request.JSON({
      url : en4.core.baseUrl + 'sitepagenote/photo/rotate',
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
      url : en4.core.baseUrl + 'sitepagenote/photo/flip',
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