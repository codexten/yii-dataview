<?php

namespace codexten\yii\dataView\grid\export;

use codexten\yii\dataView\widgets\GridViewTrait;

class ExportMenu extends \kartik\export\ExportMenu
{
    use GridViewTrait;

    public function columns()
    {
        return [];
    }
}
