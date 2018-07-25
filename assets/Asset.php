<?php
/**
 * @license https://github.com/promocat/yii2-adminlte/blob/master/LICENSE
 */
namespace promocat\adminlte\assets;

class Asset extends \yii\web\AssetBundle {
    public $sourcePath = '@vendor/promocat/yii2-adminlte';
    public $css = [
        'css/theme.css',
    ];
    public $js = [];
    public $depends = [
        'promocat\yii2-adminlte\assets\Asset'
    ];
}
