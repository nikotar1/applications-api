<?php

declare(strict_types=1);

namespace app\services;

use app\enums\LoanStatusEnum;
use app\models\LoanRequest;
use app\models\LoanRequestProcessModel;
use Throwable;
use Yii;
use yii\db\Exception;
use yii\db\IntegrityException;
use yii\db\Transaction;

class LoanRequestService
{
    /**
     * Creates loan request
     *
     * @throws Exception
     */
    public function registerLoanRequest(array $requestData): array
    {
        $model = new LoanRequest();
        $model->load($requestData, '');
        $model->status = LoanStatusEnum::PENDING->value;
        $saved = $model->save();
        if (!$saved) {
            return [
                'body' => [
                    'result' => false,
                ],
                'code' => 400
            ];
        }

        return [
            'body' => [
                'result' => true,
                'id' => $model->id,
            ],
            'code' => 201
        ];
    }

    /**
     * Processes one pending request at time with 10% chance
     * of approval without blocking other processes
     */
    public function processLoanRequest(array $params): array
    {
        $errorResponse = [
            'body' => [
                'result' => false,
            ],
            'code' => 200
        ];
        $successResponse = [
            'body' => [
                'result' => true,
            ],
            'code' => 200
        ];


        $model = new LoanRequestProcessModel();
        $model->load($params, '');
        $model->validate();
        if ($model->hasErrors()) {
            return $errorResponse;
        }

        $delay = max(0, $model->delay);
        if ($delay > 0) {
            sleep($delay);
        }

        $db = Yii::$app->db;

        $tx = $db->beginTransaction(Transaction::SERIALIZABLE);
        try {
            $query = sprintf(
                "SELECT id, user_id
                    FROM loan_requests
                    WHERE status = '%s'
                    ORDER BY id
                    FOR UPDATE SKIP LOCKED
                    LIMIT 1",
                LoanStatusEnum::PENDING->value
            );
            $row = $db->createCommand($query)->queryOne();

            if (!$row) {
                $tx->commit();
                return $errorResponse;
            }

            $requestId = (int)$row['id'];

            $approve = mt_rand(1, 10) === 1; // 10% chance

            if ($approve) {
                try {
                    $db->createCommand()->update(
                        'loan_requests',
                        ['status' => LoanStatusEnum::APPROVED->value],
                        ['id' => $requestId]
                    )->execute();
                } catch (IntegrityException) {
                    return $errorResponse;
                }
            } else {
                $db->createCommand()->update(
                    'loan_requests',
                    ['status' => LoanStatusEnum::DECLINED->value],
                    ['id' => $requestId]
                )->execute();
            }

            $tx->commit();
        } catch (Throwable) {
            $tx->rollBack();
            return $errorResponse;
        }

        return $successResponse;
    }
}