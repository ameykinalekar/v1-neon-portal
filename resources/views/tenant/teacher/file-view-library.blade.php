@extends('layouts.ajax')
@section('title', 'Library')
@section('pagecss')
<link rel="stylesheet" href="https://officetohtml.js.org/libs/handsontable/handsontable.full.css">
<style>
.card.main_top_overview_card {
    padding: 1px 20px !important;
}

.card-body {
    padding: 5px;
}

/* #btnContainer {
    text-align: right;
    margin-bottom: 15px;
    background: white;
} */
#btnContainer {
    float: inline-end;

}

.thead-dark {
    background: #5BC2B9;
}

.thead-dark th {
    color: white;
    font-weight: 600;
    font-size: 12px;
}

.list {
    display: none;
}
.pdfcss{
    position: absolute;
    top: 19px;
    right: 28px;
    width: 45px;
    height: 45px;
    background: #d1d1d1;
    pointer-events: all;
}
.doccss{
    position: absolute;
    bottom: 18px;
    /* right: 27px; */
    width: 99%;
    height: 25px;
    background: #fff;
    pointer-events: all;
}
</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css" />
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body py-1">
                @if($details['content_type']=='U')
                <iframe class="iframe" src="{{$details['content_url']}}" frameborder="0" width="100%" height="500px"
                    scrolling="no" marginheight="0" marginwidth="0" role="document" aria-label="URL document"
                    title="URL document">
                </iframe>
                @else
                @php
                $arrFile=explode('.',$details['content_file']);
                $fileExtension=$arrFile[1];
                @endphp
                @if(strtolower($fileExtension)=='pdf')
                <!-- pdf -->
                <div class="pdfcss"></div>
                <iframe id="previewFrame" class="google-docs iframe "
                    src="https://docs.google.com/viewer?url={{config('app.api_asset_url') . '/'.$details['content_file']}}&embedded=true&downloadBtn=false&openFileBtn: false"
                    frameborder="0" scrolling="no" marginheight="0" marginwidth="0" width="100%" height="500px"
                    role="document" aria-label="PDF document" onload="disableContextMenu();" title="PDF document">Click to view the document</iframe>
                @endif
                @if(strtolower($fileExtension)=='doc' || strtolower($fileExtension)=='docx' ||
                strtolower($fileExtension)=='xls' || strtolower($fileExtension)=='xlsx' ||
                strtolower($fileExtension)=='ppt' || strtolower($fileExtension)=='pptx')
                <div class="doccss"></div>
                <iframe id="previewFrame" class="office iframe" onload="disableContextMenu();"
                    src="https://view.officeapps.live.com/op/embed.aspx?src={{config('app.api_asset_url') . '/'.$details['content_file']}}"
                    frameborder="0" width="100%" height="500px" scrolling="no" marginheight="0" marginwidth="0"
                    role="document" aria-label="Doc document" title="Doc document">
                </iframe>
                @endif
                @endif


            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div>
</div>
@endsection
@section('pagescript')

<!-- <script src="https://officetohtml.js.org/libs/jquery/jquery-3.5.1.min.js"></script> -->
<script src="https://officetohtml.js.org/libs/officetohtml/officetohtml.min.js"></script>
<script src="https://officetohtml.js.org/libs/pdfjs/pdf.js"></script>
<script src="https://officetohtml.js.org/libs/handsontable/handsontable.full.js"></script>
<script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
<script>
window.addEventListener('load', function() {
    var iframe = document.querySelector('iframe');

    // Add event listener to the iframe content window
    iframe.contentWindow.document.addEventListener('contextmenu', function(event) {
      event.preventDefault(); // Prevent default right-click behavior
    });
  });
$(document).ready(function() {


    $("#previewFrame").officeToHtml({
        pdfSetting: {
            // setting for pdf
            setLang: "en",
            thumbnailViewBtn: true,
            searchBtn: true,
            nextPreviousBtn: true,
            pageNumberTxt: true,
            totalPagesLabel: true,
            zoomBtns: true,
            scaleSelector: true,
            presantationModeBtn: true,
            openFileBtn: true,
            printBtn: true,
            downloadBtn: false,
            bookmarkBtn: true,
            secondaryToolbarBtn: true,
            firstPageBtn: true,
            lastPageBtn: true,
            pageRotateCwBtn: true,
            pageRotateCcwBtn: true,
            cursorSelectTextToolbarBtn: true,
            cursorHandToolbarBtn: true
        },
        docxSetting: {
            // setting for docx
        },
        pptxSetting: {
            // setting for pptx
        },
        sheetSetting: {
            // setting for excel
        },
        imageSetting: {
            // setting for  images
        }
    });
});
</script>
@endsection
