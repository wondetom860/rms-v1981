<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Admission;

/**
 * AdmissionSearch represents the model behind the search form about `backend\models\Admission`.
 */
class AdmissionSearch extends Admission
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'admission_status', 'admission_created_by', 'admission_updated_by'], 'integer'],
            [['admission_name', 'admission_created_at', 'admission_updated_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Admission::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'admission_status' => $this->admission_status,
            'admission_created_by' => $this->admission_created_by,
            'admission_created_at' => $this->admission_created_at,
            'admission_updated_by' => $this->admission_updated_by,
            'admission_updated_at' => $this->admission_updated_at,
        ]);

        $query->andFilterWhere(['like', 'admission_name', $this->admission_name]);

        unset($_SESSION['exportData']);
        $_SESSION['exportData'] = $dataProvider;
        return $dataProvider;
    }

    public static function getExportData() {
        $data = [
            'data'=>$_SESSION['exportData'],
            'fileName'=>'Admission-List', 
            'title'=>'Admission Report',
            'exportFile'=>'@frontend/views/admission/AdmissionExportPdfExcel',
        ];

        return $data;
    }

    private function dbDateSearch($value)
    {
        if($value != "" && preg_match('/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}$/', $value,$matches))
            return date("Y-m-d",strtotime($matches[0]));
        else
            return $value;
    }

}