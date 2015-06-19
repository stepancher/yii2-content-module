<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace stepancher\content\assets;

use yii\web\AssetBundle;

/**
 * Configuration for `backend` client script files
 * @since 4.0
 */
class ContentAsset extends AssetBundle
{
    public $sourcePath = '@stepancher/content/assets/js';

    public $js = ['jquery.synctranslit.js'];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
