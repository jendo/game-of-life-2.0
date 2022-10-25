<?php

declare(strict_types=1);

namespace App\Game\Input\Validation;

class Error
{
    public const MISSING_FIELD_MESSAGE = 'Field %s is missing.';
    public const INVALID_FIELD_TYPE_MESSAGE = 'Field %s has invalid type.';
    public const EMPTY_FIELD_MESSAGE = 'Field %s is empty.';

    private string $message;
    private string $field;

    public function __construct(string $message, string $field)
    {
        $this->message = $message;
        $this->field = $field;
    }

    public function getMessage(): string
    {
        return sprintf($this->message, $this->field);
    }
}
