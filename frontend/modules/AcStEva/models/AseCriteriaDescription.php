<?php

namespace frontend\modules\AcStEva\models;

use Yii;
use backend\models\User;
use yii\db\Query;

/**
 * This is the model class for table "ase_criteria_description".
 *
 * @property int $id
 * @property int $category_relId
 * @property string $code
 * @property string $description
 * @property int $max_rating_scale
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property string $data
 *
 * @property AseCriteriaCategory $categoryRel
 * @property User $createdBy
 * @property User $updatedBy
 */
class AseCriteriaDescription extends \yii\db\ActiveRecord
{

    public $category_name;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ase_criteria_description';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_relId', 'code', 'description', 'created_at', 'created_by', 'data'], 'required'],
            [['category_relId', 'max_rating_scale', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['description', 'data'], 'string'],
            [['code'], 'string', 'max' => 15],
            [['category_relId'], 'exist', 'skipOnError' => true, 'targetClass' => AseCriteriaCategory::class, 'targetAttribute' => ['category_relId' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'category_relId' => Yii::t('rms', 'Criteria Category'),
            'code' => Yii::t('rms', 'Code'),
            'description' => Yii::t('rms', 'Description'),
            'max_rating_scale' => Yii::t('rms', 'Max Rating Scale'),
            'created_at' => Yii::t('rms', 'Created At'),
            'created_by' => Yii::t('rms', 'Created By'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }

    public function getData(){
        $this->data = md5(serialize((object)[
            'description'=>$this->description,
            'code'=>$this->code,
            'max_rating_scale'=>$this->max_rating_scale,
            'category_relId'=>intval($this->category_relId),
            'created_by'=>intval($this->created_by),
            'updated_by'=>intval($this->updated_by),
        ]));
    }

    public function getAIValue(){
        $q = "SELECT `AUTO_INCREMENT` FROM  INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '" . self::tableName() . "'";
        $auto_increment = Yii::$app->db->createCommand($q)->queryScalar();
        return $auto_increment;
    }

    public function getAIValue3(){
        // ->orderBy(['id' => SORT_DESC])
        $last = $this->find()->where(['category_relId' => $this->category_relId])->count();
        return $last+1;
    }
    public function getAIValue2(){
        $q = new Query();
        $res = $q->select("AUTO_INCREMENT")
            ->from('INFORMATION_SCHEMA.TABLES')
            ->where("TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ase_criteria_description' ")
            ->one();
        if($res)
            return $res["AUTO_INCREMENT"];
        return rand(999,10000);
    }

    /**
     * Gets query for [[CategoryRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategoryRel()
    {
        return $this->hasOne(AseCriteriaCategory::class, ['id' => 'category_relId']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[AseEvaluationResultDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAseEvaluationResultDetails()
    {
        return $this->hasMany(AseEvaluationResultDetail::class, ['criteria_relId' => 'id']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }
}
