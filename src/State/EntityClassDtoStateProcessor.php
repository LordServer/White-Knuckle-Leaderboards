<?php

namespace App\State;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Doctrine\Common\State\RemoveProcessor;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use AutoMapper\AutoMapperInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class EntityClassDtoStateProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: PersistProcessor::class)] private ProcessorInterface $persistProcessor,
        #[Autowire(service: RemoveProcessor::class)] private ProcessorInterface $removeProcessor,
        private AutoMapperInterface $autoMapper,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $stateOptions = $operation->getStateOptions();
        assert($stateOptions instanceof Options);
        $entityClass = $stateOptions->getEntityClass();

        $entity = $this->mapDtoToEntity($data, $entityClass);

        if ($operation instanceof DeleteOperationInterface) {
            $this->removeProcessor->process($entity, $operation, $uriVariables, $context);

            return null;
        }

        $this->persistProcessor->process($entity, $operation, $uriVariables, $context);
        $data->id = $entity->getId();

        return $data;
    }

    private function mapDtoToEntity(object $dto, string $entityClass): object
    {
        //        assert($dto instanceof UserApi);
        //        if ($dto->id) {
        //            $entity = $this->userRepository->find($dto->id);
        //
        //            if (!$entity) {
        //                throw new \Exception(sprintf('User with id "%s" not found.', $dto->id));
        //            }
        //        } else {
        //            $entity = new User();
        //        }
        //
        //        $entity->setUsername($dto->username);
        //        $entity->setDiscordId($dto->discord_id);
        //        $entity->setAvatar($dto->avatar);
        //        $entity->setDisplayName($dto->display_name);
        //        $entity->setStatus(UserStatus::ACTIVE);
        //
        //        return $entity;

        return $this->autoMapper->map($dto, $entityClass);
    }
}
