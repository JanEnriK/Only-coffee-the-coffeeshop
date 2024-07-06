<?php require base_path('views/partials/head.php') ?>
<?php require base_path('views/partials/nav.php') ?>

<script>
    function signUpFail() {
        swal("Sign up was unsuccessful. Please try again.", {
            icon: "error",
        });
    };
</script>

<!-- Page Header Start -->
<div class="container-fluid page-header mb-5 position-relative overlay-bottom">
    <div class="d-flex flex-column align-items-center justify-content-center pt-0 pt-lg-5" style="min-height: 200px">
        <h1 class="display-4 mb-3 mt-0 mt-lg-5 text-white text-uppercase"></h1>
    </div>
</div>
<!-- Page Header End -->

<!-- Reservation Start -->
<div class="container-fluid my-5">
    <div class="container">
        <div class="reservation position-relative overlay-top overlay-bottom">
            <div class="row align-items-center">
                <div class="col-lg-6 my-5 my-lg-0">
                    <div class="p-5">
                        <div class="mb-4">
                            <h1 class="display-3 text-primary">Sign Up and Listen!</h1>
                            <h1 class="text-white">Join us and experiece great service</h1>
                        </div>
                        <ul class="list-inline text-white m-0">
                            <?php foreach (json_decode($coffee['vision']) as $visions) : ?>
                                <li class="py-2"><i class="fa fa-check text-primary mr-3"></i><?= $visions ?></li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="text-center p-5" style="background: rgba(51, 33, 29, .8);">
                        <h1 class="text-white mb-4 mt-5">Sign Up</h1>
                        <form method="POST" action="/register" class="mb-5">
                            <div class="form-group">
                                <input style="color: white;" name="firstname" type="text" class="form-control bg-transparent border-primary p-4" placeholder="First Name" required="required" />
                            </div>
                            <?php if (isset($errors['first_name'])) : ?>
                                <script>
                                    signUpFail();
                                </script>
                                <p class="text-red-500 text-sm mt-2 text-white"><?= $errors['first_name'] ?></p>
                            <?php endif; ?>
                            <div class="form-group">
                                <input style="color: white;" name="lastname" type="text" class="form-control bg-transparent border-primary p-4" placeholder="Last Name" required="required" />
                            </div>
                            <?php if (isset($errors['last_name'])) : ?>
                                <script>
                                    signUpFail();
                                </script>
                                <p class="text-red-500 text-sm mt-2 text-white"><?= $errors['last_name'] ?></p>
                            <?php endif; ?>
                            <div class="form-group">
                                <input style="color: white;" name="email" type="email" class="form-control bg-transparent border-primary p-4" placeholder="Email" required="required" />
                            </div>
                            <?php if (isset($errors['email'])) : ?>
                                <script>
                                    signUpFail();
                                </script>
                                <p class="text-red-500 text-sm mt-2 text-white"><?= $errors['email'] ?></p>
                            <?php endif; ?>
                            <div class="form-group">
                                <input style="color: white;" name="username" type="text" class="form-control bg-transparent border-primary p-4" placeholder="Username" required="required" />
                            </div>
                            <?php if (isset($errors['username'])) : ?>
                                <script>
                                    signUpFail();
                                </script>
                                <p class="text-red-500 text-sm mt-2 text-white"><?= $errors['username'] ?></p>
                            <?php endif; ?>
                            <div class="form-group">
                                <input style="color: white;" name="password" type="password" class="form-control bg-transparent border-primary p-4" placeholder="Password" required="required" />
                            </div>
                            <?php if (isset($errors['password'])) : ?>
                                <script>
                                    signUpFail();
                                </script>
                                <p class="text-red-500 text-sm mt-2 text-white"><?= $errors['password'] ?></p>
                            <?php endif; ?>
                            <div>
                                <button class="btn btn-primary btn-block font-weight-bold py-3" type="submit">Register</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Reservation End -->

<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
<script src="lib/easing/easing.min.js"></script>
<script src="lib/waypoints/waypoints.min.js"></script>
<script src="lib/owlcarousel/owl.carousel.min.js"></script>
<script src="lib/tempusdominus/js/moment.min.js"></script>
<script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
<script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>
<script src="js/main.js"></script>

<?php require base_path('views/partials/foot.php') ?>