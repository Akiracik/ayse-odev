<?php
/**
 * Kurulum Scripti
 * Bu dosyayı sadece ilk kurulum için çalıştırın
 */

// Hata raporlamayı aç
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Dosya Paylaşım Sitesi - Kurulum</h1>\n";
echo "<pre>\n";

// 1. Gerekli dizinleri oluştur
echo "1. Gerekli dizinler oluşturuluyor...\n";

$directories = [
    'uploads',
    'logs',
    'config',
    'classes',
    'assets/css',
    'assets/js',
    'pages',
    'includes',
    'api'
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "   ✓ $dir dizini oluşturuldu\n";
        } else {
            echo "   ✗ $dir dizini oluşturulamadı\n";
        }
    } else {
        echo "   ✓ $dir dizini zaten mevcut\n";
    }
}

// 2. Güvenlik dosyalarını kontrol et
echo "\n2. Güvenlik dosyaları kontrol ediliyor...\n";

$security_files = [
    'uploads/.htaccess',
    'config/.htaccess',
    'logs/.htaccess'
];

foreach ($security_files as $file) {
    $dir = dirname($file);
    if (!file_exists($file)) {
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        
        $content = "Options -Indexes\nDeny from all\n";
        if (file_put_contents($file, $content)) {
            echo "   ✓ $file oluşturuldu\n";
        } else {
            echo "   ✗ $file oluşturulamadı\n";
        }
    } else {
        echo "   ✓ $file mevcut\n";
    }
}

// 3. PHP uzantı kontrolü
echo "\n3. PHP uzantıları kontrol ediliyor...\n";

$required_extensions = [
    'pdo',
    'pdo_mysql',
    'fileinfo',
    'mbstring',
    'openssl'
];

foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "   ✓ $ext uzantısı yüklü\n";
    } else {
        echo "   ✗ $ext uzantısı eksik (gerekli!)\n";
    }
}

// 4. Dizin izinlerini kontrol et
echo "\n4. Dizin izinleri kontrol ediliyor...\n";

$writable_dirs = ['uploads', 'logs'];

foreach ($writable_dirs as $dir) {
    if (is_writable($dir)) {
        echo "   ✓ $dir dizini yazılabilir\n";
    } else {
        echo "   ✗ $dir dizini yazılabilir değil\n";
        // Izni düzeltmeye çalış
        if (chmod($dir, 0755)) {
            echo "   ✓ $dir dizin izni düzeltildi\n";
        }
    }
}

// 5. Veritabanı bağlantı testi
echo "\n5. Veritabanı bağlantısı test ediliyor...\n";

try {
    require_once 'config/database.php';
    $db = new Database();
    $pdo = $db->getConnection();
    
    if ($pdo) {
        echo "   ✓ Veritabanı bağlantısı başarılı\n";
        
        // Tabloları kontrol et
        $tables = ['users', 'groups', 'group_members', 'files', 'group_files'];
        
        foreach ($tables as $table) {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                echo "   ✓ $table tablosu mevcut\n";
            } else {
                echo "   ✗ $table tablosu eksik (database.sql dosyasını çalıştırın)\n";
            }
        }
        
    } else {
        echo "   ✗ Veritabanı bağlantısı başarısız\n";
    }
} catch (Exception $e) {
    echo "   ✗ Veritabanı hatası: " . $e->getMessage() . "\n";
    echo "   → config/database.php dosyasındaki ayarları kontrol edin\n";
    echo "   → MySQL servisi çalışıyor mu?\n";
    echo "   → database.sql dosyasını import ettiniz mi?\n";
}

// 6. Konfigürasyon önerileri
echo "\n6. Güvenlik önerileri:\n";
echo "   • Bu kurulum dosyasını (setup.php) silin\n";
echo "   • config/database.php dosyasında güvenli şifreler kullanın\n";
echo "   • SSL sertifikası kullanın (HTTPS)\n";
echo "   • Düzenli yedekleme yapın\n";
echo "   • Log dosyalarını düzenli kontrol edin\n";

echo "\n=== KURULUM TAMAMLANDI ===\n";
echo "Ana sayfaya gitmek için: index.php\n";
echo "Yönetici hesabı oluşturmak için: pages/register.php\n";

echo "</pre>";
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kurulum Tamamlandı</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        pre { background: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px; }
    </style>
</head>
<body>
    <div>
        <a href="index.php" class="btn">Ana Sayfaya Git</a>
        <a href="pages/register.php" class="btn">Kayıt Ol</a>
    </div>
</body>
</html>