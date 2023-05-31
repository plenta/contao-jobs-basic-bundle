<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Helper;

use Symfony\Component\Intl\Currencies;

class NumberHelper
{
    protected string $currency;
    protected string $locale;

    public function __construct($currency, $locale)
    {
        $this->currency = $currency;
        $this->locale = $locale;
    }

    public function reformatDecimalForDb($value): ?int
    {
        if (null === $value || '' === $value) {
            return null;
        }

        $decimalPlaces = Currencies::getFractionDigits($this->currency);
        $exp = 10 ** $decimalPlaces;
        $threshold = $decimalPlaces + 1;

        // No decimal places
        if (false === strpos($value, '.')) {
            return (int) ($value * $exp);
        }

        $pos = \strlen($value) - strpos($value, '.');

        // Remove dot
        $value = str_replace('.', '', $value);

        // One decimal place
        if ($pos < $threshold) {
            return (int) ($value * (10 ** ($threshold - $pos)));
        }

        if ($pos > $threshold) {
            $value = substr($value, 0, -($pos - $threshold));
        }

        return (int) $value;
    }

    public function formatNumberFromDbForDCAField(?string $number): ?string
    {
        if (null === $number) {
            return null;
        }

        $decimalPlaces = Currencies::getFractionDigits($this->currency);
        $exp = 10 ** $decimalPlaces;

        $thousandSeparator = '';
        $decimalSeparator = '.';

        return number_format($number / $exp, $decimalPlaces, $decimalSeparator, $thousandSeparator);
    }

    public function formatNumberFromDb(?int $number): ?string
    {
        if (null === $number) {
            return null;
        }

        $numberFormatter = \NumberFormatter::create($this->locale, \NumberFormatter::DECIMAL_SEPARATOR_SYMBOL);
        $thousandSeparator = $numberFormatter->getSymbol(\NumberFormatter::GROUPING_SEPARATOR_SYMBOL);
        $decimalSeparator = $numberFormatter->getSymbol(\NumberFormatter::DECIMAL_SEPARATOR_SYMBOL);

        $decimalPlaces = Currencies::getFractionDigits($this->currency);
        $exp = 10 ** $decimalPlaces;

        return number_format($number / $exp, $decimalPlaces, $decimalSeparator, $thousandSeparator);
    }

    public function formatCurrency(?int $number): ?string
    {
        if (null === $number) {
            return null;
        }

        $numberFormatter = \NumberFormatter::create($this->locale, \NumberFormatter::CURRENCY);
        $decimalPlaces = Currencies::getFractionDigits($this->currency);
        $exp = 10 ** $decimalPlaces;
        return $numberFormatter->formatCurrency($number / $exp, $this->currency);
    }
}
