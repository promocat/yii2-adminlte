<?php
/**
 * @copyright Copyright (c) 2015 Factor Energia
 * @license https://github.com/promocat/yii2-adminlte/blob/master/LICENSE
 * @link http://adminlte.yiister.ru
 */

namespace promocat\adminlte\widgets\grid;

class GridViewAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@vendor/almasaeed2010/adminlte/plugins/datatables';
    public $css = [
        'dataTables.bootstrap.css',
    ];
    public $js = [];
    public $depends = [
        'promocat\adminlte\assets\Asset',
    ];
}
