$(function () {
    $(".form-validate").validate({
        errorElement: 'span',
        rules: {
            password: "required",
            confirmpassword: {
                equalTo: "#password"
            },
        },
        messages: {
            confirmpassword: {
                equalTo: "Confirm Password should match with Password"
            }
        }
    });

    jQuery.validator.addClassRules('imagefile', {
        accept: "image/jpeg,image/jpg,image/png,image/gif",
        maxFileSize: {
            "unit": "MB",
            "size": 2
        },
    });

    // Start: This code is for exten the validation checkForm for array field validation
    // $.extend( $.validator.prototype, {
    //     checkForm: function () {
    //         this.prepareForm();
    //         for (var i = 0, elements = (this.currentElements = this.elements()); elements[i]; i++) {
    //             if (this.findByName(elements[i].name).length != undefined && this.findByName(elements[i].name).length > 1) {
    //                 for (var cnt = 0; cnt < this.findByName(elements[i].name).length; cnt++) {
    //                 this.check(this.findByName(elements[i].name)[cnt]);
    //                 }
    //             } else {
    //                 this.check(elements[i]);
    //             }
    //         }
    //         return this.valid();
    //     }
    // });
    // End

    // $('.ckeditor').each(function(){
    //     id = $(this).attr('id');
    //     CKEDITOR.replace(id);
    // });

    //CKEDITOR.replace('editor1')

    $('#user_type_userpage').change(function () {
        if (this.value == 'W') {
            $('#user_discount_percentage_container').show();
        } else {
            $('#user_discount_percentage_container').hide();
        }
    })


    if ($('#order_date_from').length && $('#order_date_to').length) {
        var dateFormat = "mm/dd/yy",
            from = $("#order_date_from")
                .datepicker({
                    defaultDate: "+1w",
                    changeMonth: true,
                    numberOfMonths: 2
                })
                .on("change", function () {
                    to.datepicker("option", "minDate", getDate(this));
                }),
            to = $("#order_date_to").datepicker({
                defaultDate: "+1w",
                changeMonth: true,
                numberOfMonths: 2
            })
                .on("change", function () {
                    from.datepicker("option", "maxDate", getDate(this));
                });

        function getDate(element) {
            var date;
            try {
                date = $.datepicker.parseDate(dateFormat, element.value);
            } catch (error) {
                date = null;
            }
            return date;
        }
    }

});


$('.change-status').click(function () {

    var id = $(this).attr('data-id');
    var model = $(this).attr('data-model');
    var ths = $(this);
    bootbox.confirm({
        message: "<h4>Are you sure to change this status?</h4>",
        buttons: {
            confirm: {
                label: '<i class="fa fa-check-circle"></i> Confirm',
                className: 'btn-success'
            },
            cancel: {
                label: '<i class="fa fa-times-circle"></i> Cancel',
                className: 'btn-danger'
            }
        },
        callback: function (result) {
            if (result) {
                $.ajax({
                    'type': 'POST',
                    'url': BASEURL + 'statusChange',
                    'data': { '_token': csrf_token, 'id': id, model: model },
                    'success': function (msg) {
                        ths.html(msg);
                    }
                });
            }
        }
    });
});


$('.change-status-access').click(function () {

    var id = $(this).attr('data-id');
    var accessuser = $(this).attr('data-access-user');
    var ths = $(this);
    bootbox.confirm({
        message: "<h4>Are you sure to change this access?</h4>",
        buttons: {
            confirm: {
                label: '<i class="fa fa-check-circle"></i> Confirm',
                className: 'btn-success'
            },
            cancel: {
                label: '<i class="fa fa-times-circle"></i> Cancel',
                className: 'btn-danger'
            }
        },
        callback: function (result) {
            if (result) {
                $.ajax({
                    'type': 'POST',
                    'url': BASEURL + 'status/changeAccess',
                    'data': { '_token': csrf_token, 'id': id, accessUser: accessuser },
                    'success': function (msg) {
                        ths.html(msg);
                    }
                });
            }
        }
    });
});

function destroyData(destroyURL) {
    bootbox.confirm({
        message: "<h4>Are you sure want to delete this record?</h4>",
        buttons: {
            confirm: {
                label: '<i class="fa fa-check-circle"></i> Confirm',
                className: 'btn-success'
            },
            cancel: {
                label: '<i class="fa fa-times-circle"></i> Cancel',
                className: 'btn-danger'
            }
        },
        callback: function (result) {
            if (result) {
                $('#frmDelete').attr('action', destroyURL);
                $('#frmDelete').submit();
            }
        }
    });
}


$('.sort-field').click(function () {
    url = $(this).attr('data-url');
    field = $(this).attr('data-field');
    sort = $(this).attr('data-sort');
    if (sort == '') {
        sort = 'asc'
    } else if (sort == 'asc') {
        sort = 'desc';
    } else {
        sort = 'asc';
    }
    fullUrl = url + '?sort=' + field + ':' + sort;
    // if search keyword input exist
    if ($('#keyword').length > 0 && $('#keyword').val() != '') {
        fullUrl += "&keyword=" + $('#keyword').val();
    }
    window.location.href = fullUrl;
})

$('#user_type').change(function () {
    $.ajax({
        'type': 'POST',
        'url': BASEURL + 'users/get-users-dropdown',
        'data': { '_token': csrf_token, 'type': this.value },
        'success': function (resp) {
            $('#user_id').html(resp);
        }
    });
})

