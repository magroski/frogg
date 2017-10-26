<?php

use Frogg\Permalink;
use PHPUnit\Framework\TestCase;

/**
 * Created by PhpStorm.
 * User: magroski
 * Date: 26/10/17
 * Time: 17:48
 */
class PermalinkTest extends TestCase
{

    public function testCreateSlug()
    {
        $permalink = new Permalink('My Name');
        $this->assertEquals('my-name', $permalink->create());
    }

    public function testCreatSlugWithPrefix()
    {
        $permalink = new Permalink('Slug');
        $permalink->setPrefix('prefixed');
        $this->assertEquals('prefixedslug', $permalink->create());
    }

}