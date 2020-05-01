<?php

namespace Yosmy;

interface AnalyzeStartRegistration
{
    /**
     * @param string   $device
     * @param string   $country
     * @param string   $prefix
     * @param string   $number
     * @param string[] $roles
     */
    public function analyze(
        string $device,
        string $country,
        string $prefix,
        string $number,
        array $roles
    );
}