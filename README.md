# Kitap Mağazası API

Bu proje, kitapları yönetmek için basit bir API sağlar. Kullanıcılar kitap ekleyebilir, güncelleyebilir, silebilir ve listeleyebilir.

## Gereksinimler

- PHP 7.4+ (veya üstü)
- MySQL 5.7+ (veya üstü)
- Nginx web sunucusu
- Composer (paket yönetimi için)

## Kurulum

### 1. Uygulamayı Klonlama

Aşağıdaki kodu terminalinize yapıştırarak uygulamayı lokalinize klonlayın:

```sh
git clone https://github.com/BCincioglu/bookshelf-api.git
```

### 2. Bağımlılıkları Yükleme

Terminalden uygulamanın olduğu dosyaya erişin ve gerekli paketleri yükleyin:

cd bookshelf-api
composer install

### 3. Veritabanı Yapılandırma

MySQL'de bir veritabanı oluşturun:

CREATE DATABASE bookshelf;

### 4. Veritabanı Tablolarını Oluşturma

MySQL'de oluşturduğunuz veritabanında ‘books’ tablosunu oluşturun:

CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    isbn VARCHAR(13) UNIQUE,
    price DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

### 5. API Anahtarı

.env dosyasındaki API_KEY alanını doldurun.

### 6. Web Sunucunu Çalıştırma (Nginx)

Bilgisayarınızda Nginx'in yüklü olduğundan ve çalıştığından emin olun.

### 7. Uygulamayı Ayağa Kaldırma

Aşağıdaki kodu terminalinize yapıştırarak uygulamayı çalıştırın:

php -S localhost:8000





