<?php

/*
 *     This file is part of Loteria.
 *
 *     (c) Leonardo Rodrigues Marques <leonardo@rodriguesmarques.com.br>
 *
 *     This source file is subject to the MIT license that is bundled
 *     with this source code in the file LICENSE.
 */

namespace App\EventSubscriber;

use App\Entity\Usuario;
use App\Helper\DateTimeHelper;
use App\Repository\UsuarioRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginSubscriber implements EventSubscriberInterface
{
    private UsuarioRepository $usuarioRepository;

    public function __construct(UsuarioRepository $usuarioRepository)
    {
        $this->usuarioRepository = $usuarioRepository;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
    {
        /** @var Usuario $usuario */
        $usuario = $event->getAuthenticationToken()->getUser();
        $ip = $event->getRequest()->getClientIp();

        if (null !== $usuario) {
            $usuario->setLastLoginAt(DateTimeHelper::currentDateTime());
            $usuario->setLastLoginIp($ip);
            $this->usuarioRepository->save($usuario, true);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'security.interactive_login' => 'onSecurityInteractiveLogin',
        ];
    }
}
