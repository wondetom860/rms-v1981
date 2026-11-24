<table style="width: 100%; text-align: center; vertical-align: bottom;">
    <tr>
        <td style="width: 30%; text-align: center; margin: auto;">
            <img src="/logo/<?= CLIENT_UNIV_LOGO ?>" height="77" width="80" alt="Logo" style="display: inline;">
        </td>
        <td style="width: 40%; text-align: center; vertical-align: bottom;">
            <p style="vertical-align: bottom; font-weight: bold; font-size: 12pt; font-stretch: expanded;">
                <?= strtoupper(Yii::t('rms',Yii::$app->params['client'])).'<br> '.strtoupper(Yii::t('rms',Yii::$app->params['college'])) ?>
            </p>
        </td>
        <td style="width: 30%; text-align: center; vertical-align: bottom; margin: auto;">
            <img src="/logo/<?= CLIENT_UNIV_LOGO ?>" height="77" width="80" alt="Logo" style="display: inline;">
        </td>
    </tr>
    <tr>
        <td colspan="3" style="text-align: center;">
            <span style="font-size: 7pt; font-style: italic;"><?= Yii::$app->AcademicCalendar->getCollegeAddress() ?></span>
        </td>
    </tr>
</table>


<!-- 2808214JQRI5 -->