<?php

if (!function_exists('format_currency')) {
    /**
     * Format amount in Indian Rupees
     *
     * @param float $amount
     * @return string
     */
    function format_currency($amount)
    {
        return '₹' . number_format($amount, 2);
    }
}

if (!function_exists('currency_symbol')) {
    /**
     * Get currency symbol
     *
     * @return string
     */
    function currency_symbol()
    {
        return '₹';
    }
}
