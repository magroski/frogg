<?php

namespace Frogg;

use Frogg\Crypto\WT;
use Frogg\Exception\UnableToSaveRecord;
use Frogg\Model\Criteria;
use Frogg\Model\ResultSet;
use Phalcon\Di;
use Phalcon\DiInterface;
use Phalcon\Mvc\Model as PhalconModel;

/**
 * Class Model
 *
 * @method static static findFirstById(int $id)
 * @method static Model\ResultSet find($params = null)
 *
 * @property Di $di
 */
class Model extends PhalconModel implements \JsonSerializable
{
    public $populator = null;

    //This option will set the id of the clone to null
    const CLONE_RM_ID = 1;
    //This option will set the id of the clone to null AND save it to the DB
    const CLONE_CREATE_NEW = 2;

    public function getResultsetClass()
    {
        return ResultSet::class;
    }

    /**
     * @return \Frogg\Model\Criteria|\Phalcon\Mvc\Model\CriteriaInterface $criteria
     */
    public static function query(?Di\DiInterface $dependencyInjector = null) : PhalconModel\CriteriaInterface
    {
        $class = '\\' . get_called_class() . 'Criteria';
        if (class_exists($class)) {
            /** @var \Frogg\Model\Criteria $criteria */
            $criteria = new $class();
            $container = $dependencyInjector ?: Di::getDefault();
            if ($container=== null) {
                throw new \Exception('Container not found');
            }
            $criteria->setDI($container);
            $criteria->setModelName(get_called_class());
        } else {
            /** @var \Frogg\Model\Criteria $criteria */
            $criteria = (new Criteria())->setModelName(get_called_class());
            $criteria->setAlias(get_called_class());
        }

        return $criteria;
    }

    /**
     * Uses reflection to get all public and protected properties (excluding those that begin with a underscore)
     * and parses their names from camelCase to snake_case to be used in Phalcon column mapping
     *
     * @return array
     */
    public function columnMap()
    {
        $columnMap = [];
        $child     = new static();
        $reflect   = new \ReflectionObject($child);
        $props     = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED);
        foreach ($props as $prop) {
            if (substr($prop->getName(), 0, 1) !== '_') {
                $str = preg_replace('/(?<!^)[A-Z]/', '_$0', $prop->getName()) ?? '';
                $output             = strtolower($str);
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
    public function permalinkFor(string $attribute) : string
    {
        $tmp = new Permalink($this->$attribute);

        return $this->getNumeration($tmp->create());
    }

    /**
     * @param array $values Values that will be used to create a permalink
     *
     * @return string A permalink formatted string
     */
    public function permalinkForValues(array $values) : string
    {
        for ($i = 0; $i < count($values); $i++) {
            $values[$i] = Permalink::createSlug($values[$i]);
        }
        $value = implode('-', $values);

        return $this->getNumeration($value);
    }

    public function tokenId($key) : string
    {
        if (property_exists($this,'id')) {
            return WT::encode(['id' => $this->id], $key);
        }
        return '';
    }

    public static function getByTokenId($token, $key)
    {
        $data = WT::decode($token, $key);

        return isset($data->id) ? static::findFirstById($data->id) : null;
    }

    private function getNumeration($slug) : string
    {
        $resultset = $this->getReadConnection()->query("SELECT `permalink`
														FROM `" . $this->getSource() . "`
														WHERE `permalink` = '$slug'
														LIMIT 1");
        $i         = 1;
        $tmp       = $slug;
        while ($resultset->numRows()) {
            $slug      = $tmp . '-' . $i++;
            $resultset = $this->getReadConnection()->query("SELECT `permalink`
															FROM `" . $this->getSource() . "`
															WHERE `permalink` = '$slug'
															LIMIT 1");
        }

        return $slug;
    }

    public function jsonSerialize() : array
    {
        $myData  = [];
        $child   = new static();
        $reflect = new \ReflectionObject($child);
        $props   = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED);
        foreach ($props as $prop) {
            $propName = $prop->getName();
            if (substr($propName, 0, 1) !== '_') {
                $myData[$propName] = $this->{$propName};
            }
        }

        return $myData;
    }

    /**
     *
     * @return static |\Phalcon\Mvc\ModelInterface Returns a copy of the current object
     */
    public function clone()
    {
        $newObject = PhalconModel::cloneResult(
            new static(),
            $this->toArray(),
            PhalconModel::DIRTY_STATE_TRANSIENT
        );

        return $newObject;
    }

    /**
     * Save the entity or throw an exception.
     * @throws \Frogg\Exception\UnableToSaveRecord
     */
    public function saveOrFail() : void
    {
        $return = parent::save();

        if ($return === true) {
            return;
        }

        $arr = [];
        foreach ($this->getMessages() as $message) {
            if (\is_string($message)) {
                $arr[] = $message;
            }
            $arr[$message->getField()][] = $message->getMessage();
        }

        throw new UnableToSaveRecord('Unable to save entity. Details: ' . json_encode($arr));
    }

    public function getPopulator()
    {
        return $this->populator;
    }

    public function setPopulator($populator) : self
    {
        $this->populator = $populator;

        return $this;
    }

}
