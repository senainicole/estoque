<?php
session_start();

if (!isset($_SESSION['email']) || !isset($_SESSION['nome'])) {
    echo "Usuário não encontrado.";
    exit();
}

$nome = $_SESSION['nome'];
$email = $_SESSION['email'];
?>


<?php
$host = 'localhost';
$db = 'augebital';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}

$userId = 2; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $surname = $_POST['surname'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $countryCode = $_POST['country_code'] ?? '+55';

    $stmt = $pdo->prepare("UPDATE users SET Nome = :name, sobrenome = :surname, Telefone = :phone, email = :email, country_code = :country_code WHERE id = :id");
    $stmt->execute([
        ':name' => $name,
        ':surname' => $surname,
        ':phone' => $phone,
        ':email' => $email,
        ':country_code' => $countryCode,
        ':id' => $userId
    ]);

    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileTmpPath = $_FILES['profile_pic']['tmp_name'];
        $fileName = basename($_FILES['profile_pic']['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExt, $allowedExts)) {
            $newFileName = 'profile_' . $userId . '.' . $fileExt;
            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $stmt = $pdo->prepare("UPDATE users SET profile_pic = :profile_pic WHERE id = :id");
                $stmt->execute([':profile_pic' => $newFileName, ':id' => $userId]);
            }
        }
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Usuário não encontrado.");
}

$profilePicUrl = !empty($user['profile_pic']) ? 'uploads/' . $user['profile_pic'] : 'default-profile.png';
$userCountryCode = $user['country_code'] ?? '+55';




