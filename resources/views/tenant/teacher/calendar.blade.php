@extends('layouts.default')
@section('title', 'Task Calendar')
@section('pagecss')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.css" />

<style type="text/css">
    .fc-state-hover, .fc-state-down, .fc-state-active, .fc-state-disabled {
        color: white;
        background-color: #5BC2B9 !important;
        background-image: none;
    }
    .fc-state-default {
        color: white;
        background-color: #5BC2B9 !important;
        background-image: none;
    }
    .fc th.fc-widget-header {
        background-color: white;
    }
    .fc-month-button {
        color: white;
        background-color: #5BC2B9 !important;
        background-image: none;
    }
    .fc-agendaDay-button{
        color: white;
        background-color: #5BC2B9 !important;
        background-image: none;
    }
    .fc-agendaWeek-button{
        color: white;
        background-color: #5BC2B9 !important;
        background-image: none;
    }
    .fc-content {
        background-color: #b4dedd;
    }
    /* Media queries for responsiveness */
    @media (max-width: 768px) {
        /* Adjustments for smaller screens */
        .fc-header-toolbar {
            display: flex;
            flex-direction: column;
        }

        .fc-header-toolbar .fc-left,
        .fc-header-toolbar .fc-center,
        .fc-header-toolbar .fc-right {
            margin: 0;
            padding: 0;
            text-align: center;
        }

        .fc-view-container {
            overflow-x: auto;
        }
    }
    #btnContainer {
        float: inline-end;

    }
</style>

@endsection
@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body py-1">
                <h4 class="page-title">
                    <i></i>Task Calendar
                    <span id="btnContainer">
                    <a href="javascript:void(0);" class="btn btn-sm btn-default" title="Add Task" onclick="rightModal('{{route('tut_addtask',Session()->get('tenant_info')['subdomain'])}}', 'New Task')"><i class="fa fa-plus"></i> Add Task</a></a>
                        <!-- <a href="#" class="btn btn-sm btn-default" title=""><i class="fa fa-backward"></i> Back</a> -->

                </h4>

            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body admin_content">
                <div id='calendar'></div>
            </div>
        </div>
    </div>
</div>
<br/>

<!-- <div class="row">

    <div class="col-md-12">
        <div class="row">
            <div class="col-md-3">
            </div>

            <div class="col-md-6">
                <div  class="card" style=" border-radius: 0px !important;height: 100px;padding: 10px;">
                    <span style="font-size:50px;color:#3d73dd">&bull;</span> Homework
                </div>
            </div>

            <div class="col-md-3">
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="row">
            <div class="col-md-3">
            </div>

            <div class="col-md-6">
                <div  class="card" style=" border-radius: 0px !important;height: 100px;padding: 10px;">
                    <span style="font-size:50px;color:#428f88">&bull;</span> Other
                </div>
            </div>

            <div class="col-md-3">
            </div>
        </div>
    </div>

</div> -->

@endsection
@section('pagescript')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.js"></script>
<script>
$(document).ready(function() {


    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var calendar = $('#calendar').fullCalendar({
        events: "{{route('tut_calendar',Session()->get('tenant_info')['subdomain'])}}",
        // events: SITEURL + "/fullcalender",
        //  events: [
        //   {
        //     title: 'Single Event',
        //     start: '2024-08-06T10:00:00',
        //     end: '2024-08-06T12:00:00'
        //   }
        // ],
        displayEventTime: false,
        editable: true,
        header: {
            left: 'prev,next today',
            center : 'title',
            right: 'month,agendaWeek'//,agendaDay
        },
        eventRender: function(event, element, view) {
            console.log('load event',event);
            if (event.allDay === 'true') {
                event.allDay = true;
            } else {
                event.allDay = false;
            }
        },
        selectable: true,
        selectHelper: true,
        buttonText: {
            today: 'Today',
            month: 'Month',
            week: 'Week',
            day: 'Day',
            listMonth: 'List Month',
            listYear: 'List Year',
            listWeek: 'List Week',
            listDay: 'List Day'
        },
        // select: function(start, end, allDay) {
        //     var title = prompt('Event Title:');
        //     if (title) {
        //         var start = $.fullCalendar.formatDate(start, "Y-MM-DD");
        //         var end = $.fullCalendar.formatDate(end, "Y-MM-DD");
        //         $.ajax({
        //             url: SITEURL + "/fullcalenderAjax",
        //             data: {
        //                 title: title,
        //                 start: start,
        //                 end: end,
        //                 type: 'add'
        //             },
        //             type: "POST",
        //             success: function(data) {
        //                 displayMessage("Event Created Successfully");

        //                 calendar.fullCalendar('renderEvent', {
        //                     id: data.id,
        //                     title: title,
        //                     start: start,
        //                     end: end,
        //                     allDay: allDay
        //                 }, true);

        //                 calendar.fullCalendar('unselect');
        //             }
        //         });
        //     }
        // },
        // eventDrop: function(event, delta) {
        //     var start = $.fullCalendar.formatDate(event.start, "Y-MM-DD");
        //     var end = $.fullCalendar.formatDate(event.end, "Y-MM-DD");

        //     $.ajax({
        //         url: SITEURL + '/fullcalenderAjax',
        //         data: {
        //             title: event.title,
        //             start: start,
        //             end: end,
        //             id: event.id,
        //             type: 'update'
        //         },
        //         type: "POST",
        //         success: function(response) {
        //             displayMessage("Event Updated Successfully");
        //         }
        //     });
        // },
        eventClick: function(event) {
            // alert(event.id);
            var callUrl="<?php echo config('app.base_url') . '/' . Session()->get('tenant_info')['subdomain'] . '/teacher/view-task'; ?>";
            rightModal(callUrl+'/'+event.id, 'View Task');

        }

    });

});


</script>
@endsection
