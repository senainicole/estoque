<?php
if (!isset($profilePicUrl)) {
    // Defina $userId antes de incluir este arquivo
    $stmt = $pdo->prepare("SELECT profile_pic FROM users WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $profilePicUrl = !empty($user['profile_pic']) ? 'uploads/' . $user['profile_pic'] : 'default-profile.png';
}
?>
<div class="header">
    <div class="logo"><img src="img/logo.png" alt="AUGEBIT"></div>
    <div class="header-right">
        <button class="theme-toggle">Escuro  Claro</button>
        <div class="user-avatar"><img src="<?php echo htmlspecialchars($profilePicUrl); ?>" alt="Avatar"></div>
    </div>
</div>
