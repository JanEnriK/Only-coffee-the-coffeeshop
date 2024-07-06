<?php require base_path('views/partials/head.php') ?>


<br>
<br>
<br>
<br>
<div class="container">
    <h2>Reset Password</h2>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form method="POST" action="/reset-password">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
        <div class="form-group">
            <label for="password">New Password</label>
            <input type="password" name="password" class="form-control" id="password" placeholder="Enter new password" required>
        </div>
        <div class="form-group">
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="Confirm new password" required>
        </div>
        <button type="submit" class="btn btn-primary">Reset Password</button>
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
