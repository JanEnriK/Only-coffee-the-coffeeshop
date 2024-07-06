<!-- views/registration/check-email.view.php -->
<?php require base_path('views/partials/head.php') ?>
<br>
<br>
<br>
<br>
<div class="container">
    <h2>Check Your Email</h2>
    <p>A password reset token has been sent to your email. Please check your email and enter the token below to reset your password.</p>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form method="GET" action="/verify-token">
        <div class="form-group">
            <label for="token">Enter Token</label>
            <input type="text" name="token" class="form-control" id="token" placeholder="Enter the token you received" required>
        </div>
        <button type="submit" class="btn btn-primary">Verify Token</button>
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
