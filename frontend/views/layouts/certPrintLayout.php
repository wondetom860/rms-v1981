<?php

/**
 *  @var \yii\web\View  $this
 * @var $content string 
 */

use yii\helpers\Html;
use frontend\assets\AppAsset;

AppAsset::register($this);

$this->title = isset($_SESSION['report']) ? $_SESSION['report'] : 'Report.';
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<style>
    .watermark img {
        /* transform: rotate(-50deg); */
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

    <div class="wrap">
        <div class="col-md-12" style="font-size: 9pt; margin-top: 10px;">
            <div class="watermark">
                <img src="/logo/etdu.jpeg" alt="University Logo" style="width: 230px; height: auto;">
            </div>
            <?= $content ?>
        </div>
    </div>
    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>