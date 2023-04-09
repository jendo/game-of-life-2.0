<?php

declare(strict_types=1);

namespace App\Xml\Parser;

use InvalidArgumentException;
use LogicException;
use Sabre\Xml\Reader;

use function Sabre\Xml\Deserializer\keyValue;
use function Sabre\Xml\Deserializer\repeatingElements;

class ElementDefinition
{
    public const TYPE_KEY_VALUE = 'keyValueElement';
    public const TYPE_WITH_REPEATING_ELEMENTS = 'repeatingElements';

    private const AVAILABLE_TYPES = [
        self::TYPE_KEY_VALUE,
        self::TYPE_WITH_REPEATING_ELEMENTS,
    ];

    private string $element;

    private string $type;

    private ?string $childElement;

    private string $namespace;

    public function __construct(string $element, string $type, ?string $childElement = null, string $namespace = '')
    {
        if (in_array($type, self::AVAILABLE_TYPES, true) === false) {
            throw new InvalidArgumentException(sprintf('Unsupported xml element type: %s', $type));
        }

        $this->element = $this->getFullElementName($element, $namespace);
        $this->type = $type;
        $this->childElement = $this->getFullElementName($childElement, $namespace);
        $this->namespace = $namespace;
    }

    public function getElement(): string
    {
        return $this->element;
    }

    public function getValue(): callable
    {
        return match ($this->type) {
            self::TYPE_KEY_VALUE => function (Reader $reader) {
                return keyValue($reader, $this->namespace);
            },
            self::TYPE_WITH_REPEATING_ELEMENTS => function (Reader $reader) {
                return repeatingElements($reader, $this->getChildElement());
            },
            default => throw new LogicException(sprintf('Unsupported element type: %s', $this->type)),
        };
    }

    private function getFullElementName(?string $element, string $namespace): string
    {
        if ($element === null || $element === '') {
            return '';
        }

        return sprintf('{%s}%s', $namespace, $element);
    }

    private function getChildElement(): string
    {
        if ($this->type !== self::TYPE_WITH_REPEATING_ELEMENTS) {
            throw new LogicException(sprintf('This method can be called only for element type: %s', $this->type));
        }

        if ($this->childElement === null) {
            throw new LogicException(sprintf('Child element can not be null for element type: %s', $this->type));
        }

        return $this->childElement;
    }
}
