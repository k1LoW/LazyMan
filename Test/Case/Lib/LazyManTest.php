<?php
App::uses('LazyMan', 'LazyMan.Lib');

class LazyManTest extends CakeTestCase {

    /**
     * setUp
     *
     */
    public function setUp(){
        $settings = array('keyDir' => TMP . 'tests');

        // php-timecop
        timecop_travel(strtotime('2013-04-04 08:00:00'));

        $this->LazyMan = new LazyMan('test', $settings);
        $this->keyPath = TMP . 'tests' . DS . 'lazyman_test';
    }

    /**
     * tearDown
     *
     */
    public function tearDown(){
        unset($this->LazyMan);
        @unlink($this->keyPath);
    }

    /**
     * testCreateKeyFile
     *
     */
    public function testCreateKeyFile(){
        $this->LazyMan->doJob();
        $this->assertTrue(file_exists($this->keyPath));
    }

    /**
     * testDoJob
     *
     * jpn: 1回目のLazyMan::doJob()の実行から3s経過しないと2回目のLazyMan::doJob()は実行しない
     */
    public function testDoJob(){
        $testFile = TMP . 'tests' . DS . 'lazytest';
        $this->LazyMan
            ->addJob(function() {touch(TMP . 'tests' . DS . 'lazytest');}, array())
            ->doJob(3);
        $this->assertTrue(file_exists($testFile));

        unlink($testFile);

        $this->LazyMan
            ->addJob(function() {touch(TMP . 'tests' . DS . 'lazytest');}, array())
            ->doJob(3);
        $this->assertFalse(file_exists($testFile));
    }

    /**
     * testDoJobWith5s
     *
     * jpn: LazyMan::doJob()の更新から3s以上経過したので2回目のLazyMan::doJob()を実行する
     */
    public function testDoJobWith3s(){
        $testFile = TMP . 'tests' . DS . 'lazytest';
        $this->LazyMan
            ->addJob(function() {touch(TMP . 'tests' . DS . 'lazytest');}, array())
            ->doJob(3);
        $this->assertTrue(file_exists($testFile));

        unlink($testFile);

        timecop_travel(strtotime('2013-04-04 08:00:05'));

        $this->LazyMan
            ->addJob(function() {touch(TMP . 'tests' . DS . 'lazytest');}, array())
            ->doJob(3);
        $this->assertTrue(file_exists($testFile));

        unlink($testFile);
    }

    /**
     * testDoJobCron
     *
     * jpn: cronフォーマットを利用したインターバル設定
     */
    public function testDoJobCron(){
        timecop_travel(strtotime('2013-04-04 08:00:00'));

        $testFile = TMP . 'tests' . DS . 'lazytest';
        $this->LazyMan
            ->addJob(function() {touch(TMP . 'tests' . DS . 'lazytest');}, array())
            ->doJob();
        $this->assertTrue(file_exists($testFile));

        unlink($testFile);

        $this->LazyMan
            ->addJob(function() {touch(TMP . 'tests' . DS . 'lazytest');}, array())
            ->doJob('* * 1 * *');
        $this->assertFalse(file_exists($testFile));

        timecop_travel(strtotime('2013-05-01 08:00:00'));

        $this->LazyMan
            ->addJob(function() {touch(TMP . 'tests' . DS . 'lazytest');}, array())
            ->doJob('* * 1 * *');
        $this->assertTrue(file_exists($testFile));

        unlink($testFile);
    }

}