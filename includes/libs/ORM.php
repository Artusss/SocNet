<?php
/** @noinspection ALL */
//встроенная оболочка над запросами в БД

abstract class DB_Connection
{
    protected static function Prepare($db_name, $host, $user_name, $password, $charset){}
    protected static function Force(){}
}
interface iCRUD
{
    public static function Create($table_name, $request);
    public static function Read($table_name, $request, $value_arr);
    public static function Read_one($table_name, $request, $value_arr, $what_select);
    public static function Read_count($table_name, $request, $value_arr);
    public static function Exists($table_name, $request, $value_arr);
    public static function Update($table_name, $request_set, $request_condition, $value_arr);
    public static function Delete($table_name, $request, $value_arr);
    public static function Clean($table_name);
}
interface iMerger
{
    public static function Join($type, $table_arr, $request, $value_arr, $what_select);
    public static function Union($table_name, $request, $value_arr, $what_select);
    public static function Union_all($table_name, $request, $value_arr, $what_select);
}

class RequestHelper {
    final public static function get_Value($request)
    {
        $value = array();
        foreach ($request as $v) {
            if (is_string($v)) {
                $v = htmlspecialchars($v, ENT_NOQUOTES);
            }
            $value[] = $v;
        }
        return $value;
    }

    final public static function get_Question_mark($count)
    {
        $question_mark = array_fill(0, $count, "(?)");
        return implode(", ", $question_mark);
    }

    final public static function get_Property($request)
    {
        $property = [];
        foreach ($request as $k => $v) {
            $k = htmlspecialchars($k, ENT_NOQUOTES);
            $property[] = "`$k`";
        }
        return implode(", ", $property);
    }
}

class ORM extends DB_Connection implements iCRUD, iMerger
{
    private static $connection;

    private static $host;
    private static $user_name;
    private static $password;
    private static $db_name;
    private static $charset;

    private static function Connect()
    {
        $host = self::$host;
        $db_name = self::$db_name;
        $charset = self::$charset;
        $dsn = "mysql:host=$host;dbname=$db_name;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        self::$connection = new PDO($dsn, self::$user_name, self::$password, $options);
        return self::$connection;
    }

    public static function Prepare($db_name, $host = 'localhost', $user_name = 'root', $password = '', $charset = 'utf8')
    {
        self::$host = $host;
        self::$user_name = $user_name;
        self::$password = $password;
        self::$db_name = $db_name;
        self::$charset = $charset;
        return false;
    }

    public static function Force()
    {
        try {
            self::Connect();
            print_r(self::$connection);
            echo "<p>Подключение удалось</p>";
        } catch (PDOException $e) {
            die('Подключение не удалось: ' . $e->getMessage());
        }
        return false;
    }

    public static function Create($table_name, $request)
    {
        try {
            $pdo = self::Connect();
            $str_property = RequestHelper::get_Property($request);
            $str_question_mark = RequestHelper::get_Question_mark(count($request));
            $table_name_field = "`" . str_replace("`", "``", $table_name) . "`";
            $sql_request_field = "INSERT INTO {$table_name_field} ({$str_property}) VALUES ({$str_question_mark})";
            $stmt = $pdo->prepare($sql_request_field);
            $stmt->execute(RequestHelper::get_Value($request));
            unset($stmt, $pdo);
        } catch (PDOException $e) {
            die('Подключение не удалось: ' . $e->getMessage());
        }
        return false;
    }

    public static function Read($table_name, $request = '1', $value_arr = null, $what_select='*')
    {
        $table_content = array();
        try {
            $pdo = self::Connect();
            $table_name_field = "`" . str_replace("`", "``", $table_name) . "`";
            $request_field = "SELECT {$what_select} FROM {$table_name_field} WHERE {$request}";
            $stmt = $pdo->prepare($request_field);
            $stmt->execute($value_arr);
            while (($row = $stmt->fetch())) {
                $table_content[] = $row;
            }
            unset($stmt, $pdo);
        } catch (PDOException $e) {
            die('Подключение не удалось: ' . $e->getMessage());
        }
        return $table_content;
    }

    public static function Read_one($table_name, $request = '1', $value_arr = null, $what_select='*')
    {
        try {
            $pdo = self::Connect();
            $table_name_field = "`" . str_replace("`", "``", $table_name) . "`";
            $request_field = "SELECT {$what_select} FROM {$table_name_field} WHERE {$request}";
            $stmt = $pdo->prepare($request_field);
            $stmt->execute($value_arr);
            $table_content = $stmt->fetch();
            unset($stmt, $pdo);
        } catch (PDOException $e) {
            die('Подключение не удалось: ' . $e->getMessage());
        }
        return $table_content;
    }

    public static function Read_count($table_name, $request = '1', $value_arr = null)
    {
        try {
            $pdo = self::Connect();
            $table_name_field = "`" . str_replace("`", "``", $table_name) . "`";
            $request_field = "SELECT COUNT(`id`) as `total_count` FROM {$table_name_field} WHERE {$request}";
            $stmt = $pdo->prepare($request_field);
            $stmt->execute($value_arr);
            $count_result = $stmt->fetch()['total_count'];
            unset($stmt, $pdo);
        } catch (PDOException $e) {
            die('Подключение не удалось: ' . $e->getMessage());
        }
        return isset($count_result) ? $count_result : null;
    }

