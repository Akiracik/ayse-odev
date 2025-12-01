<?php
require_once __DIR__ . '/../config/database.php';

/**
 * Basit File sınıfı - Sadece gerekli dosya işlemleri
 */
class File {
    private $pdo;
    private $uploadDir;
    
    public function __construct() {
        $this->pdo = getDB();
        $this->uploadDir = __DIR__ . '/../uploads/';
        
        // Upload klasörünü oluştur
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }
    
    /**
     * Dosya indirme için dosya bilgilerini getir
     */
    public function getFileById($fileId) {
        try {
            $sql = "SELECT * FROM files WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$fileId]);
            
            $file = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($file && file_exists($this->uploadDir . $file['stored_name'])) {
                return $file;
            }
            
            return null;
        } catch (Exception $e) {
            return null;
        }
    }
    
    /**
     * Dosya indirme sayısını artır
     */
    public function incrementDownloadCount($fileId) {
        try {
            $sql = "UPDATE files SET download_count = download_count + 1 WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$fileId]);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Kullanıcının dosyalarını getir
     */
    public function getUserFiles($userId) {
        try {
            $sql = "SELECT * FROM files WHERE uploaded_by = ? ORDER BY upload_date DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Herkese açık dosyaları getir
     */
    public function getPublicFiles($limit = 20) {
        try {
            $sql = "SELECT f.*, u.username, u.full_name 
                    FROM files f 
                    JOIN users u ON f.uploaded_by = u.id 
                    WHERE f.is_public = 1 
                    ORDER BY f.upload_date DESC 
                    LIMIT ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Dosya sil
     */
    public function deleteFile($fileId, $userId) {
        try {
            // Kullanıcı yetkisi kontrolü
            $sql = "SELECT * FROM files WHERE id = ? AND uploaded_by = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$fileId, $userId]);
            $file = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$file) {
                return ['success' => false, 'message' => 'Dosya bulunamadı.'];
            }
            
            // Fiziksel dosyayı sil
            $filePath = $this->uploadDir . $file['stored_name'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            // Veritabanından sil
            $sql = "DELETE FROM files WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            
            if ($stmt->execute([$fileId])) {
                return ['success' => true, 'message' => 'Dosya başarıyla silindi.'];
            } else {
                return ['success' => false, 'message' => 'Dosya silinemedi.'];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Bir hata oluştu: ' . $e->getMessage()];
        }
    }
}
?>