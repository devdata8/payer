<?php
/**
 * Created by PhpStorm.
 * User: Rostislav Rusakov
 * Date: 03.10.2018
 * Time: 0:03
 */

namespace console\controllers;

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
        $deferred_time = date("Y-d-m H:i:s", mktime(0, 59, 0, 10, 3, 2018));
        echo "Current time for select: ".$deferred_time."\n";
        $aPayments = Payment::find()
            ->where('deferred_time <= :deferred_time AND status=1', ['deferred_time' => $deferred_time])
            ->all();
        echo "Count payments for refactoring: ".$aPayments." pcs\n\n";
        foreach ($aPayments as $payment) {
            echo 'Payment Id: '.$payment->id.'; amount: '.$payment->amount.'; time: '.$payment->deferred_time."\n";
            $mUserFrom = User::find()->where(['id' => $payment->id_user_from])->one();
            echo 'UserFrom Id: '.$mUserFrom->id.'; name: '.$mUserFrom->username."\n";
            $mUserTo = User::find()->where(['id' => $payment->id_user_to])->one();
            echo 'UserTo Id: '.$mUserTo->id.'; name: '.$mUserTo->username."\n\n";
            // Begin transaction
            echo "Begin transaction!\n";
            $mUserFrom->balance = $mUserFrom->balance - $payment->amount;
            $mUserFrom->deferred_balance = $mUserFrom->deferred_balance - $payment->amount;
//            $mUserFrom->updated_at = date("Y-d-m H:i:s");
            if ($mUserFrom->save()) {
                echo "UserFrom Balance updated to: ".$mUserFrom->balance."\n";
            } else {
                echo "ERROR UserFrom Balance update!";
            }
            $mUserTo->balance += $payment->amount;
//            $mUserTo->updated_at = date("Y-d-m H:i:s");
            if ($mUserTo->save()) {
                echo "UserTo Balance updated to: ".$mUserTo->balance."\n";
            } else {
                echo "ERROR UserTo Balance update!";
            }
            echo "\n";
        }

        return 1;
    }
}