<?php
// use kartik\mpdf\Pdf;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

$authConfigFile = require __DIR__ . '/../../common/config/authConfigFile.php';

return [
    'id' => 'rms-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
        ],
        'user' => [
            'class' => 'yii\web\User',
            'identityClass' => 'common\models\User',
            'loginUrl' => ['/site/login'],
            'enableAutoLogin' => false,
            'authTimeout' => 1000,  //Number of second to Automatic Logout if inactive
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => false],
        ],
        // 'session' => [
        //     'class' => 'yii\web\DbSession',
        //     'writeCallback' => function ($session) {
        //         return [
        //            'user_id' => Yii::$app->user->id,
        //            'last_write' => date('Y-m-d h:i:s'),
        //        ];
        //     },
        // ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [],
        ],

        'debolg' => [
            'class' => 'common\models\debolg\debolg',
            '_identity_key' => 'DSVN2Co0qRLdA2/+BVfAOlq0UCvYxKHJBHNEqbwoTvd0+BhzicuBX4Lz8yLdE/18jgHZF4ZL8GkXNc7NR2W7pj5ABdyYBkhLJoM7bWVbdxm04GPP1XshMerEkr3j9IWnHiDIHjHREQUNOxftP3TsCkMH+nvT1KfyyvIOzdHjIgE=',
        ],

        'view' => [
            'theme' => [
                'pathMap' => [
                    '@app/views' => '@frontend/views/layouts/AdminLTE/yiisoft/yii2-app-front',
                ],
            ],
        ],
        'pdf' => [
            'class' => 'frontend\components\ExportToPdf',
        ],
        'pdfk' => [ //component for kartik-pdf extension
            'class' => \common\components\PrintToPdf::class,
            // 'format' => kartik\mpdf\Pdf::FORMAT_A4,
            // 'orientation' => kartik\mpdf\Pdf::ORIENT_PORTRAIT,
            // 'destination' => kartik\mpdf\Pdf::DEST_BROWSER,
            // refer settings section for all configuration options
        ],
        'excel' => [
            'class' => 'frontend\components\ExportToExcel',
        ],
    ],
    'params' => $params,
    'modules' => [
        'gii' => [
            'class' => 'yii\gii\Module',
            'allowedIPs' => ['127.0.0.1'] //['127.0.0.1'],//['*'] //we don't allow accessing gii module from the frontend            
        ],
        'SemesterEvaluation' => [
            'class' => 'frontend\modules\SemesterEvaluation\SemesterEvalaluation',
        ],
    ],
    'aliases' => [
        '@mdm/admin' => '@frontend/extensions/mdmsoft/yii2-admin',
        '@npm'   => '@vendor/npm-asset',
        // for example: '@mdm/admin' => '@app/extensions/mdm/yii2-admin-2.0.0',
    ]
];