?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - AUGEBIT</title>
    <style>
        @font-face {
            font-family: 'Poppins';
            src: url('fonts/Poppins-Regular.ttf');
            font-weight: 400;
            font-style: normal;
        }

        @font-face {
            font-family: 'Poppins';
            src: url('fonts/Poppins-Medium.ttf');
            font-weight: 700;
            font-style: normal;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            color: #333;
            transition: all 0.3s ease;
        }

        body.dark {
            background: #1a202c;
            color: #e2e8f0;
        }

        /* Header Styles */
        .header {
            background: white;
            padding: 12px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border-bottom: 1px solid #e2e8f0;
            height: 72px;
            transition: all 0.3s ease;
        }

        body.dark .header {
            background: #2d3748;
            border-bottom-color: #4a5568;
        }

        .logo {
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .logo img {
            height: 32px;
            width: auto;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .theme-toggle {
            background: #555586;
            border: none;
            border-radius: 25px;
            padding: 0;
            cursor: pointer;
            display: flex;
            align-items: center;
            width: 140px;
            height: 50px;
            position: relative;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .theme-slider {
            background: #FFFFFF;
            border-radius: 25px;
            width: 100px;
            height: 50px;
            position: absolute;
            left: 0;
            top: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .theme-toggle.dark .theme-slider {
            left: 70px;
        }

        .theme-labels {
            display: flex;
            align-items: center;
            width: 100%;
            height: 100%;
            position: relative;
            z-index: 2;
        }

        .theme-label {
            width: 120px;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            gap: 6px;
        }

        .theme-label.escuro {
            color: #555586;
        }

        .theme-label.claro {
            color: #FFFFFF;
        }

        .theme-toggle.dark .theme-label.escuro {
            color: #FFFFFF;
        }

        .theme-toggle.dark .theme-label.claro {
            color: #555586;
        }

        .theme-icon {
            font-size: 16px;
            transition: all 0.3s ease;
            width: 16px;
            height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .theme-icon svg {
            width: 16px;
            height: 16px;
            fill: currentColor;
        }

        .theme-label.escuro .theme-icon {
            opacity: 1;
        }

        .theme-label.claro .theme-icon {
            opacity: 0;
        }

        .theme-toggle.dark .theme-label.escuro .theme-icon {
            opacity: 0;
        }

        .theme-toggle.dark .theme-label.claro .theme-icon {
            opacity: 1;
        }

        .notification-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            transition: background-color 0.2s ease;
            position: relative;
        }

        .notification-btn:hover {
            background: #f1f5f9;
        }

        body.dark .notification-btn:hover {
            background: #4a5568;
        }

        .notification-icon {
            width: 24px;
            height: 24px;
            color: #64748b;
        }

        body.dark .notification-icon {
            color: #cbd5e0;
        }

        .user-avatar {
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .user-avatar:hover {
            transform: scale(1.05);
        }

        .user-avatar img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid #e2e8f0;
            object-fit: cover;
        }

        body.dark .user-avatar img {
            border-color: #4a5568;
        }

        /* Profile Styles */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 20px 20px 20px;
        }

        .profile-section {
            position: relative;
        }

        .profile-title {
            position: absolute;
            top: -15px;
            left: 0;
            background: #f8fafc;
            color: #333;
            padding: 12px 24px 12px 10px;
            border-radius: 0px 0px 30px 0;
            font-size: 18px;
            font-weight: 600;
            z-index: 10;
        }

        body.dark .profile-title {
            background: #1A202C;
            color: #e2e8f0;
        }

        .profile-card {
            background: linear-gradient(135deg,rgb(112, 122, 198) 0%,rgb(101, 75, 162) 100%);
            border-radius: 24px;
            padding: 50px 0 0 0;
            position: relative;
        }

        .profile-content {
            background: white;
            border-radius: 0 0 24px 24px;
            padding: 40px 40px 50px 40px;
            color: #333;
            transition: all 0.3s ease;
        }

        body.dark .profile-content {
            background: #2d3748;
            color: #e2e8f0;
        }

        .profile-main {
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 40px;
            align-items: start;
            margin-bottom: 20px;
        }

        .profile-info-section {
            display: flex;
            align-items: flex-start;
            gap: 30px;
        }

        .profile-pic-container {
            position: relative;
            flex-shrink: 0;
        }

        .profile-pic {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e5e7eb;
        }

        body.dark .profile-pic {
            border-color: #4a5568;
        }

        #notificationOverlay {
    position: fixed;
    top: 50px;
    right: 10px;
    width: 320px;
    max-height: 400px;
    background-color: white;
    border: 1px solid #ccc;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    border-radius: 6px;
    overflow-y: auto;
    display: none;
    z-index: 1000;
}

.notificationItem {
    padding: 10px;
    border-bottom: 1px solid #eee;
    font-size: 14px;
    color: #333;
}

.notificationItem:last-child {
    border-bottom: none;
}

.notification-btn {
    position: relative; /* já deve ter no seu botão */
    cursor: pointer;
}

        .edit-pic-btn {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background-color: #6366f1;
            border: none;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
        }

        .profile-info h2 {
            font-size: 22px;
            margin: 0 0 5px 0;
            color: #1f2937;
            font-weight: 600;
        }

        body.dark .profile-info h2 {
            color: #e2e8f0;
        }

        .profile-info .position {
            color: #6b7280;
            font-size: 14px;
            margin: 0 0 8px 0;
        }

        body.dark .profile-info .position {
            color: #9ca3af;
        }

        .edit-link {
            color: #6366f1;
            cursor: pointer;
            font-size: 14px;
            text-decoration: underline;
        }

        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 25px;
            min-width: 250px;
        }

        .contact-item h4 {
            font-size: 14px;
            color: #374151;
            margin-bottom: 8px;
            font-weight: 600;
        }

        body.dark .contact-item h4 {
            color: #cbd5e0;
        }

        .phone-display {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 16px;
            color: #1f2937;
        }

        body.dark .phone-display {
            color: #e2e8f0;
        }

        .email-display {
            font-size: 16px;
            color: #1f2937;
        }

        body.dark .email-display {
            color: #e2e8f0;
        }

        .form-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        .form-group label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #374151;
            font-size: 14px;
        }

        body.dark .form-group label {
            color: #cbd5e0;
        }

        .form-group input {
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 16px;
            background-color: #f9fafb;
            transition: all 0.3s ease;
        }

        body.dark .form-group input {
            background-color: #4a5568;
            border-color: #718096;
            color: #e2e8f0;
        }

        .form-group input:focus {
            outline: none;
            border-color: #6366f1;
            background-color: white;
        }

        body.dark .form-group input:focus {
            background-color: #2d3748;
        }

        .save-btn {
            background-color: #6366f1;
            color: white;
            border: none;
            padding: 10px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            float: right;
            display: none;
            margin-bottom: 20px;
        }

        .save-btn:hover {
            background-color: #5856eb;
        }

        .save-btn.show {
            display: inline-block;
        }

        .valores {
            border: none;
        }

        .hidden {
            display: none;
        }

        @media (max-width: 1024px) {
            .profile-main {
                grid-template-columns: 1fr;
                text-align: center;
            }
        }

        @media (max-width: 800px) {
            .header {
                padding: 12px 20px;
                gap: 12px;
            }

            .header-right {
                gap: 12px;
            }

            .theme-toggle {
                font-size: 12px;
                width: 160px;
                height: 40px;
            }

            .theme-slider {
                width: 70px;
                height: 40px;
            }

            .theme-label {
                width: 60px;
                font-size: 12px;
            }

            .theme-toggle.dark .theme-slider {
                left: 60px;
            }

            .form-section {
                grid-template-columns: 1fr;
            }
            
            .form-group.full-width {
                grid-column: span 1;
            }

            .profile-info-section {
                flex-direction: column;
                align-items: center;
            }

            .container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Header integrado do primeiro código -->
    <div class="header">
        <div class="logo">
            <img src="img/logo.png" alt="AUGEBIT">
        </div>
        
        <div class="header-right">
            <button class="theme-toggle" onclick="toggleTheme()" id="themeToggle">
                <div class="theme-slider"></div>
                <div class="theme-labels">
                    <div class="theme-label escuro">
                        <span>Escuro</span>
                        <span class="theme-icon">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M21.0672 11.8568L20.4253 11.469L21.0672 11.8568ZM12.1432 2.93276L11.7553 2.29085V2.29085L12.1432 2.93276ZM21.25 12C21.25 17.1086 17.1086 21.25 12 21.25V22.75C17.9371 22.75 22.75 17.9371 22.75 12H21.25ZM12 21.25C6.89137 21.25 2.75 17.1086 2.75 12H1.25C1.25 17.9371 6.06294 22.75 12 22.75V21.25ZM2.75 12C2.75 6.89137 6.89137 2.75 12 2.75V1.25C6.06294 1.25 1.25 6.06294 1.25 12H2.75ZM15.5 14.25C12.3244 14.25 9.75 11.6756 9.75 8.5H8.25C8.25 12.5041 11.4959 15.75 15.5 15.75V14.25ZM20.4253 11.469C19.4172 13.1373 17.5882 14.25 15.5 14.25V15.75C18.1349 15.75 20.4407 14.3439 21.7092 12.2447L20.4253 11.469ZM9.75 8.5C9.75 6.41176 10.8627 4.58282 12.531 3.57467L11.7553 2.29085C9.65609 3.5593 8.25 5.86509 8.25 8.5H9.75ZM12 2.75C11.9115 2.75 11.8077 2.71008 11.7324 2.63168C11.6686 2.56527 11.6538 2.50244 11.6503 2.47703C11.6461 2.44587 11.6482 2.35557 11.7553 2.29085L12.531 3.57467C13.0342 3.27065 13.196 2.71398 13.1368 2.27042C13.0754 1.81116 12.7166 1.25 12 1.25V2.75ZM21.7092 12.2447C21.6445 12.3518 21.5541 12.3539 21.523 12.3497C21.4976 12.3462 21.4347 12.3314 21.3683 12.2676C21.2899 12.1923 21.25 12.0885 21.25 12H22.75C22.75 11.2834 22.1888 10.9246 21.7296 10.8632C21.286 10.804 20.7294 10.9658 20.4253 11.469L21.7092 12.2447Z"/>
                            </svg>
                        </span>
                    </div>
                    <div class="theme-label claro">
                        <span class="theme-icon">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="2"/>
                                <path d="M12 2V4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M12 20V22" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M20 12H22" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M2 12H4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M17.657 6.343L19.071 4.929" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M4.929 19.071L6.343 17.657" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M17.657 17.657L19.071 19.071" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M4.929 4.929L6.343 6.343" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <span>Claro</span>
                    </div>
                </div>
            </button>
            
            <button class="notification-btn" onclick="showNotifications()">
                <svg class="notification-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                </svg>
            </button>
            
            <div class="user-avatar" onclick="showUserMenu()">
                <img src="<?php echo htmlspecialchars($profilePicUrl); ?>" alt="User Avatar">
            </div>
            <div id="notificationOverlay" style="display:none;">
    <?php if (!empty($notificacoes)): ?>
        <?php foreach ($notificacoes as $notif): ?>
            <div class="notificationItem">
                <?= htmlspecialchars($notif['message']) ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="notificationItem">Sem notificações novas.</div>
    <?php endif; ?>
</div>

        </div>
    </div>

    <!-- Conteúdo do perfil original -->
    <div class="container">
        <div class="profile-section">
            <div class="profile-title">Meu perfil</div>
            
            <div class="profile-card">
                <div class="profile-content">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="profile-main">
                            <div class="profile-info-section">
                                <div class="profile-pic-container">
                                    <img src="<?php echo htmlspecialchars($profilePicUrl); ?>" alt="Profile Picture" class="profile-pic" id="profilePicPreview">
                                    <button type="button" class="edit-pic-btn" onclick="document.getElementById('profilePicInput').click();">
                                        +
                                    </button>
                                    <input type="file" id="profilePicInput" name="profile_pic" accept="image/*" class="hidden" onchange="previewImage(this)">
                                </div>
                                
                                <div class="profile-info">
                                    <h2><?php echo htmlspecialchars($user['Nome'] . ' ' . $user['sobrenome']); ?></h2>
                                    <p class="position">Assistente de Logística</p>
                                    <div class="edit-link" id="editToggle">Editar informações</div>
                                </div>
                            </div>

                            <div class="contact-info">
                                <div class="contact-item">
                                    <h4>Número de telefone</h4>
                                    <div class="phone-display">
                                        <span>🇧🇷 +55</span>
                                        <input class="valores input-campo" type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['Telefone']); ?>" required readonly>
                                    </div>
                                </div>

                                <div class="contact-item">
                                    <h4>E-mail</h4>
                                    <input class="valores input-campo" type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required readonly>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="form-group">
                                <label for="name">Nome</label>
                                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['Nome']); ?>" required readonly>
                            </div>

                            <div class="form-group">
                                <label for="surname">Sobrenome</label>
                                <input type="text" id="surname" name="surname" value="<?php echo htmlspecialchars($user['sobrenome']); ?>" required readonly>
                            </div>

                            <div class="form-group full-width">
                                <label for="admission_date">Data de admissão e local</label>
                                <input type="text" id="admission_date" name="admission_date" value="São Paulo, Paulista - 12 de setembro de 2025." readonly>
                            </div>

                            <div class="form-group full-width">
                                <label for="position">Cargo</label>
                                <input type="text" id="position" name="position" value="Assistente de Logística" readonly>
                            </div>
                        </div>

                        <button type="submit" class="save-btn">Salvar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Funcionalidade do tema
        let isDarkMode = false;

        function toggleTheme() {
            isDarkMode = !isDarkMode;
            const themeButton = document.getElementById('themeToggle');
            const body = document.body;
            
            if (isDarkMode) {
                themeButton.classList.add('dark');
                body.classList.add('dark');
            } else {
                themeButton.classList.remove('dark');
                body.classList.remove('dark');
            }
        }
        
