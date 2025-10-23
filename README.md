# 🚀 PHP MVC Framework

Modern, hafif ve güvenli bir PHP MVC Framework.

## ✨ Özellikler

- ✅ **Modern PHP** - PHP 8.0+ ile yazılmış
- 🛣️ **Gelişmiş Routing** - Dinamik URL parametreleri desteği
- 🔒 **Güvenlik** - XSS, SQL Injection, Session Hijacking koruması
- 📦 **Modüler Yapı** - Temiz MVC mimarisi
- 🎨 **View System** - Kolay view yönetimi
- 💾 **PDO Database** - Güvenli veritabanı işlemleri
- 🔐 **Session & Cookie** - Gelişmiş oturum yönetimi
- 📝 **Error Handling** - Detaylı hata yönetimi
- 🖼️ **File Upload** - Güvenli dosya yükleme
- 🔑 **Encryption** - Custom encryption sistemi
- 📱 **Device Detection** - Cihaz ve tarayıcı algılama

## 📋 Gereksinimler

- PHP 8.0 veya üstü
- MySQL 5.7 veya üstü
- Apache/Nginx web sunucusu
- mod_rewrite etkin

## 🔧 Kurulum

### 1. Projeyi İndirin

```bash
git clone https://github.com/AslanSoftCom/php-mvc-framework.git
cd php-mvc-framework
```

### 2. Veritabanı Yapılandırması

`app/config/database.php` dosyasını düzenleyin:

```php
return [
    'database' => [
        'host'     => 'localhost',
        'dbname'   => 'veritabani_adi',
        'user'     => 'kullanici_adi',
        'password' => 'sifre'
    ]
];
```

### 3. Apache Yapılandırması

`.htaccess` dosyası zaten mevcut. `mod_rewrite` modülünün aktif olduğundan emin olun.

### 4. Dizin İzinleri

```bash
chmod -R 755 .
```

## 🚀 Hızlı Başlangıç

### Rota Tanımlama

`app/config/router.php`:

```php
Router::add('users', 'GET', '/', 'HomeController::index');
Router::add('users', 'GET', '/user/{id}', 'UserController::show');
Router::add('users', 'POST', '/user/create', 'UserController::create');
Router::dispatch();
```

### Controller Oluşturma

`app/users/controllers/UserController.php`:

```php
<?php

class UserController extends Controller {
    
    public function index() {
        // Tüm kullanıcıları getir
        $users = $this->db->query("SELECT * FROM users");
        
        $this->view->render('users', 'user.index', [
            'users' => $users
        ]);
    }
    
    public function show($id) {
        // Tek kullanıcı getir
        $user = $this->db->row(
            "SELECT * FROM users WHERE id = :id",
            ['id' => $id]
        );
        
        $this->view->render('users', 'user.show', [
            'user' => $user
        ]);
    }
    
    public function create() {
        // POST verilerini al
        $name = $this->request->post('name');
        $email = $this->request->post('email');
        
        // Veritabanına kaydet
        $this->db->query(
            "INSERT INTO users (name, email) VALUES (:name, :email)",
            ['name' => $name, 'email' => $email]
        );
        
        // JSON yanıt döndür
        $this->helpers->json([
            'success' => true,
            'message' => 'Kullanıcı oluşturuldu'
        ]);
    }
}
```

### Model Oluşturma

`app/models/UserModel.php`:

```php
<?php

class UserModel {
    
    public $db;
    
    public function getAllUsers() {
        return $this->db->query("SELECT * FROM users");
    }
    
    public function getUserById($id) {
        return $this->db->row(
            "SELECT * FROM users WHERE id = :id",
            ['id' => $id]
        );
    }
}
```

### View Oluşturma

`app/users/views/user/index.php`:

```php
<h1>Kullanıcılar</h1>

<?php foreach ($users as $user): ?>
    <div class="user-card">
        <h3><?= htmlspecialchars($user['name']) ?></h3>
        <p><?= htmlspecialchars($user['email']) ?></p>
    </div>
<?php endforeach; ?>
```

## 📚 Dokümantasyon

### Core Sınıflar

#### Database (DB)
```php
// SELECT sorgusu
$users = $this->db->query("SELECT * FROM users");

// Tek satır
$user = $this->db->row("SELECT * FROM users WHERE id = :id", ['id' => 1]);

// Tek değer
$count = $this->db->single("SELECT COUNT(*) FROM users");

// INSERT/UPDATE/DELETE
$affected = $this->db->query(
    "INSERT INTO users (name) VALUES (:name)",
    ['name' => 'John']
);

// Son eklenen ID
$lastId = $this->db->lastInsertId();
```

#### Request
```php
// GET parametresi
$search = $this->request->get('search');

// POST parametresi
$email = $this->request->post('email');

// HTTP metodu
$method = $this->request->method(); // GET, POST, etc.

// Dosya yükleme
$file = $this->request->getFile('avatar');
```

#### Session
```php
// Session set
$this->session->set('user_id', 123);

// Session get
$userId = $this->session->get('user_id');

// Session delete
$this->session->delete('user_id');
```

#### Cookie
```php
// Cookie set (4 saat)
$this->cookie->set('remember_me', 'value', 14400);

// Cookie get
$value = $this->cookie->get('remember_me');

// Cookie delete
$this->cookie->delete('remember_me');
```

#### Helpers
```php
// Yönlendirme
$this->helpers->redirectToUrl('/dashboard');

// JSON yanıt
$this->helpers->json(['success' => true]);

// 404 sayfası
$this->helpers->notFound();
```

## 🔒 Güvenlik Özellikleri

- ✅ SQL Injection koruması (Prepared Statements)
- ✅ XSS koruması (htmlspecialchars)
- ✅ Session Hijacking koruması (Device Fingerprinting)
- ✅ CSRF koruması için hazır yapı
- ✅ Path Traversal koruması
- ✅ Güvenli dosya yükleme (MIME type kontrolü)
- ✅ Güvenli şifreleme sistemi

## 📁 Dizin Yapısı

```
project/
├── app/
│   ├── config/
│   │   ├── database.php
│   │   └── router.php
│   ├── models/
│   │   └── UserModel.php
│   └── users/
│       ├── controllers/
│       │   └── HomeController.php
│       └── views/
│           ├── home/
│           └── template/
├── assets/
│   ├── css/
│   └── js/
├── core/
│   ├── Controller.php
│   ├── Database.php
│   ├── Router.php
│   ├── View.php
│   ├── Request.php
│   ├── Session.php
│   ├── Cookie.php
│   ├── ErrorHandler.php
│   ├── Encryptor.php
│   ├── Fingerprint.php
│   ├── Generate.php
│   ├── Helpers.php
│   └── Upload.php
├── .htaccess
├── .gitignore
├── index.php
└── README.md
```

## 🤝 Katkıda Bulunma

1. Bu projeyi fork edin
2. Feature branch oluşturun (`git checkout -b feature/amazing-feature`)
3. Değişikliklerinizi commit edin (`git commit -m 'feat: Add amazing feature'`)
4. Branch'inizi push edin (`git push origin feature/amazing-feature`)
5. Pull Request oluşturun

## 📝 Lisans

Bu proje MIT lisansı altında lisanslanmıştır.

## 👨‍💻 Geliştirici

**Diyar Aslan** - [GitHub](https://github.com/AslanSoftCom)

## 🙏 Teşekkürler

Bu framework'ü kullandığınız için teşekkürler!

---

⭐ Bu projeyi beğendiyseniz yıldız vermeyi unutmayın!

