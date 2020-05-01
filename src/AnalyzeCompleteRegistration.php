<?php

namespace Yosmy;

interface AnalyzeCompleteRegistration
{
    /**
     * @param string $device
     * @param string $user
     */
    public function analyze(
        string $device,
        string $user
    );
}