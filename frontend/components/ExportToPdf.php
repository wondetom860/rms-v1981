<?php

/**
 * Class used for export data in PDF Formate.
 * 
 * @package EduSec.components 
 */

namespace frontend\components;

use Yii;
use yii\base\Component;
use common\models\AuthOrg8n;
use yii\helpers\Html;
use Mpdf\Mpdf as mPDF;

// use yii\mpdf\mpdf\src\Mpdf as mPDF;

class ExportToPdf extends Component
{
	public function exportData($title = '', $filename = 'Pdf File', $content = NULL)
	{
		$mpdf = new mPDF([
			'mode' => 'utf-8',
			'format' => 'A4',
			'orientation' => 'P'
		]);
		$mpdf->autoScriptToLang = true;
		$mpdf->autoLangToFont = true;
		$org = AuthOrg8n::find()->asArray()->one();
		// $src = Yii::$app->urlManager->createAbsoluteUrl('frontend/web/uploads/'.CLIENT_LOGO);
		$org_image = Html::img('uploads/' . CLIENT_UNIV_LOGO, ['alt' => 'No Image', 'width' => 90, 'height' => 100]);
		$org_name = $org['name'];
		$org_add = $org['phone1'] . ($org['phone2'] ? '/' : '') . $org['phone2'];

		// $mpdf->SetHTMLHeader('<table style="border-bottom:1.6px solid #999998;border-top:hidden;border-left:hidden;border-right:hidden;width:100%;"><tr style="border:hidden"><td vertical-align="center" style="width:35px;border:hidden" align="left">'.$org_image.'</td><td style="border:hidden;text-align:left;color:#555555;"><b style="font-size:22px;">'.$org_name.'</b><br/><span style="font-size:10.2px">'.$org_add.'</td></tr></table>');
		// $stylesheet = file_get_contents('css/pdf.css'); // external css
		// $mpdf->WriteHTML($stylesheet,0);
		// $mpdf->WriteHTML('<watermarkimage src='.$src.' alpha="0.33" size="250,930"/>');
		// $mpdf->showWatermarkImage = true;
		$html = '
			<html>
			<head>
			<style>
				@page {
				size: auto;
				odd-header-name: html_MyHeader1;
				odd-footer-name: html_MyFooter1;
				}

				@page chapter2 {
					odd-header-name: html_MyHeader2;
					odd-footer-name: html_MyFooter2;
				}

				@page noheader {
					odd-header-name: _blank;
					odd-footer-name: _blank;
				}

				div.chapter2 {
					display: block;
					font-size: 9pt;
					padding-top: 70px;
					page-break-before: no-break;
					page: chapter2;
				}

				div.noheader {
					page-break-before: always;
					page: noheader;
				}
			</style>
			</head>
			<body>
				<htmlpageheader name="MyHeader1">
					<div style="text-align: right; border-bottom: 1px solid #000000; font-weight: bold; font-size: 10pt;">My document1</div>
				</htmlpageheader>

				<htmlpageheader name="MyHeader2">
					<div style="border-bottom: 1px solid #000000; font-weight: normal;  font-size: 12pt; background-color:#ACE;">
						<table width="100%" style="vertical-align: middle; font-family: times; font-size: 14pt; color: #000000; font-weight: bold; font-style: normal;">
							<tr>
								<td width="33%" align="center" style="font-weight: bold; font-style: normal;">' . $org_image . '</td>
								<td width="77%" style="text-align: center; ">
									<h4 style="transform:uppercase; font-size:1.1em; ">' . $org_name . '</h4><br>
									<h6 style="padding-top:15px;text-align: center;">' . $org_add . '</h6>
								</td>
							</tr>
						</table>
					</div>
				</htmlpageheader>

				<htmlpagefooter name="MyFooter1">
					<table width="100%" style="vertical-align: middle; font-family: times; font-size: 8pt; color: #000000; font-weight: bold; font-style: italic;">
						<tr>
							<td width="33%"><span style="font-weight: bold; font-style: italic;">{DATE j-m-Y}</span></td>
							<td width="33%" align="center" style="font-weight: bold; font-style: italic;">{PAGENO}/{nbpg}</td>
							<td width="33%" style="text-align: right; ">My document</td>
						</tr>
					</table>
				</htmlpagefooter>

				<htmlpagefooter name="MyFooter2">
					<table width="100%" style="vertical-align: bottom; border-top: 1px solid black; font-family: serif; font-size: 8pt; color: #000000; font-weight: bold; font-style: italic;">
						<tr>
							<td width="33%"><span style="font-weight: bold; font-style: italic;">' . $org_name . '</span></td>
							<td width="33%" align="center" style="font-weight: bold; font-style: italic;">{PAGENO}/{nbpg}</td>
							<td width="33%" style="text-align: right; ">{DATE j-m-Y}</td>
						</tr>
					</table>
				</htmlpagefooter>

				<div class="chapter2" style="font-size: 9pt;">' . $content . '</div>

			</body>
			</html>';
		// $arr = [
		//   'odd' => [
		//     'L' => [
		//       'content' => $title,
		//       'font-size' => 10,
		//       'font-style' => 'B',
		//       'font-family' => 'serif',
		//       'color'=>'#27292b'
		//     ],
		//     'C' => [
		//       'content' => 'Page - {PAGENO}/{nbpg}',
		//       'font-size' => 10,
		//       'font-style' => 'B',
		//       'font-family' => 'serif',
		//       'color'=>'#27292b'
		//     ],
		//     'R' => [ 
		//       'content' => 'Printed @ {DATE j-m-Y}',
		//       'font-size' => 10,
		//       'font-style' => 'B',
		//       'font-family' => 'serif',
		//       'color'=>'#27292b'
		//     ],
		//     'line' => 1,
		//   ],
		//   'even' => []
		// ];
		// $mpdf->SetFooter($arr);
		// $mpdf->WriteHTML('<sethtmlpageheader name="MyHeader2" page="ALL" value="on" show-this-page="1">');
		$response = Yii::$app->response;
		$response->format = \yii\web\Response::FORMAT_RAW;
		$headers = Yii::$app->response->headers;
		$headers->add('Content-Type', 'application/pdf');

		$mpdf->WriteHTML($html);
		$mpdf->Output($filename . '.pdf', "I");

	}
}

?>