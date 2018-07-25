<?php
/**
 * @copyright Copyright (c) 2015 Factor Energia
 * @license https://github.com/promocat/yii2-adminlte/blob/master/LICENSE
 * @link http://adminlte.yiister.ru
 */

namespace promocat\adminlte\assets;

class ThemeAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@vendor/almasaeed2010/adminlte/dist';
    public $css = [
        'css/AdminLTE.min.css',
        'css/skins/_all-skins.min.css',
    ];
    public $js = [
        'js/adminlte.min.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'rmrevin\yii\fontawesome\AssetBundle',
    ];
}
