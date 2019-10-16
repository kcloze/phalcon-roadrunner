<?php
namespace MyApp\Models;

//use MyApp\Models\Counters;
use Phalcon\Mvc\Model;

class Counters extends Model
{
    /**
     * This model is mapped to the table sample_cars
     */
    public function getSource()
    {
        return 'counters';
    }
    //手写sql
    public function getByQuery()
    {
        $sql      = "SELECT * FROM MyApp\Models\Counters";
        $counters = $this->modelsManager->executeQuery($sql);
        return $counters;
    }
    //mysql链式操作
    public function getByQueryBuilder()
    {
        $counters = $this->modelsManager->createBuilder()
            ->from('MyApp\Models\Counters')
            ->getQuery()
            ->execute();
        return $counters;
    }
    //orm 操作
    public function findOrm()
    {
        //简单查询
        $row = Counters::findFirst(1);
        if ($row) {
            var_dump($row->name);
        }

        //绑定参数
        // Query robots binding parameters with string placeholders
        $conditions = "name = :name: AND value = :value:";
        // Parameters whose keys are the same as placeholders
        $parameters = array(
            "name"  => "kcloze",
            "value" => "101",
        );
        // Perform the query
        $row = Counters::findFirst(
            array(
                $conditions,
                "bind" => $parameters,
            )
        );
        if ($row) {
            var_dump($row->name);
        } else {
            echo 'not find';
        }
    }

}
