<?php

declare(strict_types=1);

namespace Rector\Website\Lint;

use Nette\Utils\Strings;
use Rector\Website\Exception\ForbiddenPHPFunctionException;

final class ForbiddenPHPFunctionsChecker
{
    /**
     * @var string[]
     */
    private $forbiddenFunctions = [];

    /**
     * @param string[] $forbiddenFunctions
     */
    public function __construct(array $forbiddenFunctions)
    {
        $this->forbiddenFunctions = $forbiddenFunctions;
    }

    public function checkCode(string $code): void
    {
        // https://regex101.com/r/4in3xJ/3
        $pattern = sprintf('#(?<function>%s)\s*\(#mi', implode('|', $this->forbiddenFunctions));
        $match = Strings::match($code, $pattern);

        if (isset($match['function'])) {
            throw new ForbiddenPHPFunctionException($match['function']);
        }
    }
}
