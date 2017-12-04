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
    private   $modelCriterias = [];
    protected $alias;

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

    public function createBuilder()
    {
        if (method_exists(parent::class, 'createBuilder')) {
            return parent::createBuilder();
        } else {
            $params = $this->getParams();
            /** @var PhalconModel\Manager $modelsManager */
            $modelsManager = $this->getDI()->get('modelsManager');

            return $modelsManager->createBuilder($params)->from($this->getModelName());
        }
    }

    public function execute()
    {
        foreach ($this->modelCriterias as $criteria => $value) {
            $method = lcfirst($criteria);
            $this->$method(...$value);
        }

        $builder = $this->createBuilder();
        $builder->from([$this->getAlias() => $this->getModelName()]);

        return $builder->getQuery()->execute();
    }

    public function getSql(): array
    {
        foreach ($this->modelCriterias as $criteria => $value) {
            $method = lcfirst($criteria);
            $this->$method(...$value);
        }

        $builder = $this->createBuilder();
        $builder->from([$this->getAlias() => $this->getModelName()]);

        return $builder->getQuery()->getSql();
    }

    /**
     * @return static
     */
    public function clone()
    {
        return clone $this;
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

    public function findFirst($conditions = false, $bindParams = null, $bindTypes = null)
    {
        if($conditions){
            $this->andWhere($conditions, $bindParams, $bindTypes);
        }

        return $this->execute()->getFirst();
    }

    public function findFirstBy($column, $value)
    {
        return $this->findFirst($this->getAlias().'.'.$column.' = :value:', ['value' => $value]);
    }

    public function findFirstById($id)
    {
        return $this->findFirstBy('id', $id);
    }

    public function count($column = '*')
    {
        return $this->columns('count('.$column.') as total')->execute()->getFirst()->total;
    }

    /**
     * @deprecated
     */
    private function parentExecute()
    {
        return parent::execute();
    }

    /**
     * Defaults merge to true on bind params
     *
     * @param array $bindParams
     * @param bool  $merge
     *
     * @return PhalconModel\Criteria|void
     */
    public function bind(array $bindParams, $merge = true)
    {
        parent::bind($bindParams, $merge);
    }

    /**
     * Add merge feature to bindTypes (lazy phalcon developers should made that) and defaults it to true
     *
     * @param array $bindTypes
     * @param bool  $merge
     *
     * @return PhalconModel\Criteria|void
     */
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
        if (method_exists($this, $name)) {
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

    public function getAlias()
    {
        return $this->alias;
    }

    public function setAlias($alias)
    {
        $this->alias = $alias;

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

    /**
     * Apply $filters for query
     *
     * @param array $filters
     * @param bool  $strict Exception return if property not exists on model
     *
     * @return $this
     * @throws \Exception
     */
    public function applyFilters(array $filters, bool $strict = false)
    {
        foreach ($filters ?? [] as $filterName => $filterValue) {
            if ($filterValue === null) {
                continue;
            }

            if (!property_exists($this->getModelName(), $filterName)) {
                if ($strict) {
                    throw new \Exception("Param $filterName not found for class {$this->getModelName()}");
                }
                continue;
            }

            $this->andWhere("{$this->getAlias()}.{$filterName} = :{$filterName}:", [$filterName => $filterValue]);
        }

        return $this;
    }
}
