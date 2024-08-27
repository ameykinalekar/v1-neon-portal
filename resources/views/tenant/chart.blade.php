@extends('layouts.blank')
@section('title', 'Chart')
@section('pagecss')

@endsection
@section('content')
@csrf
<canvas id="myChart" style="width:100%"></canvas>
@endsection

@section('pagescript')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
<script type="text/javascript">
var xval = "{{$ofsted_xvalues}}";
var yval = "{{$ofsted_yvalues}}";
var xlabel = "{{$xlabel}}";
var ylabel = "{{$ylabel}}";
var yprefix = "{{$yprefix}}";
var ysuffix = "{{$ysuffix}}";

const xValues = xval.split(',');
const yValues = yval.split(',');

new Chart("myChart", {
    type: "line",
    data: {
        labels: xValues,
        datasets: [{
            fill: false,
            lineTension: 0,
            backgroundColor: "rgba(0,0,255,1.0)",
            borderColor: "rgba(0,0,255,0.1)",
            data: yValues
        }]
    },
    options: {
        legend: {
            display: false
        },
        scales: {
            yAxes: [{
                scaleLabel: {
                    display: true,
                    labelString: ylabel
                },
                ticks: {
                    // Include a dollar sign in the ticks
                    callback: function(value, index, ticks) {
                        return yprefix + value + ysuffix;
                    }
                }
            }],
            xAxes: [{
                scaleLabel: {
                    display: true,
                    labelString: xlabel
                }
            }],
        }
    }
});
</script>
@endsection
