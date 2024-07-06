<?php require 'partials/head.php'; ?>
<?php require 'partials/nav.php'; ?>

<?php
// Assuming you have a session variable set after a successful login
if (isset($_SESSION['signupSuccess']) && $_SESSION['signupSuccess'] === true) {
    echo '
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script>
            window.onload = function() {
                swal("Sign up was successful!", {
                    icon: "success",
                });
            };
        </script>
        ';
    // Reset the session variable to prevent the alert from showing up again on page reload
    $_SESSION['signupSuccess'] = false;
} else if (isset($_SESSION['loginSuccess']) && $_SESSION['loginSuccess'] === true) {
    echo '
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script>
            window.onload = function() {
                swal("Login successful!", {
                    icon: "success",
                });
            };
        </script>
        ';
    // Reset the session variable to prevent the alert from showing up again on page reload
    $_SESSION['loginSuccess'] = false;
}
?>

<!-- Carousel Start -->
<div class="container-fluid p-0 mb-5">
    <div id="blog-carousel" class="carousel slide overlay-bottom" data-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img class="w-100" src="img/carousel-1.jpg" alt="Image">
                <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                    <h2 class="text-primary font-weight-medium m-0">We Have Been Serving</h2>
                    <h1 class="display-1 text-white m-0">COFFEE</h1>
                    <h2 class="text-white m-0">SINCE <?= date('Y', strtotime($coffee['date_established'])) ?></h2>
                </div>
            </div>
            <div class="carousel-item">
                <img class="w-100" src="img/carousel-2.jpg" alt="Image">
                <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                    <h2 class="text-primary font-weight-medium m-0">Only Coffee</h2>
                    <h1 class="display-1 text-white m-0">BEST COFEE</h1>
                    <h2 class="text-white m-0">in <?= $coffee['branch'] ?></h2>
                </div>
            </div>
        </div>
        <a class="carousel-control-prev" href="#blog-carousel" data-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </a>
        <a class="carousel-control-next" href="#blog-carousel" data-slide="next">
            <span class="carousel-control-next-icon"></span>
        </a>
    </div>
</div>
<!-- Carousel End -->


<!-- About Start -->
<div class="container-fluid py-5">
    <div class="container">
        <div class="section-title">
            <h4 class="text-primary text-uppercase" style="letter-spacing: 5px;">About Us</h4>
            <h1 class="display-4">Serving Since <?= date('Y', strtotime($coffee['date_established'])) ?></h1>
        </div>
        <div class="row">
            <div class="col-lg-4 py-0 py-lg-5">
                <h1 class="mb-3">Our Tagline</h1>
                <p class="mb-3 text-justify" style="text-indent: 2em;"><?= $coffee['tagline'] ?></p>
                <!-- <a href="" class="btn btn-secondary font-weight-bold py-2 px-4 mt-2 ">Learn More</a> -->
            </div>
            <div class="col-lg-4 py-5 py-lg-0" style="min-height: 500px;">
                <div class="position-relative h-100">
                    <img class="position-absolute w-100 h-100" src="img/about.png" style="object-fit: cover;">
                </div>
            </div>
            <div class="col-lg-4 py-0 py-lg-5">
                <h1 class="mb-3">Our Vision</h1>
                <?php foreach (json_decode($coffee['vision']) as $visions) : ?>
                    <h5 class="mb-3"><i class="fa fa-check text-primary mr-3"></i><?= $visions ?></h5>
                <?php endforeach ?>
                <!-- <a href="" class="btn btn-primary font-weight-bold py-2 px-4 mt-2">Learn More</a> -->
            </div>
        </div>
    </div>
</div>
<!-- About End -->


