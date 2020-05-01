<?php

namespace Yosmy;

use Yosmy;
use LogicException;

/**
 * @di\service()
 */
class CompleteRegistration
{
    /**
     * @var Yosmy\GatherSession
     */
    private $gatherSession;

    /**
     * @var Yosmy\AddPassword
     */
    private $addPassword;

    /**
     * @var AnalyzeCompleteRegistration[]
     */
    private $analyzeCompleteRegistrationServices;

    /**
     * @di\arguments({
     *     analyzeCompleteRegistrationServices: '#yosmy.complete_registration'
     * })
     *
     * @param GatherSession $gatherSession
     * @param AddPassword $addPassword
     * @param AnalyzeCompleteRegistration[] $analyzeCompleteRegistrationServices
     */
    public function __construct(
        GatherSession $gatherSession,
        AddPassword $addPassword,
        array $analyzeCompleteRegistrationServices
    ) {
        $this->gatherSession = $gatherSession;
        $this->addPassword = $addPassword;
        $this->analyzeCompleteRegistrationServices = $analyzeCompleteRegistrationServices;
    }

    /**
     * @param string $device
     * @param string $user
     * @param string $password
     */
    public function complete(
        string $device,
        string $user,
        string $password
    ) {
        $session = $this->gatherSession->gather(
            null,
            $user,
            $device
        );

        if (!$session) {
            throw new LogicException();
        }

        $this->addPassword->add(
            $user,
            $password
        );

        foreach ($this->analyzeCompleteRegistrationServices as $analyzeCompleteRegistration) {
            $analyzeCompleteRegistration->analyze(
                $device,
                $user
            );
        }
    }
}