    public static function Exists($table_name, $request = '1', $value_arr = null)
    {
        try {
            $pdo = self::Connect();
            $table_name_field = "`" . str_replace("`", "``", $table_name) . "`";
            $request_field = "SELECT `id` FROM {$table_name_field} WHERE {$request}";
            $stmt = $pdo->prepare($request_field);
            $stmt->execute($value_arr);
            if($stmt->fetch()) {
                $exists = true;
            }
            unset($stmt, $pdo);
        } catch (PDOException $e) {
            die('Подключение не удалось: ' . $e->getMessage());
        }
        return isset($exists) ? $exists : false;
    }

    public static function Update($table_name, $request_set, $request_condition, $value_arr = null)
    {
        try {
            $pdo = self::Connect();
            $table_name_field = "`" . str_replace("`", "``", $table_name) . "`";
            foreach ($value_arr as &$v) {
                if (is_string($v)) {
                    $v = htmlspecialchars($v, ENT_NOQUOTES);
                }
            }
            $request_field = "UPDATE {$table_name_field} SET {$request_set} WHERE {$request_condition}";
            $stmt = $pdo->prepare($request_field);
            $stmt->execute($value_arr);
            unset($stmt, $pdo);
        } catch (PDOException $e) {
            die('Подключение не удалось: ' . $e->getMessage());
        }
        return false;
    }

    public static function Delete($table_name, $request, $value_arr = null)
    {
        try {
            $pdo = self::Connect();
            $table_name_field = "`" . str_replace("`", "``", $table_name) . "`";
            $request_field = "DELETE FROM {$table_name_field} WHERE {$request}";
            $stmt = $pdo->prepare($request_field);
            $stmt->execute($value_arr);
            unset($stmt, $pdo);
        } catch (PDOException $e) {
            die('Подключение не удалось: ' . $e->getMessage());
        }
        return false;
    }

    public static function Clean($table_name){
        try {
            $pdo = self::Connect();
            $table_name_field = "`" . str_replace("`", "``", $table_name) . "`";
            $request_field = "TRUNCATE TABLE {$table_name_field}";
            $stmt = $pdo->query($request_field);
            unset($stmt, $pdo);
        } catch (PDOException $e) {
            die('Подключение не удалось: ' . $e->getMessage());
        }
        return false;
    }

    public static function Join($type, $table_arr, $request = '1', $value_arr = null, $what_select='*')
    {
        $two_tables_content = array();
        try {
            $pdo = self::Connect();
            $name = array();
            $col = array();
            foreach($table_arr as $k => $v){
                $name[] = $k;
                $col[] = "{$k}.{$v}";
            }
            $request_field = "SELECT {$what_select} FROM {$name[0]} {$type} JOIN {$name[1]} ON {$col[0]} = {$col[1]} WHERE {$request}";
            $stmt = $pdo->prepare($request_field);
            $stmt->execute($value_arr);
            while (($row = $stmt->fetch())) {
                $two_tables_content[] = $row;
            }
            unset($stmt, $pdo);
        } catch (PDOException $e) {
            die('Подключение не удалось: ' . $e->getMessage());
        }
        return $two_tables_content;
    }

    public static function Union($table_name, $request = ['1', '1'], $value_arr = null, $what_select=['*', '*']){
        $table_content = array();
        try {
            $pdo = self::Connect();
            $table_name_field = "`" . str_replace("`", "``", $table_name) . "`";
            $request_1 = "SELECT {$what_select[0]} FROM {$table_name_field} WHERE {$request[0]}";
            $request_2 = "SELECT {$what_select[1]} FROM {$table_name_field} WHERE {$request[1]}";
            $request_field = "({$request_1}) UNION ({$request_2})";
            $stmt = $pdo->prepare($request_field);
            $stmt->execute($value_arr);
            while (($row = $stmt->fetch())) {
                $table_content[] = $row;
            }
            unset($stmt, $pdo);
        } catch (PDOException $e) {
            die('Подключение не удалось: ' . $e->getMessage());
        }
        return $table_content;
    }

    public static function Union_all($table_name, $request = ['1', '1'], $value_arr = null, $what_select=['*', '*']){
        $table_content = array();
        try {
            $pdo = self::Connect();
            $table_name_field = "`" . str_replace("`", "``", $table_name) . "`";
            $request_1 = "SELECT {$what_select[0]} FROM {$table_name_field} WHERE {$request[0]}";
            $request_2 = "SELECT {$what_select[1]} FROM {$table_name_field} WHERE {$request[1]}";
            $request_field = "({$request_1}) UNION ALL ({$request_2})";
            $stmt = $pdo->prepare($request_field);
            $stmt->execute($value_arr);
            while (($row = $stmt->fetch())) {
                $table_content[] = $row;
            }
            unset($stmt, $pdo);
        } catch (PDOException $e) {
            die('Подключение не удалось: ' . $e->getMessage());
        }
        return $table_content;
    }
}