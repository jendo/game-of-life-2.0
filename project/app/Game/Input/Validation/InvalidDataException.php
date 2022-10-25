<?php

declare(strict_types=1);

namespace App\Game\Input\Validation;

use Exception;
use Webmozart\Assert\Assert;

class InvalidDataException extends Exception
{
    /**
     * @var Error[]
     */
    private array $errors;

    /**
     * @param Error[] $errors
     * @return self
     */
    public static function createFromErrors(array $errors): self
    {
        Assert::allIsInstanceOf($errors, Error::class);

        $self = new self('Invalid input data.');
        $self->errors = $errors;

        return $self;
    }

    /**
     * @return Error[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getPrintableMessage(): string
    {
        $message = $this->getMessage() . PHP_EOL;
        foreach ($this->errors as $error) {
            $message .= $error->getMessage() . PHP_EOL;
        }

        return $message;
    }
}
