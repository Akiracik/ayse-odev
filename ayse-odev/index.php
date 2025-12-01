<?php
session_start();
require_once 'config/database.php';
require_once 'classes/User.php';

// Kullanıcı giriş yapmış mı kontrol et
$user = null;
if (isset($_SESSION['user_id'])) {
    $userObj = new User();
    $user = $userObj->getUserById($_SESSION['user_id']);
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FileSync - Modern Bulut Depolama</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <?php if ($user): ?>
            <!-- Giriş yapmış kullanıcı için ana sayfa -->
            <div class="welcome-section">
                <h1>Merhaba, <?php echo htmlspecialchars($user['full_name']); ?>!</h1>
                <p>Dosyalarınızı kolayca yönetin ve paylaşın</p>
            </div>
            
            <div class="main-actions">
                <div class="action-card">
                    <div class="card-icon">📤</div>
                    <h3>Dosya Yükle</h3>
                    <p>Dosyalarınızı hızlı ve güvenli şekilde yükleyin</p>
                    <a href="pages/upload.php" class="btn btn-primary">Yükle</a>
                </div>
                
                <div class="action-card">
                    <div class="card-icon">👥</div>
                    <h3>Takımlar</h3>
                    <p>Ekibinizle kolaborasyon yapın</p>
                    <a href="pages/groups.php" class="btn btn-secondary">Takımlarım</a>
                </div>
            </div>
            
            <div class="recent-files">
                <h2>Son Dosyalar</h2>
                <?php include 'includes/recent_files.php'; ?>
            </div>
            
        <?php else: ?>
            <!-- Giriş yapmamış kullanıcı için ana sayfa -->
            <div class="hero-section">
                <h1>FileSync</h1>
                <p class="hero-subtitle">Modern, güvenli ve kolay kullanımlı bulut depolama çözümü</p>
                <p>Dosyalarınızı her yerden erişilebilir şekilde saklayın, organize edin ve paylaşın.</p>
                <div class="hero-actions">
                    <a href="pages/login.php" class="btn btn-primary">Giriş Yap</a>
                    <a href="pages/register.php" class="btn btn-secondary">Ücretsiz Başla</a>
                </div>
            </div>
            
            <div class="features">
                <div class="feature">
                    <div class="feature-icon">🔒</div>
                    <h3>Güvenli Saklama</h3>
                    <p>Dosyalarınız 256-bit şifreleme ile korunur ve güvenli sunucularda saklanır</p>
                </div>
                
                <div class="feature">
                    <div class="feature-icon">🚀</div>
                    <h3>Hızlı Senkronizasyon</h3>
                    <p>Dosyalarınız tüm cihazlarınızda anında senkronize edilir</p>
                </div>
                
                <div class="feature">
                    <div class="feature-icon">🤝</div>
                    <h3>Kolay Paylaşım</h3>
                    <p>Takımınızla kolayca işbirliği yapın ve dosyalarınızı güvenle paylaşın</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Link panoya kopyalandı!');
            }).catch(function(err) {
                console.error('Kopyalama hatası: ', err);
            });
        }
    </script>
</body>
</html>