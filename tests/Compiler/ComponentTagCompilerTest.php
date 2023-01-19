<?php

declare(strict_types=1);

namespace Lmc\TwigXBundle\Compiler;

use PHPUnit\Framework\TestCase;

class ComponentTagCompilerTest extends TestCase
{
    public function testShouldCompile(): void
    {
        $compiler = new ComponentTagCompiler('<Alert color="primary" />', 'alias');

        $this->assertSame('{% embed "@alias/alert.twig" with { props: {\'color\': "primary"} } %}{% endembed %}', $compiler->compile());
    }

    public function testShouldCompileTwigVariables(): void
    {
        $compiler = new ComponentTagCompiler('<Alert variable="{{value}}" variableWithoutQuotes={{value}} />', 'alias');

        $this->assertSame('{% embed "@alias/alert.twig" with { props: {\'variable\': value,\'variableWithoutQuotes\': value} } %}{% endembed %}', $compiler->compile());
    }

    public function testShouldCompileTwigVariablesWithTwigCommentSelfClosingTag(): void
    {
        $compiler = new ComponentTagCompiler('<Alert variable="{{value}}" {# This is comment #} variableWithoutQuotes={{value}} />', 'alias');

        $this->assertSame('{% embed "@alias/alert.twig" with { props: {\'variable\': value,\'variableWithoutQuotes\': value} } %}{% endembed %}', $compiler->compile());
    }

    public function testShouldCompileTwigVariablesWithTwigComment(): void
    {
        $compiler = new ComponentTagCompiler('<Alert variable="{{value}}" {# This is comment #} variableWithoutQuotes={{value}}>Test</Alert>', 'alias');

        $this->assertSame('{% embed "@alias/alert.twig" with { props: {\'variable\': value,\'variableWithoutQuotes\': value} } %}{% block content %}Test{% endblock %}{% endembed %}', $compiler->compile());
    }

    public function testShouldCompileTwigAttributeJSXSyntax(): void
    {
        $compiler = new ComponentTagCompiler('<Alert number={12} value={ aaa } isBool={false} />', 'alias');

        $this->assertSame('{% embed "@alias/alert.twig" with { props: {\'number\': 12,\'value\': aaa,\'isBool\': false} } %}{% endembed %}', $compiler->compile());
    }

    public function testShouldCompileTwigConditions(): void
    {
        $compiler = new ComponentTagCompiler('<Alert number={ true ? 12 : 10 } />', 'alias');

        $this->assertSame('{% embed "@alias/alert.twig" with { props: {\'number\': true ? 12 : 10} } %}{% endembed %}', $compiler->compile());
    }
}
