<?php

class ModelBase 
{
    public $error_message;
    protected $db;
    protected static $db_table;
    protected $properties = array();

    ############################################################
    ### MAGIC METHODS - Constructor / Getter / Setter
    ############################################################

    public function __construct($data_array = null) {
        if (isset($data_array) && is_array($data_array)) {
            $this->properties = $data_array;
        }
        $this->db = DB::getDB();
    }

    public function __get($key) {
        return $this->properties[$key];
    }

    public function __set($key, $value) {
        return $this->properties[$key] = $value;
    }

    # Returns the string table name based on the [very basic] pluralized ModelName
    public static function table_name() {
        return strtolower(
            trim( 
                preg_replace_callback(
                    '/[A-Z]+/',
                    function ($matches) {
                        $character = reset($matches);
                        return '_' . $character;
                    },
                    get_called_class() . 's'
                ), 
                '_'
            )
        );
    }
    
    private static function _getQuery( $stmt )
    {
        ob_start();
        $stmt->debugDumpParams();
        $r = ob_get_contents();
        ob_end_clean();
        $arr = explode(PHP_EOL, $r);
        return $arr[1] ?? '';
    }
    
    ############################################################
    ### FINDER METHODS
    ############################################################

    public static function last(){
        $db = DB::getDB();
        $sql = "SELECT * FROM " . self::table_name() . " ORDER BY id DESC LIMIT 1";
        $q = $db->prepare($sql);
        $q->execute();
        $q->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $return_var = $q->fetch();
        return $return_var;
    }

    public static function all( $extra_unsafe_sql = false ){
        $db = DB::getDB();
        $sql = "SELECT * FROM " . self::table_name();
        if ($extra_unsafe_sql) {
            $sql .= " " . $extra;
        }

        $q = $db->prepare($sql);
        $q->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $q->execute();
        return $q->fetchAll();
    }

    public static function find( $id ){
        $db = DB::getDB();
        $sql = "SELECT * FROM " . self::table_name() . " WHERE id = ?";
        $q = $db->prepare($sql);
        $q->execute([ (int)$id ]);
        $q->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        //if ( self::table_name() == 'clients' ){ var_dump($q->debugDumpParams());die(); }
        $objects = $q->fetchAll();
        if ( empty($objects) ){ return ; }
        return (count($objects) === 1) ? $objects[0] : $objects;
    }

    public static function findByArray(array $array)
    {
        $db = DB::getDB();

        if (count($array) == 0) {
            return self::all();
        }

        // Build the SQL && Bind-Var Array
        $sql_where = "";
        $bind_vars = [];
        foreach ($array as $col => $val) {
            $bind_vars[":" . $col] = $val;
            if ( is_null($val) ){
                $sql_where .= $col.' IS :'.$col;
            } elseif ( is_array($val) ) {
                $sql_where .= 
                    in_array('NULL', $val, true) 
                        ? sprintf( '(%s IS NULL OR %s IN (%s))', $col, $col, implode(',', array_diff($val, ['NULL'])) )
                        : sprintf( '%s IN (%s)', $col, implode(',', $val) );
                unset( $bind_vars[":" . $col] );
            } else {
                $sql_where .= $col.'=:'.$col;
            }
            $sql_where .= ' AND ';
        }
        $sql_where = $sql_where ? substr($sql_where, 0, -5) : '';

        $sql = "SELECT * FROM " . self::table_name() . " WHERE " . $sql_where;

        $q = $db->prepare($sql);
        $q->execute($bind_vars);
        $q->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        //if ( self::table_name() == 'clients' ){ Tests::dumpVar([ $sql, self::_getQuery($q) ]); die(); }
        $objects = $q->fetchAll();
        return $objects;
    }
    
    /**
     * Execute random SQL query. Use '%tbl%' for current table_name
     * @param string $query
     * @return array
     */
    public static function findByQuery( string $query )
    {
        $db = DB::getDB();
        $q = str_replace("`tbl`", "`".self::table_name()."`", $query);
        $r = $db->query( $q, PDO::FETCH_ASSOC );
        return $r ? $r->fetchAll() : [];
    }
    
    ############################################################
    ### INSTANCE METHODS - Validation, Load, Save
    ############################################################
    # Placeholder; Override this within individual models!
    
    public function validate(){ return true; }

    public function __asArray() : Array
    {
        return $this->properties['id'] ? $this->properties : [];
    }
    
    public function exists()
    {
        return ( 
            isset($this->properties) 
            && isset($this->properties['id']) 
            && is_numeric($this->id)
        );
    }

    protected function loadPropertiesFromDatabase()
    {
        $sql = "SELECT * FROM " . self::table_name() . " WHERE id = ? ";
        $q = $this->db->prepare($sql);
        $q->execute(array($this->id));
        $this->properties = $q->fetch(PDO::FETCH_ASSOC);
    }

    public function delete()
    {
        if ( ! $this->validate() || ! $this->exists() ){ return false; }
        
        $q = $this->db->prepare( "DELETE FROM ".self::table_name()." WHERE id=?" );
        return $q->execute([ $this->properties['id'] ]);
    }
    
    public function copy()
    {
        unset( $this->properties['id'] );
        unset( $this->properties['updated_at'] );
        unset( $this->properties['created_at'] );
    }
    
    public function save()
    {
        # Validations MUST pass!
        if ($this->validate() === false) { 
            return false;
        }

        # Table Name && Created/Updated Fields
        $table_name = self::table_name();
        $this->updated_at = date('Y-m-d H:i:s');
        if ($this->exists() === false) {
            $this->created_at = date('Y-m-d H:i:s');
        }
        
        # Create SQL Query
        $sql_set_string = "";
        $total_properties_count = count($this->properties);
        $x = 0;
        foreach ($this->properties as $k => $v) {
            $x++;
            if ($k == 'id') {
                continue;
            }
            $sql_set_string .= $k . "=" . ":" . $k;
            if ($x != $total_properties_count) {
                $sql_set_string .= ", ";
            }
        }

        $sql = $table_name . " SET " . $sql_set_string;
        if ($this->exists()) {
            $final_sql = "UPDATE " . $sql . " WHERE id=:id";
        } else {
            $final_sql = "INSERT INTO " . $sql;
        }

        # Bind Vars
        foreach ($this->properties as $k => $v) {
            $bind_vars[(":" . $k)] = $v;
        }

        # Run the Insert or Update
        $q = $this->db->prepare($final_sql);
        $run = $q->execute($bind_vars);
        
        # Update the Object if SUCCESS
        if ($run === true) {
            if (!$this->exists()) {
                $this->id = $this->db->lastInsertId();
            }
            $this->loadPropertiesFromDatabase();
            return true;
        } else {
            $this->error_message = $q->errorInfo();
            return false;
        }
    }

}
