<?php
namespace Netsensia\Provider;

trait ProvidesTranslator
{
    protected function translate($text)
    {
        $translator = $this->getServiceLocator()->get('translator');
        return $translator->translate($text);
    }
}

?>