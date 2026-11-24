<?php

namespace common\components;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use common\models\AuthOrg8n;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;
// use mPDF;
use \kartik\mpdf\Pdf as Pdf;
 
class PrintToPdf extends Component{

    public function generatePDF($content) {
        // get your HTML raw content without any layouts or scripts
        // $content = $this->renderPartial($__path);
        
        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE, 
            // A4 paper format
            'format' => Pdf::FORMAT_A4, 
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT, 
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER, 
            // your html content input
            'content' => $content,  
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting 
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px}', 
            // set mPDF properties on the fly
            'options' => [
                'title' => 'Krajee Report Title'
            ],
            // call mPDF methods on the fly
            'methods' => [ 
                'SetHeader'=>['Krajee Report Header'], 
                'SetFooter'=>['{PAGENO}'],
            ]
        ]);
        
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/pdf');

        // return $pdf->render(); 
        // return the pdf output as per the destination setting
        return $pdf->render(); 
    }

}


?>