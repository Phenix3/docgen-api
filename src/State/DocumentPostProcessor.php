<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Document;
use App\Message\GenerateDocumentMessage;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class DocumentPostProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        private MessageBusInterface $bus
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        // First, persist the entity
        $result = $this->persistProcessor->process($data, $operation, $uriVariables, $context);

        // Then, dispatch the message if it's a new Document
        if ($data instanceof Document && $operation->getMethod() === 'POST') {
             $this->bus->dispatch(new GenerateDocumentMessage($data->getId()->toString()));
        }

        return $result;
    }
}
