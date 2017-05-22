<?php

namespace app\controllers;

use app\models\Stato;
use app\models\Percorso;
use app\models\Segmento;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

class ApiController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }
    
    /**
     * setVgetP?id=x&v=y[&f=z]
     * @return type
     */
//    public function actionSetVGetP($id,$v,$f=null){
    public function actionSetv_getp($id,$v,$f=null){
//        $model = Stato::findOne(['who' => 'gb', 'id' => $id, 'what' => 'v']);
        $model = Stato::findOne(['who' => 'gb', 'id' => 0, 'what' => 'v']);
        $model->how=$v;
//        $model->how=10;
        $model->ts=date("Y-m-d H:i:s");
        $model->save();
        $model = Stato::findOne(['who' => 'wc', 'id' => 0, 'what' => 'p']);
        
        $items=$model->how;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        return $items;
    }

    public function actionSetp_getv($id,$p,$f=null){
//        $model = Stato::findOne(['who' => 'gb', 'id' => $id, 'what' => 'v']);
        $model = Stato::findOne(['who' => 'wc', 'id' => 0, 'what' => 'p']);
        $model->how=$p;
//        $model->how=-2;
        $model->ts=date("Y-m-d H:i:s");
        $model->save();
        $model = Stato::findOne(['who' => 'gb', 'id' => 0, 'what' => 'v']);
//        $items=$model->how;
        $items=[
            'ck'=>'OK',
            'resp'=>[
                'v'=>$model->how,
            ]
        ];
        //{ck=”esito”,resp={v:n}}
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $items;
    }

//    public function actionGetp_getv(){
    public function actionGetstato(){
//        $model = Stato::findOne(['who' => 'gb', 'id' => $id, 'what' => 'v']);
        $model = Stato::findOne(['who' => 'wc', 'id' => 0, 'what' => 'p']);
        $p=$model->how;
        $model = Stato::findOne(['who' => 'gb', 'id' => 0, 'what' => 'v']);
//        $items=$model->how;
        $items=[
            'ck'=>'OK',
            'resp'=>[
                'v'=>$model->how,
                'p'=>$p,
            ]
        ];
        //{ck=”esito”,resp={v:n}}
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $items;
    }

    public function actionGetall(){
//        $model = Stato::findOne(['who' => 'gb', 'id' => $id, 'what' => 'v']);
        $model = Stato::find()->all();
//        $items=$model->how;
        $items=[
            'ck'=>'OK',
            'resp'=>$model,
        ];
        //{ck=”esito”,resp={v:n}}
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $items;
    }

    public function actionGetpath(){
//        $model = Stato::findOne(['who' => 'gb', 'id' => $id, 'what' => 'v']);
        $percorso = Percorso::findOne(['id' => 1]);
        $segmenti = $percorso->getSegmentos()
            ->orderBy('progr')
            ->all();
        $s=[];
        foreach ($segmenti as $sg){
            $s[]=[
                'desc'=>$sg->descr,
                'start'=>$sg->start,
                'stop'=>$sg->stop,
            ];
        }
        /*
        {desc=””,path=[
{desc:””,start:"xx",stop:"yy"},
{desc:””,start:"xx",stop:"yy"},
...
{desc:””,start:"xx",stop:"yy"}
]}
         */
        $items=[
            'ck'=>'OK',
            'resp'=>[
                'desc'=>$percorso->desc,
                'path'=>$s,
            ]
        ];
        //{ck=”esito”,resp={v:n}}
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $items;
    }

/*
 * promemoria: doc su come impostare output di vario formato
 * https://github.com/samdark/yii2-cookbook/blob/master/book/response-formats.md
 * 
   Valid formats are:
    FORMAT_RAW
    FORMAT_HTML
    FORMAT_JSON
    FORMAT_JSONP
    FORMAT_XML
 */

    public function actionJson()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $items = ['some', 'array', 'of', 'data' => ['associative', 'array']];
        return $items;
    }

    public function actionXml()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_XML;
        $items = ['some', 'array', 'of', 'data' => ['associative', 'array']];
        return $items;
    }

    public function actionRaw()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $items = ['some', 'array', 'of', 'data' => ['associative', 'array']];
        return print_r($items,true);
    }

}
