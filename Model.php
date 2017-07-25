<?php

namespace Frogg;

use Phalcon\Mvc\Model as PhalconModel;
use Frogg\Crypto\WT;

class Model extends PhalconModel
{

    public function permalinkFor($attribute)
    {
        $tmp = new Permalink($this->$attribute);

        return $this->getNumeration($tmp->create());
    }

    public function permalinkForValues($values)
    {
        for ($i = 0; $i < count($values); $i++) {
            $values[$i] = Permalink::createSlug($values[$i]);
        }
        $value = implode('-', $values);

        return $this->getNumeration($value);
    }

    public function tokenId($key)
    {
        return WT::encode(['id' => $this->id], $key);
    }

    public static function getByTokenId($token, $key)
    {
        $data = WT::decode($token, $key);

        return isset($data->id) ? static::findFirstById($data->id) : false;
    }

    private function getNumeration($slug)
    {
        $resultset = $this->getReadConnection()->query("SELECT `permalink`
														FROM `".$this->getSource()."`
														WHERE `permalink` = '$slug'
														LIMIT 1");
        $i         = 1;
        $tmp       = $slug;
        while ($resultset->numRows()) {
            $slug      = $tmp.'-'.$i++;
            $resultset = $this->getReadConnection()->query("SELECT `permalink`
															FROM `".$this->getSource()."`
															WHERE `permalink` = '$slug'
															LIMIT 1");
        }

        return $slug;
    }

}