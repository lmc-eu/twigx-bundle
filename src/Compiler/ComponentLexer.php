<?php

declare(strict_types=1);

namespace Lmc\TwigXBundle\Compiler;

use Twig\Environment;
use Twig\Lexer;
use Twig\Source;
use Twig\TokenStream;

class ComponentLexer extends Lexer
{
    /**
     * @param array<string, mixed> $options
     */
    public function __construct(
        Environment $env,
        array $options,
        private string $twigPathAlias
    ) {
        parent::__construct($env, $options);
    }

    public function tokenize(Source $source, ?string $name = null): TokenStream
    {
        $preparsed = $this->preparse($source->getCode());

        return parent::tokenize(
            new Source(
                $preparsed,
                $source->getName(),
                $source->getPath()
            )
        );
    }

    protected function preparse(string $value): string
    {
        return (new ComponentTagCompiler($value, $this->twigPathAlias))->compile();
    }
}
