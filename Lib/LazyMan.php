<?php
App::uses('File', 'Utility');
if (file_exists(dirname(__FILE__) . '/../vendor/autoload.php')) {
    require_once dirname(__FILE__) . '/../vendor/autoload.php';
}

/**
 * LazyMan
 *
 *
 * @params
 */
class LazyMan {
    private $key;
    private $keyPath;
    private $interval;
    private $jobs = array();
    public $settings = array(
        'keyDir' => CACHE
    );

    public function __construct($key, $settings = array()){
        if (empty($key)) {
            throw new Exception('No $key');
        }
        $this->settings = array_merge($this->settings, (array)$settings);
        $this->key = $key;
        $this->keyPath = $this->settings['keyDir'] . DS . 'lazyman_' . $this->key;
    }

    /**
     * addJob
     *
     * @param $func, $args = array()
     */
    public function addJob($func, $args = array()){
        $this->jobs[] = array($func, $args);
        return $this;
    }

    /**
     * doJob
     *
     */
    public function doJob($interval = null){
        $f = new File($this->keyPath, false);
        if ($interval === null || !$f->exists()) {
            $this->_do();
            $data = $f->prepare(date('Y-m-d H:i:s'));
            $f->write($data);
            return $this;
        }
        if (is_numeric($interval)) {
            if ($f->lastChange() + $interval < time()) {
                $this->_do();
                $data = $f->prepare(date('Y-m-d H:i:s'));
                $f->write($data);
                return $this;
            }
            return $this;
        }
        if ($f->lastChange() < $this->parseCronFormat($interval)) {
            $this->_do();
            $data = $f->prepare(date('Y-m-d H:i:s'));
            $f->write($data);
            return $this;
        }
    }

    /**
     * parseCronFormat
     *
     * @param $interval
     */
    public function parseCronFormat($interval){
        if (!class_exists('Cron\CronExpression')) {
            throw new Exception('Not installed PHP Cron Expression Parser');
        }
        $cron = Cron\CronExpression::factory($interval);
        return $cron->getPreviousRunDate('+1 second')->format('U');
    }

    /**
     * _do
     *
     */
    private function _do(){
        foreach ($this->jobs as $job) {
            call_user_func_array($job[0], (array)$job[1]);
        }
    }

    /**
     * sweep
     *
     */
    public static function sweep($dir, $pattern, $past = 60){
        $folder = new Folder($dir);
        $files = $folder->find($pattern);
        foreach ($files as $fileName) {
            $file = new File($dir . DS . $fileName, true);
            if ($file->lastChange() + $past < time()) {
                $file->delete();
            }
        }
    }
}