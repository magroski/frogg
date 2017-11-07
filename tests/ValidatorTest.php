<?php
/**
 * Created by PhpStorm.
 * User: magroski
 * Date: 07/11/17
 * Time: 16:50
 */

use Frogg\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{

    public function testValidate()
    {
        $this->assertTrue(Validator::validate(Validator::V_NAME, 'Peter'));
        $this->assertFalse(Validator::validate(Validator::V_NAME, 'M4r1o'));
        $this->assertTrue(Validator::validate(Validator::V_EMAIL, 'valid@email.com'));
        $this->assertFalse(Validator::validate(Validator::V_EMAIL, 'inv@lid@email.!com'));
    }

    public function testIsUTF8()
    {
        $this->assertTrue(Validator::isUTF8('This is UTF-8 encoded'));
        $this->assertFalse(Validator::isUTF8("\xc3\x28"));
    }

    public function testSanitize()
    {
        $this->assertEquals('&lt; ola', Validator::sanitize('< <?php?>ola   '));
        $this->assertEquals('alert(&#039;oi&#039;);', Validator::sanitize('<script>alert(\'oi\');</script>'));
    }

    public function testSanitizeUrl()
    {
        $this->assertEquals('mydomain.com', Validator::sanitizeUrl('https://mydomain.com/'));
        $this->assertEquals('mydomain.com/nice', Validator::sanitizeUrl('http://mydomain.com/nice/'));
        $this->assertEquals('www.mydomain.com', Validator::sanitizeUrl('http://www.mydomain.com'));
    }

}