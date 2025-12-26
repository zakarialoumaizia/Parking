<?php
require_once 'includes/functions.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = sanitize($_POST['nom']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($nom) || empty($email) || empty($password)) {
        $error = "Tous les champs sont obligatoires.";
    } elseif ($password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Cet email est déjà utilisé.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'usager'; // Default role
            
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)");
            
            if ($stmt->execute([$nom, $email, $hashed_password, $role])) {
                setFlashMessage('success', "Compte créé avec succès ! Connectez-vous.");
                header('Location: login.php');
                exit();
            } else {
                $error = "Erreur lors de l'inscription.";
            }
        }
    }
}

require_once 'includes/header.php';
?>

<div style="max-width: 450px; margin: 40px auto;">
    <div class="card">
        <h2 style="text-align: center; margin-bottom: 30px; color: var(--primary);">Inscription</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Nom complet</label>
                <input type="text" name="nom" required placeholder="Jean Dupont">
            </div>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required placeholder="votre@email.com">
            </div>
            
            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="password" required placeholder="••••••••">
            </div>

            <div class="form-group">
                <label>Confirmer le mot de passe</label>
                <input type="password" name="confirm_password" required placeholder="••••••••">
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">S'inscrire</button>
        </form>
        
        <p style="text-align: center; margin-top: 20px;">
            Déjà un compte ? <a href="login.php">Se connecter</a>
        </p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
