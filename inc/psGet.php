<?php

class psGet extends psCli {

    const LONGOPTS = Array("property=", "list-properties");

    static $properties = false;

    public function init($argv, $longopts = false, $shortopts = false) {
        $options = parent::init($argv, self::LONGOPTS);
        self::$properties = Array();
        if (is_array(self::getarg("property", $options))) {
            foreach (self::getarg("property", $options) as $p) {
                self::$properties[$p] = 1;
            }
        } else if (self::getarg("property", $options)) {
            self::$properties[self::getarg("property", $options)] = 1;
        }
        if (self::$debug) {
            psOut::msg("Properties:\n" . print_r(self::$properties, true));
        }
        self::$options = $options;
        return($options);
    }

    public function getValues($obj, $properties = false) {
        $row = Array();
        if (!$properties) {
            if (!self::$properties) {
                $properties = self::listProperties($obj);
            } else {
                $properties = self::$properties;
            }
        }
        foreach ($properties as $p => $v) {
            if (!isset($obj->$p)) {
                psOut::error("Property $p unknown!'");
            }
            if (!is_object($obj->$p)) {
                $row[$p] = $obj->$p;
            } elseif (isset($obj->$p->language)) {
                $row[$p] = $obj->$p->language[psCli::$lang];
            } else {
                $row[$p] = null;
            }
        }

        return($row);
    }

    public function listProperties($obj) {
        return(array_flip(array_keys(get_object_vars($obj))));
    }

}
