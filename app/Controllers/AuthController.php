<?php
class AuthController extends Controller {
    public function login() {
        if (isLoggedIn()) {
            $this->redirect('');
        }

        $data = ['error' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = $this->model('User');
            
            $email = sanitize($_POST['email']);
            $password = $_POST['password'];

            if (empty($email) || empty($password)) {
                $data['error'] = "Veuillez remplir tous les champs.";
            } else {
                $user = $userModel->findUserByEmail($email);
                
                if ($user && password_verify($password, $user['mot_de_passe'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['nom'];
                    $_SESSION['user_role'] = $user['role'];
                    $_SESSION['user_email'] = $user['email'];
                    
                    // Redirect based on role
                    if ($user['role'] === 'admin') {
                        $this->redirect('admin/dashboard');
                    } elseif ($user['role'] === 'agent') {
                        $this->redirect('agent/dashboard');
                    } else {
                        $this->redirect('user/dashboard');
                    }
                } else {
                    $data['error'] = "Email ou mot de passe incorrect.";
                }
            }
        }

        $this->view('auth/login', $data);
    }

    public function register() {
         if (isLoggedIn()) {
            $this->redirect('');
        }

        $data = ['error' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
             $userModel = $this->model('User');

             // Basic validation
             $nom = sanitize($_POST['nom']);
             $email = sanitize($_POST['email']);
             $password = $_POST['password'];
             $confirm_password = $_POST['confirm_password'];
             
             if (empty($nom) || empty($email) || empty($password)) {
                 $data['error'] = "Tous les champs sont obligatoires.";
             } elseif ($password !== $confirm_password) {
                 $data['error'] = "Les mots de passe ne correspondent pas.";
             } else {
                  // Check if email exists
                  if ($userModel->findUserByEmail($email)) {
                      $data['error'] = "Cet email est déjà utilisé.";
                  } else {
                      // Register
                      $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                      $userData = [
                          'nom' => $nom,
                          'email' => $email,
                          'password' => $hashed_password,
                          'role' => 'usager' // Default role
                      ];
                      
                      if ($userModel->register($userData)) {
                          setFlashMessage('success', 'Inscription réussie. Connectez-vous.');
                          $this->redirect('auth/login');
                      } else {
                          $data['error'] = "Erreur lors de l'inscription.";
                      }
                  }
             }
        }

        $this->view('auth/register', $data);
    }

    public function logout() {
        session_unset();
        session_destroy();
        $this->redirect('auth/login');
    }
}
