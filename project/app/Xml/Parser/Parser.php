<?php

declare(strict_types=1);

namespace App\Xml\Parser;

use LogicException;
use Sabre\Xml\ParseException;
use Sabre\Xml\Service;
use Webmozart\Assert\Assert;

class Parser
{
    private Service $xmlService;

    public function __construct(Service $xmlService)
    {
        $this->xmlService = $xmlService;
    }

    /**
     * @param ElementDefinition[] $elementsDefinitions
     * @return void
     */
    public function mapElements(array $elementsDefinitions): void
    {
        Assert::allIsInstanceOf($elementsDefinitions, ElementDefinition::class);

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
        if ($this->xmlService->elementMap === []) {
            throw new LogicException('First map the xml elements.');
        }

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
