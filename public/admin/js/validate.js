$(document).ready(function(){
    $("#cmsForm").validate({
        rules: {
            'data[en][title]': {
                required: true
            },
            'data[ar][title]': {
                required: true
            },
            'data[en][content]': {
                required: true
            },
            'data[ar][content]': {
                required: true
            }
        },
        messages: {
            'data[en][title]': {
                required: "Please provide english title"
            },
            'data[ar][title]': {
                required: "Please provide arabic title"
            },
            'data[en][content]': {
                required: "Please provide english content"
            },
            'data[ar][content]': {
                required: "Please provide arabic content"
            }
        }
    });
});
