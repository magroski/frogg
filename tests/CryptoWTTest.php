<?php
/**
 * Created by PhpStorm.
 * User: magroski
 * Date: 07/11/17
 * Time: 16:50
 */

use Frogg\Crypto\WT;
use PHPUnit\Framework\TestCase;

class CryptoWTTest extends TestCase
{

    public function testEncodeDecode()
    {
        define('ENCRYPTION_TYPE', 'AES-256-CBC');
        define('WT_IV', 'j52sc8ur');
        define('WT_KEY', 'as9d7#dQ&23yd!928dh*(d(DJ89dj1928(*~AD)A');

        $myObject = ['data' => 1, 'subdata' => ['entry' => 1]];

        $token   = WT::encode($myObject);
        $decoded = WT::decode($token);

        $this->assertEquals(1, $decoded->data);
        $this->assertEquals(1, $decoded->subdata->entry);
    }

}