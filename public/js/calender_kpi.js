 $(function () {

            $('.calendar-container').calendar();

        });
        $('.calendar-container').calendar({
            date: new Date() // today
        });
        $('.calendar-container').calendar({
            weekDayLength: 2
        });
        $('.calendar-container').calendar({

            onClickDate: function (date) {

                // do something

            }

        });
        $('.calendar-container').calendar({

            // text for prev/next buttons
            prevButton: "<",
            nextButton: ">",

            // custom separator between the month and the year in the month view.
            monthYearSeparator: " ",

            // false = 4 months in a row
            showThreeMonthsInARow: true,

            // whether to change either month or year
            enableMonthChange: true,

            // whether to disable year view
            enableYearView: true,

            // shows a Today button on the bottom of the calendar
            showTodayButton: true,
            todayButtonContent: "Today",

            // highlights all other dates with the same week-day
            highlightSelectedWeekday: true,

            // highlights the selected week that contains the selected date
            highlightSelectedWeek: true,

            // whether to enable/disable the year selector
            showYearDropdown: true,

            // min/max dates
            // Date Object or Date String
            min: null,
            max: null,

            // start on Sunday instead
            startOnMonday: true,

            // format week day
            formatWeekDay: function (weekDay) {
                // function to format the week day
            },

            // format date
            formatDate: function (day) {
                // function to format date
            },

            // <a href="https://www.jqueryscript.net/tags.php?/map/">map</a> the month number to a string
            monthMap: {
                1: "january",
                2: "february",
                3: "march",
                4: "april",
                5: "may",
                6: "june",
                7: "july",
                8: "august",
                9: "september",
                10: "october",
                11: "november",
                12: "december",
            },

            // map the week number to a string
            dayMap: {
                0: "sunday",
                1: "monday",
                2: "tuesday",
                3: "wednesday",
                4: "thursday",
                5: "friday",
                6: "saturday",
            },

            // map the week number to a string when monday is the start of the week
            alternateDayMap: {
                1: "monday",
                2: "tuesday",
                3: "wednesday",
                4: "thursday",
                5: "friday",
                6: "saturday",
                7: "sunday",
            },

        });
        
        
          $('.Show_event').click(function () {
            $('#add_event_div').show(200);
            $('.Show_event').hide(0);
            $('.Hide_event').show(0);
        });
        $('.Hide_event').click(function () {
            $('#add_event_div').hide(500);
            $('.Show_event').show(0);
            $('.Hide_event').hide(0);
        });
        
        $(document).ready(function () {
            $("#incHeight").click(function () {
                $("div.calender_div_wrap").height(483);
                $('#incHeight').hide(0);
                $('#decHeight').show(0);
            });
            $("#decHeight").click(function () {
                $("div.calender_div_wrap").height(205);
                $('#decHeight').hide(0);
                $('#incHeight').show(0);
            });

        });
        
        