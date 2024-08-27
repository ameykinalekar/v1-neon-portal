@extends('layouts.default')
@section('title', 'NTP Support')
@section('pagecss')
<style>

    @media (min-width: 992px) {
        main {
             margin-left: 0px;
        }
    }
    .table>:not(caption)>*>* {
        padding: 0px;
    }
    .alert{
        padding : 3px 15px;
    }
    .container {
        display: flex;
        flex-wrap: wrap;
        padding: 20px;
    }

    .filters {
        margin-left: -20px;
        flex: 1 1 200px;
        max-width: 300px;
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .filter-section {
        margin-bottom: 20px;
    }

    .filter-section h3 {
        margin-bottom: 10px;
        font-size: 18px;
    }

    .filter-section label {
        display: block;
        margin-bottom: 5px;
        font:13px;
        font-weight: normal;
    }

    .course-grid {
        flex: 3 1 600px;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 20px;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .course-item {
        background-color: #fafafa;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .course-item img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin-bottom: 10px;
    }

    .course-item h4 {
        margin-bottom: 10px;
        font-size: 20px;
    }

    .course-item p {
        margin: 5px 0;
    }

    .price-original {
        text-decoration: line-through;
        color: #999;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .container {
            flex-direction: column;
        }

        .filters {
            max-width: 100%;
            margin-bottom: 20px;
        }

        .course-grid {
            grid-template-columns: 1fr;
        }
    }
    .alert{
        padding : 3px 15px;
    }
</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css" />
@endsection
@section('content')
<div class="row" style="padding-top:10px;">
    <div class="col-xl-12">
        <div class="alert alert-light">
            <h4>E-Shop</h4>
        </div> <!-- end card body-->
    </div> <!-- end card -->
</div><!-- end col-->
</div>
<div class="container" style="padding-top:-45px;">

    <aside class="filters">
        <div class="filter-section">
            <h3>Categories</h3>
            <label><input type="checkbox" checked> Courses</label>
            <label><input type="checkbox"> Merchandise</label>
            <label><input type="checkbox"> Daily Lunch</label>
        </div>
        <div class="filter-section">
            <h3>Size</h3>
            <label><input type="checkbox"> XS</label>
            <label><input type="checkbox"> S</label>
            <label><input type="checkbox"> M</label>
            <label><input type="checkbox"> L</label>
            <label><input type="checkbox"> XL</label>
        </div>
        <div class="filter-section">
            <h3>Discount Range</h3>
            <label><input type="checkbox"> 10% and above</label>
            <label><input type="checkbox"> 20% and above</label>
            <label><input type="checkbox"> 30% and above</label>
            <label><input type="checkbox"> More</label>
        </div>
    </aside>

    <main class="course-grid">
        <div class="course-item">
            <img src="maths.jpg" alt="Mathematics">
            <h4>Mathematics</h4>
            <p>4 months Duration</p>
            <p><b>£40</b></p>
        </div>
        <div class="course-item">
            <img src="chemistry.jpg" alt="Chemistry">
            <h4>Chemistry</h4>
            <p>10 months Duration</p>
            <p><span class="price-original"><b>£90</b></span> £80 <red>(10% OFF)</red></p>
        </div>
        <div class="course-item">
            <img src="physics.jpg" alt="Physics">
            <h4>Physics</h4>
            <p>8 months Duration</p>
            <p><b>£70</b></p>
        </div>
        <div class="course-item">
            <img src="biology.jpg" alt="Biology">
            <h4>Biology</h4>
            <p>8 months Duration</p>
            <p><b>£70</b></p>
        </div>
        <div class="course-item">
            <img src="english.jpg" alt="English">
            <h4>English</h4>
            <p>4 months Duration</p>
            <p><span class="price-original"><b>£90</b></span> £80 (10% OFF)</p>
        </div>
    </main>
</div>

    @endsection
    @section('pagescript')
    <script>

        initDataTable('datatable');


    </script>




    @endsection
