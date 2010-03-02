$().ready(function(){

  // Variables
  window.jcontent = $('#jcontent');
  window.jbrowse_tab = $('#jbrowse_tab');
  window.jupload_tab = $('#jupload_tab');

  // Tab 1
  jbrowse_tab.bind('click', function(){
    show_loader(jcontent);
    $.get(AJAX_URL + 'jbrowser/view_files', {settings : settings}, function(data) {
      jbrowse_tab.addClass('jactive');
      jupload_tab.removeClass('jactive');
      jcontent.html(data);
      show_loader(jcontent);
      
      init_browser();
    });
  });
  
  // Tab 2
  jupload_tab.bind('click', function(){
    show_loader(jcontent);
    $.get(AJAX_URL + 'jbrowser/upload_form', {settings : settings}, function(data) {
      jbrowse_tab.removeClass('jactive');
      jupload_tab.addClass('jactive');
      jcontent.html(data);
      show_loader(jcontent);
      
      init_upload();
    });
  });
  
  
  init_browser();
});




function init_browser()
{
  window.jscope_select = $('#jscope_select');
  jscope_select.bind('change', function() {
    if (this.value != '')
    {
      show_loader(jcontent);
      $.post(AJAX_URL + 'jbrowser/view_files', {settings : '{"scope" : "'+ this.value +'"}'}, function(data) {
        jcontent.html(data);
        show_loader(jcontent);
        init_browser();
      });
    }
  });
  
  
  window.jfile = $('.jfile');
  window.jpreview = $('#jpreview');
  jfile.bind('click', function() {
    show_loader(jpreview);
    $.post(AJAX_URL + 'jbrowser/preview', {image_id : this.id.replace('jfile-', '')}, function(data) {
      jpreview.html(data);
      show_loader(jpreview);
    });
  });
}

function init_upload()
{

  // Some variables
  window.jscope_select = $('#jscope_select');
  window.jscope_input = $('#jscope_input');
  window.jfilename = $('#jfilename');
  window.jpercent = $('#jpercent');

  // Multiple upload
  var settings = {
  	// Backend Settings
  	upload_url: AJAX_URL + 'jbrowser/upload/',
  	file_post_name: 'image',
  	post_params: {
      'thesid': window.session_id,
      'settings' : window.settings
    },
  
  	// File Upload Settings
  	file_size_limit : '6 MB',
  	file_types : '*.jpg;*.png;*.gif',
  	file_types_description : 'Bildes',
  
  	// Event Handlers
  	file_queue_error_handler : fileQueueError,
  	file_dialog_complete_handler : fileDialogComplete,
  	upload_progress_handler : uploadProgress,
  	upload_error_handler : uploadError,
  	upload_success_handler : uploadSuccess,
  	upload_complete_handler : uploadComplete,

  	// Flash Settings
  	flash_url : BASE_URL + "files/swfupload.swf",

    // Button Settings
		button_image_url : BASE_URL + 'css/images/swfupload-button.png',
		button_placeholder_id : 'jupload_holder',
		button_width: 128,
		button_height: 118,
		button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
		button_cursor: SWFUpload.CURSOR.HAND,

  	// Debug Settings
  	debug: false
  };

  // Init
  window.swfu = new SWFUpload(settings);
}





/* Upload handlers */

function fileQueueError(file, errorCode, message){
    switch (errorCode) {
			case SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED:
				errorName = "QUEUE LIMIT EXCEEDED";
				break;
			case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
				errorName = "FILE EXCEEDS SIZE LIMIT";
				break;
			case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
				errorName = "ZERO BYTE FILE";
				break;
			case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
				errorName = "INVALID FILE TYPE";
				break;
			default:
				errorName = "UNKNOWN";
				break;
		}

  alert(errorName);
}


function fileDialogComplete(files_selected, files_queued, total_queued){
  if (total_queued > 0)
  {
    start_upload(this);
  }
}

function start_upload(obj)
{
  if ($.trim(jscope_input.val()) != '')
  {
    var scope = jscope_input.val();
  }
  else if (jscope_select.val() != '')
  {
    var scope = jscope_select.val();
  }
  else
  {
    alert('norādi scope');
    jscope_input.focus();
    return false;
  }

  obj.addPostParam('scope', scope);
  obj.startUpload();
}


function uploadProgress(file, bytes_complete, total_bytes){
  jfilename.html(file.name);
  var percent = Math.ceil((bytes_complete / file.size) * 100);
  jpercent.html(percent + '%');
}
 
 
function uploadError(file, error, message){
  alert(message);
}


function uploadSuccess(file, data, response){
  
}


function uploadComplete(file){
  if (this.getStats().files_queued > 0) {
    start_upload(this);
	}
	else
	{
	 jpercent.html('');
	 jfilename.html('Succedded').addClass('msg_ok');
	}
}
