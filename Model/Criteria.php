<?php
/**
 * Created by PhpStorm.
 * User: Alexandre
 * Date: 29/09/2017
 * Time: 13:47
 */

namespace Frogg\Model;

use Phalcon\Mvc\Model as PhalconModel;

/**
 * Class Criteria
 *
 * default call criteria on model, example:
 *
 *     public static function query(DiInterface $dependencyInjector = null)
 *     {
 *         return parent::query($dependencyInjector)->softDelete();
 *     }
 *
 * @package Frogg
 * @method static addSoftDelete(string $column = 'deleted', int $activeValue = 0) add soft delete criteria to the query
 * @method static removeSoftDelete() removes soft delete criteria from the criteriaQueue
 */
class Criteria extends PhalconModel\Criteria
{
    private $modelCriterias = [];

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
    public function softDelete($column = 'deleted', $activeValue = 0)
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
            $method   = lcfirst($criteria);
            $instance = $instance->$method(...$value);
        }

        return $instance->parentExecute();
    }

    public function getPhql()
    {
        return $this->createBuilder()->getPhql();
    }

    public function getQuery()
    {
        return $this->createBuilder()->getQuery();
    }

    public function getActiveCriterias()
    {
        return $this->modelCriterias;
    }

    public function findFirstById($id)
    {
        return $this->andWhere($this->getModelName().'.id = '.$id)->execute()->getFirst();
    }

    private function parentExecute()
    {
        return parent::execute();
    }

    public function bind(array $bindParams, $merge = true)
    {
        parent::bind($bindParams, $merge);
    }

    public function bindTypes(array $bindTypes, $merge = true)
    {
        if ($merge) {
            $query_types = $this->getQuery()->getBindTypes();
            $bindTypes   = array_merge($query_types ? : [], $bindTypes);
        }
        parent::bindTypes($bindTypes);
    }

    public function addCriteria($name, $arguments = [])
    {
        if(method_exists($this, $name)) {
            $this->modelCriterias[$name] = $arguments;
        } else {
            Throw new \Exception('Criteria '.$name.' does not exist.');
        }
        return $this;
    }

    public function removeCriteria($name)
    {
        if (isset($this->modelCriterias[$name])) {
            unset($this->modelCriterias[$name]);
        }

        return $this;
    }

    public function __call($name, $arguments)
    {
        if (strpos($name, 'add') !== false) {
            $criteria = str_replace('add', '', $name);
            $this->addCriteria($criteria, $arguments);
        } else if (strpos($name, 'remove') !== false) {
            $criteria = str_replace('remove', '', $name);
            $this->removeCriteria($criteria);
        } else {
            Throw new \Exception('Method '.$name.' does not exist.');
        }

        return $this;
    }
}