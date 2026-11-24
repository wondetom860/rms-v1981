<?php

namespace frontend\modules\AuthOrg8n;

/**
 * AuthOrg8n module definition class
 */
class AuthOrg8n extends \yii\base\Module
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
               'class' => \yii\filters\AccessControl::className(),
               // We will override the default rule config with the new AccessRule class
               'ruleConfig' => [
                   'class' => \yii\filters\AccessRule::className(),
               ],
               'rules' => [
                   [
                       'actions' => [],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Admin',],
                   ],
               ]
           ]
        ];
    }
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'frontend\modules\AuthOrg8n\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
