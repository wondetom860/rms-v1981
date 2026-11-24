<?php

namespace mdm\admin;

if(!class_exists('yii\base\BaseObject')){
    class_alias('yii\base\Object', 'yii\base\BaseObject');
}

/**
 * Description of BaseObject
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 2.9
 */
class BaseObject extends \yii\base\BaseObject
{
    //put your code here
    /**
     * {@inheritdoc}
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
                       'roles' => ['Admin'],
                   ],
               ]
           ]
        ];
    }
}
