<?php

namespace App\Model\Table;

use Cake\ORM\Table;

class AssinaturasTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('assinaturas');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created_at' => 'new',
                    'updated_at' => 'always',
                ],
            ],
        ]);

        $this->belongsTo('Planos', [
            'foreignKey' => 'plano_id',
            'propertyName' => 'plano',
        ]);
    }
}
