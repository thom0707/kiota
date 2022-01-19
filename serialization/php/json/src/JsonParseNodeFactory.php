<?php

namespace Microsoft\Kiota\Serialization\Json;

use JsonException;
use InvalidArgumentException;
use Microsoft\Kiota\Abstractions\Serialization\ParseNode;
use Microsoft\Kiota\Abstractions\Serialization\ParseNodeFactory;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

class JsonParseNodeFactory implements ParseNodeFactory
{

    /**
     * @inheritDoc
     */
    public function getParseNode(string $contentType, StreamInterface $rawResponse): ParseNode {
        if (empty($contentType)) {
            throw new InvalidArgumentException('$contentType cannot be empty.');
        }

        if ($this->getValidContentType() !== $contentType){
            throw new InvalidArgumentException("expected a {$this->getValidContentType()} content type.");
        }
        if (empty($rawResponse->getContents())){
            throw new InvalidArgumentException('$rawResponse cannot be empty.');
        }
        try {
            $content = json_decode($rawResponse->getContents(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $ex){
            throw new RuntimeException('The was a problem parsing the response.', 1, $ex);
        }
        return new JsonParseNode($content);
    }

    /**
     * @inheritDoc
     */
    public function getValidContentType(): string {
        return 'application/json';
    }
}