<!-- Service Start -->
<!-- <div class="container-fluid pt-5">
    <div class="container">
        <div class="section-title">
            <h4 class="text-primary text-uppercase" style="letter-spacing: 5px;">Our Services</h4>
            <h1 class="display-4">Excellence In Every Sip!</h1>
        </div>
        <div class="row">
            <div class="col-lg-6 mb-5">
                <div class="row align-items-center">
                    <div class="col-sm-5">
                        <img class="img-fluid mb-3 mb-sm-0" src="img/service-2.jpg" alt="">
                    </div>
                    <div class="col-sm-7">
                        <h4><i class="fa fa-coffee service-icon"></i>Fresh Coffee Beans</h4>
                        <p class="m-0">Coffee Bean imported in different part of the Philippines</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-5">
                <div class="row align-items-center">
                    <div class="col-sm-5">
                        <img class="img-fluid mb-3 mb-sm-0" src="img/service-3.jpg" alt="">
                    </div>
                    <div class="col-sm-7">
                        <h4><i class="fa fa-award service-icon"></i>Best Quality Coffee</h4>
                        <p class="m-0">Best quality in town placeholder</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> -->
<!-- Service End -->


<!-- Offer Start -->
<!-- <div class="offer container-fluid my-5 py-5 text-center position-relative overlay-top overlay-bottom">
    <div class="container py-5">
        <h1 class="display-3 text-primary mt-3"></h1>
        <h1 class="text-white mb-3">Special Offer Placeholder</h1>
        <h4 class="text-white font-weight-normal mb-4 pb-3">Details offer Placeholder</h4>
        <form class="form-inline justify-content-center mb-4">
            <div class="input-group">
                <input type="text" class="form-control p-4" placeholder="Your Email" style="height: 60px;">
                <div class="input-group-append">
                    <button class="btn btn-primary font-weight-bold px-4" type="submit">Sign Up</button>
                </div>
            </div>
        </form>
    </div>
</div> -->
<!-- Offer End -->


<!-- Menu Start -->
<div class="container-fluid pt-5">
    <div class="container">
        <div class="section-title">
            <h4 class="text-primary text-uppercase" style="letter-spacing: 5px;">Menu & Pricing</h4>
            <h1 class="display-4">Competitive Pricing</h1>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="row align-items-center mb-5">
                    <div class="col-4 col-sm-3">
                        <img style="width: 100%; height: 110px; object-fit: cover;" class="w-100 rounded-circle mb-3 mb-sm-0" src="/uploads/<?= $products[0]['image'] ?>" alt="">
                    </div>
                    <div class="col-8 col-sm-9">
                        <h4><?= $products[0]['product_name'] ?></h4>
                        <p class="m-0"><?= $products[0]['product_description'] ?></p>
                        <a href="/show_product?id=<?= $products[0]['product_id'] ?>" class="pt-2 btn btn-primary">₱<?= $products[0]['price'] ?></a>
                    </div>
                </div>
                <div class="row align-items-center mb-5">
                    <div class="col-4 col-sm-3">
                        <img style="width: 100%; height: 110px; object-fit: cover;" class="w-100 rounded-circle mb-3 mb-sm-0" src="/uploads/<?= $products[1]['image'] ?>" alt="">
                    </div>
                    <div class="col-8 col-sm-9">
                        <h4><?= $products[1]['product_name'] ?></h4>
                        <p class="m-0"><?= $products[1]['product_description'] ?></p>
                        <a href="/show_product?id=<?= $products[1]['product_id'] ?>" class="pt-2 btn btn-primary">₱<?= $products[1]['price'] ?></a>
                    </div>
                </div>
                <div class="row align-items-center mb-5">
                    <div class="col-4 col-sm-3">
                        <img style="width: 100%; height: 110px; object-fit: cover;" class="w-100 rounded-circle mb-3 mb-sm-0" src="/uploads/<?= $products[2]['image'] ?>" alt="">
                    </div>
                    <div class="col-8 col-sm-9">
                        <h4><?= $products[2]['product_name'] ?></h4>
                        <p class="m-0"><?= $products[2]['product_description'] ?></p>
                        <a href="/show_product?id=<?= $products[2]['product_id'] ?>" class="pt-2 btn btn-primary">₱<?= $products[2]['price'] ?></a>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="row align-items-center mb-5">
                    <div class="col-4 col-sm-3">
                        <img style="width: 100%; height: 110px; object-fit: cover;" class="w-100 rounded-circle mb-3 mb-sm-0" src="/uploads/<?= $products[3]['image'] ?>" alt="">
                    </div>
                    <div class="col-8 col-sm-9">
                        <h4><?= $products[3]['product_name'] ?></h4>
                        <p class="m-0"><?= $products[3]['product_description'] ?></p>
                        <a href="/show_product?id=<?= $products[3]['product_id'] ?>" class="pt-2 btn btn-primary">₱<?= $products[3]['price'] ?></a>
                    </div>
                </div>
                <div class="row align-items-center mb-5">
                    <div class="col-4 col-sm-3">
                        <img style="width: 100%; height: 110px; object-fit: cover;" class="w-100 rounded-circle mb-3 mb-sm-0" src="/uploads/<?= $products[4]['image'] ?>" alt="">
                    </div>
                    <div class="col-8 col-sm-9">
                        <h4><?= $products[4]['product_name'] ?></h4>
                        <p class="m-0"><?= $products[4]['product_description'] ?></p>
                        <a href="/show_product?id=<?= $products[4]['product_id'] ?>" class="pt-2 btn btn-primary">₱<?= $products[4]['price'] ?></a>
                    </div>
                </div>
                <div class="row align-items-center mb-5">
                    <div class="col-4 col-sm-3">
                        <img style="width: 100%; height: 110px; object-fit: cover;" class="w-100 rounded-circle mb-3 mb-sm-0" src="/uploads/<?= $products[5]['image'] ?>" alt="">
                    </div>
                    <div class="col-8 col-sm-9">
                        <h4><?= $products[5]['product_name'] ?></h4>
                        <p class="m-0"><?= $products[5]['product_description'] ?></p>
                        <a href="/show_product?id=<?= $products[5]['product_id'] ?>" class="pt-2 btn btn-primary">₱<?= $products[5]['price'] ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Menu End -->


