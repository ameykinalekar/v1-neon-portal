<script type="text/javascript">
var callBackFunction;
var callBackFunctionForGenericConfirmationModal;

function rightModal(url, header) {
    jQuery('#right-modal .modal-body').html(`<div class="container-fluid text-center">
          <img src="{{asset('img/system/straight-loader.gif')}}" style="width: 60px; padding: 50% 0px; opacity: .6;">
        </div>`);
    // LOADING THE AJAX MODAL
    jQuery('#right-modal').modal('show', {
        backdrop: 'true'
    });

    // SHOW AJAX RESPONSE ON REQUEST SUCCESS
    $.ajax({
        url: url,
        success: function(response) {
            jQuery('#right-modal .modal-body').html(response);
            jQuery('#right-modal .modal-title').html(header);
        }
    });
}

function questionModal(url, header) {
    jQuery('#question-modal .modal-body').html(`<div class="container-fluid text-center">
          <img src="{{asset('img/system/straight-loader.gif')}}" style="width: 60px; padding: 50% 0px; opacity: .6;">
        </div>`);
    // LOADING THE AJAX MODAL
    jQuery('#question-modal').modal('show', {
        backdrop: 'true'
    });

    // SHOW AJAX RESPONSE ON REQUEST SUCCESS
    $.ajax({
        url: url,
        success: function(response) {
            jQuery('#question-modal .modal-body').html(response);
            jQuery('#question-modal .modal-title').html(header);
        }
    });
}
function fullModal(url, header) {
    jQuery('#full-modal .modal-body').html(`<div class="container-fluid text-center">
          <img src="{{asset('img/system/straight-loader.gif')}}" style="width: 60px; padding: 20% 0px; opacity: .6;">
        </div>`);
    // LOADING THE AJAX MODAL
    jQuery('#full-modal').modal('show', {
        backdrop: 'true'
    });

    // SHOW AJAX RESPONSE ON REQUEST SUCCESS
    $.ajax({
        url: url,
        success: function(response) {
            jQuery('#full-modal .modal-body').html(response);
            jQuery('#full-modal .modal-title').html(header);
        }
    });
}
function disclaimerExamModal(url,examid) {
    jQuery('#disclaimer-modal .modal-body').html(`<div class="container-fluid text-center">
          <img src="{{asset('img/system/straight-loader.gif')}}" style="width: 60px; padding: 50% 0px; opacity: .6;">
        </div>`);
    jQuery('#disclaimer-modal').modal('show', {
        backdrop: 'true',
        height: 'auto'
    });

    // SHOW AJAX RESPONSE ON REQUEST SUCCESS
    $.ajax({
        url: url,
        success: function(response) {
            jQuery('#disclaimer-modal .modal-body').html(response);
        }
    });

}
</script>



<!-- Right modal content -->
<div id="right-modal" class="modal fade" tabindex="0" role="dialog" aria-hidden="true"
    style="overflow-y: hidden !important;">
    <div class="modal-dialog modal-lg modal-right"
        style="width: 100% !important; max-width: 440px !important; min-height: 100% !important;">
        <div class="modal-content modal_height">

            <div class="modal-header border-1">
                <button type="button" class="btn btn-outline-secondary py-0 px-1" data-bs-dismiss="modal"
                    aria-hidden="true">×</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body" style="overflow-y: auto !important;">
                <div class="container-fluid text-center">
                    <img src="{{asset('img/system/straight-loader.gif')}}"
                        style="width: 60px; padding: 50% 0px; opacity: .6;">
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<!-- Question modal content -->
<div id="question-modal" class="modal fade" tabindex="0" role="dialog" aria-hidden="true"
    style="overflow-y: hidden !important;">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content modal_height">

            <div class="modal-header border-bottom">

                <h1 class="modal-title fs-5" id="staticBackdropLabel"></h1>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>
            <div class="modal-body" style="overflow-y: auto !important;">
                <div class="container-fluid text-center">
                    <img src="{{asset('img/system/straight-loader.gif')}}"
                        style="width: 60px; padding: 50% 0px; opacity: .6;">
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<!-- Full modal content -->
<div id="full-modal" class="modal fade" tabindex="0" role="dialog" aria-hidden="true"
    style="overflow-y: hidden !important;">
    <div class="modal-dialog  modal-fullscreen">
        <div class="modal-content">

            <div class="modal-header border-bottom">

                <h1 class="modal-title fs-5" id="staticBackdropLabel"></h1>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>
            <div class="modal-body" style="overflow-y: auto !important;">
                <div class="container-fluid text-center">
                    <img src="{{asset('img/system/straight-loader.gif')}}"
                        style="width: 60px; padding: 50% 0px; opacity: .6;">
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<!-- Disclaimer modal content -->
<div id="disclaimer-modal" class="modal fade" tabindex="0" role="dialog" aria-hidden="true"
    style="overflow-y: hidden !important;">
    <div class="modal-dialog  modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header border-bottom">

                <h1 class="modal-title fs-5" id="staticBackdropLabel">Disclaimer</h1>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>
            <div class="modal-body" style="overflow-y: auto !important;">

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<script type="text/javascript">
var myModalEl = document.getElementById('right-modal')
myModalEl.addEventListener('hidden.bs.modal', function(event) {
    $('select.select2:not(.normal)').each(function() {
        $(this).select2();
    });
});
</script>

<script>
function pageReload() {
    //filterCourse();
    filterCourseFullPage();
    //location.reload();
}
</script>
