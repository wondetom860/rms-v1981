<?php

namespace frontend\modules\AcStEva;

/**
 * AcStEva module definition class
 */
class AcStEva extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'frontend\modules\AcStEva\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}


// ALTER TABLE `ase_evaluation_result` 
// ADD COLUMN `eval_point_rate` JSON NULL AFTER `average_percentage`;
