<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "student_profile_change".
 *
 * @property int $id 	 
 * @property string $std_attribute
 * @property string $attribute_label
 * @property string $old_value
 * @property string $new_value
 * @property int $std_relId
 * @property int|null $approval_status
 * @property string|null $reason
 * @property string|null $relation_name
 * @property string|null $related_attribute_name
 * @property int|null $approved_by
 * @property int|null $approved_at
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property string $data
 *
 * @property Student $stdRel
 * @property User $approvedBy
 * @property User $createdBy
 * @property User $updatedBy
 */
class StudentProfileChange extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'student_profile_change';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['std_attribute', 'attribute_label', 'old_value', 'new_value', 'std_relId', 'created_at', 'created_by', 'data'], 'required'],
            [['std_relId', 'approval_status', 'approved_by', 'approved_at', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['data', 'reason', 'relation_name', 'related_attribute_name'], 'string'],
            [['std_attribute'], 'string', 'max' => 45],
            [['attribute_label'], 'string', 'max' => 50],
            [['old_value', 'new_value'], 'string', 'max' => 100],
            [['std_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Student::class, 'targetAttribute' => ['std_relId' => 'id']],
            [['approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['approved_by' => 'id']],
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
            'std_attribute' => Yii::t('rms', 'Attribute'),
            'attribute_label' => Yii::t('rms', 'Attribute Label'),
            'old_value' => Yii::t('rms', 'Old Value'),
            'new_value' => Yii::t('rms', 'New Value'),
            'std_relId' => Yii::t('rms', 'Student Name'),
            'reason' => Yii::t('rms', 'Reason'),
            'related_attribute_name' => Yii::t('rms', 'Attribute Name'),
            'approval_status' => Yii::t('rms', 'Approval Status'),
            'approved_by' => Yii::t('rms', 'Approved By'),
            'approved_at' => Yii::t('rms', 'Approved At'),
            'created_at' => Yii::t('rms', 'Created At'),
            'created_by' => Yii::t('rms', 'Created By'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }

    public function getData()
    {
        // Get the current date and time
        $this->data = md5(serialize([
            'old_value' => $this->old_value,
            'new_value' => $this->new_value,
            'reason' => $this->reason,
            'std_relId' => $this->std_relId,
            'approval_status' => $this->approval_status,
            'std_attribute' => $this->std_attribute,
            'attribute_label' => $this->attribute_label,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by
        ]));
    }

    public function approveRequest()
    {
        // $this->refresh();
        if ($this->approval_status == NULL) {
            $std = $this->stdRel;
            if ($this->relation_name) {
                // $realtionName = $this->relation_name;
                // $realtedAttributeName = $this->related_attribute_name;
                $this->approved_by = Yii::$app->user->id;
                $this->approved_at = time();
                $this->approval_status = 1;
                $this->getData();
                if ($this->save()) {
                    $std->{$this->std_attribute} = $this->new_value;
                    $std->std_updated_by = Yii::$app->user->id;
                    $std->std_updated_at = date('Y-m-d H:i:s');
                    if ($std->save()) {
                        return true;
                    } else {
                        return json_encode($std->errors);
                    }
                } else {
                    return 'Request approving faield, Error: ' . json_encode($this->errors);
                }
            } else {
                $this->approved_by = Yii::$app->user->id;
                $this->approved_at = time();
                $this->approval_status = 1;
                $this->getData();
                if ($this->save()) {
                    $std->{$this->std_attribute} = $this->new_value;
                    $std->std_updated_by = Yii::$app->user->id;
                    $std->std_updated_at = date('Y-m-d H:i:s');
                    if ($std->save()) {
                        return true;
                    } else {
                        return json_encode($std->errors);
                    }
                } else {
                    return 'Request approving faield, Error: ' . json_encode($this->errors);
                }
            }
        } else {
            return "Already approved request, " . $this->approval_status;
        }
    }


    /**
     * Gets query for [[StdRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStdRel()
    {
        return $this->hasOne(Student::class, ['id' => 'std_relId']);
    }

    /**
     * Gets query for [[ApprovedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getApprovedBy()
    {
        return $this->hasOne(User::class, ['id' => 'approved_by']);
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
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }
}
