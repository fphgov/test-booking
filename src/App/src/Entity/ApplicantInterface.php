<?php

declare(strict_types=1);

namespace App\Entity;

use App\Interfaces\EntityInterface;
use DateTime;

interface ApplicantInterface extends EntityInterface
{
    public function getActive(): bool;

    public function setActive(bool $active): void;

    public function setHumanId(string $humanId): void;

    public function getHumanId(): string;

    public function setCancelHash(string $cancelHash): void;

    public function getCancelHash(): string;

    public function setPrivacy(bool $privacy): void;

    public function getPrivacy(): bool;

    public function setNotified(bool $notified): void;

    public function getNotified(): bool;

    public function setSurvey(bool $survey): void;

    public function getSurvey(): bool;

    public function setAttended(bool $attended): void;

    public function getAttended(): bool;

    public function setFirstname(string $firstname): void;

    public function getFirstname(): string;

    public function setLastname(string $lastname): void;

    public function getLastname(): string;

    public function setEmail(string $email): void;

    public function getEmail(): string;

    public function setAddress(string $address): void;

    public function getAddress(): string;

    public function setBirthdayPlace(string $birthdayPlace): void;

    public function getBirthdayPlace(): string;

    public function setBirthday(DateTime $birthday): void;

    public function getBirthday(): DateTime;

    public function setPhone(string $phone): void;

    public function getPhone(): string;

    public function setTaj(string $taj): void;

    public function getTaj(): string;

    public function getAppointment(): AppointmentInterface;

    public function setAppointment(AppointmentInterface $appointment): void;
}
