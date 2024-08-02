<?php

namespace Spatie\Mailcoach\Domain\ConditionBuilder\Enums;

use RuntimeException;

enum ComparisonOperator: string
{
    case Equals = 'equals';
    case NotEquals = 'not-equals';

    case In = 'in';
    case NotIn = 'not-in';

    case All = 'all';
    case None = 'none';
    case Any = 'any';

    case GreaterThanOrEquals = 'greater-than-or-equals';
    case SmallerThanOrEquals = 'smaller-than-or-equals';

    case Between = 'between';

    case EndsWith = 'ends-with';
    case StartsWith = 'starts-with';

    public static function fromName(string $name): self
    {
        foreach (self::cases() as $comparison) {
            if (strtolower($comparison->value) === strtolower($name)) {
                return $comparison;
            }
        }

        throw new RuntimeException("Comparison operator with name `{$name}` not found.");
    }

    public static function labels(): array
    {
        return [
            self::Equals->value => __mc('Equals To'),
            self::NotEquals->value => __mc('Not Equals To'),
            self::In->value => __mc('Has One Of'),
            self::NotIn->value => __mc('Has None Of'),
            self::All->value => __mc('Contains All'),
            self::None->value => __mc('Contains None'),
            self::Any->value => __mc('Contains Any'),
            self::GreaterThanOrEquals->value => __mc('After'),
            self::SmallerThanOrEquals->value => __mc('Before'),
            self::Between->value => __mc('Between'),
            self::EndsWith->value => __mc('Ends With'),
            self::StartsWith->value => __mc('Starts With'),
        ];
    }

    public function toOption(): array
    {
        return [$this->value => self::labels()[$this->value]];
    }

    public function toSymbol(): string
    {
        return match ($this) {
            self::Equals => '=',
            self::NotEquals => '!=',
            self::All => '>=',
            self::None => '<',
            self::GreaterThanOrEquals => '>=',
            self::SmallerThanOrEquals => '<=',
            default => throw new RuntimeException("Comparison operator `{$this->value}` does not have a matching symbol."),
        };
    }
}
