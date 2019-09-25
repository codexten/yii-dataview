<?php

namespace codexten\yii\dataView\grid\export;

use codexten\yii\dataView\widgets\GridViewTrait;

class ExportMenu extends \kartik\export\ExportMenu
{
    use GridViewTrait;

    public $clearBuffers = true;

    public function columns()
    {
        return [];
    }

    public function run()
    {
        ini_set("pcre.backtrack_limit", "5000000");
        return parent::run(); // TODO: Change the autogenerated stub
    }
}
