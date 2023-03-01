<?php

class __Mustache_b14e7f8fe393662edd8530e9ca4992ec extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $buffer .= $indent . '<div class="easyenrol">
';
        $buffer .= $indent . '<form id="enrolform_easy" class="form-inline enrolform_easy text-xs-center" action="';
        $value = $this->resolveValue($context->findDot('pages.enrol_easy'), $context);
        $buffer .= call_user_func($this->mustache->getEscape(), $value);
        $buffer .= '" method="post">
';
        $buffer .= $indent . '    <div class="form-group">
';
        $buffer .= $indent . '        <label class="sr-only" for="enrolform_course_code">';
        $value = $this->resolveValue($context->findDot('lang.enrolform_course_code'), $context);
        $buffer .= call_user_func($this->mustache->getEscape(), $value);
        $buffer .= '</label>
';
        $buffer .= $indent . '        <div class="input-group">
';
        // 'config.qrenabled' section
        $value = $context->findDot('config.qrenabled');
        $buffer .= $this->section44f6c2290d1a2dd91ba737f594f22f14($context, $indent, $value);
        $buffer .= $indent . '            <input type="text" class="form-control" id="enrolform_course_code" name="enrolform_course_code" placeholder="';
        $value = $this->resolveValue($context->findDot('lang.enrolform_course_code'), $context);
        $buffer .= call_user_func($this->mustache->getEscape(), $value);
        $buffer .= '">
';
        $buffer .= $indent . '        </div>
';
        $buffer .= $indent . '    </div>
';
        $buffer .= $indent . '    <input type="hidden" name="sesskey" value="';
        $value = $this->resolveValue($context->findDot('internal.sesskey'), $context);
        $buffer .= call_user_func($this->mustache->getEscape(), $value);
        $buffer .= '">
';
        $buffer .= $indent . '    <input type="hidden" name="_qf__enrolform" value="1">
';
        $buffer .= $indent . '    <button type="submit" class="btn btn-primary">';
        $value = $this->resolveValue($context->findDot('lang.enrolform_submit'), $context);
        $buffer .= call_user_func($this->mustache->getEscape(), $value);
        $buffer .= '</button>
';
        $buffer .= $indent . '</form>
';
        $buffer .= $indent . '</div>
';
        // 'config.qrenabled' section
        $value = $context->findDot('config.qrenabled');
        $buffer .= $this->sectionA8f27a97976e2c40623e717818b215f6($context, $indent, $value);
        $buffer .= $indent . '<script src="';
        $value = $this->resolveValue($context->findDot('component.main_javascript'), $context);
        $buffer .= call_user_func($this->mustache->getEscape(), $value);
        $buffer .= '"></script>
';

        return $buffer;
    }

    private function section44f6c2290d1a2dd91ba737f594f22f14(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                <div style="cursor: pointer;" class="btn btn-secondary input-group-addon qr-button" data-url="{{ pages.enrol_easy_qr }}"><i class="fa fa-qrcode" aria-hidden="true"></i></div>
            ';
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
                
                $buffer .= $indent . '                <div style="cursor: pointer;" class="btn btn-secondary input-group-addon qr-button" data-url="';
                $value = $this->resolveValue($context->findDot('pages.enrol_easy_qr'), $context);
                $buffer .= call_user_func($this->mustache->getEscape(), $value);
                $buffer .= '"><i class="fa fa-qrcode" aria-hidden="true"></i></div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionA8f27a97976e2c40623e717818b215f6(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '

<script src="{{ component.jquery }}"></script>
';
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
                
                $buffer .= $indent . '
';
                $buffer .= $indent . '<script src="';
                $value = $this->resolveValue($context->findDot('component.jquery'), $context);
                $buffer .= call_user_func($this->mustache->getEscape(), $value);
                $buffer .= '"></script>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
