  //hide all tabs first
  $('.flow_admin_select_tab-content').hide();
  //show the first tab content
  $('#flow_admin_select_tab-1').show();

  $('#select-box').change(function () {
      dropdown = $('#select-box').val();
      //first hide all tabs again when a new option is selected
      $('.flow_admin_select_tab-content').hide();
      //then show the tab content of whatever option value was selected
      $('#' + "flow_admin_select_tab-" + dropdown).show();
  });


//  var myChart25 = new Chart(schPerStuTeaLineChar, {
//       type: 'line',


//       data: {
//           labels: ["MON", "TUE", "WED", "THU", "FRI", "SAT", "SUN"],
//           datasets: [{
//                   data: [60, 22, 45, 4, 60, 90, 15],

//                   borderColor: 'rgb(99,197,188)',
//                   backgroundColor: 'rgb(91 194 185 / 30%)',
//                   options: {
//                     //   maintainAsspectRatio:false;
//                       animations: {
//                           radius: {
//                               duration: 400,
//                               easing: 'linear',

//                           },
//                       },
//                       legend: false,

//                   },
//                   pointBorderColor: 'transparent',
//                   hoverRadius: 12,
//                   hoverBackgroundColor: 'rgb(91 194 185 / 50%)',
//                   fill: true,
//                   tension: 0.4,

//               },
//               {
//                   data: [75, 32, 55, 14, 70, 100, 25],

//                   borderColor: '#4464FF',
//                   backgroundColor: 'rgb(91 194 185 / 30%)',
//                   options: {
//                       animations: {
//                           radius: {
//                               duration: 400,
//                               easing: 'linear',

//                           },
//                       },
//                       legend: false,

//                   },
//                   pointBorderColor: 'transparent',
//                   hoverRadius: 12,
//                   hoverBackgroundColor: 'rgb(91 194 185 / 50%)',
//                   fill: true,
//                   tension: 0.4,

//               }
//           ],



//       },
//       options: {
        
          
//           plugins: {
//               legend: {
//                   display: false,
//               }
//           },
//       },



