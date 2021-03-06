<?php

namespace frontend\controllers;

use common\models\Payment;
use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use common\models\LoginForm;
use common\models\User;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only'  => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow'   => true,
                        'roles'   => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
            'verbs'  => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error'   => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class'           => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error',
                    'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     *
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * List all payers with last payment.
     *
     * @return mixed
     */
    public function actionPayers()
    {
        $models = Yii::$app->db->createCommand(
            "SELECT u.id as uid, u.username, p.*
                  FROM payer.public.user u
                  LEFT JOIN payer.public.payment p
                    ON p.id=(SELECT p1.id FROM payer.public.payment p1
                               WHERE p1.id_user_from=u.id
                               ORDER BY p1.created_at DESC
                               LIMIT 1)
                               ORDER BY u.username")
            ->queryAll();
        return $this->render('payers', [
            'model' => $models,
        ]);
    }

    /**
     * Displays Pay Form.
     *
     * @return mixed
     */
    public function actionPay()
    {
        $user = Yii::$app->getUser();
        $model = new Payment();
        $model->id_user_from = $user->id;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->deferred_time = Yii::$app->formatter->asTimestamp($model->deferred_time, 'dd-MM-yyyy H:mm');
            $model->created_at = time();
            $model->updated_at = time();
            if ($model->save()) {
                $user->identity->deferred_balance += $model->amount;
                if ($user->identity->validate()) {
                    if ($user->identity->save()) {
                        Yii::$app->session->setFlash('success', 'Thank you for your payment!');
                    } else {
                        Yii::$app->session->setFlash('error', 'Payment saved but have problem user balance. Error 1');
                    }
                } else {
                    Yii::$app->session->setFlash('error', 'Payment saved but have problem user balance. Error 2');
                }
            } else {
                Yii::$app->session->setFlash('error', 'There was an error with your payment.');
            }

            return $this->refresh();
        } else {
            $users = User::find()->where('id != :id', ['id' => $model->id_user_from])->all();
            $users = ArrayHelper::map($users, 'id', 'username');

            return $this->render('pay', [
                'user'  => $user,
                'model' => $model,
                'users' => $users,
            ]);
        }
    }

}
