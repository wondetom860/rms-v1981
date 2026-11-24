<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\SemesterRegistration2;

/**
 * SemesterRegistration2Search represents the model behind the search form about `frontend\models\SemesterRegistration2`.
 */
class SemesterRegistration2Search extends SemesterRegistration2
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'semreg_std_relId', 'semreg_acadYear_relId', 'semreg_clslvl_relId', 'semreg_semester_relId', 'semreg_approved', 'semreg_approved_by', 'semreg_created_by', 'semreg_updated_by'], 'integer'],
            [['semreg_SGPA', 'semreg_CGPA'], 'number'],
            [['semreg_approved_at', 'semreg_created_at', 'semreg_updated_at'], 'safe'],
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
        $query = SemesterRegistration2::find();

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
            'semreg_std_relId' => $this->semreg_std_relId,
            'semreg_acadYear_relId' => $this->semreg_acadYear_relId,
            'semreg_clslvl_relId' => $this->semreg_clslvl_relId,
            'semreg_semester_relId' => $this->semreg_semester_relId,
            'semreg_SGPA' => $this->semreg_SGPA,
            'semreg_CGPA' => $this->semreg_CGPA,
            'semreg_approved' => $this->semreg_approved,
            'semreg_approved_by' => $this->semreg_approved_by,
            'semreg_approved_at' => $this->semreg_approved_at,
            'semreg_created_by' => $this->semreg_created_by,
            'semreg_updated_by' => $this->semreg_updated_by,
            'semreg_created_at' => $this->semreg_created_at,
            'semreg_updated_at' => $this->semreg_updated_at,
        ]);

        return $dataProvider;
    }
}
