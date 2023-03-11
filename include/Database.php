<?php

class Database {

    var $host = "";
    var $user = "";
    var $pass = "";
    var $db = "";
    var $attempt = null;
    var $take;
    function __construct($host, $user, $pass, $db, $warn = "") {

        $this->attempt = new mysqli($host, $user, $pass, $db);

        if (mysqli_connect_errno()) {
            exit($warn);
        }

        $this->attempt->query("set names utf8mb4");
        $this->attempt->query("set sql_mode='';");
    }

    function qry($qry) {
        $this->attempt->query($qry);
    }

    function select($table, $cells, $query2, $warn = "", $debug = false) {

        if ($debug == true) {
            print "<div style='display:none' class='debug'>set sql_mode=''; select $cells from $table $query2</div>";
        }

        $take = $this->attempt->query("select $cells from $table $query2");

        if (!$take) {
            return $returnedvar = array(false, $warn);
        }
        else {
            return $take;
        }
    }

    function read($result, $ifjoin = false) {

        if (!$result) {
            return $returnedvar = array(false, $warn);
        }
        else {
            if ($ifjoin == false) {
                $readz = $result->fetch_assoc();
                return $returnedvar = array(true, $readz);
            }
            else {
                $readtemp = $result->fetch_assoc();
                $joininfo = $result->fetch_fields();
                foreach ($joininfo as $val) {
                    $readz[$val->table][$val->name] = $readtemp[$val->name];
                }
                return $returnedvar = array(true, $readz);
            }
        }
    }

    function readall($result, $ifjoin = false) {
        $readallz = array();

        if (!$result) {
            return $returnedvar = array(false, $warn);
        }
        else {

            if ($ifjoin == false) {
                while ($reads = $result->fetch_assoc()) {
                    $readallz[] = $reads;
                }
                return $returnedvar = array(true, $readallz);
            }
            else {
                $joininfo = $result->fetch_fields();
                while ($reads = $result->fetch_assoc()) {

                    foreach ($joininfo as $val) {
                        $readz[$val->table][$val->name] = $reads[$val->name];
                    }

                    $readallz[] = $readz;
                }
                return $returnedvar = array(true, $readallz);
            }
        }
    }

    function insert($table, $val, $query2 = "", $warn = "", $debug = false) {

        $cells = "";
        $values = "";

        foreach ($val as $k => $v) {

            $cells .= "$k,";
            if ($v != "null") {
                $values .= "'$v',";
            }
            else {
                $values .= "null,";
            }
        }

        $cells = substr($cells, 0, -1);
        $values = substr($values, 0, -1);

        if ($debug == true) {
            print "<div style='display:none' class='debug'>set sql_mode=''; INSERT INTO $table ($cells) values ($values) $query2</div>";
        }
        else {
            $insert = $this->attempt->query("INSERT INTO $table ($cells) values ($values) $query2");
            if (!$insert) {
                return $returnedvar = array(false, $warn);
            }
            else {
                return $returnedvar = array(true);
            }
        }
    }

    function update($table, $val, $query2 = "", $warn = "", $debug = false) {

        $cellvalues = "";

        foreach ($val as $k => $v) {

            if ($v != "null") {
                $cellvalues .= "$k='$v',";
            }
            else {
                $cellvalues .= "$k=null,";
            }
        }


        $cellvalues = substr($cellvalues, 0, -1);

        if ($debug == false) {
            $update = $this->attempt->query("update $table set $cellvalues $query2");
        }
        else {
            print "<div style='display:none' class='debug'>set sql_mode=''; update $table set $cellvalues $query2</div>";
        }

        if (!$update) {
            return $returnedvar = array(false, $warn);
        }
        else {
            return $returnedvar = array(true);
        }
    }

    function delete($table, $query2, $warn = "", $debug = false) {

        if ($debug == false) {
            $delete = $this->attempt->query("delete from $table $query2");
        }
        else {
            print "<div style='display:none' class='debug'>set sql_mode=''; delete from $table $query2</div>";
        }

        if (!$delete) {
            return $returnedvar = array(false, $warn);
        }
        else {
            return $returnedvar = array(true);
        }
    }

    function num_rowz($quer) {
        return $quer->num_rows;
    }

}

?>
