<?php
/**
 * Created by PhpStorm.
 * User: magroski
 * Date: 02/10/17
 * Time: 12:14
 */

namespace Frogg\Model;

use Frogg\Exceptions\InvalidAttributeException;
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
    public function getAttribute(string $attributeName): array
    {
        if (!$this->count()) {
            return [];
        }

        $entries = $this->toArray();
        if (!array_key_exists($attributeName, $entries[0])) {
            throw new InvalidAttributeException($attributeName);
        }

        $values = array_map(function ($entry) use ($attributeName) {
            return $entry[$attributeName];
        }, $entries);

        return $values;
    }

    /**
     * Returns an array containing the id of each object in the ResultSet
     *
     * @return array
     * @throws InvalidAttributeException When the object has no 'id' field
     */
    public function getIds(): array
    {
        return $this->getAttribute('id');
    }

}