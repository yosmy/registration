<?php

namespace Yosmy;

/**
 * @di\service()
 */
class StartRegistration
{
    /**
     * @var AddUniqueness
     */
    private $addUniqueness;

    /**
     * @var PickPhone
     */
    private $pickPhone;

    /**
     * @var AddPhone
     */
    private $addPhone;

    /**
     * @var PickSession
     */
    private $pickSession;

    /**
     * @var AddSession
     */
    private $addSession;

    /**
     * @var AddPrivilege
     */
    private $addPrivilege;

    /**
     * @var AnalyzeStartRegistration[]
     */
    private $analyzeStartRegistrationServices;

    /**
     * @di\arguments({
     *     analyzeStartRegistrationServices: '#yosmy.start_registration'
     * })
     *
     * @param AddUniqueness $addUniqueness
     * @param PickPhone $pickPhone
     * @param AddPhone $addPhone
     * @param PickSession $pickSession
     * @param AddSession $addSession
     * @param AddPrivilege $addPrivilege
     * @param AnalyzeStartRegistration[] $analyzeStartRegistrationServices
     */
    public function __construct(
        AddUniqueness $addUniqueness,
        PickPhone $pickPhone,
        AddPhone $addPhone,
        PickSession $pickSession,
        AddSession $addSession,
        AddPrivilege $addPrivilege,
        array $analyzeStartRegistrationServices
    ) {
        $this->addUniqueness = $addUniqueness;
        $this->pickPhone = $pickPhone;
        $this->addPhone = $addPhone;
        $this->pickSession = $pickSession;
        $this->addSession = $addSession;
        $this->addPrivilege = $addPrivilege;
        $this->analyzeStartRegistrationServices = $analyzeStartRegistrationServices;
    }

    /**
     * @param string   $device
     * @param string   $country
     * @param string   $prefix
     * @param string   $number
     * @param string[] $roles
     *
     * @return string
     */
    public function start(
        string $device,
        string $country,
        string $prefix,
        string $number,
        array $roles
    ): string {
        try {
            $phone = $this->pickPhone->pick(
                null,
                $country,
                $prefix,
                $number
            );

            $user = $phone->getUser();
        } catch (NonexistentPhoneException $e) {
            $user = $this->addUniqueness->add();

            $this->addPhone->add(
                $user,
                $country,
                $prefix,
                $number
            );

            $this->addPrivilege->add($user, $roles);
        }

        foreach ($this->analyzeStartRegistrationServices as $analyzeStartRegistration) {
            $analyzeStartRegistration->analyze(
                $device,
                $country,
                $prefix,
                $number,
                $roles
            );
        }

        try {
            $this->pickSession->pick(
                null,
                $user,
                $device
            );
        } catch (NonexistentSessionException $e) {
            $this->addSession->add(
                $user,
                $device
            );
        }

        return $user;
    }
}
