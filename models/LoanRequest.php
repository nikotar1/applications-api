<?php

declare(strict_types=1);

namespace app\models;

use app\enums\LoanStatusEnum;
use app\validators\UniqueValidator;
use yii\db\ActiveRecord;

/**
 * @property-read int $id
 * @property int $user_id
 * @property float $amount
 * @property int $term
 * @property string $status
 */
class LoanRequest extends ActiveRecord
{
    public function rules(): array
    {
        return [
            [['user_id', 'amount', 'term'], 'required'],
            ['amount', 'number', 'min' => 0.01, 'tooSmall' => 'Amount must be greater than 0.'],
            ['term', 'integer', 'min' => 1, 'tooSmall' => 'Term must be greater than 0.'],
            [
                ['user_id', 'product_id'],
                UniqueValidator::class,
                'targetClass' => self::class,
                'comparedAttributes'  => [
                    'user_id',
                    'status' => LoanStatusEnum::APPROVED->value
                ],
                'message'     => 'This user already has this product.',
            ],
        ];
    }

    public static function tableName(): string
    {
        return '{{%loan_requests}}';
    }
}