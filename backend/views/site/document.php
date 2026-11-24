<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
$this->title = \Yii::t('docvault','Home');
?>
<div class="site-index">

    <div class="jumbotron">
        <h1><?= Html::encode(Yii::$app->name) ?></h1>

        <p class="lead"><?= \Yii::t('docvault', 'Control Your Documents.') ?></p>

        <p><?= Html::a(\Yii::t('docvault', 'Upload New Document').' &raquo;', ['document/create'], ['class'=>'btn btn-lg btn-success']) ?></a></p>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4">
                <h2><?= Yii::t('docvault', 'Categories') ?></h2>

                <p><?= Yii::t('docvault', 'Manage Document Categories') ?><br />
                &nbsp;<br />
                &nbsp;</p>

                <p><?= Html::a(Yii::t('docvault', 'Manage Categories').' &raquo;', ['document-category/index'], ['class'=>'btn btn-primary']) ?></a></p>
            </div>
            <div class="col-lg-4">
                <h2><?= Yii::t('docvault', 'Documents') ?></h2>

                <p><?= Yii::t('docvault', 'Upload new documents.') ?><br />
                <?= Yii::t('docvault', 'View documents.') ?><br />
                <?= Yii::t('docvault', 'Check in, Check out documents.') ?></p>

                <p><?= Html::a(Yii::t('docvault', 'Manage Documents').' &raquo;', ['document/index'], ['class'=>'btn btn-primary']) ?></a></p>
            </div>
            <div class="col-lg-4">
                <h2><?= Yii::t('docvault', 'Users') ?></h2>

                <p><?= Yii::t('docvault', 'Manage application users.') ?><br />
                <?= Yii::t('docvault', 'Add new users.') ?><br />
                <?= Yii::t('docvault', 'Change user settings.') ?></p>

                <p><?= Html::a(Yii::t('docvault', 'Manage Users').' &raquo;', ['/user/admin'], ['class'=>'btn btn-primary']) ?></a></p>
            </div>
        </div>

    </div>
</div>
