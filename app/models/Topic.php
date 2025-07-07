<?php
namespace App\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Behavior\SoftDelete;
use Phalcon\Mvc\Model\Behavior\Timestampable;

class Topic extends Model
{
    public function initialize()
    {
        $this->setSource('topics');

        $this->addBehavior(new Timestampable([
            'beforeCreate' => [
                'field' => ['created_at', 'updated_at'],
                'format' => 'Y-m-d H:i:s'
            ],
            'beforeUpdate' => [
                'field' => 'updated_at',
                'format' => 'Y-m-d H:i:s'
            ]
        ]));

        $this->addBehavior(new SoftDelete([
            'field' => 'deleted_at',
            'value' => date('Y-m-d H:i:s')
        ]));
    }
}
