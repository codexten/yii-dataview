<?php

namespace codexten\yii\dataView\widgets;

class GridView extends \hiqdev\higrid\GridView
{
    /**
     * {@inheritDoc}
     */
    public function init()
    {
        if (empty($this->columns)) {
            $this->columns = $this->columns();
        }
        parent::init();
    }

}
