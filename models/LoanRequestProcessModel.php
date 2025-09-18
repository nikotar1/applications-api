<?php

declare(strict_types=1);

namespace app\models;

use yii\base\Model;

class LoanRequestProcessModel extends Model
{
    public int $delay;

    public function rules(): array
    {
        return [
            ['delay', 'default', 'value' => 0],
            ['delay', 'integer', 'min' => 0, 'tooSmall' => 'Delay must be 0 or greater.'],
        ];
    }
}
