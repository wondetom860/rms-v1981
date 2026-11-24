<?php

/** @var \yii\web\View  $this
 * 
 * @var $content string */

use yii\helpers\Html;
use frontend\assets\AppAsset;

AppAsset::register($this);

$this->title = isset($_SESSION['report']) ? $_SESSION['report'] : 'Report.';
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<style>
    .diagonal-text2 span {
        transform: rotate(-50deg);
        white-space: nowrap;
        position: absolute;
        top: 40%;
        left: 0.9%;
        text-align: center;
        font-size: 3.75em;
        font-style: italic;
        font-weight: bold;
        font-family: 'Courier New', Courier, monospace;
        opacity: 0.1;
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

    <div class="wrap">
        <center>
            <img src="/uploads/<?= CLIENT_LOGO ?>" height="80" width="80" alt="Logo" style="display: inline;">
            <h3 style="text-align: center;">
                <?= strtoupper(Yii::t('hrms', Yii::$app->params['client'])) . '<br> ' . strtoupper(Yii::t('hrms', Yii::$app->params['college'])) ?>
            </h3>
        </center>
        <div class="container-fluid" style="font-size: 10pt;">
            <div class="diagonal-text2">
                <span>
                    <?=
                    Yii::t('rms', Yii::$app->params['client']) . '<br>' .
                        Yii::t('rms', Yii::$app->params['college']) . '<br>' .
                        Yii::t('rms', Yii::$app->params['project'])
                    ?>
                </span>
            </div>
            <?= $content ?>
        </div>
    </div>
    <footer style="display: none">
        <h3 style="font-size: 8pt; text-align: center; border-top: 1px solid black; bottom: 1px; " class="aler-info"><?= Yii::t('hrms', Yii::$app->params['project']) . ' - ' . Yii::t('hrms', Yii::$app->params['client']) . ' ' . Yii::t('hrms', Yii::$app->params['college'])  ?><span class="pull-right" style="font-size: 9pt; font-style: italic;"><?= 'Date: ' . date('r') ?></span></h3>
    </footer>
    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>