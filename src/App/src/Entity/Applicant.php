<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\EntityMetaTrait;
use App\Traits\EntityTrait;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

use function base64_decode;
use function base64_encode;
use function getenv;
use function hash;
use function openssl_decrypt;
use function openssl_encrypt;
use function str_replace;
use function substr;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ApplicantRepository")
 * @ORM\Table(name="applicants")
 */
class Applicant implements JsonSerializable, ApplicantInterface
{
    use EntityMetaTrait;
    use EntityTrait;

    public const DISABLE_SHOW_DEFAULT = [
        'updatedAt',
    ];

    /**
     * @ORM\ManyToOne(targetEntity="AppointmentInterface")
     * @ORM\JoinColumn(name="appointmentId", referencedColumnName="id", unique=true, nullable=false)
     *
     * @var AppointmentInterface
     */
    private $appointment;

    /**
     * @ORM\Column(name="humanId", type="string", length=8, unique=true, nullable=false)
     *
     * @var string
     */
    private $humanId;

    /**
     * @ORM\Column(name="cancelHash", type="string", length=16, unique=true, nullable=false)
     *
     * @var string
     */
    private $cancelHash;

    /**
     * @ORM\Column(name="firstname", type="text")
     *
     * @var string
     */
    private $firstname;

    /**
     * @ORM\Column(name="lastname", type="text")
     *
     * @var string
     */
    private $lastname;

    /**
     * @ORM\Column(name="birthday", type="text")
     *
     * @var string
     */
    private $birthday;

    /**
     * @ORM\Column(name="birthdayPlace", type="text")
     *
     * @var string
     */
    private $birthdayPlace;

    /**
     * @ORM\Column(name="phone", type="text")
     *
     * @var string
     */
    private $phone;

    /**
     * @ORM\Column(name="email", type="text")
     *
     * @var string
     */
    private $email;

    /**
     * @ORM\Column(name="address", type="text")
     *
     * @var string
     */
    private $address;

    /**
     * @ORM\Column(name="taj", type="text")
     *
     * @var string
     */
    private $taj;

    /**
     * @ORM\Column(name="privacy", type="boolean")
     *
     * @var bool
     */
    private $privacy = false;

    /**
     * @ORM\Column(name="notified", type="boolean")
     *
     * @var bool
     */
    private $notified = false;

    /**
     * @ORM\Column(name="survey", type="boolean")
     *
     * @var bool
     */
    private $survey = false;

    /**
     * @ORM\Column(name="reminder", type="boolean")
     *
     * @var bool
     */
    private $reminder = false;

    /**
     * @ORM\Column(name="attended", type="boolean")
     *
     * @var bool
     */
    private $attended = false;

    public function setHumanId(string $humanId): void
    {
        $this->humanId = $humanId;
    }

    public function getHumanId(): string
    {
        return $this->humanId;
    }

    public function setCancelHash(string $cancelHash): void
    {
        $this->cancelHash = $cancelHash;
    }

    public function getCancelHash(): string
    {
        return $this->cancelHash;
    }

    public function setPrivacy(bool $privacy): void
    {
        $this->privacy = $privacy;
    }

    public function getPrivacy(): bool
    {
        return (bool) $this->privacy;
    }

    public function setNotified(bool $notified): void
    {
        $this->notified = $notified;
    }

    public function getNotified(): bool
    {
        return (bool) $this->notified;
    }

    public function setSurvey(bool $survey): void
    {
        $this->survey = $survey;
    }

    public function getSurvey(): bool
    {
        return (bool) $this->survey;
    }

    public function setReminder(bool $reminder): void
    {
        $this->reminder = $reminder;
    }

    public function getReminder(): bool
    {
        return (bool) $this->reminder;
    }

    public function setAttended(bool $attended): void
    {
        $this->attended = $attended;
    }

    public function getAttended(): bool
    {
        return (bool) $this->attended;
    }

    public function setFirstname(string $firstname): void
    {
        $this->firstname = $this->encrypt($firstname);
    }

    public function getFirstname(): string
    {
        return $this->decrypt($this->firstname);
    }

    public function setLastname(string $lastname): void
    {
        $this->lastname = $this->encrypt($lastname);
    }

    public function getLastname(): string
    {
        return $this->decrypt($this->lastname);
    }

    public function setEmail(string $email): void
    {
        $this->email = $this->encrypt($email);
    }

    public function getEmail(): string
    {
        return $this->decrypt($this->email);
    }

    public function setAddress(string $address): void
    {
        $this->address = $this->encrypt($address);
    }

    public function getAddress(): string
    {
        return $this->decrypt($this->address);
    }

    public function setBirthdayPlace(string $birthdayPlace): void
    {
        $this->birthdayPlace = $this->encrypt($birthdayPlace);
    }

    public function getBirthdayPlace(): string
    {
        return $this->decrypt($this->birthdayPlace);
    }

    public function setBirthday(DateTime $birthday): void
    {
        $this->birthday = $this->encrypt((string) $birthday->format('Y-m-d'));
    }

    public function getBirthday(): DateTime
    {
        return DateTime::createFromFormat('Y-m-d', $this->decrypt($this->birthday));
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $this->encrypt(str_replace(' ', '', $phone));
    }

    public function getPhone(): string
    {
        return $this->decrypt($this->phone);
    }

    public function setTaj(string $taj): void
    {
        $this->taj = $this->encrypt($taj);
    }

    public function getTaj(): string
    {
        return $this->decrypt($this->taj);
    }

    public function getAppointment(): AppointmentInterface
    {
        return $this->appointment;
    }

    public function setAppointment(AppointmentInterface $appointment): void
    {
        $this->appointment = $appointment;
    }

    private function encrypt(string $value): string
    {
        $key = hash(getenv('ENCRYPT_SHA_TYPE'), getenv('ENCRYPT_SECRET_KEY'));
        $iv  = substr(hash(getenv('ENCRYPT_SHA_TYPE'), getenv('ENCRYPT_SECRET_IV')), 0, 16);

        return base64_encode(openssl_encrypt($value, getenv('ENCRYPT_ENCRYPT_METHOD'), $key, 0, $iv));
    }

    private function decrypt(string $value): string
    {
        $key = hash(getenv('ENCRYPT_SHA_TYPE'), getenv('ENCRYPT_SECRET_KEY'));
        $iv  = substr(hash(getenv('ENCRYPT_SHA_TYPE'), getenv('ENCRYPT_SECRET_IV')), 0, 16);

        return openssl_decrypt(base64_decode($value), getenv('ENCRYPT_ENCRYPT_METHOD'), $key, 0, $iv);
    }
}