function showNotifications() {
    const overlay = document.getElementById('notificationOverlay');
    overlay.style.display = (overlay.style.display === 'block') ? 'none' : 'block';
}

// Fechar overlay se clicar fora
document.addEventListener('click', function(event) {
    const overlay = document.getElementById('notificationOverlay');
    const button = document.querySelector('.notification-btn');

    if (!overlay.contains(event.target) && !button.contains(event.target)) {
        overlay.style.display = 'none';
    }
});


       function showUserMenu() {
    window.location.href = 'perfil.php'; 
}


        // Funcionalidade do perfil original
        let isEditing = false;
        const editableFields = ['name', 'surname', 'phone', 'email'];
        const editToggle = document.getElementById('editToggle');
        const saveBtn = document.querySelector('.save-btn');

        editToggle.addEventListener('click', function() {
            isEditing = !isEditing;
            
            if (isEditing) {
                editableFields.forEach(fieldId => {
                    const field = document.getElementById(fieldId);
                    field.removeAttribute('readonly');
                    field.style.backgroundColor = 'white';
                    field.style.borderColor = '#6366f1';
                });
                
                editToggle.textContent = 'Cancelar';
                saveBtn.classList.add('show');
            } else {
                editableFields.forEach(fieldId => {
                    const field = document.getElementById(fieldId);
                    field.setAttribute('readonly', 'readonly');
                    field.style.backgroundColor = '#f9fafb';
                    field.style.borderColor = '#e5e7eb';
                });
                
                editToggle.textContent = 'Editar informações';
                saveBtn.classList.remove('show');
                location.reload();
            }
        });

        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profilePicPreview').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
                saveBtn.classList.add('show');
            }
        }
    </script>
</body>
</html>