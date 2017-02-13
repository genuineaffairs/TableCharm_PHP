(function() { // START NAMESPACE
var $ = 'id' in document ? document.id : window.$;


Composer.Plugin.Document = new Class({

  Extends : Composer.Plugin.Interface,

  name : 'document',

  options : {
    title : 'Add Document',
    lang : {},
    requestOptions : false,
    fancyUploadEnabled : true,
    fancyUploadOptions : {}
  },

  initialize : function(options) {
    this.elements = new Hash(this.elements);
    this.params = new Hash(this.params);
    this.parent(options);
  },

  attach : function() {
    this.parent();
    this.makeActivator();
    return this;
  },

  detach : function() {
    this.parent();
    return this;
  },

  activate : function() {
    if( this.active ) return;
    this.parent();

    this.makeMenu();
    this.makeBody();

    // create the regular, non-fancyupload form that will be used as a fallback if fancyupload fails to initialise
    var fullUrl = this.options.requestOptions.url;
    this.elements.form = new Element('form', {
      'id' : 'compose-document-form',
      'class' : 'compose-form',
      'method' : 'post',
      'action' : fullUrl,
      'enctype' : 'multipart/form-data'
    }).inject(this.elements.body);

    // add title input field
    this.elements.formTitleContainer = new Element('div').inject(this.elements.form);

    this.elements.formTitleLabel = new Element('label', {
      'for' : 'compose-document-form-title',
      'html' : this._lang('Title') + ': '
    }).inject(this.elements.formTitleContainer);

    this.elements.formTitle = new Element('input', {
      'id' : 'compose-document-form-title',
      'class' : 'compose-form-input',
      'type' : 'text',
      'name' : 'title'
    }).inject(this.elements.formTitleContainer);

    // add description input field
    this.elements.formDescriptionContainer = new Element('div').inject(this.elements.form);

    this.elements.formDescriptionLabel = new Element('label', {
      'for' : 'compose-document-form-description',
      'html' : this._lang('Description') + ': '
    }).inject(this.elements.formDescriptionContainer);

    this.elements.formDescription = new Element('input', {
      'id' : 'compose-document-form-description',
      'class' : 'compose-form-input',
      'type' : 'text',
      'name' : 'description'
    }).inject(this.elements.formDescriptionContainer);

    // add file input field
    this.elements.formFileContainer = new Element('div').inject(this.elements.form);

    this.elements.formFileLabel = new Element('label', {
      'for' : 'compose-document-form-file',
      'html' : this._lang('File') + ': '
    }).inject(this.elements.formFileContainer);

    this.elements.formFile = new Element('input', {
      'id' : 'compose-document-form-file',
      'class' : 'compose-form-input',
      'type' : 'file',
      'name' : 'Filedata',
      'events' : {
        'change' : this.doRequest.bind(this)
      }
    }).inject(this.elements.formFileContainer);

    // try to init fancyupload
    if( this.options.fancyUploadEnabled && this.options.fancyUploadOptions ) {
      this.elements.formFancyContainer = new Element('div');

      // create the "fancy" file input field that will replace the regular one
      this.elements.formFancyFileContainer = new Element('div').inject(this.elements.formFancyContainer);

      this.elements.formFancyFileLabel = new Element('label', {
        'for' : 'compose-document-form-fancy-file',
        'html' : this._lang('File') + ': '
      }).inject(this.elements.formFancyFileContainer);

      this.elements.formFancyFile = new Element('a', {
        'href' : 'javascript:void(0);',
        'id' : 'compose-document-form-fancy-file',
        'class' : 'buttonlink',
        'html' : this._lang('Select File')
      }).inject(this.elements.formFancyFileContainer);

      // this is the status
      this.elements.formFancyStatus = new Element('div', {
        'html' : '<div style="display:none;">\n\
                    <div class="demo-status-overall" id="demo-status-overall" style="display:none;">\n\
                      <div class="overall-title"></div>\n\
                      <img src="" class="progress overall-progress" />\n\
                    </div>\n\
                    <div class="demo-status-current" id="demo-status-current" style="display:none;">\n\
                      <div class="current-title"></div>\n\
                      <img src="" class="progress current-progress" />\n\
                    </div>\n\
                    <div class="current-text"></div>\n\
                  </div>'
      }).inject(this.elements.formFancyContainer);

      // this is the list
      this.elements.formFancyList = new Element('div', {
        'styles' : {
          'display' : 'none'
        }
      }).inject(this.elements.formFancyContainer);

      var self = this;
      var opts = $merge({
        policyFile : ('https:' == document.location.protocol ? 'https://' : 'http://')
            + document.location.host
            + en4.core.baseUrl + 'cross-domain',
        url : fullUrl,
        appendCookieData: true,
        multiple : false,
        typeFilter: {
          'Documents (*.pdf, *.doc, *.docx, *.xls, *.xlsx)': '*.pdf; *.doc; *.docx; *.xls; *.xlsx'
        },
        target : this.elements.formFancyFile,
        container : self.elements.body,
        onLoad : function() {
          self.elements.formDescriptionContainer.inject(self.elements.formFancyContainer, 'top'); // move from form to container
          self.elements.formTitleContainer.inject(self.elements.formFancyContainer, 'top'); // move from form to container
          self.elements.formFancyContainer.replaces(self.elements.form); // replace form
          this.target.addEvents({
            click: function() {
              return false;
            },
            mouseenter: function() {
              this.addClass('hover');
            },
            mouseleave: function() {
              this.removeClass('hover');
              this.blur();
            },
            mousedown: function() {
              this.focus();
            }
          });
        },
        onSelectSuccess : function() {
          self.makeLoading('invisible');
          this.start();
        },
        onFileSuccess : function(file, response) {
          var json = new Hash(JSON.decode(response, true) || {});
          self.doProcessResponse(json);
        }
      }, this.options.fancyUploadOptions);

      try {
        this.elements.formFancyUpload = new FancyUpload2(this.elements.formFancyStatus, this.elements.formFancyList, opts);
      } catch( e ) {
        //if( $type(console) ) console.log(e);
      }
    }
  },

  deactivate : function() {
    if( !this.active ) return;
    this.parent();
  },

  doRequest : function() {
    // note: this function is only called for regular, non-fancyupload file uploads
    this.elements.iframe = new IFrame({
      'name' : 'composeDocumentFrame',
      'src' : 'javascript:false;',
      'styles' : {
        'display' : 'none'
      },
      'events' : {
        'load' : function() {
          this.doProcessResponse(window._composeDocumentResponse);
          window._composeDocumentResponse = false;
        }.bind(this)
      }
    }).inject(this.elements.body);

    window._composeDocumentResponse = false;
    this.elements.form.set('target', 'composeDocumentFrame');

    // submit and then destroy form
    this.elements.form.submit();
    this.elements.form.destroy();

    // start loading screen
    this.makeLoading();
  },

  doProcessResponse : function(responseJSON) {
    // an error occurred
    if( ($type(responseJSON) != 'hash' && $type(responseJSON) != 'object') || $type(responseJSON.document_title) != 'string' || $type(parseInt(responseJSON.document_id)) != 'number' ) {
      if( this.elements.loading ) {
        this.elements.loading.destroy();
      }
      this.makeError(responseJSON.message);
      return;
    }

    // success
    if($('compose-document-error')){
      $('compose-document-error').destroy();
    }

    if( this.elements.loading ) this.elements.loading.destroy();
    if( this.elements.formFancyContainer ) this.elements.formFancyContainer.destroy();

    this.elements.preview = new Element('div', {
      'id' : 'compose-document-preview'
    }).inject(this.elements.body);

    this.elements.previewTitle = new Element('div', {
      // display default title assigned in the backend (DocumentController.composeUploadAction) if one was not entered by the user
      'html': this._lang('Title') + ': ' + (this.elements.formTitle.value.length > 0 ? this.elements.formTitle.value : responseJSON.document_title)
    }).inject(this.elements.preview);

    this.elements.previewDescription = new Element('div', {
      'html': this._lang('Description') + ': ' + (this.elements.formDescription.value.length > 0 ? this.elements.formDescription.value : '(not specified)')
    }).inject(this.elements.preview);

    this.elements.previewFile = new Element('div', {
      'html': this._lang('File') + ': ' + '<a href="'+ responseJSON.document_file_path + '">Download</a>'
    }).inject(this.elements.preview);

    this.params.set('rawParams', responseJSON);
    this.params.set('document_id', responseJSON.document_id);

    // the following params will eventually be saved in Document_Plugin_Composer.onAttachDocument
    this.params.set('document_title', this.elements.formTitle.value);
    this.params.set('document_description', this.elements.formDescription.value);

    this.makeFormInputs();
  },

  makeFormInputs : function() {
    this.ready();
    this.parent({
      'document_id' : this.params.document_id,
      'document_title' : this.params.document_title,
      'document_description' : this.params.document_description
    });
  }

});


})(); // END NAMESPACE
