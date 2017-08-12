<?php
/**
 * @copyright 2012 Anthon Pang
 * @license MIT
 */

namespace VIPSoft\Unzip\Tests;

use VIPSoft\Unzip\Unzip;

/**
 * Unzip test class
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 */
class UnzipTest extends \PHPUnit_Framework_TestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if (!class_exists('ZipArchive', false)) {
            $this->markTestSkipped('ZipArchive class does not exist');
        }

        clearstatcache();
    }

    /**
     * Normal case where file is relative to current directory
     */
    public function test_relativePath()
    {
        $extractDir = __DIR__ . '/fixtures/tmp/';
        $test = 'relative';
        $filename = __DIR__ . '/fixtures/'.$test.'.zip';

        $unzip = new Unzip();

        $res = $unzip->extract($filename, $extractDir);

        $this->assertEquals(1, count($res));
        $this->assertFileExists($extractDir . $test . '.txt');
        $this->assertFileNotExists(dirname(__FILE__) . '/' . $test . '.txt');
        $this->assertFileNotExists(dirname(__FILE__) . '/../../tests/' . $test . '.txt');

        unlink($extractDir . $test . '.txt');
    }

    /**
     * .zip file contains a file which attempts to navigate to a parent directory (i.e., '..')
     */
    public function test_relativePathAttack()
    {
        $extractDir = __DIR__ . '/fixtures/tmp/';
        $test = 'zaatt';
        $filename = __DIR__ . '/fixtures/'.$test.'.zip';

        $unzip = new Unzip();

        try {
            $res = $unzip->extract($filename, $extractDir);
            $this->fail();
        } catch (\Exception $e) {
            $this->assertEquals('Invalid filename path in zip archive', $e->getMessage());
        }

        $this->assertFalse(isset($res));
        $this->assertFileNotExists($extractDir . $test . '.txt');
        $this->assertFileNotExists($extractDir . '../' . $test . '.txt');
        $this->assertFileNotExists(dirname(__FILE__) . '/' . $test . '.txt');
        $this->assertFileNotExists(dirname(__FILE__) . '/../' . $test . '.txt');
        $this->assertFileNotExists(dirname(__FILE__) . '/../../' . $test . '.txt');
    }

    /**
     * .zip file contains a file with an absolute path
     */
    public function test_absolutePathAttack()
    {
        $extractDir = __DIR__ . '/fixtures/tmp/';
        $test = 'zaabs';
        $filename = __DIR__ . '/fixtures/'.$test.'.zip';

        $unzip = new Unzip();

        try {
            $res = $unzip->extract($filename, $extractDir);
            $this->fail();
        } catch (\Exception $e) {
            $this->assertEquals('Invalid filename path in zip archive', $e->getMessage());
        }

        $this->assertFalse(isset($res));
        $this->assertFileNotExists($extractDir . $test . '.txt');
        $this->assertFileNotExists(dirname(__FILE__) . '/' . $test . '.txt');
    }
}
