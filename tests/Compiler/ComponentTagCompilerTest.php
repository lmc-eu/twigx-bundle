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

    /**
     * @dataProvider twigParenthesesDataProvider
     */
    public function testShouldCompileTwigVariablesParentheses(string $component, string $expected): void
    {
        $compiler = new ComponentTagCompiler($component, 'alias');

        $this->assertSame($expected, $compiler->compile());
    }

    /**
     * @return array<string, string[]>
     */
    public function twigParenthesesDataProvider(): array
    {
        return [
            // component, expected
            'jsx syntax parentheses' => [
                '<Alert variable="{value}" variableWithoutQuotes={value} />',
                '{% embed "@alias/alert.twig" with { props: {\'variable\': value,\'variableWithoutQuotes\': value} } %}{% endembed %}',
            ],
            'jsx syntax parentheses with spaces' => [
                '<Alert variable="{ value }" variableWithoutQuotes={ value } />',
                '{% embed "@alias/alert.twig" with { props: {\'variable\': value,\'variableWithoutQuotes\': value} } %}{% endembed %}',
            ],
            'twig syntax parentheses' => [
                '<Alert variable="{{value}}" variableWithoutQuotes={{value}}>Test</Alert>',
                '{% embed "@alias/alert.twig" with { props: {\'variable\': value,\'variableWithoutQuotes\': value} } %}{% block content %}Test{% endblock %}{% endembed %}',
            ],
            'twig syntax parentheses with spaces' => [
                '<Alert variable="{{ value }}" variableWithoutQuotes={{ value }}>Test</Alert>',
                '{% embed "@alias/alert.twig" with { props: {\'variable\': value,\'variableWithoutQuotes\': value} } %}{% block content %}Test{% endblock %}{% endembed %}',
            ],
        ];
    }

    /**
     * @dataProvider twigCommentsDataProvider
     */
    public function testShouldCompileTwigVariablesWithTwigComment(string $component, string $expected): void
    {
        $compiler = new ComponentTagCompiler($component, 'alias');

        $this->assertSame($expected, $compiler->compile());
    }

    /**
     * @return array<string, string[]>
     */
    public function twigCommentsDataProvider(): array
    {
        return [
            // component, expected
            'comment in self-closing component' => [
                '<Alert variable="{{value}}" {# This is comment #} variableWithoutQuotes={{value}} />',
                '{% embed "@alias/alert.twig" with { props: {\'variable\': value,\'variableWithoutQuotes\': value} } %}{% endembed %}',
            ],
            'multiple comments in self-closing component' => [
                '<Alert {# This is comment #} variable="{{value}}" {# This is second comment #} variableWithoutQuotes={{value}} />',
                '{% embed "@alias/alert.twig" with { props: {\'variable\': value,\'variableWithoutQuotes\': value} } %}{% endembed %}',
            ],
            'comment in pair component' => [
                '<Alert variable="{{value}}" {# This is comment #} variableWithoutQuotes={{value}}>Test</Alert>',
                '{% embed "@alias/alert.twig" with { props: {\'variable\': value,\'variableWithoutQuotes\': value} } %}{% block content %}Test{% endblock %}{% endembed %}',
            ],
            'multiple comments in pair component' => [
                '<Alert {# This is comment #} variable="{{value}}" {# This is second comment #} variableWithoutQuotes={{value}}>Test</Alert>',
                '{% embed "@alias/alert.twig" with { props: {\'variable\': value,\'variableWithoutQuotes\': value} } %}{% block content %}Test{% endblock %}{% endembed %}',
            ],
            'comment out prop in self-closing component' => [
                '<Alert {# variable="{{value}}" #} variableWithoutQuotes={{value}} />',
                '{% embed "@alias/alert.twig" with { props: {\'variableWithoutQuotes\': value} } %}{% endembed %}',
            ],
            'comment out prop in pair component' => [
                '<Alert {# variable="{{value}}" #} variableWithoutQuotes={{value}}>Test</Alert>',
                '{% embed "@alias/alert.twig" with { props: {\'variableWithoutQuotes\': value} } %}{% block content %}Test{% endblock %}{% endembed %}',
            ],
            'empty comment in self-closing component' => [
                '<Alert variable="{{value}}" {# #} variableWithoutQuotes={{value}} />',
                '{% embed "@alias/alert.twig" with { props: {\'variable\': value,\'variableWithoutQuotes\': value} } %}{% endembed %}',
            ],
            'empty comment in pair component' => [
                '<Alert variable="{{value}}" {#  #} variableWithoutQuotes={{value}}>Test</Alert>',
                '{% embed "@alias/alert.twig" with { props: {\'variable\': value,\'variableWithoutQuotes\': value} } %}{% block content %}Test{% endblock %}{% endembed %}',
            ],
        ];
    }
}
