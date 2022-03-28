<?php

declare(strict_types=1);

namespace Lmc\SpiritWebTwigBundle\DependencyInjection\CompilerPass;

use Lmc\SpiritWebTwigBundle\DependencyInjection\SpiritWebTwigExtension;
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
        $this->builder->setParameter('spirit_web_twig.paths', $paths);
        $this->builder->setParameter('spirit_web_twig.paths_alias', 'test');
        $this->builder->setParameter('spirit_web_twig.html_syntax_lexer', false);
        $this->builder->setParameter('spirit_web_twig.spirit_css_class_prefix', null);
        $this->overrideService->process($this->builder);

        $this->assertCount($expectedCalls, $this->loader->getMethodCalls());
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
                ['dir1/', 'dir2', $defaultPath], 4,
            ],
        ];
    }

    /**
     * @dataProvider extendTwigServiceDataProvider
     */
    public function testShouldExtendTwigService(bool $isLexer, int $expectedCalls): void
    {
        $this->builder->setParameter('spirit_web_twig.paths', []);
        $this->builder->setParameter('spirit_web_twig.paths_alias', 'test');
        $this->builder->setParameter('spirit_web_twig.html_syntax_lexer', $isLexer);
        $this->builder->setParameter('spirit_web_twig.spirit_css_class_prefix', null);
        $this->overrideService->process($this->builder);

        $this->assertCount($expectedCalls, $this->twig->getMethodCalls());
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