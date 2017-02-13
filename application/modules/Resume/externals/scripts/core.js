
en4.RadcodesResume = {

  urls : {
    vote : en4.core.basePath + 'resumes/vote/',
    bury: en4.core.basePath + 'resumes/bury/',
    login : en4.core.basePath + 'login'
  },

  data : {},

  vote: function(identity) {
    if( !en4.user.viewer.id ) {
      window.location = this.urls.login + '?return_url=' + encodeURIComponent(window.location.href);
      return false;
    }

    var resumeFeedbackContainer = $('resume_vote_feedback_resume_' + identity);

    var options = {
		'url' : this.urls.vote + identity,
	    'data' : {
	      'format' : 'json'
	    },
	    'onComplete' : function(responseJSON, responseText) {
		    // Handle error
		  if( $type(responseJSON) != 'object' ) {
		      responseJSON = {
		        'error' : true
		    };
		  }

		  if (responseJSON.error)
		  {
			alert(responseJSON.message);  
			return false;
		  }

		  //var resumeContainer = ;
		  //alert(resumeContainer);
		  $$('.resume_record_' + identity + ' a.resume_point_count').each(function(el) {
			  el.set('text', responseJSON.point_count);
		  });
		  
		  $$('.resume_record_' + identity + ' .resume_vote_box .resume_vote').each(function(el) {
			  var voted_element = new Element('span', {
				  'class' : 'resume_voted_up',
				  html: responseJSON.vote_button_text
			  });
			  voted_element.inject(el.empty(), 'top');
		  });
		  
		  $$('.resume_record_' + identity + ' li.resume_meta_bury').each(function(el) {
			  el.dispose();
		  });
		}
    };   

	var request = new Request.JSON(options);
	request.send();    
  
	return false;
  },
  
  bury: function(identity) {
    if( !en4.user.viewer.id ) {
        window.location = this.urls.login + '?return_url=' + encodeURIComponent(window.location.href);
        return false;
    }

	  var resumeFeedbackContainer = $('resume_vote_feedback_resume_' + identity);
	
	  var options = {
		'url' : this.urls.bury + identity,
	    'data' : {
	      'format' : 'json'
	    },
	    'onComplete' : function(responseJSON, responseText) {
		    // Handle error
		  if( $type(responseJSON) != 'object' ) {
		      responseJSON = {
		        'error' : true
		    };
		  }
	
		  if (responseJSON.error)
		  {
			alert(responseJSON.message);  
			return false;
		  }
	
		  //var resumeContainer = ;
		  //alert(resumeContainer);
		  $$('.resume_record_' + identity + ' a.resume_point_count').each(function(el) {
			  el.set('text', responseJSON.point_count);
		  });
		  
		  $$('.resume_record_' + identity + ' li.resume_meta_bury').each(function(el) {
			  el.set('text', responseJSON.bury_button_text);
		  });
		  
		  $$('.resume_record_' + identity + ' .resume_vote_box .resume_vote').each(function(el) {
			  var vote_burried = new Element('span', {
				  'class' : 'resume_voted_down',
				  html: responseJSON.vote_button_text
			  });
			  vote_burried.inject(el.empty(), 'top');
		  });
		  
		}
	  };   

  	var request = new Request.JSON(options);
  	request.send();    
    
  	return false;   
  }
  
};

