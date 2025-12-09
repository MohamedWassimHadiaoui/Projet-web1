<?php
class AuthController {
    public static function showLoginForm() {
        require __DIR__ . '/../View/BackOffice/login.php';
        exit;
    }

    public static function handleLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=admin_login');
            exit;
        }

        $password = $_POST['admin_password'];

        // Authentification simplifiée: mot de passe fixe "admin123"
        $expectedHash = hash('sha256', 'admin123');
        if (hash('sha256', $password) === $expectedHash) {
            $_SESSION['user'] = [
                'id' => 1,
                'name' => 'Admin démo',
                'email' => 'admin@peaceconnect.tn',
                'role' => 'admin'
            ];
            header('Location: index.php?action=dashboard&section=backoffice');
            exit;
        }

        // Mot de passe incorrect
        $_SESSION['auth_error'] = 'Mot de passe administrateur incorrect.';
        header('Location: index.php?action=admin_login');
        exit;
    }

    public static function logout() {
        unset($_SESSION['user']);
        session_regenerate_id(true);
        header('Location: index.php');
        exit;
    }
}
?>