#### Model Criteria usage example:

```php
class ModelCriteria extends Frogg\Model\Criteria
{
    /**
     * @return \Phalcon\Mvc\Model\Criteria
     */
    public function ofUser($id)
    {
        return $this->where('user_id = '.$id);
    }
}
```

```php
$result = Model::query()->ofUser(1)->where('x' > 'y')->execute();
```

#### Global Criteria usage example:

```php
class Model extends Frogg\Model
{
    /**
     * @return ModelCriteria
     */
    public static function query(DiInterface $dependencyInjector = null)
    {
        return parent::query($dependencyInjector)->addSoftDelete();
    }
}
```

```php
// this result applies softDelete for default, so all 'deleted = 1' results will be filtered 
// (this filter is added at the end of the query)
$result = Model::query()->where('x' > 'y')->execute();

// but you can remove a global criteria calling removeCriteriaName
$resultWithDeleted = Model::query()->removeSoftDelete()->where('x' > 'y')->execute();
// or for this specific method, we have an alias
$resultWithDeleted = Model::query()->withDeleted()->where('x' > 'y')->execute();
```

#### Debugg tip

You can get some infos like:
```php
$builder         = Model::query()->where('x' > 'y');
$phql            = $builder->getPhql();
$buildedQuery    = $builder->getQuery();
$criterias       = $builder->getActiveCriterias();
```
