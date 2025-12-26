<?php
require_once 'includes/functions.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = "Veuillez remplir tous les champs.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['mot_de_passe'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nom'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_email'] = $user['email'];
            
            // Redirect based on role
            if ($user['role'] === 'admin') {
                header('Location: dashboard_admin.php');
            } elseif ($user['role'] === 'agent') {
                header('Location: dashboard_agent.php');
            } else {
                header('Location: dashboard_user.php'); // specific user dashboard
            }
            exit();
        } else {
            $error = "Email ou mot de passe incorrect.";
        }
    }
}

require_once 'includes/header.php';
?>

<div style="max-width: 400px; margin: 40px auto;">
    <div class="card">
        <h2 style="text-align: center; margin-bottom: 30px; color: var(--primary);">Connexion</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required placeholder="votre@email.com">
            </div>
            
            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="password" required placeholder="••••••••">
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">Se connecter</button>
        </form>
        
        <p style="text-align: center; margin-top: 20px;">
            Pas encore de compte ? <a href="register.php">S'inscrire</a>
        </p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
