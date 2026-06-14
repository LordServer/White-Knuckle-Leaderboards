<?php

namespace App\State;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Doctrine\Common\State\RemoveProcessor;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use AutoMapper\AutoMapperInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class EntityClassDtoStateProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: PersistProcessor::class)] private ProcessorInterface $persistProcessor,
        #[Autowire(service: RemoveProcessor::class)] private ProcessorInterface $removeProcessor,
        private AutoMapperInterface $autoMapper,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $stateOptions = $operation->getStateOptions();
        assert($stateOptions instanceof Options);
        $entityClass = $stateOptions->getEntityClass();

        $entity = $this->mapDtoToEntity($data, $entityClass, $uriVariables);

        if ($operation instanceof DeleteOperationInterface) {
            $this->removeProcessor->process($entity, $operation, $uriVariables, $context);

            return null;
        }

        $this->persistProcessor->process($entity, $operation, $uriVariables, $context);
        $data->id = $entity->getId();

        return $data;
    }

    private function mapDtoToEntity(
        object $dto,
        string $entityClass,
        array $uriVariables,
    ): object {
        if (isset($uriVariables['id'])) {
            $entity = $this->entityManager
                ->getRepository($entityClass)
                ->find($uriVariables['id']);

            if ($entity) {
                $this->autoMapper->map($dto, $entity);

                return $entity;
            }
        }

        return $this->autoMapper->map($dto, $entityClass);
    }
}
