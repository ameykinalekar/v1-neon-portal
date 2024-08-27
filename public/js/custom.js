String.prototype.endsWith = function(suffix) {
    return this.indexOf(suffix, this.length - suffix.length) !== -1;
 };

 var doAjax_params_default = {
     'url': null,
     'requestType': "GET",
     'contentType': 'application/x-www-form-urlencoded; charset=UTF-8',
     'dataType': 'json',
     'headers': {},
     'data': {},
     'beforeSendCallbackFunction': null,
     'successCallbackFunction': null,
     'completeCallbackFunction': null,
     'errorCallBackFunction': null,
 };


 function doAjax(doAjax_params) {

     var url = doAjax_params['url'];
     var requestType = doAjax_params['requestType'];
     var contentType = doAjax_params['contentType'];
     var dataType = doAjax_params['dataType'];
     var headers = doAjax_params['headers'];
     var data = doAjax_params['data'];
     var beforeSendCallbackFunction = doAjax_params['beforeSendCallbackFunction'];
     var successCallbackFunction = doAjax_params['successCallbackFunction'];
     var completeCallbackFunction = doAjax_params['completeCallbackFunction'];
     var errorCallBackFunction = doAjax_params['errorCallBackFunction'];

     //make sure that url ends with '/'
     /*if(!url.endsWith("/")){
      url = url + "/";
     }*/

     $.ajax({
         url: url,
         crossDomain: true,
         type: requestType,
         contentType: contentType,
         dataType: dataType,
         data: data,
         headers: headers,
         beforeSend: function(jqXHR, settings) {
             if (typeof beforeSendCallbackFunction === "function") {
                 beforeSendCallbackFunction();
             }
         },
         success: function(data, textStatus, jqXHR) {
             if (typeof successCallbackFunction === "function") {
                 successCallbackFunction(data);
             }
         },
         error: function(jqXHR, textStatus, errorThrown) {
             if (typeof errorCallBackFunction === "function") {
                 errorCallBackFunction(jqXHR);
             }

         },
         complete: function(jqXHR, textStatus) {
             if (typeof completeCallbackFunction === "function") {
                 completeCallbackFunction();
             }
         }
     });
 }

 var doCrop_params_default = {
    'file': null,
    'imageId': "imgshowactualpic",
    'dataImageId': "imagedata_profile_image",
    'previewWrapperClass': "rcrop-preview-wrapper",
    'requiredImageWidth': 300,
    'requiredImageHeight': 300,
    'previewImageWidth': 100,
    'previewImageHeight': 100,

};
 function doCrop(doCrop_params){
    var file = doCrop_params['file'];
    var imageId = doCrop_params['imageId'];
    var dataImageId = doCrop_params['dataImageId'];
    var previewWrapperClass = doCrop_params['previewWrapperClass'];
    var requiredImageWidth = doCrop_params['requiredImageWidth'];
    var requiredImageHeight = doCrop_params['requiredImageHeight'];
    var previewImageWidth = doCrop_params['previewImageWidth'];
    var previewImageHeight = doCrop_params['previewImageHeight'];
     var reader = new FileReader();
    reader.onload = function(e) {
        var img = new Image();
        img.onload = function() {
            if (img.naturalWidth < requiredImageWidth || img.naturalHeight < requiredImageHeight) {
                alert("Image dimensions are smaller than the required size: " + requiredImageWidth + "x" + requiredImageHeight);
                return;
            }

            $('.' + previewWrapperClass).remove();
            $('#' + imageId).rcrop('destroy');
            $('#' + imageId).removeAttr('src');
            $('#' + imageId).attr('src', e.target.result);
            $('#' + imageId).rcrop({
                minSize: [requiredImageWidth, requiredImageHeight],
                preserveAspectRatio: true,
                preview: {
                    display: false,
                    size: [previewImageWidth, previewImageHeight],
                },
                grid: true
            });
            // $('#btnCrop').show();
        };
        img.src = e.target.result;
    };
     reader.readAsDataURL(file);
 }
$(document).ready(function() {
    document.querySelectorAll('a').forEach(function(anchor) {
        anchor.addEventListener('click', function(event) {
            // Remove the active class from all links
            document.querySelectorAll('a.myLink').forEach(function(a) {
                a.classList.remove('active');
            });
            // Add the active class to the clicked link
            this.classList.add('active');
        });
    });
});
