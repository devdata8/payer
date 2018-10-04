<?php
/**
 * Created by PhpStorm.
 * User: Rostislav Rusakov
 * Date: 03.10.2018
 * Time: 0:03
 */

namespace console\controllers;

use Yii;
use common\models\Payment;

/**
 * Processing payments controller
 */
class ProcessingController extends \yii\console\Controller
{
    /**
     * Processing payments.
     *
     * @return boolean
     */
    public function actionIndex()
    {
        $deferred_time_TS = mktime(date('H'), 59, 59, date('m'), date('d'), date('Y'));
        $deferred_time = date("Y-m-d H:i:s", $deferred_time_TS);
        echo "Current Server time: ".date("Y-m-d H:i:s")." in TS: ".time()."\n";
        echo "Current time for select: ".$deferred_time_TS." - ".$deferred_time." in TS: ".$deferred_time_TS."\n";
        $aPayments = Payment::find()
            ->where('deferred_time <= :deferred_time AND status=:status',
                ['deferred_time' => $deferred_time_TS, ':status' => Payment::STATUS_DEFERRED])
            ->all();
        echo "Count payments for refactoring: ".count($aPayments)." pcs\n\n";
        foreach ($aPayments as $payment) {
            echo ' - Payment Id: '.$payment->id.'; amount: '.$payment->amount.'; deferred_time: '.
                $payment->deferred_time.":\n";
            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            try {
                $connection->createCommand()
                    ->update(
                        'payer.public.user',
                        [
                            'balance'          => new \yii\db\Expression('balance - '.$payment->amount),
                            'deferred_balance' => new \yii\db\Expression('deferred_balance - '.$payment->amount),
                            'updated_at'       => time(),
                        ],
                        'id='.$payment->id_user_from
                    )
                    ->execute();
                $connection->createCommand()
                    ->update(
                        'payer.public.user',
                        [
                            'balance'          => new \yii\db\Expression('balance + '.$payment->amount),
                            'updated_at'       => time(),
                        ],
                        'id='.$payment->id_user_to
                    )
                    ->execute();
                $connection->createCommand()
                    ->update(
                        'payer.public.payment',
                        [
                            'status'          => Payment::STATUS_FINISHED,
                            'updated_at'       => time(),
                        ],
                        'id='.$payment->id
                    )
                    ->execute();
                $transaction->commit();
                echo "    > Users balances Updated succesfuly ans Payment Updated to STATUS_FINISHED\n";
            } catch (\Throwable $e) {
                $transaction->rollBack();
                throw $e;
            }
            echo "\n";
        }

        return 1;
    }
}