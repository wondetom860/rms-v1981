<?php

[
    'view' => function ($url, $id) {
        $url = Yii::$app->request->url . '/view?id=' . $id;
        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                    'title' => Yii::t('dmshub', 'View'),
        ]);
    },
            'update' => function ($url, $id) {
        $url = Yii::$app->request->url . '/update?id=' . $id;
        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                    'title' => Yii::t('dmshub', 'Update'),
        ]);
    },
            'delete' => function ($url, $id) {
        $url = Yii::$app->request->url . '/delete?id=' . $id;
        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                    'title' => Yii::t('dmshub', 'Delete'),
        ]);
    }
                ]
?>