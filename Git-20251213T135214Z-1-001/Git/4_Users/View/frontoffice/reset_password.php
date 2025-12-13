<?php
include __DIR__ . '/partials/header.php';
if (isset($_SESSION['user_id'])) { header("Location: " . $frontoffice . "index.php"); exit; }

$errors = $_SESSION['errors'] ?? [];
$success = $_SESSION['success'] ?? null;
$resetEmail = $_SESSION['reset_email'] ?? '';
unset($_SESSION['errors'], $_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - PeaceConnect</title>
    <link rel="icon" type="image/svg+xml" href="<?= $assets ?>favicon.svg">
    <link rel="stylesheet" href="<?= $assets ?>style.css?v=<?php echo filemtime(__DIR__ . "/../assets/style.css"); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        .auth-container { min-height: calc(100vh - 80px); display: flex; align-items: center; justify-content: center; padding: 2rem; }
        .auth-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 24px; padding: 3rem; width: 100%; max-width: 420px; box-shadow: 0 20px 60px var(--shadow); }
        .auth-header { text-align: center; margin-bottom: 2rem; }
        .auth-icon { width: 70px; height: 70px; border-radius: 50%; background: linear-gradient(135deg, #10b981, #059669); display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 1.25rem; }
        .auth-header h1 { font-size: 1.75rem; font-weight: 700; margin-bottom: 0.5rem; }
        .auth-header p { color: var(--text-muted); }
        .password-requirements { font-size: 0.8rem; color: var(--text-muted); margin-top: 0.25rem; }
        .password-requirements.valid { color: #10b981; }
        .password-requirements.invalid { color: #ef4444; }
    </style>
</head>
<body>
    <div class="bg-animation"><span></span><span></span><span></span><span></span><span></span><span></span></div>
    <?php include 'partials/navbar.php'; ?>

    <main class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-icon">🔐</div>
                <h1>Reset Password</h1>
                <p>Enter the code and your new password</p>
            </div>

            <?php if ($success): ?>
            <div class="alert alert-success"><div><?= htmlspecialchars($success) ?></div></div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger"><div><?php foreach($errors as $e) echo htmlspecialchars($e).'<br>'; ?></div></div>
            <?php endif; ?>

            <form id="resetForm" action="../../Controller/userController.php" method="POST" novalidate>
                <input type="hidden" name="action" value="reset_password">
                <input type="hidden" name="email" value="<?= htmlspecialchars($resetEmail) ?>">
                
                <div class="form-group">
                    <label>Reset Code</label>
                    <input type="text" name="code" class="form-control" placeholder="Enter 6-digit code" style="text-align:center;font-size:1.5rem;letter-spacing:0.3rem">
                </div>

                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Min 8 chars, uppercase, lowercase, number">
                    <div id="password-strength" class="password-requirements">
                        Password must contain: 8+ characters, uppercase, lowercase, number
                    </div>
                </div>

                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="password_confirm" class="form-control" placeholder="Repeat password">
                </div>

                <button type="submit" class="btn btn-success" style="width:100%;margin-top:1rem;padding:0.9rem">
                    Reset password
                </button>
            </form>

            <div style="text-align:center;margin-top:1.5rem">
                <a href="forgot_password.php" style="color:var(--text-muted)">Request a new code</a>
            </div>
        </div>
    </main>

    <script src="<?= $assets ?>theme.js"></script>
    <script src="<?= $assets ?>validation.js"></script>
    <script>
        document.querySelector('input[name="code"]').addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '').substring(0, 6);
        });
        
        const passwordInput = document.getElementById('password');
        const strengthDiv = document.getElementById('password-strength');
        
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const hasLength = password.length >= 8;
            const hasUpper = /[A-Z]/.test(password);
            const hasLower = /[a-z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            
            if (hasLength && hasUpper && hasLower && hasNumber) {
                strengthDiv.textContent = 'Password meets all requirements';
                strengthDiv.className = 'password-requirements valid';
            } else {
                let missing = [];
                if (!hasLength) missing.push('8+ chars');
                if (!hasUpper) missing.push('uppercase');
                if (!hasLower) missing.push('lowercase');
                if (!hasNumber) missing.push('number');
                strengthDiv.textContent = 'Missing: ' + missing.join(', ');
                strengthDiv.className = 'password-requirements invalid';
            }
        });

        document.getElementById('resetForm').addEventListener('submit', function(e) {
            e.preventDefault();
            let isValid = true;
            
            const code = document.querySelector('[name="code"]');
            if (code.value.trim().length !== 6) {
                Validator.showError(code, 'Enter the 6-digit code');
                isValid = false;
            } else {
                Validator.showSuccess(code);
            }
            
            const password = document.querySelector('[name="password"]');
            const hasLength = password.value.length >= 8;
            const hasUpper = /[A-Z]/.test(password.value);
            const hasLower = /[a-z]/.test(password.value);
            const hasNumber = /[0-9]/.test(password.value);
            
            if (!(hasLength && hasUpper && hasLower && hasNumber)) {
                Validator.showError(password, 'Password does not meet requirements');
                isValid = false;
            } else {
                Validator.showSuccess(password);
            }
            
            const confirm = document.querySelector('[name="password_confirm"]');
            if (confirm.value !== password.value) {
                Validator.showError(confirm, 'Passwords do not match');
                isValid = false;
            } else {
                Validator.showSuccess(confirm);
            }
            
            if (isValid) this.submit();
        });
    </script>
</body>
</html>


