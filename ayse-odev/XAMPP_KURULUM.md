## 📋 XAMPP Kurulum Adımları

### 1. XAMPP Servisleri Başlatma
1. XAMPP Control Panel'i açın
2. **Apache** ve **MySQL** servislerini başlatın
3. Her ikisinin de yeşil "Running" durumunda olduğundan emin olun

### 2. Veritabanı Oluşturma

#### phpMyAdmin ile Veritabanı Oluşturma:
1. Web tarayıcınızda `http://localhost/phpmyadmin` adresine gidin
2. Sol üstteki "New" (Yeni) butonuna tıklayın
3. Database name (Veritabanı adı) alanına `file_sharing_site` yazın
4. Collation kısmından `utf8mb4_unicode_ci` seçin
5. "Create" (Oluştur) butonuna tıklayın


### 3. Tabloları İmport Etme

#### SQL Sekmesi ile
1. `file_sharing_site` veritabanını seçin
2. "SQL" sekmesine tıklayın
3. `database.sql` dosyasının içeriğini kopyalayıp yapıştırın
4. "Go" butonuna tıklayın

### 4. Dosyaları Kontrol Etme
Aşağıdaki dosyaların `C:\xampp\htdocs\` klasöründe olduğundan emin olun:
```
htdocs/
├── database.sql
├── index.php
├── setup.php
├── assets/
├── classes/
├── config/
├── includes/
├── pages/
├── uploads/
└── logs/
```

### 5. Konfigürasyonu Test Etme
1. Web tarayıcınızda `http://localhost/setup.php` adresine gidin
2. Kurulum kontrollerini çalıştırın
3. Tüm checkler ✓ işareti alana kadar devam edin

### 6. Siteyi Kullanmaya Başlama
1. `http://localhost/index.php` adresine gidin
2. "Üye Ol" ile yeni hesap oluşturun
3. Giriş yapın ve siteyi kullanmaya başlayın