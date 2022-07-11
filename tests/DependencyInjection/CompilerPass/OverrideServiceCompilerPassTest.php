<?php

declare(strict_types=1);

namespace Lmc\TwigXBundle\DependencyInjection\CompilerPass;

use Lmc\TwigXBundle\DependencyInjection\TwigXExtension;
use Lmc\TwigXBundle\Helper\DefinitionHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Dumper\YamlReferenceDumper;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class OverrideServiceCompilerPassTest extends TestCase
{
    private ContainerBuilder $builder;

    private Definition $loader;

    private OverrideServiceCompilerPass $overrideService;

    private Definition $twig;

    protected function setUp(): void
    {
        $this->builder = new ContainerBuilder();
        $this->twig = new Definition(Environment::class);
        $this->builder->setDefinition('twig', $this->twig);
        $this->loader = new Definition(FilesystemLoader::class);
        $this->builder->setDefinition('twig.loader', $this->loader);
        $this->overrideService = new OverrideServiceCompilerPass();
    }

    /**
     * @dataProvider registerTwigPathsDataProvider
     * @param array<string> $paths
     */
    public function testShouldRegisterTwigPaths(array $paths, int $expectedCalls): void
    {
        $this->builder->setParameter('twigx.paths', $paths);
        $this->builder->setParameter('twigx.paths_alias', 'test');
        $this->builder->setParameter('twigx.html_syntax_lexer', false);
        $this->builder->setParameter('twigx.css_class_prefix', null);
        $this->builder->setParameter('twigx.icons.paths', []);
        $this->builder->setParameter('twigx.icons.alias', 'test-icons');
        $this->overrideService->process($this->builder);

        $filteredAddPathCalls = DefinitionHelper::getMethodCalls(
            $this->loader,
            'addPath',
            [TwigXExtension::DEFAULT_PARTIALS_PATH, TwigXExtension::DEFAULT_PARTIALS_ALIAS]
        );

        $this->assertCount($expectedCalls, $filteredAddPathCalls);
    }

    /**
     * @return array<string, mixed>
     */
    public function registerTwigPathsDataProvider(): array
    {
        $defaultPath = realpath(__DIR__ . '/../../../src/DependencyInjection') . '/../Resources/components';

        return [
            'test should register random one path' => [
                ['dir1/'], 1,
            ],
            'test should register random multiple paths' => [
                ['dir1/', 'dir2'], 2,
            ],
            'test should register paths with default' => [
                ['dir1/', 'dir2', $defaultPath], 3,
            ],
        ];
    }

    /**
     * @dataProvider extendTwigServiceDataProvider
     */
    public function testShouldExtendTwigService(bool $isLexer, int $expectedCalls): void
    {
        $this->builder->setParameter('twigx.paths', []);
        $this->builder->setParameter('twigx.paths_alias', 'test');
        $this->builder->setParameter('twigx.html_syntax_lexer', $isLexer);
        $this->builder->setParameter('twigx.css_class_prefix', null);
        $this->builder->setParameter('twigx.icons.paths', []);
        $this->builder->setParameter('twigx.icons.alias', 'test-icons');
        $this->overrideService->process($this->builder);

        $filteredAddGlobal = DefinitionHelper::getMethodCalls(
            $this->twig,
            'addGlobal',
        );

        $filteredAddLexer = DefinitionHelper::getMethodCalls(
            $this->twig,
            'setLexer',
        );

        $this->assertSame($expectedCalls, count($filteredAddGlobal) + count($filteredAddLexer));
    }

    /**
     * @return array<string, mixed>
     */
    public function extendTwigServiceDataProvider(): array
    {
        return [
            'test should register global' => [false, 1],
            'test should register lexer' => [true, 2],
        ];
    }
}
