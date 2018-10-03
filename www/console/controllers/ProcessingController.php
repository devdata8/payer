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
use common\models\User;

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
            // mktime ( [int hour [, int minute [, int second [, int month [, int day [, int year [, int is_dst]]]]]]] )
        $deferred_time = date("Y-m-d H:i:s", $deferred_time_TS);
        echo "Current Server time: ".date("Y-m-d H:i:s")." in TS: ".time()."\n";
        echo "Current time for select: ".$deferred_time_TS." - ".$deferred_time." in TS: ".$deferred_time_TS."\n";
        $aPayments = Payment::find()
            ->where('deferred_time <= :deferred_time AND status=:status',
                ['deferred_time' => $deferred_time_TS, ':status' => Payment::STATUS_DEFERRED])
            ->all();
        echo "Count payments for refactoring: ".count($aPayments)." pcs\n\n";
        foreach ($aPayments as $payment) {
            echo ' - Payment Id: '.$payment->id.'; amount: '.$payment->amount.'; deferred_time: '.$payment->deferred_time.":\n";
            $mUserFrom = User::find()->where(['id' => $payment->id_user_from])->one();
            $mUserTo = User::find()->where(['id' => $payment->id_user_to])->one();
            $payment->status = Payment::STATUS_PROCESS;
            $payment->updated_at = time();
            if ($payment->save()) {
                echo "    > Payment Updated to STATUS_PROCESS ... begin transaction...\n";
                // Begin transaction
                $mUserFrom->balance = $mUserFrom->balance - $payment->amount;
                $mUserFrom->deferred_balance = $mUserFrom->deferred_balance - $payment->amount;
                $mUserFrom->updated_at = time();
                echo '    > UserFrom Id: '.$mUserFrom->id.'; name: '.$mUserFrom->username;
                if ($mUserFrom->save()) {
                    echo " > Balance updated to: ".$mUserFrom->balance."\n";
                } else {
                    echo " > ERROR UserFrom Balance update!";
                }
                $mUserTo->balance += $payment->amount;
                $mUserTo->updated_at = time();
                echo '    > UserTo Id: '.$mUserTo->id.'; name: '.$mUserTo->username;
                if ($mUserTo->save()) {
                    echo " > Balance updated to: ".$mUserTo->balance."\n";
                } else {
                    echo " > ERROR UserTo Balance update!";
                }
                $payment->status = Payment::STATUS_FINISHED;
                $payment->updated_at = time();
                if ($payment->save()) {
                    echo "    > Payment Updated to STATUS_FINISHED\n";
                } else {
                    echo "    > ERROR Payment update!";
                }
            } else {
                echo "    > ERROR Payment update to STATUS_PROCESS!\n";
                echo "    > ERROR Description: ".print_r($payment->getErrors(),true)."\n";
            }
            echo "\n";
        }

        return 1;
    }
}