<?php

declare(strict_types=1);

namespace app\validators;

use InvalidArgumentException;
use yii\db\ActiveRecord;
use yii\validators\Validator;

class UniqueValidator extends Validator
{
    public string $targetClass;
    public array $comparedAttributes = [];

    public function init(): void
    {
        parent::init();
        if (!$this->targetClass) {
            throw new InvalidArgumentException('`targetClass` must be set.');
        }
        if (empty($this->comparedAttributes)) {
            throw new InvalidArgumentException('`comparedAttributes` must be a non-empty array.');
        }
    }

    public function validateAttributes($model, $attributes = null): void
    {
        /** @var ActiveRecord $class */
        $class = $this->targetClass;
        $cond  = ['and'];

        foreach ($this->comparedAttributes as $key => $value) {
            if (is_int($key) && is_string($value)) {
                $cond[] = [$value => $model->$value];
                continue;
            }

            if (is_string($key)) {
                $cond[] = [$key => $value];
            }
        }

        $query = $class::find()->where($cond);

        if ($model instanceof $class && !$model->isNewRecord) {
            $pk = $class::primaryKey();
            if (count($pk) === 1) {
                $query->andWhere(['<>', $pk[0], $model->{$pk[0]}]);
            }
        }

        if ($query->exists()) {
            foreach ($this->attributes as $key => $value) {
                $attr = is_int($key) ? $value : (is_string($value) ? $key : null);
                if ($attr) {
                    $model->addError($attr, $this->message);
                }
            }
        }
    }
}