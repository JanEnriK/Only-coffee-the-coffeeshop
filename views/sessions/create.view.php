<?php require base_path('views/partials/head.php') ?>
<?php require base_path('views/partials/nav.php') ?>
<?php
// Assuming you have a session variable set after a successful login
if (isset($_SESSION['signupSuccess']) && $_SESSION['signupSuccess'] === true) {
    echo '
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script>
            window.onload = function() {
                swal({
                    title: "Sign up was successful!",
                    text: "You may now login with your registered credentials",
                    icon: "success",
                    confirmButtonText: "OK"
                });
            };
        </script>
        ';
    // Reset the session variable to prevent the alert from showing up again on page reload
    $_SESSION['signupSuccess'] = false;
}
?>
<script>
    function loginFail() {
        swal("Login was unsuccessful. Please try again.", {
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
                            <h1 class="display-3 text-primary">Come Log in!</h1>
                            <h1 class="text-white">To see amazing products offered</h1>
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
                        <h1 class="text-white mb-4 mt-5">Sign In</h1>
                        <form method="POST" action="/sessions" class="mb-5">
                            <div class="form-group">
                                <input style="color: white;" value="<?= old('email') ?>" name="email" type="email" class="form-control bg-transparent border-primary p-4" placeholder="Email" required="required" />
                            </div>
                            <?php if (isset($errors['email'])) : ?>
                                <script>
                                    loginFail();
                                </script>
                                <p class="text-white text-sm mt-2"><?= $errors['email'] ?></p>
                            <?php endif; ?>
                            <div class="form-group">
                                <input style="color: white;" name="password" type="password" class="form-control bg-transparent border-primary p-4" placeholder="Password" required="required" />
                            </div>
                            <?php if (isset($errors['password'])) : ?>
                                <script>
                                    loginFail();
                                </script>
                                <p class="text-white text-sm mt-2"><?= $errors['password'] ?></p>
                            <?php endif; ?>
                            <div>
                                <button class="btn btn-primary btn-block font-weight-bold py-3" type="submit">Sign In</button>
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

<!-- Contact Javascript File -->
<script src="mail/jqBootstrapValidation.min.js"></script>
<script src="mail/contact.js"></script>

<!-- Template Javascript -->
<script src="js/main.js"></script>

<?php require base_path('views/partials/foot.php') ?>