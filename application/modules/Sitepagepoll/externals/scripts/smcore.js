//pagepoll plugin
sm4.core.Module.sitepagepoll={
  sitemobileIndex:{
    view:function(params,pageContent,data){
    }
  }
}
sm4.sitepagepoll = {

  urls : {
    vote : 'sitepagepoll/vote/',
    login : 'login'
  },

  data : {},

  addSitepagepollData : function(identity, data) {

    if( $.type(data) != 'object' ) {
      data = {};
    }

    $(this).data[identity] = data;
    return $(this);
  },

  getSitepagepollDatum : function(identity, key, defaultValue) {

    if( !defaultValue ) {
      defaultValue = false;
    }
    if( !(identity in this.data) ) {
      return defaultValue;
    }
    if( !(key in this.data[identity]) ) {
      return defaultValue;
    }
    return this.data[identity][key];
  },

  toggleResults : function(identity) { 
    var pollContainer = $('#sitepagepoll_form_' + identity);
    if( 'none' == pollContainer.find('.sitepagepoll_has_voted').css('display') ) {
      pollContainer.find('.sitepagepoll_has_voted').show();
      pollContainer.find('.sitepagepoll_not_voted').hide();
      pollContainer.find('.sitepagepoll_toggleResultsLink').text(sm4.core.language.translate('Show Questions'));
    } else {
      pollContainer.find('.sitepagepoll_has_voted').hide();
      pollContainer.find('.sitepagepoll_not_voted').show();
      pollContainer.find('.sitepagepoll_toggleResultsLink').text(sm4.core.language.translate('Show Results'));
    }
  },

  renderResults : function(identity, answers, votes) {

    if( !answers || 'array' != $.type(answers) ) {
      return;
    }
    var pollContainer = $('#sitepagepoll_form_' + identity);

    $(answers).each(function(index, option) {
      var div = $('#sitepagepoll-answer-' + option.poll_option_id);
      var pct = votes > 0
      ? Math.floor(100*(option.votes / votes))
      : 1;
      if (pct < 1)
        pct = 1;
      // set width to 70% of actual width to fit text on same line
      div.width((.7*pct)+'%');//div.width((.7*pct)+'%');//next//attr
      div.next('.sitepagepoll_answer_total')
      .text(option.votesTranslated + ' (' + sm4.core.language.translate('%1$s%%', (option.votes ? pct : '0')) + ')');
      if( !this.getSitepagepollDatum(identity, 'canVote') ||
        (!this.getSitepagepollDatum(identity, 'canChangeVote') || this.getSitepagepollDatum(identity, 'hasVoted')) ||
        this.getSitepagepollDatum(identity, 'isClosed') ) {
        pollContainer.find('.sitepagepoll_radio').find('input').attr('disabled', true);
      }
    }.bind(this));
  },

  vote: function(identity, option) {
    option = $(option);

    var SitepagepollContainer = $('#sitepagepoll_form_' + identity);
    var value = option.val();

    // $('#sitepagepoll_radio_' + option.val()).toggleClass('sitepagepoll_radio_loading');
    $.mobile.showPageLoadingMsg();
    sm4.core.request.send({
      type: "POST", 
      dataType: "html",
      url: this.urls.vote + '/' + identity,
      method: 'post',
      data : {
        'format' : 'json',
        'poll_id' : identity,
        'option_id' : value
      },
      success: function(responseJSON) {
        $.mobile.hidePageLoadingMsg();
        responseJSON = $.parseJSON(responseJSON);
        if( $.type(responseJSON) == 'object' && responseJSON.error ) {
          $.mobile.showPageLoadingMsg( $.mobile.pageLoadErrorMessageTheme, responseJSON.error, true );
          setTimeout((function(){
            $.mobile.hidePageLoadingMsg();
          }),500);
        } else {
          SitepagepollContainer.find('.sitepagepoll_vote_total')
          //sm4.core.language.translate(['%1$s vote', '%1$s votes', responseJSON.votes_total], responseJSON.votes_total)
          .text(responseJSON.votes_total+' votes');
          this.renderResults(identity, responseJSON.sitepagepollOptions, responseJSON.votes_total);
          this.toggleResults(identity);
        }
        if( !this.getSitepagepollDatum(identity, 'canChangeVote') ) {
          SitepagepollContainer.find('.sitepagepoll_radio input').attr('disabled', true);
        }
      }.bind(this)
    });
    
  //request.send()
  }
};
