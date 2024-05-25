<?php
declare(strict_types=1);

namespace Headsnet\DoctrineToolsBundle\Types;

use Headsnet\DoctrineToolsBundle\Attribute\DoctrineType;
use Headsnet\DoctrineToolsBundle\Types\StandardTypes\MappingPrototype;
use League\ConstructFinder\ConstructFinder;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Automatically create Doctrine types for any class that implements #[DoctrineType].
 */
final class DoctrineTypesCompilerPass implements CompilerPassInterface
{
    private const TYPE_DEFINITION_PARAMETER = 'doctrine.dbal.connection_factory.types';

    private string $rootNamespace;

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter(self::TYPE_DEFINITION_PARAMETER)) {
            return;
        }

        /** @var array<string, array{class: class-string}> $typeDefinitions */
        $typeDefinitions = $container->getParameter(self::TYPE_DEFINITION_PARAMETER);
        /** @var array<string> $scanDirs */
        $scanDirs = $container->getParameter('headsnet_doctrine_tools.custom_types.scan_dirs');
        $this->rootNamespace = $container->getParameter('headsnet_doctrine_tools.root_namespace'); // @phpstan-ignore-line

        $objectsToRegister = $this->findObjectsToRegister($scanDirs);

        $prototypeTypes = array_keys($container->findTaggedServiceIds(MappingPrototype::TAG));

        foreach ($objectsToRegister as $candidate) {
            // Do not add the type if it's been manually defined already
            if (array_key_exists($candidate->typeName, $typeDefinitions)) {
                continue;
            }

            /** @var MappingPrototype $prototypeType */
            foreach ($prototypeTypes as $prototypeType) {
                if ($prototypeType::supports($candidate->baseType)) {
                    $candidate->setBaseTypeClass(
                        $prototypeType::mappedBy()
                    );
                }
            }

            if (!$candidate->getBaseTypeClass()) {
                throw new \RuntimeException('Unsupported base type for Doctrine!');
            }

            $this->writeClassToFile($candidate);

            $typeDefinitions[$candidate->typeName] = [
                'class' => sprintf(
                    '%s\_generated\HeadsnetDoctrineTools\Types\\%s',
                    $this->rootNamespace,
                    $candidate->typeClass
                ),
            ];
        }

        $container->setParameter(self::TYPE_DEFINITION_PARAMETER, $typeDefinitions);
    }

    /**
     * @param array<string> $scanDirs
     *
     * @return iterable<CandidateType>
     */
    private function findObjectsToRegister(array $scanDirs): iterable
    {
        $classNames = ConstructFinder::locatedIn(...$scanDirs)->findClassNames();

        foreach ($classNames as $className) {
            $reflection = new ReflectionClass($className);

            // Skip any abstract parent types
            if ($reflection->isAbstract()) {
                continue;
            }

            // Only register types that have the #[DoctrineType] attribute
            if ($reflection->getAttributes(DoctrineType::class)) {
                $attribute = $reflection->getAttributes(DoctrineType::class)[0];
                $attributeArgs = $attribute->getArguments();

                yield new CandidateType(
                    typeName: $attributeArgs['name'],
                    typeClass: $reflection->getShortName() . 'Type',
                    baseType: $attributeArgs['type'],
                    objectClass: $className
                );
            }
        }
    }

    private function generateClass(CandidateType $candidate): string
    {
        return <<<PHP
<?php

namespace $this->rootNamespace\_generated\HeadsnetDoctrineTools\Types;

class $candidate->typeClass extends \\{$candidate->getBaseTypeClass()} {
    public function getName(): string
    {
        return '$candidate->typeName';
    }

    public function getClass(): string
    {
        return '$candidate->objectClass';
    }
}
PHP;
    }

    private function writeClassToFile(CandidateType $candidate): void
    {
        $classCode = $this->generateClass($candidate);

        $filePath = sprintf('src/_generated/HeadsnetDoctrineTools/Types/%s.php', $candidate->typeClass);

        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0777, true);
        }

        file_put_contents($filePath, $classCode);
    }
}
