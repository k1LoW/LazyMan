<?php
App::uses('LazyMan', 'LazyMan.Lib');

class LazyManTest extends CakeTestCase {

    /**
     * setUp
     *
     */
    public function setUp(){
        $settings = array('keyDir' => TMP . 'tests');
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
        $this->LazyMan->lazyDo();
        $this->assertTrue(file_exists($this->keyPath));
    }

    /**
     * testLazyDo
     *
     * jpn: 1回目のLazyMan::lazyDo()の実行から3s経過しないと2回目のLazyMan::lazyDo()は実行しない
     */
    public function testLazyDo(){
        $testFile = TMP . 'tests' . DS . 'lazytest';
        $this->LazyMan
            ->addJob(function() {touch(TMP . 'tests' . DS . 'lazytest');}, array())
            ->lazyDo(3);
        $this->assertTrue(file_exists($testFile));

        unlink($testFile);

        $this->LazyMan
            ->addJob(function() {touch(TMP . 'tests' . DS . 'lazytest');}, array())
            ->lazyDo(3);
        $this->assertFalse(file_exists($testFile));
    }

    /**
     * testLazyDoWith5s
     *
     * jpn: LazyMan::lazyDo()の更新から3s以上経過したので2回目のLazyMan::lazyDo()を実行する
     */
    public function testLazyDoWith3s(){
        $testFile = TMP . 'tests' . DS . 'lazytest';
        $this->LazyMan
            ->addJob(function() {touch(TMP . 'tests' . DS . 'lazytest');}, array())
            ->lazyDo(3);
        $this->assertTrue(file_exists($testFile));

        unlink($testFile);

        sleep(5);

        $this->LazyMan
            ->addJob(function() {touch(TMP . 'tests' . DS . 'lazytest');}, array())
            ->lazyDo(3);
        $this->assertTrue(file_exists($testFile));

        unlink($testFile);
    }
}