<?php
session_start();
require_once '../config/database.php';
require_once '../classes/User.php';
require_once '../classes/Group.php';
require_once '../classes/File.php';

// Giriş kontrolü
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: groups.php');
    exit;
}

$groupId = (int)$_GET['id'];
$groupHandler = new Group();
$fileHandler = new File();

// Grup bilgilerini getir
$group = $groupHandler->getGroupInfo($groupId);
if (!$group) {
    header('Location: groups.php');
    exit;
}

// Üyelik kontrolü
if (!$groupHandler->isMember($groupId, $_SESSION['user_id'])) {
    echo '<h1>Erişim Engellendi</h1>';
    echo '<p>Bu grubun üyesi değilsiniz.</p>';
    echo '<a href="groups.php">Gruplarıma dön</a>';
    exit;
}

$error = '';
$success = '';

// Dosya paylaşma işlemi
if ($_POST && isset($_POST['share_file'])) {
    $fileId = (int)$_POST['file_id'];
    $message = trim($_POST['message'] ?? '');
    
    $result = $groupHandler->shareFileToGroup($groupId, $fileId, $_SESSION['user_id'], $message);
    
    if ($result['success']) {
        $success = $result['message'];
    } else {
        $error = $result['message'];
    }
}

// Grup üyeleri ve dosyaları getir
$members = $groupHandler->getGroupMembers($groupId);
$groupFiles = $groupHandler->getGroupFiles($groupId);
$userFiles = $fileHandler->getUserFiles($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($group['group_name']); ?> - Dosya Paylaşım Sitesi</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="group-detail">
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <div class="group-header-detail">
            <div class="group-info">
                <h1><?php echo htmlspecialchars($group['group_name']); ?></h1>
                <?php if ($group['description']): ?>
                    <p class="group-description"><?php echo htmlspecialchars($group['description']); ?></p>
                <?php endif; ?>
                <p class="group-owner">👑 Sahip: <?php echo htmlspecialchars($group['owner_name']); ?></p>
            </div>
            
            <div class="group-actions">
                <a href="groups.php" class="btn btn-secondary">← Gruplarıma Dön</a>
                <?php if ($group['owner_id'] == $_SESSION['user_id']): ?>
                    <a href="group_manage.php?id=<?php echo $groupId; ?>" class="btn btn-primary">⚙️ Grubu Yönet</a>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- İstatistikler -->
        <div class="group-stats">
            <div class="stat-card">
                <span class="stat-number"><?php echo count($members); ?></span>
                <span class="stat-label">Üyeler</span>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?php echo count($groupFiles); ?></span>
                <span class="stat-label">Dosyalar</span>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?php echo array_sum(array_column($groupFiles, 'download_count')); ?></span>
                <span class="stat-label">İndirmeler</span>
            </div>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <div class="group-content">
            <!-- Üyeler -->
            <div class="members-section">
                <h3>Grup Üyeleri</h3>
                <div class="members-list">
                    <?php foreach ($members as $member): ?>
                        <div class="member-tag">
                            <?php echo htmlspecialchars($member['full_name']); ?>
                            <?php if ($member['is_owner']): ?>
                                <span class="owner-label">👑</span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="right-column">
                <!-- Dosya paylaşma -->
                <?php if (!empty($userFiles)): ?>
                    <div class="share-file-section">
                        <h3>Dosya Paylaş</h3>
                        <button class="btn btn-primary" onclick="toggleShareForm()">🚀 Yeni Dosya Paylaş</button>
                        
                        <div id="share-file-form" class="share-form-container" style="display: none;">
                            <form method="POST">
                                <div class="form-group">
                                    <label for="file_id">📎 Paylaşılacak Dosya:</label>
                                    <select id="file_id" name="file_id" required>
                                        <option value="">Dosya seçin...</option>
                                        <?php foreach ($userFiles as $file): ?>
                                            <option value="<?php echo $file['id']; ?>">
                                                📄 <?php echo htmlspecialchars($file['original_name']); ?>
                                                (<?php echo formatFileSize($file['file_size']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="message">💬 Mesaj (opsiyonel):</label>
                                    <textarea id="message" name="message" rows="3" placeholder="Bu dosya hakkında bir mesaj yazın..."></textarea>
                                </div>
                                
                                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                                    <button type="button" class="btn btn-secondary" onclick="toggleShareForm()">❌ İptal</button>
                                    <button type="submit" name="share_file" class="btn btn-primary">✅ Paylaş</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Grup dosyaları -->
                <div class="group-files-section">
                    <h3>Grup Dosyaları</h3>
                    
                    <?php if (empty($groupFiles)): ?>
                        <div class="empty-files-state">
                            <p>Bu grupta henüz dosya paylaşılmamış.</p>
                            <p>İlk dosyayı paylaşarak başlayın!</p>
                        </div>
                    <?php else: ?>
                        <div class="files-list">
                            <?php foreach ($groupFiles as $file): ?>
                                <div class="file-item">
                                    <div class="file-info">
                                        <h4><?php echo htmlspecialchars($file['original_name']); ?></h4>
                                        <p>👤 Paylaşan: <strong><?php echo htmlspecialchars($file['shared_by_name']); ?></strong></p>
                                        <p>📅 Paylaşım tarihi: <?php echo date('d.m.Y H:i', strtotime($file['shared_at'])); ?></p>
                                        <p>📊 Boyut: <?php echo formatFileSize($file['file_size']); ?></p>
                                        <p>⬇️ İndirme: <?php echo $file['download_count']; ?> kez</p>
                                        <?php if ($file['message']): ?>
                                            <div class="file-message">
                                                <strong>💬 Mesaj:</strong> <?php echo htmlspecialchars($file['message']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="file-actions">
                                        <a href="download.php?id=<?php echo $file['file_id']; ?>" class="btn btn-sm btn-primary">⬇️ İndir</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/main.js"></script>
    <script>
        function toggleShareForm() {
            const form = document.getElementById('share-file-form');
            if (form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
                // Formu temizle
                document.getElementById('file_id').value = '';
                document.getElementById('message').value = '';
            }
        }
    </script>
</body>
</html>

<?php
// Yardımcı fonksiyonlar
function formatFileSize($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}
?>