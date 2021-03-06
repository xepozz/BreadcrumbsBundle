<?php

namespace Xepozz\BreadcrumbsBundle\Model;

class SingleBreadcrumb
{
    public $url;
    public $text;
    public $translationParameters;
    public $translate;

    public function __construct($text = '', $url = '', array $translationParameters = [], $translate = true)
    {
        $this->url = $url;
        $this->text = $text;
        $this->translationParameters = $translationParameters;
        $this->translate = $translate;
    }
}