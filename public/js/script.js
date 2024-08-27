// const charts = document.querySelectorAll(".chart");

// charts.forEach(function (chart) {
//   var ctx = chart.getContext("2d");

//   var myChart = new Chart(ctx, {
//     type: "line",
//     data: {
//       labels: ["MON", "TUE", "WED", "THU", "FRI", "SAT"],
//       datasets: [{
//         label: "Class learning activity",
//         data: [12, 19, 3, 5, 2, 3],
//         fill: true,
//         backgroundColor: [

//           "rgba(75, 192, 192, 0.2)",
//           "rgba(153, 102, 255, 0.2)",

//         ],
//         borderColor: [

//           "rgba(75, 192, 192, 1)",
//           "rgba(153, 102, 255, 1)",

//         ],
//         borderWidth: 1,
//       }, ],
//     },
//     data: {
//       labels: ["MON", "TUE", "WED", "THU", "FRI", "SAT"],
//       datasets: [{
//         label: "Class learning activity",
//         data: [13, 19, 3, 5, 2, 3],
//         fill: true,
//         backgroundColor: [


//           "rgba(153, 102, 255, 0.2)",

//         ],
//         borderColor: [


//           "rgba(153, 102, 255, 1)",

//         ],
//         borderWidth: 1,
//       }, ],
//     },
//     options: {
//       scales: {
//         y: {
//           beginAtZero: true,
//         },
//       },
//       animations: {
//         tension: {
//           duration: 1000,
//           easing: 'linear',
//           from: 1,
//           to: 0,
//           loop: true
//         },

//       },
//     }
//   });
// });



// $(document).ready(function () {
//   $(".data-table").each(function (_, table) {
//     $(table).DataTable();
//   });
// });

// ==Circle progress
const meters = document.querySelectorAll('svg[data-value] .meter');

meters.forEach((path) => {
  // Get the length of the path
  let length = path.getTotalLength();

  // console.log(length);

  // Just need to set this once manually on the .meter element and then can be commented out
  // path.style.strokeDashoffset = length;
  // path.style.strokeDasharray = length;

  // Get the value of the meter
  let value = parseInt(path.parentNode.getAttribute('data-value'));
  // Calculate the percentage of the total length
  let to = length * ((100 - value) / 100);
  // Trigger Layout in Safari hack https://jakearchibald.com/2013/animated-line-drawing-svg/
  path.getBoundingClientRect();
  // Set the Offset
  path.style.strokeDashoffset = Math.max(0, to);
  path.nextElementSibling.textContent = `${value}%`;
});
// ===============circle progress




const activityNumbers = [{
    day: 'MON',
    activity: {
      totalClass: 4,
      classActive: 2
    }
  },
  {
    day: 'TUE',
    activity: {
      totalClass: 7,
      classActive: 5
    }
  },
  {
    day: 'WED',
    activity: {
      totalClass: 10,
      classActive: 8
    },
  },
  {
    day: 'THU',
    activity: {
      totalClass: 5,
      classActive: 3
    }
  },
  {
    day: 'FRI',
    activity: {
      totalClass: 8,
      classActive: 6
    },
  },
  {
    day: 'SAT',
    activity: {
      totalClass: 6,
      classActive: 4
    }
  },
  {
    day: 'SUN',
    activity: {
      totalClass: 3,
      classActive: 2
    }
  },

];

// 'rgba(54, 162, 235, 0.2)',

// 'rgba(75, 192, 192, 0.2)',

const data = {

  datasets: [{
      label: false,
      data: activityNumbers,
      fill: true,
      backgroundColor: [

        'rgba(54, 162, 235, 0.2)',



      ],
      borderColor: [

        'rgba(54, 162, 235, 1)',



      ],
      borderWidth: 1,
      parsing: {
        xAxisKey: 'day',
        yAxisKey: 'activity.totalClass',
      }
    },
    {
      label: false,
      data: activityNumbers,
      fill: true,
      backgroundColor: [

        'rgba(75, 192, 192,0.2)',



      ],
      borderColor: [

        'rgba(75, 192, 192, 1)',



      ],
      borderWidth: 1,
      parsing: {
        xAxisKey: 'day',
        yAxisKey: 'activity.classActive',
      }
    },
  ],

};

// config 
const config = {
  type: 'line',
  data,
  options: {
    scales: {
      y: {
        beginAtZero: true
      }
    },
    animations: {
      tension: {
        duration: 1000,
        easing: 'linear',
        from: 1,
        to: 0,
        loop: true
      }
    },
  }
};

// render init block
const myChart = new Chart(
  document.getElementById('myChart'),
  config
);

// Instantly assign Chart.js version
const chartVersion = document.getElementById('chartVersion');
chartVersion.innerText = Chart.version;



// ===========CalenderJs
var app = {
  settings: {
    container: $('.calendar'),
    calendar: $('.front'),
    days: $('.weeks span'),
    form: $('.back'),
    input: $('.back input'),
    buttons: $('.back button')
  },

  init: function () {
    instance = this;
    settings = this.settings;
    this.bindUIActions();
  },

  swap: function (currentSide, desiredSide) {
    settings.container.toggleClass('flip');

    currentSide.fadeOut(900);
    currentSide.hide();
    desiredSide.show();

  },

  bindUIActions: function () {
    settings.days.on('click', function () {
      instance.swap(settings.calendar, settings.form);
      settings.input.focus();
    });

    settings.buttons.on('click', function () {
      instance.swap(settings.form, settings.calendar);
    });
  }
}

app.init();

// ====================