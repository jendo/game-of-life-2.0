<?php

declare(strict_types=1);

namespace App\Parser;

use LogicException;
use Sabre\Xml\ParseException;
use Sabre\Xml\Service;
use Webmozart\Assert\Assert;

class XmlParser
{
    private Service $xmlService;

    public function __construct(Service $xmlService)
    {
        $this->xmlService = $xmlService;
    }

    /**
     * @param XmlElementDefinition[] $elementsDefinitions
     * @return void
     */
    public function mapElements(array $elementsDefinitions): void
    {
        Assert::allIsInstanceOf($elementsDefinitions, XmlElementDefinition::class);

        $elementMap = [];
        foreach ($elementsDefinitions as $elementDefinition) {
            $elementMap[$elementDefinition->getElement()] = $elementDefinition->getValue();
        }

        $this->xmlService->elementMap = $elementMap;
    }

    /**
     * @param string $input
     * @return array<string, mixed>
     * @throws FileIsNoParsableException
     */
    public function parse(string $input): array
    {
        try {
            $data = $this->xmlService->parse($input);
        } catch (ParseException $e) {
            throw new FileIsNoParsableException(
                sprintf(
                    'Can not parse xml content. ' . PHP_EOL . 'Reason: %s',
                    str_replace(PHP_EOL, '', $e->getMessage())
                ),
                0,
                $e
            );
        }

        if (is_array($data) === false) {
            throw new LogicException('Returned value of parsed xml document must be an array');
        }

        return $data;
    }
}
