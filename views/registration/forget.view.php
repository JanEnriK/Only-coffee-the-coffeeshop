<?php require base_path('views/partials/head.php') ?>

<br>
<br>
<br>
<br>
<div class="container">
    <h2>Forgot Password</h2>
    <form method="POST" action="/forgot-password">
        <div class="form-group">
            <label for="email">Email address</label>
            <input type="email" name="email" class="form-control" id="email" placeholder="Enter your email" required>
        </div>
        <button type="submit" class="btn btn-primary">Send Password Reset Link</button>
    </form>
</div>

<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<?php require base_path('views/partials/foot.php') ?>
