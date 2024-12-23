<?php

declare(strict_types=1);

namespace Lmc\TwigXBundle\DependencyInjection\CompilerPass;

use Lmc\TwigXBundle\DependencyInjection\TwigXExtension;
use Lmc\TwigXBundle\Helper\DefinitionHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
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
     * @param array<string> $paths
     */
    #[DataProvider('registerTwigPathsDataProvider')]
    public function testShouldRegisterTwigPaths(array $paths, int $expectedCalls): void
    {
        $this->builder->setParameter('twigx.paths', $paths);
        $this->builder->setParameter('twigx.paths_alias', 'test');
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
    public static function registerTwigPathsDataProvider(): array
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

    public function testShouldExtendTwigService(): void
    {
        $this->builder->setParameter('twigx.paths', []);
        $this->builder->setParameter('twigx.paths_alias', 'test');
        $this->overrideService->process($this->builder);

        $filteredAddGlobal = DefinitionHelper::getMethodCalls(
            $this->twig,
            'addGlobal',
        );

        $filteredAddLexer = DefinitionHelper::getMethodCalls(
            $this->twig,
            'setLexer',
        );

        $this->assertSame(1, count($filteredAddGlobal) + count($filteredAddLexer));
    }
}
