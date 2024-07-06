<?php

use Core\App;
use Core\Database;

$db = App::resolve('Core\Database');

$coffee_data = $db->query("SELECT * FROM tblcoffeeshop")->find();

?>
<!-- Footer Start -->
<div class="container-fluid footer text-white mt-0 pt-5 px-0 position-relative overlay-top">
    <div class="row mx-0 pt-3 px-sm-3 px-lg-5 mt-0">

        <div class="col-lg-3 col-md-6 mb-5">
            <h4 class="text-white text-uppercase mb-4" style="letter-spacing: 3px;">Our Location</h4>
            <p><i class="fa fa-solid fa-store mr-3"></i><?= $coffee_data['shopname'] ?> - <?= $coffee_data['branch'] ?></p>
            <p><i class="fa fa-map-marker-alt mr-3"></i><?= $coffee_data['address'] ?></p>

        </div>
        <div class="col-lg-3 col-md-6 mb-5">
            <h4 class="text-white text-uppercase mb-4" style="letter-spacing: 3px;">Get In Touch!</h4>
            <a href="tel:<?= $coffee_data['contact_no'] ?>" class="text-white">
                <p><i class="fa fa-phone-alt mr-3"></i><?= $coffee_data['contact_no'] ?></p>
            </a>
            <a href="mailto:<?= $coffee_data['email'] ?>" class="text-white">
                <p><i class="fa fa-envelope mr-3"></i><?= $coffee_data['email'] ?></p>
            </a>

        </div>
        <div class="col-lg-3 col-md-6 mb-5">
            <h4 class="text-white text-uppercase mb-4" style="letter-spacing: 3px;">Operating Hours</h4>
            <div>
                <h6 class="text-white text-uppercase">Monday - Friday</h6>
                <p><?= date('h:ia', strtotime($coffee_data['weekday_start_time'])) ?> - <?= date('h:ia', strtotime($coffee_data['weekday_end_time'])) ?></p>

            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-5">
            <h4 class="text-white text-uppercase mb-4" style="letter-spacing: 3px;">&nbsp;</h4>
            <div>

                <h6 class="text-white text-uppercase">Saturday - Sunday</h6>
                <p><?= date('h:ia', strtotime($coffee_data['weekend_start_time'])) ?> - <?= date('h:ia', strtotime($coffee_data['weekend_end_time'])) ?></p>
            </div>
        </div>
    </div>

</div>
<!-- Footer End -->

<!-- Back to Top -->
<a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="fa fa-angle-double-up"></i></a>

</body>

</html>