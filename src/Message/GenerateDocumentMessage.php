<?php

namespace App\Message;

class GenerateDocumentMessage
{
    public function __construct(
        private string $documentId
    ) {
    }

    public function getDocumentId(): string
    {
        return $this->documentId;
    }
}
