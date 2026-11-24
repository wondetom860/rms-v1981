<?php

namespace frontend\modules\CourseAssessment\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\CourseAssessment\models\ContAssessCatagory;

/**
 * ContAssessCatagorySearch represents the model behind the search form of `frontend\modules\CourseAssessment\models\ContAssessCatagory`.
 */
class ContAssessCatagorySearch extends ContAssessCatagory
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'cat_acadYear_relId', 'cat_sem_relId', 'cat_coff_relId', 'cat_course_relId', 'cat_catagory_relId', 'cat_created_by', 'cat_updated_by'], 'integer'],
            [['cat_maxPoint'], 'number'],
            [['cat_created_at', 'cat_updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = ContAssessCatagory::find();

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
            'cat_acadYear_relId' => $this->cat_acadYear_relId,
            'cat_sem_relId' => $this->cat_sem_relId,
            'cat_coff_relId' => $this->cat_coff_relId,
            'cat_course_relId' => $this->cat_course_relId,
            'cat_catagory_relId' => $this->cat_catagory_relId,
            'cat_maxPoint' => $this->cat_maxPoint,
            'cat_created_at' => $this->cat_created_at,
            'cat_created_by' => $this->cat_created_by,
            'cat_updated_by' => $this->cat_updated_by,
            'cat_updated_at' => $this->cat_updated_at,
        ]);

        return $dataProvider;
    }
}
