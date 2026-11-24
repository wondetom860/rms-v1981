<?php

namespace frontend\modules\Reassessment;

/**
 * Reassessment module definition class
 */
class Reassessment extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'frontend\modules\Reassessment\controllers';

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
                        'roles' => ['Admin', 'AcademicDean', 'Registrar', 'DepartmentHead','Instructor'],
                    ],
                    // [
                    //     'actions' => ['index', 'view'],
                    //     'allow' => true,
                    //     // Allow  admins to create, update, and delete, and view (index)
                    //     'roles' => ['DepartmentHead'],
                    // ],
                ]
            ]
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
