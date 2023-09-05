<?php

/**
 * common methods are
 * insert('value')
 * remove({field: value})
 * read()
 *  - {fild: value}
 */
class InMemory {
    public $filename;
    public $fullpath;

    public $lastInsertedRec;

    public function __construct($filename) {
        if(!file_exists("./storage/".$filename)) {
            file_put_contents("./storage/".$filename, "");
        }

        $this->filename = $filename;
        $this->fullpath = "./storage/".$filename;

        $data = file_get_contents($this->fullpath);
        if($data) {
            $this->maintainRecords();
        }
    }

    public function read($arr = "") {
        $data = file_get_contents($this->fullpath);
        if($data) {
            $outputArr = explode("\n", $data);
        } else {
            return [];
        }

        if(is_array($arr)) {
            $arrKey = array_keys($arr)[0];
            foreach($outputArr as $key => $value) {
                if($value) {
                    $dataObj = json_decode($value);
                    if($dataObj->$arrKey == $arr[$arrKey]) {
                        return $dataObj; // returns an object
                    }
                }
            }
        } else {
            return $outputArr; // returns an array
        }
    }

    public function insert($value) {
        if(file_put_contents($this->fullpath, trim($value)."\n", FILE_APPEND)) {
            $this->lastInsertedRec = $value;
            return true;
        } else {
            return false;
        }
    }

    public function update($oldValue, $newValue) {
        $data = file_get_contents($this->fullpath);
        $replaced_str = str_replace($oldValue, $newValue, $data);
        if(file_put_contents($this->fullpath, trim($replaced_str)."\n")) {
            $this->maintainRecords();
            $this->lastInsertedRec = $newValue;
            return true;
        } else {
            return false;
        }
    }

    public function remove($value) {
        $data = file_get_contents($this->fullpath);
        $replaced_str = str_replace($value, "", $data);
        if(file_put_contents($this->fullpath, trim($replaced_str)."\n")) {
            $this->maintainRecords();
            return true;
        } else {
            return false;
        }
    }


    private function maintainRecords() {
        $str = "";

        $data = file_get_contents($this->fullpath);
        $outputArr = preg_split('/({"uniqueID"|{"weekID")/', $data);
        foreach($outputArr as $key => $value) {
            $prep = ($this->filename == "week_plan.txt") ? '{"weekID"' : '{"uniqueID"';

            if(strlen($value) > 1) {
                $str .= $prep.trim($value)."\n";
            }
        }

        file_put_contents($this->fullpath, trim($str)."\n");
    }
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}

?>