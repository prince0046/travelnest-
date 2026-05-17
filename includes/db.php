<?php
class DB {
    private static ?PDO $pdo=null;
    public static function conn():PDO{
        if(!self::$pdo){
            try{
                self::$pdo=new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4",DB_USER,DB_PASS,[
                    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES=>false
                ]);
            }catch(PDOException $e){
                die('<div style="font-family:monospace;background:#fee;color:#900;padding:20px;margin:20px;border-radius:8px"><b>DB Error:</b> '.$e->getMessage().'<br><br>Check includes/config.php and run database.sql</div>');
            }
        }
        return self::$pdo;
    }
    public static function q(string $sql,array $p=[]):PDOStatement{
        $s=self::conn()->prepare($sql);$s->execute($p);return $s;
    }
    public static function all(string $sql,array $p=[]):array{return self::q($sql,$p)->fetchAll();}
    public static function one(string $sql,array $p=[]):array|false{return self::q($sql,$p)->fetch();}
    public static function val(string $sql,array $p=[]):mixed{return self::q($sql,$p)->fetchColumn();}
    public static function insert(string $t,array $d):int{
        foreach(array_keys($d) as $k) if(!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/',$k)) throw new \InvalidArgumentException("Invalid column: $k");
        $cols=implode(',',array_keys($d));
        $vals=implode(',',array_fill(0,count($d),'?'));
        self::q("INSERT INTO $t ($cols) VALUES ($vals)",array_values($d));
        return (int)self::conn()->lastInsertId();
    }
    // FIXED: uses ? placeholders consistently, no named param conflicts
    public static function update(string $t,array $d,string $w,array $wp=[]):void{
        foreach(array_keys($d) as $k) if(!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/',$k)) throw new \InvalidArgumentException("Invalid column: $k");
        $sets=implode(',',array_map(fn($k)=>"$k=?",array_keys($d)));
        self::q("UPDATE $t SET $sets WHERE $w",array_merge(array_values($d),$wp));
    }
    public static function delete(string $t,string $w,array $p=[]):void{
        self::q("DELETE FROM $t WHERE $w",$p);
    }
    public static function count(string $t,string $w='1',array $p=[]):int{
        return (int)self::val("SELECT COUNT(*) FROM $t WHERE $w",$p);
    }
    public static function paginate(string $sql,array $p,int $pg,int $pp=PER_PAGE):array{
        $total=(int)self::val("SELECT COUNT(*) FROM ($sql) _c",$p);
        $off=($pg-1)*$pp;
        $rows=self::all("$sql LIMIT $pp OFFSET $off",$p);
        return['data'=>$rows,'total'=>$total,'page'=>$pg,'per_page'=>$pp,'last'=>(int)ceil($total/$pp)];
    }
}
