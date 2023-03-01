<?php

class __Mustache_4de00273a26bf9ac39782939c752832e extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $buffer .= $indent . '<form method="get" action="';
        $value = $this->resolveValue($context->find('action'), $context);
        $buffer .= call_user_func($this->mustache->getEscape(), $value);
        $buffer .= '" class="adminsearchform form-inline" role="search">
';
        $buffer .= $indent . '    <div class="form-group">
';
        $buffer .= $indent . '        <label class="sr-only" for="adminsearchquery">';
        $value = $this->resolveValue($context->find('label'), $context);
        $buffer .= call_user_func($this->mustache->getEscape(), $value);
        $buffer .= '</label>
';
        $buffer .= $indent . '        <input class="form-control" type="text" name="query" value="';
        $value = $this->resolveValue($context->find('searchvalue'), $context);
        $buffer .= call_user_func($this->mustache->getEscape(), $value);
        $buffer .= '" id="adminsearchquery" size="10">
';
        $buffer .= $indent . '    </div>
';
        $buffer .= $indent . '    <input type="submit" class="btn btn-secondary" value=';
        // 'quote' section
        $value = $context->find('quote');
        $buffer .= $this->section1abd7f467b6663ce2e524708ea22c59c($context, $indent, $value);
        $buffer .= '>
';
        $buffer .= $indent . '</form>
';

        return $buffer;
    }

    private function sectionBec198647490b4fd7cf2a5fed37757ce(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'search';
            $result = call_user_func($value, $source, $this->lambdaHelper);
            if (strpos($result, '{{') === false) {
                $buffer .= $result;
            } else {
                $buffer .= $this->mustache
                    ->loadLambda((string) $result)
                    ->renderInternal($context);
            }
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'search';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section1abd7f467b6663ce2e524708ea22c59c(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '{{#str}}search{{/str}}';
            $result = call_user_func($value, $source, $this->lambdaHelper);
            if (strpos($result, '{{') === false) {
                $buffer .= $result;
            } else {
                $buffer .= $this->mustache
                    ->loadLambda((string) $result)
                    ->renderInternal($context);
            }
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                // 'str' section
                $value = $context->find('str');
                $buffer .= $this->sectionBec198647490b4fd7cf2a5fed37757ce($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