//   });


 var statTotalClsTeach = document.getElementById("statTotalClsTeach").getContext('2d');

  const gradientBgStatTotalClsTeach = statTotalClsTeach.createLinearGradient(0, 0, 0, 400);
  gradientBgStatTotalClsTeach.addColorStop(1, 'rgb(91 194 185 / 30%)');
  gradientBgStatTotalClsTeach.addColorStop(0.5, 'transparent');


  var myChart26 = new Chart(statTotalClsTeach, {
      type: 'line',


      data: {
          labels: ["MON", "TUE", "WED", "THU", "FRI", "SAT", "SUN"],
          datasets: [{
                  data: [60, 22, 45, 4, 60, 90, 15],

                  borderColor: 'rgb(99,197,188)',
                  backgroundColor: 'rgb(91 194 185 / 30%)',
                  options: {
                    //   maintainAsspectRatio:false;
                      animations: {
                          radius: {
                              duration: 400,
                              easing: 'linear',

                          },
                      },
                      legend: false,

                  },
                  pointBorderColor: 'transparent',
                  hoverRadius: 12,
                  hoverBackgroundColor: 'rgb(91 194 185 / 50%)',
                  fill: true,
                  tension: 0.4,

              },
              {
                  data: [75, 32, 55, 14, 70, 100, 25],

                  borderColor: '#4464FF',
                  backgroundColor: 'rgb(91 194 185 / 30%)',
                  options: {
                      animations: {
                          radius: {
                              duration: 400,
                              easing: 'linear',

                          },
                      },
                      legend: false,

                  },
                  pointBorderColor: 'transparent',
                  hoverRadius: 12,
                  hoverBackgroundColor: 'rgb(91 194 185 / 50%)',
                  fill: true,
                  tension: 0.4,

              }
          ],



      },
      options: {
        //   maintainAsspectRatio:false,
        //   responsive:false,
          
          plugins: {
              legend: {
                  display: false,
              }
          },
      },



  });


  var ctx = document.getElementById("myChartFlowLineLoop").getContext('2d');

  var gradientBg = ctx.createLinearGradient(0, 0, 0, 300);
  //   gradientBg.addColorStop(1, ' rgba(99,197,188,0.7)72%');
  //   gradientBg.addColorStop(0, 'rgba(99,197,188,0.3)28%');

  gradientBg.addColorStop(0, 'rgba(99,197,188, .7)');
  gradientBg.addColorStop(0.5, 'rgba(99,197,188, 0.35)');
  gradientBg.addColorStop(1, 'rgba(99,197,188, 0)');

  //   gradientBg.addColorStop(0, 'rgba(255, 0,0, 0.5)');
  //   gradientBg.addColorStop(0.5, 'rgba(255, 0, 0, 0.25)');
  //   gradientBg.addColorStop(1, 'rgba(255, 0, 0, 0)');


  var myChart2 = new Chart(ctx, {
      type: 'line',


      data: {
          labels: ["", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
          datasets: [{
              data: [43, 10, 20, 35, 29, 8, 5, 20, 60, 68, 56, 30, 10],

              borderColor: 'rgb(99,197,188)',
              //   backgroundColor: gradientBg,
              backgroundColor: gradientBg,
              //   fillColor: gradientBg,
              //   backgroundColor: gradientBg,

              options: {
                  responsive: false,
            maintainAspectRatio: false,
                  animations: {
                      radius: {
                          duration: 400,
                          easing: 'linear',

                      },
                  },


              },
              pointBorderColor: 'transparent',
              hoverRadius: 12,
              hoverBackgroundColor: 'rgb(91 194 185 / 50%)',
              fill: true,
              tension: 0.4,

          }],



      },
      options: {
          
          plugins: {
              legend: {
                  display: false,
              }
          },
      },



  });


  const doughnutChartData = {
      labels: ["Sum of Correct Answers",
          "Total No. of questions"
      ],
      data: [62, 38],

  };
  const myChart3 = document.getElementById("myChartFlowDoughnut");

  new Chart(myChart3, {
      type: "doughnut",
      data: {
          labels: doughnutChartData.labels,
          datasets: [{
              label: "",
              data: doughnutChartData.data,
              backgroundColor: ['#5BC2B9', '#FCC244']
          }]
      },
      options: {
          plugins: {
              legend: {
                  display: false,
              }
          }
      }
  });

  var chartLineTimeSpent = document.getElementById("chartLineTimeSpent");

  var myChart4 = new Chart(chartLineTimeSpent, {
      type: 'line',


      data: {
          labels: ["", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
          datasets: [{
                  data: [0, 60, 40, 60, 70, 70, 70, 40, 70, 80, 100, 80, 70, 100],

                  borderColor: 'rgb(99,197,188)',
                  backgroundColor: 'rgb(91 194 185 / 30%)',
                  options: {
                      animations: {
                          radius: {
                              duration: 400,
                              easing: 'linear',

                          },
                      },

                  },
                  hoverRadius: 12,
                  hoverBackgroundColor: 'rgb(91 194 185 / 50%)',


              },
              {
                  data: [70, 70, 70, 70, 70, 70, 70, 70, 70, 70, 70, 70, 70, ],

                  borderColor: '#E87E69',
                  backgroundColor: 'rgb(91 194 185 / 30%)',
                  options: {
                      animations: {
                          radius: {
                              duration: 400,
                              easing: 'linear',

                          },
                      },

                  },
                  pointBorderColor: 'transparent',
                  hoverRadius: 12,
                  hoverBackgroundColor: 'rgb(91 194 185 / 50%)',


              }

          ],




      },
      options: {
          plugins: {
              legend: {
                  display: false,
              }
          },
      },



  });

  var enscChartBar = document.getElementById("enscChartBar");

  var myChart5 = new Chart(enscChartBar, {
      type: 'bar',


      data: {
          labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
          datasets: [{
                  data: [800, 0, 1000, 0, 0, 0, 0, 1200, 0, 0, 0, 800],

                  borderColor: '#5BC2B9',
                  backgroundColor: '#5BC2B9',
                  options: {
                      animations: {
                          radius: {
                              duration: 400,
                              easing: 'linear',

                          },
                      },

                  },
                  hoverRadius: 12,
                  hoverBackgroundColor: 'rgb(91 194 185 / 50%)',
                  stack: 'Stack 0',

              },
              {
                  data: [0, 0, 0, 0, -800, 0, 0, -1000, 0, -1100, 0, 0],

                  borderColor: '#E87E69',
                  backgroundColor: '#E87E69',
                  options: {
                      animations: {
                          radius: {
                              duration: 400,
                              easing: 'linear',

                          },
                      },

                  },
                  hoverRadius: 12,
                  hoverBackgroundColor: 'rgb(91 194 185 / 50%)',
                  stack: 'Stack 0',

              },
              {
                  data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                  type: 'line',
                  borderColor: '#FCC244',
                  backgroundColor: '#FCC244',
                  options: {
                      animations: {
                          radius: {
                              duration: 400,
                              easing: 'linear',

                          },
                      },

                  },
                  pointBorderColor: 'transparent',
                  stack: 'Stack 0',


              }




          ],




      },
      options: {
          plugins: {
              legend: {
                  display: false,
              }
          },

          //   scales: {
          //       x: {
          //           position: 'top'
          //       },

          //       y: {
          //           min: -15,
          //           beginAtZero: true,
          //           grid: {
          //               color: {
          //                   zeroline: "",
          //                   barcolor: '#FFEDC7',

          //               }

          //           }
          //       }
          //   },
      },



  });
  //   const config = {
  //       type: 'bar',
  //       data,
  //       options: {
  //           scales: {
  //               x: {
  //                   position: 'top'
  //               },
  //               y: {
  //                 min:-15
  //                   beginAtZero: true,
  //                   grid: {
  //                       color: (context) => {
  //                           const zeroLine = context.tick.value;
  //                           const barcolor = zeroLine === 0 ? '#666' : '#ccc';
  //                           return barcolor;
  //                       }
  //                   }
  //               }
  //           }
  //       }
  //   }








  //   var myChart2 = new Chart(ctx, {
  //       type: 'doughnut',


  //       data: {
  //           labels: ["Sum of Correct Answers", "Total No. of questions", ],
  //           datasets: [{
  //               data: [13, 15, 5, 10, 9, 10],
  //               label: "Attendence   ",
  //               borderColor: 'rgb(99,197,188)',
  //               backgroundColor: 'rgb(99,197,188)',
  //               options: {
  //                   animations: {
  //                       radius: {
  //                           duration: 400,
  //                           easing: 'linear',
  //                           loop: (context) => context.active
  //                       }
  //                   },
  //               },
  //               hoverRadius: 12,
  //               hoverBackgroundColor: 'rgb(99,197,188)',

  //           }],

  //       },


  //   });


  //   Table Script







  var myChartFlowAttBarLoop = document.getElementById("myChartFlowAttBarLoop");

  var myChart6 = new Chart(myChartFlowAttBarLoop, {
      type: 'bar',


      data: {
          labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
          datasets: [{
                  data: [20, 40, 53, 80, 30, 90, 60],

                  borderColor: '#5BC2B9',
                  backgroundColor: '#5BC2B9',
                  options: {
                      animations: {
                          radius: {
                              duration: 400,
                              easing: 'linear',

                          },
                      },

                  },
                  hoverRadius: 12,
                  hoverBackgroundColor: 'rgb(91 194 185 / 50%)',
                  stack: 'Stack 0',

              },





          ],




      },
      options: {
          plugins: {
              legend: {
                  display: false,
              }
          },

          //   scales: {
          //       x: {
          //           position: 'top'
          //       },

          //       y: {
          //           min: -15,
          //           beginAtZero: true,
          //           grid: {
          //               color: {
          //                   zeroline: "",
          //                   barcolor: '#FFEDC7',

          //               }

          //           }
          //       }
          //   },
      },



  });



  const doughnutChartDataBeha = {
      labels: ["No. of Faculty",
          "No. of admin staff"
      ],
      data: [62, 38],

  };
  const myChart7 = document.getElementById("myChartFlowDoughnutBeha");

  new Chart(myChart7, {
      type: "doughnut",
      data: {
          labels: doughnutChartDataBeha.labels,
          datasets: [{
              label: "",
              data: doughnutChartDataBeha.data,
              backgroundColor: ['#5BC2B9', '#FCC244']
          }]
      },
      options: {
          plugins: {
              legend: {
                  display: false,
              }
          }
      }
  });






  var chartLineBehavioral = document.getElementById("chartLineBehavioral");

  var myChart8 = new Chart(chartLineBehavioral, {
      type: 'line',


      data: {
          labels: ["", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
          datasets: [{
                  data: [0, 20, 40, 20, 20, 40, 40, 40, 60, 80, 80, 60, 80],

                  borderColor: 'rgb(99,197,188)',
                  backgroundColor: 'rgb(91 194 185 / 30%)',
                  options: {
                      animations: {
                          radius: {
                              duration: 400,
                              easing: 'linear',

                          },
                      },

                  },
                  hoverRadius: 12,
                  hoverBackgroundColor: 'rgb(91 194 185 / 50%)',


              },


          ],




      },
      options: {
          plugins: {
              legend: {
                  display: false,
              }
          },
      },



  });


  //   myChartFlowLineLoopNot
  var myChartFlowLineLoopNot = document.getElementById("myChartFlowLineLoopNot");


  var gradientBgNot = ctx.createLinearGradient(0, 0, 0, 300);
  //   gradientBg.addColorStop(1, ' rgba(99,197,188,0.7)72%');
  //   gradientBg.addColorStop(0, 'rgba(99,197,188,0.3)28%');

  gradientBgNot.addColorStop(0, 'rgba(99,197,188, .7)');
  gradientBgNot.addColorStop(0.5, 'rgba(99,197,188, 0.35)');
  gradientBgNot.addColorStop(1, 'rgba(99,197,188, 0)');

  var myChart9 = new Chart(myChartFlowLineLoopNot, {
      type: 'line',


      data: {
          labels: ["", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
          datasets: [{
              data: [43, 10, 20, 35, 29, 8, 5, 20, 60, 68, 56, 30, 10],

              borderColor: 'rgb(99,197,188)',
              backgroundColor: gradientBgNot,
              options: {
                  animations: {
                      radius: {
                          duration: 400,
                          easing: 'linear',

                      },
                  },
                  legend: false,

              },
              pointBorderColor: 'transparent',
              hoverRadius: 12,
              hoverBackgroundColor: 'rgb(91 194 185 / 50%)',
              fill: true,
              tension: 0.4,

          }],



      },
      options: {
          plugins: {
              legend: {
                  display: false,
              }
          },
      },



  });



  //   myChartFlowDoughnutFtar
  const doughnutChartFtarData = {
      labels: ["No. of Faculty",
          "No. of admin staff"
      ],
      data: [62, 38],

  };
  const myChart10 = document.getElementById("myChartFlowDoughnutFtar");

  new Chart(myChart10, {
      type: "doughnut",
      data: {
          labels: doughnutChartFtarData.labels,
          datasets: [{
              label: "",
              data: doughnutChartFtarData.data,
              backgroundColor: ['#5BC2B9', '#FCC244']
          }]
      },
      options: {
          plugins: {
              legend: {
                  display: false,
              }
          }
      }
  });


  //   chartLineFaAtt

  var chartLineFaAtt = document.getElementById("chartLineFaAtt");

  var myChart11 = new Chart(chartLineFaAtt, {
      type: 'line',


      data: {
          labels: ["", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
          datasets: [{
                  data: [0, 60, 40, 60, 70, 70, 70, 40, 80, 80, 80, 60, 80],

                  borderColor: 'rgb(99,197,188)',
                  backgroundColor: 'rgb(91 194 185 / 30%)',
                  options: {
                      animations: {
                          radius: {
                              duration: 400,
                              easing: 'linear',

                          },
                      },

                  },
                  hoverRadius: 12,
                  hoverBackgroundColor: 'rgb(91 194 185 / 50%)',


              },
              {
                  data: [80, 80, 80, 80, 80, 80, 80, 80, 80, 80, 80, 80, 80, ],

                  borderColor: '#E87E69',
                  backgroundColor: 'rgb(91 194 185 / 30%)',
                  options: {
                      animations: {
                          radius: {
                              duration: 400,
                              easing: 'linear',

                          },
                      },

                  },
                  pointBorderColor: 'transparent',
                  hoverRadius: 12,
                  hoverBackgroundColor: 'rgb(91 194 185 / 50%)',


              }

          ],




      },
      options: {
          plugins: {
              legend: {
                  display: false,
              }
          },
      },



  });

  //   twqtChartBar

  var twqtChartBar = document.getElementById("twqtChartBar");

  var myChart12 = new Chart(twqtChartBar, {
      type: 'bar',


      data: {
          labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
          datasets: [{
                  data: [600, 1000, 400, 1000, 700, 800, 500, 900, 1000, 1000, 700, 600],

                  borderColor: '#5BC2B9',
                  backgroundColor: '#5BC2B9',
                  options: {
                      animations: {
                          radius: {
                              duration: 400,
                              easing: 'linear',

                          },
                      },

                  },
                  borderwidth: 1,
                  barPercentage: 0.25,
                  categoryPercentage: 0.5,
                  hoverRadius: 12,
                  hoverBackgroundColor: 'rgb(91 194 185 / 50%)',
                  stack: 'Stack 0',

              },





          ],




      },
      options: {
          plugins: {
              legend: {
                  display: false,
              }
          },

          //   scales: {
          //       x: {
          //           position: 'top'
          //       },

          //       y: {
          //           min: -15,
          //           beginAtZero: true,
          //           grid: {
          //               color: {
          //                   zeroline: "",
          //                   barcolor: '#FFEDC7',

          //               }

          //           }
          //       }
          //   },
      },



  });


  //   var myChart13 = AmCharts.makeChart("chartdiv", {
  //       "type": "serial",
  //       "theme": "light",
  //       "dataProvider": [{
  //               "month": "Jan",
  //               "visits": 40
  //           }, {
  //               "month": "Feb",
  //               "visits": 60
  //           }, {
  //               "month": "Mar",
  //               "visits": 80
  //           }, {
  //               "month": "Apr",
  //               "visits": 20
  //           }, {
  //               "month": "May",
  //               "visits": 30
  //           }, {
  //               "month": "Jun",
  //               "visits": 50
  //           },
  //           {
  //               "month": "July",
  //               "visits": 70
  //           },
  //           {
  //               "month": "Aug",
  //               "visits": 85
  //           },
  //           {
  //               "month": "Sep",
  //               "visits": 90
  //           },
  //           {
  //               "month": "Oct",
  //               "visits": 60
  //           },
  //           {
  //               "month": "Nov",
  //               "visits": 20
  //           },
  //           {
  //               "month": "Dec",
  //               "visits": 30
  //           }
  //       ],
  //       "graphs": [{
  //           "fillAlphas": 0.9,
  //           "lineAlpha": 0.2,
  //           "type": "column",
  //           "fixedColumnWidth": 1,
  //           "valueField": "visits",
  //           "bullet": "round",
  //       }],
  //       "categoryField": "month",
  //       "rotate": false,

  //   });

  //   twqtChartBar

  var senLeadChartBar = document.getElementById("senLeadChartBar");

  var myChart12 = new Chart(senLeadChartBar, {
      type: 'bar',


      data: {
          labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
          datasets: [{
                  data: [40, 60, 80, 20, 30, 55, 70, 88, 96, 60, 20, 35],

                  borderColor: '#5BC2B9',
                  backgroundColor: '#5BC2B9',
                  options: {
                      animations: {
                          radius: {
                              duration: 400,
                              easing: 'linear',

                          },
                      },

                  },
                  borderwidth: 1,
                  barPercentage: 0.25,
                  categoryPercentage: 0.5,
                  hoverRadius: 12,
                  hoverBackgroundColor: 'rgb(91 194 185 / 50%)',
                  stack: 'Stack 0',

              },





          ],




      },
      options: {
          plugins: {
              legend: {
                  display: false,
              }
          },

          //   scales: {
          //       x: {
          //           position: 'top'
          //       },

          //       y: {
          //           min: -15,
          //           beginAtZero: true,
          //           grid: {
          //               color: {
          //                   zeroline: "",
          //                   barcolor: '#FFEDC7',

          //               }

          //           }
          //       }
          //   },
      },



  });

  var myChartFlowBarTeAs = document.getElementById("myChartFlowBarTeAs");

  var myChart14 = new Chart(myChartFlowBarTeAs, {
      type: 'bar',


      data: {
          labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
          datasets: [{
                  data: [60, 80, 100, 20, 50, 30, 70, 50, 90, 80, 10, 40],

                  borderColor: '#5BC2B9',
                  backgroundColor: '#5BC2B9',
                  options: {
                      animations: {
                          radius: {
                              duration: 400,
                              easing: 'linear',

                          },
                      },

                  },
                  hoverRadius: 12,
                  hoverBackgroundColor: 'rgb(91 194 185 / 50%)',
                  stack: 'Stack 0',

              },





          ],




      },
      options: {
          plugins: {
              legend: {
                  display: false,
              }
          },

          //   scales: {
          //       x: {
          //           position: 'top'
          //       },

          //       y: {
          //           min: -15,
          //           beginAtZero: true,
          //           grid: {
          //               color: {
          //                   zeroline: "",
          //                   barcolor: '#FFEDC7',

          //               }

          //           }
          //       }
          //   },
      },



  });


  var myChartFlowLineNoClSuSt = document.getElementById("myChartFlowLineNoClSuSt");

  var myChart15 = new Chart(myChartFlowLineNoClSuSt, {
      type: 'line',


      data: {
          labels: ["", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
          datasets: [{
                  data: [0, 20, 40, 20, 20, 40, 40, 40, 60, 80, 80, 60, 80],

                  borderColor: 'rgb(99,197,188)',
                  backgroundColor: 'rgb(91 194 185 / 30%)',
                  options: {
                      animations: {
                          radius: {
                              duration: 400,
                              easing: 'linear',

                          },
                      },

                  },
                  pointBorderColor: 'transparent',
                  hoverRadius: 12,
                  hoverBackgroundColor: 'rgb(91 194 185 / 50%)',


              },


          ],




      },
      options: {
          plugins: {
              legend: {
                  display: false,
              }
          },
      },



  });

  var myChartFlowLineNoAuxSta = document.getElementById("myChartFlowLineNoAuxSta");

  var gradientBg = ctx.createLinearGradient(0, 0, 0, 450);
  //   gradientBg.addColorStop(1, ' rgba(99,197,188,0.7)72%');
  //   gradientBg.addColorStop(0, 'rgba(99,197,188,0.3)28%');

  gradientBg.addColorStop(0, 'rgba(99,197,188, .7)');
  gradientBg.addColorStop(0.5, 'rgba(99,197,188, 0.35)');
  gradientBg.addColorStop(1, 'rgba(99,197,188, 0)');


  var myChart16 = new Chart(myChartFlowLineNoAuxSta, {
      type: 'line',


      data: {
          labels: ["", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
          datasets: [{
                  data: [0, 60, 40, 60, 70, 70, 70, 40, 80, 80, 80, 60, 80],

                  borderColor: 'rgb(99,197,188)',
                  backgroundColor: gradientBg,
                  options: {
                      animations: {
                          radius: {
                              duration: 400,
                              easing: 'linear',

                          },
                      },

                  },
                  pointBorderColor: 'transparent',
                  hoverRadius: 12,
                  hoverBackgroundColor: 'rgb(91 194 185 / 50%)',
                  fill: true,

              },


          ],




      },
      options: {
          plugins: {
              legend: {
                  display: false,
              }
          },

      },



  });

  var myChartFlowLineNoTotWor = document.getElementById("myChartFlowLineNoTotWor");

  var myChart17 = new Chart(myChartFlowLineNoTotWor, {
      type: 'bar',


      data: {
          labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
          datasets: [{
                  data: [60, 80, 100, 20, 50, 30, 70, 50, 90, 80, 10, 40],

                  borderColor: '#5BC2B9',
                  backgroundColor: '#5BC2B9',
                  options: {
                      animations: {
                          radius: {
                              duration: 400,
                              easing: 'linear',

                          },
                      },

                  },

                  borderRadius: 15,
                  borderSkipped: false,
                  hoverRadius: 12,
                  hoverBackgroundColor: 'rgb(91 194 185 / 50%)',
                  stack: 'Stack 0',

              },





          ],




      },
      options: {
          plugins: {
              legend: {
                  display: false,
              }
          },

          //   scales: {
          //       x: {
          //           position: 'top'
          //       },

          //       y: {
          //           min: -15,
          //           beginAtZero: true,
          //           grid: {
          //               color: {
          //                   zeroline: "",
          //                   barcolor: '#FFEDC7',

          //               }

          //           }
          //       }
          //   },
      },



  });

  //   myChartFlowDoughnutToIn

  const doughnutChartDataToIn = {
      labels: ["Grant",
          "Self Generated",
          "Self Generated"
      ],
      data: [62, 17, 38, ],

  };
  const myChart18 = document.getElementById("myChartFlowDoughnutToIn");

  new Chart(myChart18, {
      type: "doughnut",
      data: {
          labels: doughnutChartDataToIn.labels,
          datasets: [{
              label: "",
              data: doughnutChartDataToIn.data,
              backgroundColor: ['#5BC2B9', '#4C94DB', '#FCC244']
          }]
      },
      options: {
          plugins: {
              legend: {
                  display: false,
              }
          }
      }
  });

  //   myChartFlowLineIncome

  var myChartFlowLineIncome = document.getElementById("myChartFlowLineIncome");

  var myChart19 = new Chart(myChartFlowLineIncome, {
      type: 'line',


      data: {
          labels: ["", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
          datasets: [{
                  data: [0, 20, 40, 20, 20, 40, 40, 40, 60, 80, 80, 60, 80],

                  borderColor: 'rgb(99,197,188)',
                  backgroundColor: 'rgb(91 194 185 / 30%)',
                  options: {
                      animations: {
                          radius: {
                              duration: 400,
                              easing: 'linear',

                          },
                      },

                  },

                  hoverRadius: 12,
                  hoverBackgroundColor: 'rgb(91 194 185 / 50%)',


              },


          ],




      },
      options: {
          plugins: {
              legend: {
                  display: false,
              }
          },
      },



  });

  var myChartFlowLineLoopLorIP = document.getElementById("myChartFlowLineLoopLorIP").getContext('2d');

  //   const gradientBgtwo = myChartFlowLineLoopLorIP.createLinearGradient(0, 0, 0, 400);
  //   gradientBgtwo.addColorStop(1, 'rgb(91 194 185 / 30%)');
  //   gradientBgtwo.addColorStop(0.5, 'transparent');
  var gradientBgLorIP = ctx.createLinearGradient(0, 0, 0, 300);
  //   gradientBg.addColorStop(1, ' rgba(99,197,188,0.7)72%');
  //   gradientBg.addColorStop(0, 'rgba(99,197,188,0.3)28%');

  gradientBgLorIP.addColorStop(0, 'rgba(99,197,188, .7)');
  gradientBgLorIP.addColorStop(0.5, 'rgba(99,197,188, 0.35)');
  gradientBgLorIP.addColorStop(1, 'rgba(99,197,188, 0)');


  var myChart20 = new Chart(myChartFlowLineLoopLorIP, {
      type: 'line',


      data: {
          labels: ["", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
          datasets: [{
              data: [43, 10, 20, 35, 29, 8, 5, 20, 60, 68, 56, 30, 10],

              borderColor: 'rgb(99,197,188)',
              backgroundColor: gradientBgLorIP,
              options: {
                  animations: {
                      radius: {
                          duration: 400,
                          easing: 'linear',

                      },
                  },
                  legend: false,

              },
              pointBorderColor: 'transparent',
              hoverRadius: 12,
              hoverBackgroundColor: 'rgb(91 194 185 / 50%)',
              fill: true,
              tension: 0.4,

          }],



      },
      options: {
          plugins: {
              legend: {
                  display: false,
              }
          },
      },



  });

  //   myChartFlowDoughnutToInTwo
  const doughnutChartDataToInTwo = {
      labels: ["Staff Expenditure",
          "Cost per Meal",
          "Supplies and Services",
          "Occupation Expenses", "Premises Expenditure", "Finance Expenditure"
      ],
      data: [20, 15, 15, 20, 25, 5],

  };
  const myChart21 = document.getElementById("myChartFlowDoughnutToInTwo");

  new Chart(myChart21, {
      type: "doughnut",
      data: {
          labels: doughnutChartDataToInTwo.labels,
          datasets: [{
              label: "",
              data: doughnutChartDataToInTwo.data,
              backgroundColor: ['#5BC2B9', '#DB4CD6', '#E87E69', '#4C94DB', '#FCC244', '#69FC44']
          }]
      },
      options: {
          plugins: {
              legend: {
                  display: false,
              }
          }
      }
  });


  var chartLineIncoExp = document.getElementById("chartLineIncoExp");

  var myChart22 = new Chart(chartLineIncoExp, {
      type: 'line',


      data: {
          labels: ["", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
          datasets: [{
                  data: [0, 20, 40, 20, 20, 40, 40, 40, 60, 80, 80, 60, 80],

                  borderColor: 'rgb(99,197,188)',
                  backgroundColor: 'rgb(91 194 185 / 30%)',
                  options: {
                      animations: {
                          radius: {
                              duration: 400,
                              easing: 'linear',

                          },
                      },

                  },
                  hoverRadius: 12,
                  hoverBackgroundColor: 'rgb(91 194 185 / 50%)',


              },
              {
                  data: [0, 10, 20, 40, 40, 60, 20, 20, 40, 20, 60, 40, 20, ],

                  borderColor: '#E87E69',
                  backgroundColor: 'rgb(91 194 185 / 30%)',
                  options: {
                      animations: {
                          radius: {
                              duration: 400,
                              easing: 'linear',

                          },
                      },

                  },
                  pointBorderColor: 'transparent',
                  hoverRadius: 12,
                  hoverBackgroundColor: 'rgb(91 194 185 / 50%)',


              }

          ],




      },
      options: {
          plugins: {
              legend: {
                  display: false,
              }
          },
      },



  });


  var myChartFlowBarTeacpreTotwor = document.getElementById("myChartFlowBarTeacpreTotwor");

  var myChart23 = new Chart(myChartFlowBarTeacpreTotwor, {
      type: 'bar',


      data: {
          labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
          datasets: [{
                  data: [60, 80, 100, 20, 50, 30, 70, 50, 90, 80, 10, 40],

                  borderColor: '#5BC2B9',
                  backgroundColor: '#5BC2B9',
                  options: {
                      animations: {
                          radius: {
                              duration: 400,
                              easing: 'linear',

                          },
                      },

                  },
                  hoverRadius: 12,
                  hoverBackgroundColor: 'rgb(91 194 185 / 50%)',
                  stack: 'Stack 0',

              },





          ],




      },
      options: {
          plugins: {
              legend: {
                  display: false,
              }
          },

          //   scales: {
          //       x: {
          //           position: 'top'
          //       },

          //       y: {
          //           min: -15,
          //           beginAtZero: true,
          //           grid: {
          //               color: {
          //                   zeroline: "",
          //                   barcolor: '#FFEDC7',

          //               }

          //           }
          //       }
          //   },
      },



  });

  var myChartFlowBarCoMaDete = document.getElementById("myChartFlowBarCoMaDete");

  var myChart24 = new Chart(myChartFlowBarCoMaDete, {
      type: 'bar',


      data: {
          labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
          datasets: [{
                  data: [60, 80, 100, 20, 50, 30, 70, 50, 90, 80, 10, 40],

                  borderColor: '#5BC2B9',
                  backgroundColor: '#5BC2B9',
                  options: {
                      animations: {
                          radius: {
                              duration: 400,
                              easing: 'linear',

                          },
                      },

                  },
                  borderRadius: 15,
                  borderSkipped: false,
                  hoverRadius: 12,
                  hoverBackgroundColor: 'rgb(91 194 185 / 50%)',
                  stack: 'Stack 0',

              },





          ],




      },
      options: {
          plugins: {
              legend: {
                  display: false,
              }
          },

          //   scales: {
          //       x: {
          //           position: 'top'
          //       },

          //       y: {
          //           min: -15,
          //           beginAtZero: true,
          //           grid: {
          //               color: {
          //                   zeroline: "",
          //                   barcolor: '#FFEDC7',

          //               }

          //           }
          //       }
          //   },
      },



  });

  $(document).ready(function () {
      $(".table_tab_container .table_tab_single").each(function (e) {
          if (e != 0)
              $(this).hide();
      });

      $("#table_next").click(function () {
          if ($(".table_tab_container .table_tab_single:visible").next().length != 0)
              $(".table_tab_container .table_tab_single:visible").next().show().prev().hide();
          else {
              $(".table_tab_container .table_tab_single:visible").hide();
              $(".table_tab_container .table_tab_single:first").show();
          }
          return false;
      });

      $("#table_prev").click(function () {
          if ($(".table_tab_container .table_tab_single:visible").prev().length != 0)
              $(".table_tab_container .table_tab_single:visible").prev().show().next().hide();
          else {
              $(".table_tab_container .table_tab_single:visible").hide();
              $(".table_tab_container .table_tab_single:last").show();
          }
          return false;
      });
  });