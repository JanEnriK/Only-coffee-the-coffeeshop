<?php require 'partials/head.php'; ?>
<?php require 'partials/nav.php'; ?>
<style>
    .card-body {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        flex-grow: 1;
    }
</style>
<!-- Page Header Start -->
<div class="container-fluid page-header mb-5 position-relative overlay-bottom">
    <div class="d-flex flex-column align-items-center justify-content-center pt-0 pt-lg-5" style="min-height: 100px">

    </div>
</div>
<!-- Page Header End -->

<?php if (isset($errors['body'])) : ?>
    <h2 style="text-align: center; color: red;"><?= $errors['body'] ?></h2>
<?php endif; ?>

<!-- Reservation Start -->
<div class="container-fluid py-5">
    <div class="container">
        <div class="reservation position-relative overlay-top overlay-bottom">
            <div class="row align-items-center">
                <div class="col-lg-6 my-5 my-lg-0">
                    <div class="p-5">
                        <div class="mb-4">
                            <h1 class="display-3 text-primary">We Listen!</h1>
                            <h1 class="text-white">Tell us About your Experience</h1>
                        </div>
                        <ul class="list-inline text-white m-0">
                            <?php foreach (json_decode($coffee['vision']) as $visions) : ?>
                                <li class="py-2"><i class="fa fa-check text-primary mr-3"></i><?= $visions ?></li>
                            <?php endforeach ?>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="text-center p-5" style="background: rgba(51, 33, 29, .8);">
                        <h1 class="text-white mb-4 mt-5">Submit Feedback</h1>
                        <form class="mb-5" action="/feedback" method="POST">
                            <div class="form-group">
                                <input style="color: white;" name="title" type="text" class="form-control bg-transparent border-primary p-4" placeholder="Title" required="required" />
                            </div>
                            <div class="form-group">
                                <textarea style="color: white; overflow-y: auto; resize: none;" name="feedback_desc" cols="30" rows="10" class="form-control bg-transparent border-primary p-4" placeholder="Enter your feedback. . ." required="required"></textarea>
                            </div>

                            <div>
                                <button class="btn btn-primary btn-block font-weight-bold py-3" type="submit">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Reservation End -->

<!-- Testimonial Start -->
<div class="container-fluid py-5 bg-light">
    <div class="container">
        <div class="text-center">
            <h4 class="text-primary text-uppercase" style="letter-spacing: 5px;">Testimonials</h4>
            <h1 class="display-4">Our Clients Says!</h1>
        </div>
        <div class="owl-carousel testimonial-carousel">

            <?php foreach ($feedback as $fback) : ?>

                <div class="testimonial-item card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="ml-3 text-center">
                            <h4 style="text-transform: capitalize;"><?= $fback['title'] ?></h4>
                            <p class="m-0" style="padding-right:20px">"<?= $fback['feedback_desc'] ?>"</p>
                            <p class="m-0 text-left font-weight-bold" style="text-transform: capitalize;">- <?= $fback['firstname'] ?></p>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>

        </div>
    </div>
</div>
<!-- Testimonial End -->

<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
<script src="lib/easing/easing.min.js"></script>
<script src="lib/waypoints/waypoints.min.js"></script>
<script src="lib/owlcarousel/owl.carousel.min.js"></script>
<script src="lib/tempusdominus/js/moment.min.js"></script>
<script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
<script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

<!-- Contact Javascript File -->
<script src="mail/jqBootstrapValidation.min.js"></script>
<script src="mail/contact.js"></script>

<!-- Template Javascript -->
<script src="js/main.js"></script>

<script>
    $(document).ready(function() {
        $(".owl-carousel").owlCarousel({
            loop: true,
            margin: 10,
            nav: false,
            dots: false,
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 2
                },
                1000: {
                    items: 3
                }
            }
        });
    });
</script>

<?php require 'partials/foot.php'; ?>