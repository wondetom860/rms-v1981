<?php

/** @var \yii\web\View  $this
 * 
 */
/* @var $content string */

use yii\helpers\Html;
use frontend\assets\AppAsset;

AppAsset::register($this);

$this->title = isset($_SESSION['report']) ? $_SESSION['report'] : 'Report_' . rand(999, 100000);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<style>
    .diagonal-text span {
        transform: rotate(-50deg);
        white-space: nowrap;
        position: absolute;
        top: 30%;
        left: 5%;
        text-align: center;
        font-size: 3.75em;
        font-style: italic;
        font-weight: bold;
        font-family: 'Courier New', Courier, monospace;
        opacity: 0.1;
    }
    .watermark img {
        white-space: nowrap;
        position: absolute;
        top: 38%;
        left: 39%;
        text-align: center;
        font-size: 3.75em;
        font-style: italic;
        font-weight: bold;
        font-family: 'Courier New', Courier, monospace;
        opacity: 0.21;
    }
</style>

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<body>
    <?php $this->beginBody() ?>

    <div class="wrap" style="border: 1px dashed black;">
        <div class="header" style="background-color: #FFD; padding: 2px;">
            <?= $this->render('__header') ?>
        </div>
        <hr>
        <div class="container-fluid" style="font-size: 8pt;">
            <div class="col-md-12" style="font-size: 9pt;">
            <div class="watermark">
                <img src="/logo/etdu.jpeg" alt="University Logo" style="width: 230px; height: auto;">
                <?= $content ?>
            </div>
        </div>
        </div>
    </div>
    <footer  style="display: none">
        <h3 style="font-size: 8pt; text-align: center; border-top: 1px solid black; bottom: 1px; padding: 4px 10px; margin-top: 10px;" class="aler-info">
            <?= Yii::t('rms', Yii::$app->params['project']) . ' - ' . Yii::t('rms', Yii::$app->params['client']) . ' ' . Yii::t('rms', Yii::$app->params['college'])  ?>
            <span class="pull-right" style="font-size: 9pt; font-style: italic;"><?= 'Date: ' . date('r') ?></span>
        </h3>
    </footer>
    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>