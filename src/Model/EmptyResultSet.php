<?php
/**
 * Created by PhpStorm.
 * User: magroski
 * Date: 11/12/17
 * Time: 15:29
 */

namespace Frogg\Model;

use Frogg\Model;

class EmptyResultSet extends ResultSet
{

    public function __construct()
    {
        parent::__construct([], new Model(), null);
    }

}