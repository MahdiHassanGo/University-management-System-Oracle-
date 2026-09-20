<?php
require_once __DIR__ . '/app.php';


// Load local untracked credentials override if it exists
if (file_exists(__DIR__ . '/database.local.php')) {
    require_once __DIR__ . '/database.local.php';
}

if (!defined('DB_USER')) {
    define('DB_USER', getenv('DB_USER') ?: 'CF_OWNER');
}
if (!defined('DB_PASS')) {
    define('DB_PASS', getenv('DB_PASS') ?: 'CHANGE_ME');
}
if (!defined('DB_CONNECTION_STRING')) {
    define('DB_CONNECTION_STRING', getenv('DB_CONNECTION_STRING') ?: '127.0.0.1:1521/XE');
}
if (!defined('DB_CHARSET')) {
    define('DB_CHARSET', getenv('DB_CHARSET') ?: 'AL32UTF8');
}

final class OracleDB
{
    private $conn;
    private bool $transaction = false;

    public function __construct()
    {
        if (!function_exists('oci_connect')) {
            throw new RuntimeException(
                'PHP OCI8 extension is not enabled. Enable OCI8 first, restart Apache, then reload this page.'
            );
        }

        $this->conn = @oci_connect(DB_USER, DB_PASS, DB_CONNECTION_STRING, DB_CHARSET);
        if (!$this->conn) {
            $e = oci_error();
            $message = $e['message'] ?? 'Unknown Oracle connection error.';
            throw new RuntimeException(trim($message));
        }

        // Make Oracle DATE values predictable for the PHP UI.
        $stmt = @oci_parse($this->conn, "ALTER SESSION SET NLS_DATE_FORMAT='YYYY-MM-DD HH24:MI:SS'");
        if ($stmt) {
            @oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
            @oci_free_statement($stmt);
        }
    }

    public function connection()
    {
        return $this->conn;
    }

    public function prepare(string $sql): OracleStatement
    {
        return new OracleStatement($this, $sql);
    }

    public function query(string $sql): OracleStatement
    {
        $stmt = $this->prepare($sql);
        $stmt->execute();
        return $stmt;
    }

    public function beginTransaction(): bool
    {
        $this->transaction = true;
        return true;
    }

    public function inTransaction(): bool
    {
        return $this->transaction;
    }

    public function commit(): bool
    {
        if (!@oci_commit($this->conn)) {
            $e = oci_error($this->conn);
            throw new RuntimeException(trim($e['message'] ?? 'Oracle commit failed.'));
        }
        $this->transaction = false;
        return true;
    }

    public function rollBack(): bool
    {
        if (!@oci_rollback($this->conn)) {
            $e = oci_error($this->conn);
            throw new RuntimeException(trim($e['message'] ?? 'Oracle rollback failed.'));
        }
        $this->transaction = false;
        return true;
    }

    public function executeMode(): int
    {
        return $this->transaction ? OCI_NO_AUTO_COMMIT : OCI_COMMIT_ON_SUCCESS;
    }

    public function serverVersion(): string
    {
        return (string)oci_server_version($this->conn);
    }
}

final class OracleStatement
{
    private OracleDB $db;
    private string $sql;
    private $stid;
    private array $bound = [];

    public function __construct(OracleDB $db, string $sql)
    {
        $this->db = $db;
        $this->sql = $this->convertPositionalParameters($sql);
        $this->stid = @oci_parse($db->connection(), $this->sql);
        if (!$this->stid) {
            $e = oci_error($db->connection());
            throw new RuntimeException(trim($e['message'] ?? 'Oracle could not parse the SQL statement.'));
        }
    }

    private function convertPositionalParameters(string $sql): string
    {
        $index = 0;
        return preg_replace_callback('/\?/', function () use (&$index) {
            $index++;
            return ':p' . $index;
        }, $sql);
    }

    public function execute(array $params = []): bool
    {
        $this->bound = [];
        foreach (array_values($params) as $i => $value) {
            $name = ':p' . ($i + 1);
            // OCI binding requires a stable variable reference.
            $this->bound[$i] = $value;
            $type = is_int($value) ? SQLT_INT : SQLT_CHR;
            if ($value === null) {
                $this->bound[$i] = null;
                $type = SQLT_CHR;
            }
            if (!@oci_bind_by_name($this->stid, $name, $this->bound[$i], -1, $type)) {
                $e = oci_error($this->stid);
                throw new RuntimeException(trim($e['message'] ?? 'Oracle parameter binding failed.'));
            }
        }

        if (!@oci_execute($this->stid, $this->db->executeMode())) {
            $e = oci_error($this->stid);
            throw new RuntimeException(trim($e['message'] ?? 'Oracle statement execution failed.'));
        }
        return true;
    }

    public function fetch()
    {
        $row = oci_fetch_array($this->stid, OCI_ASSOC | OCI_RETURN_NULLS | OCI_RETURN_LOBS);
        return $row === false ? false : $this->normalizeRow($row);
    }

    public function fetchAll(): array
    {
        $rows = [];
        while (($row = $this->fetch()) !== false) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function fetchColumn(int $column = 0)
    {
        $row = oci_fetch_array($this->stid, OCI_NUM | OCI_RETURN_NULLS | OCI_RETURN_LOBS);
        return $row === false ? false : ($row[$column] ?? false);
    }

    public function rowCount(): int
    {
        return (int)oci_num_rows($this->stid);
    }

    private function normalizeRow(array $row): array
    {
        $out = [];
        foreach ($row as $key => $value) {
            $out[strtolower((string)$key)] = $value;
        }
        return $out;
    }
}

function db(): OracleDB
{
    static $db = null;
    if (!$db instanceof OracleDB) {
        $db = new OracleDB();
    }
    return $db;
}
