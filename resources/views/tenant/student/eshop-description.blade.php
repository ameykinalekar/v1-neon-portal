@extends('layouts.default')
@section('title', 'NTP Support')
@section('pagecss')
<style>
    .alert{
        padding : 3px 15px;
    }
    .course-container {
        display: flex;
        flex-wrap: wrap;
        padding: 20px;
        max-width: 1200px;
        margin: auto;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .course-image {
        flex: 1 1 300px;
        text-align: center;
        padding: 20px;
    }

    .course-image img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
    }

    .course-details {
        flex: 2 1 600px;
        padding: 20px;
    }

    .course-details h1 {
        margin-bottom: 10px;
        font-size: 24px;
    }

    .course-duration {
        margin-bottom: 10px;
        font-size: 18px;
        color: #666;
    }

    .course-price {
        margin-bottom: 20px;
        font-size: 20px;
        color: #333;
    }

    .price-original {
        text-decoration: line-through;
        color: #999;
    }

    .discount {
        color: #e74c3c;
    }

    .course-description, .learning-activities {
        margin-bottom: 20px;
    }

    .course-description h3, .learning-activities h3 {
        margin-bottom: 10px;
        font-size: 18px;
        border-bottom: 2px solid #ddd;
        padding-bottom: 5px;
    }

    .learning-activities ul {
        list-style-type: disc;
        padding-left: 20px;
    }

    .course-actions {
        margin-top: 20px;
    }

    .course-actions .btn {
        padding: 10px 20px;
        font-size: 16px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-right: 10px;
    }

    .course-actions .btn.add-to-cart {
        background-color: #27ae60;
        color: #fff;
    }

    .course-actions .btn.buy-now {
        background-color: #2980b9;
        color: #fff;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .course-container {
            flex-direction: column;
        }

        .course-image {
            max-width: 100%;
            padding: 10px;
        }

        .course-details {
            padding: 10px;
        }

        .course-actions {
            text-align: center;
        }

        .course-actions .btn {
            width: 100%;
            margin-bottom: 10px;
        }
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
<div class="course-container">
    <div class="course-image">
        <img src="chemistry.jpg" alt="Chemistry" style="width: 200px;height:150px;">
        <h3>Chemistry</h3>
        <p class="course-duration">10 months Duration</p>
        <p class="course-price"><span class="price-original">£100</span> £90 <span class="discount">(10% OFF)</span></p>

    </div>
    <div class="course-details">
       <div class="course-description">
            <h3>Description</h3>
            <p>Chemistry course is ideal for learners who want to study chemistry or a wide variety of related subjects at university or to follow a career in science.</p>
        </div>
        <div class="learning-activities">
            <h3>Monthly Learning Activities</h3>
            <ul>
                <li>1 hour x 4 of live Online Zoom lesson</li>
                <li>4 mandatory assignment Paper solved</li>
                <li>4 weekly Quiz</li>
                <li>Handouts, Teacher’s presentation & mind maps</li>
                <li>Video recordings of the On-line classes in Library</li>
                <li>Defined learning objectives & planner</li>
                <li>Leader boards & Real Time Dashboard</li>
                <li>Dashboard powered by advanced analytics for parents & students</li>
                <li>Credit/Debit card monthly payments</li>
                <li>Easy cancelation anytime by providing One month's notice</li>
            </ul>
        </div>
        <div class="col-md-12">

        <div class="row">


            <div class="col-md-4">
            <button class="btn w-100" style="background: #5BC2B9;font-weight: normal;color:#fff;border-radius: 5px;">Add to cart</button>
            </div>
            <div class="col-md-4">
                <button class="btn w-100" style="background: #5BC2B9;font-weight: normal;color:#fff;border-radius: 5px;">Buy Now</button>
            </div>
            </div>
    </div>
</div>
@endsection
@section('pagescript')
<script>

    initDataTable('datatable');


</script>




@endsection
