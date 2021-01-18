<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Setting;
use App\Repository\SettingRepositoryInterface;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

final class SettingService implements SettingServiceInterface
{
    /** @var EntityManagerInterface */
    protected $em;

    /** @var SettingRepositoryInterface */
    protected $settingRepository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em                = $em;
        $this->settingRepository = $this->em->getRepository(Setting::class);
    }

    public function getRepository(): SettingRepositoryInterface
    {
        return $this->settingRepository;
    }

    public function modifySetting(array $body): Setting
    {
        $id   = 1;
        $date = new DateTime();

        $setting = $this->getRepository()->find($id);

        $hasSettings = $setting instanceof Setting;

        if (! $hasSettings) {
            $setting = new Setting();

            $setting->setId($id);
            $setting->setCreatedAt($date);
        }

        $close = isset($body['close']) ? $body['close'] === true || $body['close'] === 'true' : false;

        $setting->setClose((bool) $close);
        $setting->setUpdatedAt($date);

        if (! $hasSettings) {
            $this->em->persist($setting);
        }

        $this->em->flush();

        return $setting;
    }
}
