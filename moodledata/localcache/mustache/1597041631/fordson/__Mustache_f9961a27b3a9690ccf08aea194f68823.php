<?php

class __Mustache_f9961a27b3a9690ccf08aea194f68823 extends Mustache_Template
{
    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $buffer = '';

        if ($partial = $this->mustache->loadPartial('mod_assign/loading')) {
            $buffer .= $partial->renderInternal($context);
        }

        return $buffer;
    }
}
