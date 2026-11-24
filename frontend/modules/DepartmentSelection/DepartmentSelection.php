<?php

namespace frontend\modules\DepartmentSelection;

/**
 * DepartmentSelection module definition class
 */
class DepartmentSelection extends \yii\base\Module
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
               'class' => \yii\filters\AccessControl::class,
               // We will override the default rule config with the new AccessRule class
               'ruleConfig' => [
                   'class' => \yii\filters\AccessRule::class,
               ],
               'rules' => [
                   [
                       'actions' => [],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['@',],
                   ],
               ]
           ]
        ];
    }
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'frontend\modules\DepartmentSelection\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