<!-- Reservation Start
<div class="container-fluid my-5">
    <div class="container">
        <div class="reservation position-relative overlay-top overlay-bottom">
            <div class="row align-items-center">
                <div class="col-lg-6 my-5 my-lg-0">
                    <div class="p-5">
                        <div class="mb-4">
                            <h1 class="display-3 text-primary">promo discount</h1>
                            <h1 class="text-white">For Online Reservation</h1>
                        </div>
                        <p class="text-white">online reservation placeholder</p>
                        <ul class="list-inline text-white m-0">
                            <li class="py-2"><i class="fa fa-check text-primary mr-3"></i>Mabilis pa sa Fast</li>
                            <li class="py-2"><i class="fa fa-check text-primary mr-3"></i>Mura pa sa Cheap</li>
                            <li class="py-2"><i class="fa fa-check text-primary mr-3"></i>Mas maasahan pa sa reliable</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="text-center p-5" style="background: rgba(51, 33, 29, .8);">
                        <h1 class="text-white mb-4 mt-5">Book Your Table</h1>
                        <form class="mb-5">
                            <div class="form-group">
                                <input type="text" class="form-control bg-transparent border-primary p-4" placeholder="Name" required="required" />
                            </div>
                            <div class="form-group">
                                <input type="email" class="form-control bg-transparent border-primary p-4" placeholder="Email" required="required" />
                            </div>
                            <div class="form-group">
                                <div class="date" id="date" data-target-input="nearest">
                                    <input type="text" class="form-control bg-transparent border-primary p-4 datetimepicker-input" placeholder="Date" data-target="#date" data-toggle="datetimepicker" />
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="time" id="time" data-target-input="nearest">
                                    <input type="text" class="form-control bg-transparent border-primary p-4 datetimepicker-input" placeholder="Time" data-target="#time" data-toggle="datetimepicker" />
                                </div>
                            </div>
                            <div class="form-group">
                                <select class="custom-select bg-transparent border-primary px-4" style="height: 49px;">
                                    <option selected>Person</option>
                                    <option value="1">Person 1</option>
                                    <option value="2">Person 2</option>
                                    <option value="3">Person 3</option>
                                    <option value="3">Person 4</option>
                                </select>
                            </div>

                            <div>
                                <button class="btn btn-primary btn-block font-weight-bold py-3" type="submit">Book Now</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> -->
<!-- Reservation End -->


<!-- Testimonial Start -->
<div class="container-fluid py-5 bg-light">
    <div class="container">
        <div class="section-title">
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