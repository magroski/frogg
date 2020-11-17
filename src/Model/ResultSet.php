<?php

namespace Frogg\Model;

use Frogg\Exception\InvalidAttributeException;
use Frogg\Model;
use Generator;
use Phalcon\Mvc\Model\Resultset\Simple;

class ResultSet extends Simple
{

    /**
     * Returns an array containing the values of a given attribute of each object in the ResultSet
     *
     * @param string $attributeName Attribute name
     *
     * @return array
     * @throws InvalidAttributeException When the attribute is not found on the object
     */
    public function getAttribute(string $attributeName) : array
    {
        if ($this->isEmpty()) {
            return [];
        }

        $entries = $this->toArray();
        if (!array_key_exists($attributeName, $entries[0])) {
            throw new InvalidAttributeException($attributeName);
        }

        return array_column($entries, $attributeName);
    }

    /**
     * Returns an array containing the values of the given attributes of each object in the ResultSet
     * in a key-value format.
     * Example: $users->getAttributes('name', 'age');
     * > [ ['name' => 'Peter', 'age' => 20], ['name' => 'Hilda', 'age' => 22] ];
     *
     * @param array ...$attributeNames A list of attribute names
     *
     * @return array
     * @throws InvalidAttributeException Thrown if one of the attributes is not found on the object
     */
    public function getAttributes(...$attributeNames) : array
    {
        if (func_num_args() === 0) {
            return [];
        }

        if ($this->isEmpty()) {
            return [];
        }

        $entries = $this->toArray();
        foreach ($attributeNames as $attributeName) {
            if (!array_key_exists($attributeName, $entries[0])) {
                throw new InvalidAttributeException($attributeName);
            }
        }

        return array_map(function ($entry) use ($attributeNames) {
            $filteredEntry = [];
            foreach ($attributeNames as $attributeName) {
                $filteredEntry[$attributeName] = $entry[$attributeName];
            }

            return $filteredEntry;
        }, $entries);
    }

    /**
     * Returns an array containing the query results grouped by a given attribute value.
     * Example: $persons->groupBy('name');
     * > [ 'Peter' => [entry1, entry2, ...], 'Anne' => [entry3], ...];
     *
     * @param string $attributeName Attribute name
     * @param bool   $asObject      Flag to decide if you want to fecth the entries as object or as key-value arrays
     *
     * @return array
     * @throws InvalidAttributeException When the attribute is not found on the object
     */
    public function groupBy(string $attributeName, $asObject = true) : array
    {
        if ($this->isEmpty()) {
            return [];
        }

        $entries = $this->toArray();
        if (!array_key_exists($attributeName, $entries[0])) {
            throw new InvalidAttributeException($attributeName);
        }

        $result = [];

        //This ResultSet is an Iterable, so when iterating over it each entry is an Object that extends Frogg\Model
        foreach ($this as $entry) {
            $groupKey = $entry->$attributeName;
            if (!array_key_exists($groupKey, $result)) {
                $result[$groupKey] = [];
            }
            $result[$groupKey][] = $asObject ? $entry : $entry->toArray();
        }

        return $result;
    }

    /**
     * Returns the ResultSet as an array contain instances of each entry original Model
     * @deprecated Versão antiga
     *
     * @return array
     */
    public function toObject() : array
    {
        if ($this->isEmpty()) {
            return [];
        }

        $skeleton = $this->_model;

        return $this->filter(function ($entry) use ($skeleton) {
            return Model::cloneResult($skeleton, $entry);
        });
    }

    /**
     * Returns the ResultSet as an array contain instances of each entry original Model, in iterable generator
     *
     * @return Generator
     */
    public function toGenerator(): Generator
    {
        if ($this->isEmpty()) {
            yield;
        }

        $skeleton = $this->_model;
        $this->rewind();

        // read loop (Model)
        while($this->valid()) {
            //toArray for single result, per row
            yield Model::cloneResult($skeleton, $this->current()->toArray());

            // if garbage enable, force cycle, free memory
            if (gc_enabled()) {
                gc_collect_cycles();
            }

            $this->next();
        }
    }

    /**
     * @return bool true if the ResultSet is empty
     */
    public function isEmpty() : bool
    {
        return $this->count() === 0;
    }

    /**
     * @return bool true if the ResultSet is not empty
     */
    public function isNotEmpty() : bool
    {
        return $this->count() > 0;
    }

    /**
     * Returns an array containing the id of each object in the ResultSet
     *
     * @return array
     * @throws InvalidAttributeException When the object has no 'id' field
     */
    public function getIds() : array
    {
        return $this->getAttribute('id');
    }

}
