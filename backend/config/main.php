<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'rms-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],

        'debolg'=>[
            'class'=>'common\models\debolg\debolg',
            '_identity_key'=>'DSVN2Co0qRLdA2/+BVfAOlq0UCvYxKHJBHNEqbwoTvd0+BhzicuBX4Lz8yLdE/18jgHZF4ZL8GkXNc7NR2W7pj5ABdyYBkhLJoM7bWVbdxm04GPP1XshMerEkr3j9IWnHiDIHjHREQUNOxftP3TsCkMH+nvT1KfyyvIOzdHjIgE=',
        ],
        
        'view' => [
            'theme' => [
                'pathMap' => [
                   '@app/views' => '@vendor/dmstr/yii2-adminlte-asset/example-views/yiisoft/yii2-app-back'
                ],
            ],
        ],
    ],
    'params' => $params,

    'modules'=>[
        'gii' => [
            'class' => 'yii\gii\Module',
            'allowedIPs' => ['127.0.0.1'], // adjust this to your needs  ?only local machine access this site - controller.
            ],
    ],
];
