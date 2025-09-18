<?php

namespace app\controllers;

use Yii;
use yii\db\Exception;
use yii\helpers\Json;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;

class LoanRequestsController extends Controller
{
    /**
     * Handles the loan request creation
     *
     * @throws BadRequestHttpException|Exception
     */
    public function actionRequest(): array
    {
        if (!Yii::$app->request->isPost) {
            throw new BadRequestHttpException(
                sprintf(
                    'Cannot make %s request on POST endpoint',
                    Yii::$app->request->method
                )
            );
        }
        $data = Yii::$app->request->getRawBody();
        $data = Json::decode($data);
        $data = Yii::$app->loanRequestService->registerLoanRequest($data);
        Yii::$app->response->statusCode = $data['code'];

        return $data['body'];
    }

    /**
     * Processes the request
     *
     * @throws BadRequestHttpException|Exception
     * @throws \Throwable
     */
    public function actionProcessor(): array
    {
        if (!Yii::$app->request->isGet) {
            throw new BadRequestHttpException(
                sprintf(
                    'Cannot make %s request on GET endpoint',
                    Yii::$app->request->method
                )
            );
        }
        $data = Yii::$app->request->get();

        $data = Yii::$app->loanRequestService->processLoanRequest($data);
        Yii::$app->response->statusCode = $data['code'];

        return $data['body'];
    }
}
