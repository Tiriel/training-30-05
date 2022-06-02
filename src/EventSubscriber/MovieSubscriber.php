<?php

namespace App\EventSubscriber;

use App\Event\MovieOrderEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MovieSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $logger;
    private TokenStorageInterface $storage;

    public function __construct(LoggerInterface $logger, TokenStorageInterface $storage)
    {
        $this->logger = $logger;
        $this->storage = $storage;
    }

    public function onMovieOrder(MovieOrderEvent $event): void
    {
        if (!$user = $this->storage->getToken()->getUser()) {
            return;
        }

        $this->logger->info(sprintf(
            "A movie was viewed by user %s : \"%s\"", $user->getUserIdentifier(), $event->getMovie()->getTitle()
        ));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'movie.order' => 'onMovieOrder',
        ];
    }
}
