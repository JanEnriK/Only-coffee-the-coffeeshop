<?php

use Core\App;
use Core\Database;

$db = App::resolve('Core\Database');

$coffee_data = $db->query("SELECT * FROM tblcoffeeshop")->get();

?>
<!-- Footer Start -->
<div class="container-fluid footer text-white mt-5 pt-5 px-0 position-relative overlay-top">
    <div class="row mx-0 pt-5 px-sm-3 px-lg-5 mt-4">
        <div class="col-lg-4 col-md-6 mb-5">
            <h4 class="text-white text-uppercase mb-4" style="letter-spacing: 3px;">Get In Touch</h4>
            <?php foreach ($coffee_data as $data) : ?>
                <p><i class="fa fa-solid fa-store mr-3"></i><?= $data['shopname'] ?> - <?= $data['branch'] ?></p>
                <p><i class="fa fa-map-marker-alt mr-3"></i><?= $data['address'] ?></p>
                <p><i class="fa fa-phone-alt mr-3"></i><?= $data['contact_no'] ?></p>
                <p class="m-0"><i class="fa fa-envelope mr-3"></i><?= $data['email'] ?></p>
            <?php endforeach; ?>
        </div>
        <div class="col-lg-4 col-md-6 mb-5">
            <h4 class="text-white text-uppercase mb-4" style="letter-spacing: 3px;">Follow Us</h4>
            <p>Here are our links to our specific pages</p>
            <div class="d-flex justify-content-start">
                <a class="btn btn-lg btn-outline-light btn-lg-square mr-2" href=""><i class="fab fa-twitter"></i></a>
                <a class="btn btn-lg btn-outline-light btn-lg-square mr-2" href=""><i class="fab fa-facebook-f"></i></a>
                <a class="btn btn-lg btn-outline-light btn-lg-square mr-2" href=""><i class="fab fa-linkedin-in"></i></a>
                <a class="btn btn-lg btn-outline-light btn-lg-square" href=""><i class="fab fa-instagram"></i></a>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mb-5">
            <h4 class="text-white text-uppercase mb-4" style="letter-spacing: 3px;">Open Hours</h4>
            <div>
                <h6 class="text-white text-uppercase">Monday - Friday</h6>
                <p>8.00 AM - 8.00 PM</p>
                <h6 class="text-white text-uppercase">Saturday - Sunday</h6>
                <p>2.00 PM - 8.00 PM</p>
            </div>
        </div>
        <!-- <div class="col-lg-3 col-md-6 mb-5">
                <h4 class="text-white text-uppercase mb-4" style="letter-spacing: 3px;">Newsletter</h4>
                <p>Enter your email address here to be subscribed</p>
                <div class="w-100">
                    <div class="input-group">
                        <input type="text" class="form-control border-light" style="padding: 25px;" placeholder="Your Email">
                        <div class="input-group-append">
                            <button class="btn btn-primary font-weight-bold px-3">Sign Up</button>
                        </div>
                    </div>
                </div>
            </div> -->
    </div>

</div>
<!-- Footer End -->

<!-- Back to Top -->
<a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="fa fa-angle-double-up"></i></a>

</body>

</html>