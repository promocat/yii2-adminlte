<?php

namespace promocat\adminlte\widgets;

use yii\helpers\BaseHtml;
use yii\helpers\Html;
use yii\web\AssetBundle;
use yii\widgets\InputWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;


class Toggle extends InputWidget
{
    public $value = true;
    public $uncheck = null;
    public $label = false;

    public $onText = null;
    public $offText = null;
    public $class = null;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->onText === null) {
            $this->onText = \Yii::t('adminlte', 'On');
        }
        if ($this->offText === null) {
            $this->offText = \Yii::t('adminlte', 'Off');
        }
        $view = $this->getView();
        $view->registerCssFile('https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css');
        $view->registerJsFile('https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js',
            ['depends' => 'yii\web\JqueryAsset', 'position' => $view::POS_END]);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $options = [
            'value' => $this->value,
            'label' => $this->label,
            'data-on' => $this->onText,
            'data-off' => $this->offText,
            'data-toggle' => 'toggle',
        ];
        if($this->uncheck !== null) {
            $options['uncheck'] = $this->uncheck;
        }
        echo Html::activeCheckbox($this->model, $this->attribute, $options + $this->options);
        $class = $this->options['class'];
        $js = "$('.$class').change(function() {
            if($(this).prop('checked')) {
                $('.$class').attr('checked', true);    
            } else {
                $('.$class').removeAttr('checked');
            }
        })";
        $this->getView()->registerJs($js, $this->getView()::POS_END);
        parent::run();
    }
}
