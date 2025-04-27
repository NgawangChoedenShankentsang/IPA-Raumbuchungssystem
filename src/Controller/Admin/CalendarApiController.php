<?php

namespace App\Controller\Admin;

use App\Repository\TelefonBoxRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class CalendarApiController extends AbstractController
{
    #[Route('/api/telefonbox-events', name: 'api_telefonbox_events')]
    public function getTelefonBoxEvents(TelefonBoxRepository $repository): JsonResponse
    {
        $events = [];

        foreach ($repository->findAll() as $box) {
            $start = $box->getStartTime();
            $end = $box->getEndTime();
            $user = $box->getUserId();

            $events[] = [
                'title' => sprintf(
                    '%s | %s',
                    $user ? $user->getName() : 'Unknown User',
                    $box->getTitle()
                ),
                'start' => $start?->format('Y-m-d\TH:i:s'),
                'end'   => $end?->format('Y-m-d\TH:i:s'),
            ];
        }

        return new JsonResponse($events);
    }

}
