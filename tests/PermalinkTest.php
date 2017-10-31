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

    public function testCreateSlugWithPrefix()
    {
        $permalink = new Permalink('Slug');
        $permalink->setPrefix('prefixed');
        $this->assertEquals('prefixedslug', $permalink->create());
    }

    public function testCreateSlugWithSuffix()
    {
        $permalink = new Permalink('Slug');
        $permalink->setSuffix('suffix');
        $this->assertEquals('slugsuffix', $permalink->create());
    }

    public function testStaticCreateSlug()
    {
        $this->assertEquals('metalslug', Permalink::createSlug('MetalSlug'));
    }

}