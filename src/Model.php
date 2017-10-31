<?php

namespace Frogg;

use Frogg\Crypto\WT;
use Frogg\Model\Criteria;
use Phalcon\Di;
use Phalcon\DiInterface;
use Phalcon\Mvc\Model as PhalconModel;

/**
 * Class Model
 * @package Frogg
 *
 * @method static static findFirstById(int $id)
 * @method static Model\ResultSet find($params = null)
 */
class Model extends PhalconModel implements \JsonSerializable
{

    public function getResultsetClass()
    {
        return 'Frogg\Model\ResultSet';
    }

    /**
     * @param DiInterface|null $dependencyInjector
     *
     * @return \Frogg\Model\Criteria $criteria
     */
    public static function query(DiInterface $dependencyInjector = null)
    {
        $class = '\\'.get_called_class().'Criteria';
        if(class_exists($class)) {
            /** @var \Frogg\Model\Criteria $criteria */
            $criteria = new $class();
            $criteria->setDI($dependencyInjector ?: Di::getDefault());
            $criteria->setModelName(get_called_class());
        } else {
            $criteria = (new Criteria())->setModelName(get_called_class())->setAlias(get_called_class());
        }

        return $criteria;
    }

    public function columnMap()
    {
        $columnMap = [];
        $child     = new static();
        $reflect   = new \ReflectionObject($child);
        $props     = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach ($props as $prop) {
            if (substr($prop->getName(), 0, 1) !== '_') {
                $output             = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $prop->getName()));
                $columnMap[$output] = $prop->getName();
            }
        }

        return $columnMap;
    }

    /**
     * @param string $attribute Name of the attribute that will be used to create a permalink
     *
     * @return string A permalink formatted string
     */
    public function permalinkFor(string $attribute): string
    {
        $tmp = new Permalink($this->$attribute);

        return $this->getNumeration($tmp->create());
    }

    /**
     * @param array $values Values that will be used to create a permalink
     *
     * @return string A permalink formatted string
     */
    public function permalinkForValues(array $values): string
    {
        for ($i = 0; $i < count($values); $i++) {
            $values[$i] = Permalink::createSlug($values[$i]);
        }
        $value = implode('-', $values);

        return $this->getNumeration($value);
    }

    public function tokenId($key): string
    {
        return WT::encode(['id' => $this->id], $key);
    }

    public static function getByTokenId($token, $key)
    {
        $data = WT::decode($token, $key);

        return isset($data->id) ? static::findFirstById($data->id) : false;
    }

    private function getNumeration($slug): string
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

    public function jsonSerialize()
    {
        $myData  = [];
        $child   = new static();
        $reflect = new \ReflectionObject($child);
        $props   = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach ($props as $prop) {
            $propName = $prop->getName();
            if (substr($propName, 0, 1) !== '_') {
                $myData[$propName] = $this->{$propName};
            }
        }

        return $myData;
    }

}