<?php

if (! function_exists('only_numbers')) {
    function only_numbers(string $data): string
    {
        return preg_replace('/[^0-9]/', '', $data);
    }
}

if (! function_exists('format_phone')) {
    function format_phone(string $data): string
    {
        $numbers = only_numbers($data);

        if (strlen($numbers) < 10) {
            return $numbers;
        }

        if (strlen($numbers) === 10) {
            return '(' . substr($numbers, 0, 2) . ') '
                . substr($numbers, 2, 4) . '-'
                . substr($numbers, 6);
        }

        return '(' . substr($numbers, 0, 2) . ') '
                . substr($numbers, 2, 5) . '-'
                . substr($numbers, 7);
    }
}

if (! function_exists('format_cpf')) {
    function format_cpf(string $data): string
    {
        $numbers = only_numbers($data);

        if (strlen($numbers) < 11) {
            return $numbers;
        }

        return substr($numbers, 0, 3) . '.'
            . substr($numbers, 3, 3) . '.'
            . substr($numbers, 6, 3) . '-'
            . substr($numbers, 9);
    }
}

if (! function_exists('format_cnpj')) {
    function format_cnpj(string $data): string
    {
        $numbers = only_numbers($data);

        if (strlen($numbers) < 14) {
            return $numbers;
        }

        return substr($numbers, 0, 2) . '.'
            . substr($numbers, 2, 3) . '.'
            . substr($numbers, 5, 3) . '/'
            . substr($numbers, 8, 4) . '-'
            . substr($numbers, 12);
    }
}

if (! function_exists('format_doc')) {
    function format_doc(string $data): string
    {
        $numbers = only_numbers($data);

        if (strlen($numbers) < 11) {
            return $numbers;
        }

        if (strlen($numbers) === 11) {
            return format_cpf($numbers);
        }

        return format_cnpj($numbers);
    }
}
