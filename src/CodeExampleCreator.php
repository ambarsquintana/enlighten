<?php

namespace Styde\Enlighten;

use Closure;
use ReflectionFunction;
use Styde\Enlighten\Utils\ResultTransformer;
use Throwable;

class CodeExampleCreator
{
    /**
     * @var TestInspector
     */
    private $testInspector;

    /**
     * @var CodeInspector
     */
    private $codeInspector;

    public function __construct(TestInspector $testInspector, CodeInspector $codeInspector)
    {
        $this->testInspector = $testInspector;
        $this->codeInspector = $codeInspector;
    }

    public function createSnippet(Closure $callback)
    {
        $testExample = $this->testInspector->getCurrentTestExample();

        if ($testExample->isIgnored()) {
            return $callback();
        }

        $testExample->createSnippet($this->codeInspector->getCodeFrom($callback));

        try {
            $result = call_user_func($callback);

            $testExample->saveSnippetResult(ResultTransformer::toArray($result));

            return $result;
        } catch (Throwable $throwable) {
            $testExample->setException($throwable);

            throw $throwable;
        }
    }
}
