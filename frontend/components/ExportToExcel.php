<?php

/**
 * Class used for export data in Excel Formate.
 * 
 * @package decris.components 
 */

namespace frontend\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use common\models\Organization;
use yii\helpers\Html;
use yii\web\Response;
use yii\web\NotFoundHttpException;

class ExportToExcel extends Component
{
	public function exportExcel($file, $fileName, $options)
	{
		if($file == NULL) {
			 throw new NotFoundHttpException('No Data Available for Export to Excel');
		}

		Yii::$app->response->sendContentAsFile($file ,$fileName,$options);
		Yii::$app->end();
	}
}

?>
