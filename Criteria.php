<?php
/**
 * Created by PhpStorm.
 * User: Alexandre
 * Date: 29/09/2017
 * Time: 13:47
 */

namespace Frogg;

use Phalcon\Mvc\Model as PhalconModel;

/**
 * Class Criteria
 *
 * default call criteria on model, example:
 *
 *     public static function query(DiInterface $dependencyInjector = null)
 *     {
 *         return parent::query($dependencyInjector)->addCriteriaName();
 *     }
 *
 * @package Frogg
 * @method self addSoftDelete(string $column, int $activeValue) add soft delete criteria to the query
 * @method self removeSoftDelete() removes soft delete criteria from the criteriaQueue
 */
class Criteria extends PhalconModel\Criteria
{
    public $modelCriterias = [];

    /**
     * removes soft deleted entries from the result.
     *
     * @param string $column
     * @param int    $activeValue
     *
     * @return PhalconModel\Criteria
     * @internal param $add
     *
     */
    public function softDeleteCriteria($column = 'deleted', $activeValue = 0)
    {
        return $this->andWhere($column.'='.$activeValue);
    }

    /**
     * alias to make more sense when calling it on query building.
     *
     * @return $this
     */
    public function withDeleted()
    {
        return $this->removeSoftDelete();
    }

    public function execute()
    {
        $instance = $this;
        foreach ($this->modelCriterias as $criteria => $value) {
            $method   = lcfirst($criteria).'Criteria';
            $instance = $instance->$method(...$value);
        }

        return $instance->parentExecute();
    }

    public function parentExecute()
    {
        return parent::execute();
    }

    public function __call($name, $arguments)
    {
        if (strpos($name, 'add') !== false) {
            $criteria                        = str_replace('add', '', $name);
            $this->modelCriterias[$criteria] = $arguments;
        } else if (strpos($name, 'remove') !== false) {
            $criteria = str_replace('remove', '', $name);
            if (isset($this->modelCriterias[$criteria])) {
                unset($this->modelCriterias[$criteria]);
            }
        } else {
            Throw new \Exception('Method '.$name.' does not exist.');
        }

        return $this;
    }
}
