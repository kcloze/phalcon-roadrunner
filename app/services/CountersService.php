<?php

namespace MyApp\Services;

use MyApp\Models\Counters;
use MyApp\Services\ServiceBase;

class CountersService extends ServiceBase
{

    public function demo()
    {

        $this->logger->critical("service demo! ");
        $counters = new Counters();
        $result   = $counters->getByQuery();
        foreach ($result as $value) {
            var_dump($value->id);
            var_dump($value->name);
            var_dump($value->value);
        }

        $result = $counters->getByQueryBuilder();
        foreach ($result as $value) {
            var_dump($value->id);
            var_dump($value->name);
            var_dump($value->value);
        }
        $counters->findOrm();

    }

    public function getData()
    {
        $cacheKey = 'my.cache';

        $data = $this->cache->get($cacheKey);
        if ($data === null) {
            $data = array('kcloze', 'hello');
            $this->cache->save($cacheKey, $data, 3600);
        }
        var_dump($this->cache->get($cacheKey));
    }

}
