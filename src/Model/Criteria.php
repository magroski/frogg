<?php
/**
 * Created by PhpStorm.
 * User: Alexandre
 * Date: 29/09/2017
 * Time: 13:47
 */

namespace Frogg\Model;

use Frogg\Exception\DuplicatedBindException;
use Phalcon\Mvc\Model as PhalconModel;
use Phalcon\Mvc\ModelInterface;

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
 * @method self addSoftDelete(string $column = 'deleted', int $activeValue = 0) add soft delete criteria to the query
 * @method self removeSoftDelete() removes soft delete criteria from the criteriaQueue
 */
class Criteria extends PhalconModel\Criteria
{
    /**
     * @var array<string>
     */
    private   array $modelCriterias = [];
    protected string $alias;

    /**
     * removes soft deleted entries from the result.
     *
     * @param string $column
     * @param int    $activeValue
     *
     * @internal param $add
     *
     */
    public function softDelete($column = 'deleted', $activeValue = 0)  : PhalconModel\CriteriaInterface
    {
        return $this->andWhere($column . '=' . $activeValue);
    }

    /**
     * sugar sintax to call instances using their classNames other than his aliases
     * eg:
     *
     * $dripCandidates = CandidateOpening::query()
     *       ->joinOpening()
     *       ->joinExternalReference()
     *       ->columns([CandidateOpening::class, ExternalReference::class])
     *       ->execute();
     *
     * @param array<string>|string $columns
     */
    public function columns($columns) : PhalconModel\CriteriaInterface
    {
        if (!is_array($columns)) {
            return parent::columns($columns);
        }

        $columns = array_map(function ($column) {
            if (preg_match("/\.\*/", $column)) {
                return $column;
            }

            if ($column == $this->getModelName()) {
                return $this->getAlias() . '.*';
            }

            $joins = $this->createBuilder()->getJoins();

            foreach ($joins as $join) {
                if ($join[0] == $column) {
                    if (!empty($join[2])) {
                        return $join[2] . '.*';
                    }
                }
            }

            return $column;
        }, $columns);

        return parent::columns($columns);
    }

    /**
     * alias to make more sense when calling it on query building.
     *
     * @return \Phalcon\Mvc\Model\CriteriaInterface
     */
    public function withDeleted()
    {
        return $this->removeSoftDelete();
    }

    public function createBuilder() : PhalconModel\Query\BuilderInterface
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

    public function execute() : PhalconModel\ResultsetInterface
    {
        foreach ($this->modelCriterias as $criteria => $value) {
            $method = lcfirst($criteria);
            $this->$method(...$value);
        }

        $builder = $this->createBuilder();
        $builder->from([$this->getAlias() => $this->getModelName()]);

        return $builder->getQuery()->execute();
    }

    /**
     * @return array<string>
     */
    public function getSql() : array
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

    public function getPhql() : string
    {
        return $this->createBuilder()->getPhql();
    }

    public function getQuery() : PhalconModel\QueryInterface
    {
        return $this->createBuilder()->getQuery();
    }

    /**
     * @return string[]
     */
    public function getActiveCriterias() : array
    {
        return $this->modelCriterias;
    }

    /**
     * @param string|false $conditions
     * @param mixed $bindParams
     * @param mixed $bindTypes
     */
    public function findFirst($conditions = false, $bindParams = null, $bindTypes = null) : ?ModelInterface
    {
        $this->limit(1);
        if ($conditions) {
            $this->andWhere($conditions, $bindParams, $bindTypes);
        }

        return $this->execute()->getFirst();
    }

    /**
     * @param mixed        $value
     */
    public function findFirstBy(string $column, $value) : ?ModelInterface
    {
        return $this->findFirst($this->getAlias() . '.' . $column . ' = :value:', ['value' => $value]);
    }

    /**
     * @param int|string $id
     */
    public function findFirstById($id) : ?ModelInterface
    {
        return $this->findFirstBy('id', $id);
    }

    public function count(string $column = '*') : int
    {
        return (int)$this->columns('count(' . $column . ') as total')->execute()->getFirst()->total;
    }

    /**
     * @deprecated
     */
    private function parentExecute() : PhalconModel\ResultsetInterface
    {
        return parent::execute();
    }

    /**
     * Defaults merge to true on bind params
     * @param array<mixed> $bindParams
     */
    public function bind(array $bindParams, bool $merge = false) : PhalconModel\CriteriaInterface
    {
        return parent::bind($bindParams, $merge);
    }

    /**
     * Add merge feature to bindTypes (lazy phalcon developers should made that) and defaults it to true.
     * Also add duplicated bind check.
     *
     * eg: given a criteria with an 'alreadyAddedBind' in an previously `andWhere` call. When you try:
     * $criteria->andWhere('column = :alreadyAddedBind:', ['alreadyAddedBind' => 'other value'])
     * it will @throws DuplicatedBindException;
     *
     * but if you want to reassign this bind to another value, you can skip this check using a bind type 'skipBindCheck' = true:
     * $criteria->andWhere('column = :alreadyAddedBind:', ['alreadyAddedBind' => 'other value'], ['skipBindCheck' => true])
     *
     * I'm not proud of it, but some times we will need to skip it and we can't add more
     * parameters to this function cuz it's interfaced...
     *
     *
     * @param array<mixed> $bindTypes
     */
    public function bindTypes(array $bindTypes) : PhalconModel\CriteriaInterface
    {
        if (is_array($bindTypes)) {
            $params = $this->getParams();
            if (isset($params['bind'])) {
                if (!(is_array($bindTypes) && array_key_exists('skipBindCheck', $bindTypes))) {
                    foreach ($bindTypes as $bind => $value) {
                        if (array_key_exists($bind, $params['bind'])) {
                            throw new DuplicatedBindException($bind);
                        }
                    }
                }
            }
        }

        return parent::bindTypes($bindTypes);
    }

    /**
     * @param array<mixed> $arguments
     *
     * @return $this
     * @throws \Exception
     */
    public function addCriteria(string $name, array $arguments = [])
    {
        if (method_exists($this, $name)) {
            $this->modelCriterias[$name] = $arguments;
        } else {
            Throw new \Exception('Criteria ' . $name . ' does not exist.');
        }

        return $this;
    }

    public function removeCriteria(string $name) : self
    {
        if (isset($this->modelCriterias[$name])) {
            unset($this->modelCriterias[$name]);
        }

        return $this;
    }

    public function getAlias() : string
    {
        return $this->alias;
    }

    public function setAlias(string $alias) : self
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @param string $name
     * @param mixed $arguments
     */
    public function __call($name, $arguments) : self
    {
        if (strpos($name, 'add') !== false) {
            $criteria = str_replace('add', '', $name);
            $this->addCriteria($criteria, $arguments);
        } else {
            if (strpos($name, 'remove') !== false) {
                $criteria = str_replace('remove', '', $name);
                $this->removeCriteria($criteria);
            } else {
                Throw new \Exception('Method ' . $name . ' does not exist.');
            }
        }

        return $this;
    }

    /**
     * Apply $filters for query
     *
     * @param array<string,mixed> $filters
     * @param bool  $strict Exception return if property not exists on model
     *
     * @return $this
     * @throws \Exception
     */
    public function applyFilters(array $filters, bool $strict = false)
    {
        foreach ($filters as $filterName => $filterValue) {
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
