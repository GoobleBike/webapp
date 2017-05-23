<?php

namespace app\controllers;

use app\models\Config;
use app\models\Stato;
use app\models\Percorso;
use app\models\Segmento;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use \Datetime;

class ApiController extends Controller
{
    private $config;//i parametri di config vengono caricati da init()
    
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

    public function init() {
        //recupero da db i parametri di config
        // es:
        // {"gb":"0","me":"0","path":"1","wc":"0"} 
        $model = Config::find()->all();
        $this->config=[];
        foreach ($model as $c){
            $this->config[$c['conf_key']]=$c['conf_value'];
        }
        parent::init();
    }
    
    /**
     * setVgetP?id=x&v=y[&f=z]
     * @return type
     */
    
    /**
     * set di velocità da parte della gb 
     * setv_getp?id=x&v=y[&f=z]
     * @param type $id gb id (gooblebike che chiama)
     * @param type $v velocità in km/h
     * @param type $f force : se !== null ignora id
     * @return int output raw della pendenza (%)
     */
    public function actionSetv_getp($id,$v,$f=null){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        if ($f===null){
            //chek di id
            if ($id != $this->config['gb']){
                //id di gb non conforme
                //restituisce uno 0
                return 0;
            }
        }
        //check passato oppure f !== null (force!)
//        $model = Stato::findOne(['who' => 'gb', 'id' => $id, 'what' => 'v']);
        $model = Stato::findOne(['who' => 'gb', 'id' => $this->config['gb'], 'what' => 'v']);
        $model->how=$v;
//        $model->how=10;
        $now=date("Y-m-d H:i:s");
        $model->ts=$now;
        $model->save();
        $model = Stato::findOne(['who' => 'wc', 'id' => $this->config['wc'], 'what' => 'p']);
        //verifica di timeout
        $datetime1 = new DateTime($now);
        $datetime2 = new DateTime($model->ts);
        $diff=$datetime1->getTimestamp()-$datetime2->getTimestamp();
        if ($diff > $this->config['wcto']){
            //timeout!
            $p=0;
        }
        else {
            $p=$model->how;
        }
        
        $items=$p;
        return $items;
    }

    /**
     * set di pendenza da parte del webclient
     * setp_getv?id=x&p=y[&f=z]
     * @param type $id wc id (webclient che chiama)
     * @param type $p pendenza in percentuale
     * @param type $f force : se !== null ignora id
     * @return array output JSON di esito e responso
     */
    public function actionSetp_getv($id,$p,$f=null){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if ($f===null){
            //chek di id
            if ($id != $this->config['wc']){
                //id di wc non conforme
                //restituisce ID n MISMATCH
                $items=[
                    'ck'=>"ID $id MISMATCH",
                ];
                //{ck=”esito”,resp={v:n}}
                return $items;
            }
        }
        //check passato oppure f !== null (force!)
//        $model = Stato::findOne(['who' => 'gb', 'id' => $id, 'what' => 'v']);
        $model = Stato::findOne(['who' => 'wc', 'id' => $this->config['wc'], 'what' => 'p']);
        $model->how=$p;
//        $model->how=-2;
        $now=date("Y-m-d H:i:s");        
        $model->ts=$now;
        $model->save();
        $model = Stato::findOne(['who' => 'gb', 'id' => $this->config['gb'], 'what' => 'v']);
        //verifica di timeout
        $datetime1 = new DateTime($now);
        $datetime2 = new DateTime($model->ts);
        $diff=$datetime1->getTimestamp()-$datetime2->getTimestamp();
        if ($diff > $this->config['gbto']){
            //timeout!
            $v="UNKNOWN";
        }
        else {
            $v=$model->how;
        }
        $items=[
            'ck'=>'OK',
            'resp'=>[
                'v'=>$v,
            ]
        ];
        //{ck=”esito”,resp={v:n}}
        return $items;
    }

//    public function actionGetp_getv(){
    public function actionGetstato(){
//        $model = Stato::findOne(['who' => 'gb', 'id' => $id, 'what' => 'v']);
        $model = Stato::findOne(['who' => 'wc', 'id' => $this->config['wc'], 'what' => 'p']);
        $p=$model->how;
        $model = Stato::findOne(['who' => 'gb', 'id' => $this->config['gb'], 'what' => 'v']);
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
            'resp'=>[
                'stato'=>$model,
                'config'=>  $this->config,
                ],
        ];
        //{ck=”esito”,resp={v:n}}
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $items;
    }

    public function actionGetconf(){
//        $model = Stato::findOne(['who' => 'gb', 'id' => $id, 'what' => 'v']);
//        $model = Config::find()->all();
        $model = $this->config;
//        $items=$model->how;
        $items=[
            'ck'=>'OK',
            'resp'=>$model,
        ];
        //{ck=”esito”,resp={v:n}}
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $items;
    }

    /**
     * richiesta di path
     * @param type $id wc id (webclient che chiama)
     * @param type $f force : se !== null ignora id
     * @return array output JSON di esito e responso
     */
    public function actionGetpath($id,$f=null){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if ($f===null){
            //chek di id
            if ($id != $this->config['wc']){
                //id di wc non conforme
                //restituisce ID n MISMATCH
                $items=[
                    'ck'=>"ID $id MISMATCH",
                ];
                //{ck=”esito”,resp={v:n}}
                return $items;
            }
        }
        //check passato oppure f !== null (force!)
//        $model = Stato::findOne(['who' => 'gb', 'id' => $id, 'what' => 'v']);
        $percorso = Percorso::findOne(['id' => $this->config['path']]);
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
