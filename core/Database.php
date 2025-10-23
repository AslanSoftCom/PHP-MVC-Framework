<?php

class DB {
    
    private $pdo;
    private $sQuery;
    private $settings;
    private $bConnected = false;
    private $parameters;
    private static $errorTemplate = __DIR__ . '/../core/errors/database.php';

    public function __construct()
    {
        $this->settings = require(__DIR__ . '/../app/config/database.php');
        $this->Connect();
        $this->parameters = [];
    }

    /**
     * Veritabanına bağlantı kurar.
     * PDO nesnesi oluşturulur ve bağlantı ayarları yapılır.
     * Eğer bağlantı başarısız olursa 500 hata kodu ile özel bir hata sayfası gösterilir.
     */
    private function Connect()
    {
        $dsn = 'mysql:dbname=' . $this->settings['database']['dbname'] . ';host=' . $this->settings['database']['host'] . ';charset=utf8mb4';
        try {
            $this->pdo = new PDO(
                $dsn,
                $this->settings['database']['user'],
                $this->settings['database']['password'],
                [
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_PERSISTENT => false
                ]
            );

            $this->bConnected = true;
        } catch (PDOException $e) {
            http_response_code(500);
            include self::$errorTemplate;
            exit;
        }
    }

    /**
     * Sorguyu hazırlar ve gerekli parametreleri bağlar.
     * Parametrelerin türlerini otomatik olarak belirler ve sorguyu çalıştırır.
     *
     * @param string $query SQL sorgusu.
     * @param array|string $parameters Sorguya bağlanacak parametreler.
     */
    private function Init($query, $parameters = "")
    {
        if (!$this->bConnected) {
            $this->Connect();
        }
        try {
            $this->sQuery = $this->pdo->prepare($query);
            $this->bindMore($parameters);

            if (!empty($this->parameters)) {
                foreach ($this->parameters as $param => $value) {
                    $type = match (true) {
                        is_int($value[1]) => PDO::PARAM_INT,
                        is_bool($value[1]) => PDO::PARAM_BOOL,
                        is_null($value[1]) => PDO::PARAM_NULL,
                        default => PDO::PARAM_STR,
                    };
                    $this->sQuery->bindValue($value[0], $value[1], $type);
                }
            }

            $this->sQuery->execute();
        } catch (PDOException $e) {
            ErrorHandler::handleException($e);
        }

        $this->parameters = [];
    }

    /**
     * Tek bir parametreyi sorguya bağlar.
     *
     * @param string $para Parametre adı.
     * @param mixed $value Parametre değeri.
     */
    public function bind($para, $value)
    {
        $this->parameters[sizeof($this->parameters)] = [":" . $para, $value];
    }

    /**
     * Birden fazla parametreyi sorguya bağlar.
     *
     * @param array $parray Parametreler (anahtar-değer çiftleri).
     */
    public function bindMore($parray)
    {
        if (empty($this->parameters) && is_array($parray)) {
            foreach ($parray as $column => $value) {
                $this->bind($column, $value);
            }
        }
    }

    /**
     * SQL sorgusunu çalıştırır ve sonuç döndürür.
     *
     * @param string $query SQL sorgusu.
     * @param array|null $params Sorguya bağlanacak parametreler.
     * @param int $fetchmode PDO'nun veri çekme modu.
     * @return mixed Sorgunun türüne göre farklı sonuçlar döner.
     */
    public function query($query, $params = null, $fetchmode = PDO::FETCH_ASSOC)
    {
        $query = trim(str_replace("\r", " ", $query));
        $this->Init($query, $params);
        $rawStatement = explode(" ", preg_replace("/\s+|\t+|\n+/", " ", $query));
        $statement = strtolower($rawStatement[0]);

        return match ($statement) {
            'select', 'show' => $this->sQuery->fetchAll($fetchmode),
            'insert', 'update', 'delete' => $this->sQuery->rowCount(),
            default => null,
        };
    }

    /**
     * Son eklenen kaydın ID'sini döner.
     *
     * @return string Son eklenen kaydın ID'si.
     */
    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * Bir veritabanı işlemi başlatır.
     *
     * @return bool İşlem başlatıldıysa `true` döner.
     */
    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * Veritabanı işlemini tamamlar (commit).
     *
     * @return bool İşlem tamamlandıysa `true` döner.
     */
    public function executeTransaction()
    {
        return $this->pdo->commit();
    }

    /**
     * Veritabanı işlemini geri alır (rollback).
     *
     * @return bool İşlem geri alındıysa `true` döner.
     */
    public function rollBack()
    {
        return $this->pdo->rollBack();
    }

    /**
     * Bir SQL sorgusundan dönen tüm sütunları döner.
     *
     * @param string $query SQL sorgusu.
     * @param array|null $params Sorguya bağlanacak parametreler.
     * @return array Sütun değerlerini içeren bir dizi döner.
     */
    public function column($query, $params = null)
    {
        $this->Init($query, $params);
        $Columns = $this->sQuery->fetchAll(PDO::FETCH_NUM);

        return array_map(fn($cells) => $cells[0], $Columns);
    }

    /**
     * Bir SQL sorgusundan dönen ilk satırı döner.
     *
     * @param string $query SQL sorgusu.
     * @param array|null $params Sorguya bağlanacak parametreler.
     * @param int $fetchmode PDO'nun veri çekme modu.
     * @return array|false İlk satır verisi veya başarısızlık durumunda `false` döner.
     */
    public function row($query, $params = null, $fetchmode = PDO::FETCH_ASSOC)
    {
        $this->Init($query, $params);
        $result = $this->sQuery->fetch($fetchmode);
        $this->sQuery->closeCursor();
        return $result;
    }

    /**
     * Bir SQL sorgusundan dönen tek bir hücreyi döner.
     *
     * @param string $query SQL sorgusu.
     * @param array|null $params Sorguya bağlanacak parametreler.
     * @return mixed Hücre değeri.
     */
    public function single($query, $params = null)
    {
        $this->Init($query, $params);
        $result = $this->sQuery->fetchColumn();
        $this->sQuery->closeCursor();
        return $result;
    }
}
