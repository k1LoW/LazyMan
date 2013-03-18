# LazyMan: Lazy worker plugin for CakePHP

[![Build Status](https://travis-ci.org/k1LoW/LazyMan.png?branch=master)](https://travis-ci.org/k1LoW/LazyMan)

lazy...lazy...lazy...lazy...lazy...

## Requirement

- CakePHP >=2.1
- PHP >=5.3
- [PHP Cron Expression Parser](https://github.com/mtdowling/cron-expression)

## Usage

### [Lazy interval style] Clear old (past 2 days) cache every "about" 24 hours

        $lazy = new LazyMan('cache_clear');
        $lazy
            ->addJob('LazyMan::sweep', array(TMP . 'cache', '.*', 2 * 24 * 60 * 60))
            ->doJob(24 * 60 * 60);

### [Lazy cron style] Create file "about" 1st day 3:30 of every month

        $lazy = new LazyMan('create_file');
        $lazy
            ->addJob(function() {touch(TMP . 'lazytest.lock');}, array())
            ->doJob('30 3 1 * *');

## Why "about"

Because, LazyMan is driven by access.

## Lisence

under the MIT Lisence
