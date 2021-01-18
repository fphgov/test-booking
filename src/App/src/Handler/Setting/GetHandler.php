<?php

declare(strict_types=1);

namespace App\Handler\Setting;

use App\Entity\Place;
use App\Entity\Setting;
use App\Repository\PlaceRepositoryInterface;
use App\Repository\SettingRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class GetHandler implements RequestHandlerInterface
{
    /** @var EntityManagerInterface **/
    private $em;

    /** @var SettingRepositoryInterface **/
    private $settingRepository;

    /** @var PlaceRepositoryInterface **/
    private $placeRepository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em                = $em;
        $this->settingRepository = $this->em->getRepository(Setting::class);
        $this->placeRepository   = $this->em->getRepository(Place::class);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $setting = $this->settingRepository->find(1);
        $places  = $this->placeRepository->findBy([
            'active' => true,
        ]);

        return new JsonResponse([
            'options' => $setting,
            'places'  => $places,
        ]);
    }
}
