<?php

/*
 *     This file is part of Loteria.
 *
 *     (c) Leonardo Rodrigues Marques <leonardo@rodriguesmarques.com.br>
 *
 *     This source file is subject to the MIT license that is bundled
 *     with this source code in the file LICENSE.
 */

namespace App\MessageHandler;

use App\Entity\Aposta;
use App\Entity\Apostador;
use App\Message\BolaoConferidoMessage;
use App\Repository\ApostadorRepository;
use App\Repository\ApostaRepository;
use App\Repository\BolaoRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

final class BolaoConferidoMessageHandler implements MessageHandlerInterface
{

    private ApostadorRepository $apostadorRepository;
    private BolaoRepository $bolaoRepository;
    private ApostaRepository $apostaRepository;
    private MailerInterface $mailer;
    private Environment $twig;
    private LoggerInterface $logger;

    public function __construct(
            ApostadorRepository $apostadorRepository,
            BolaoRepository $bolaoRepository,
            ApostaRepository $apostaRepository,
            MailerInterface $mailer,
            Environment $twig,
            LoggerInterface $logger
    )
    {
        $this->apostadorRepository = $apostadorRepository;
        $this->bolaoRepository = $bolaoRepository;
        $this->apostaRepository = $apostaRepository;
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->logger = $logger;
    }

    public function __invoke(BolaoConferidoMessage $message)
    {
        $bolaoDb = $this->bolaoRepository->findByUuid($message->getBolao()->getId());
        $apostas = $this->apostaRepository->findByBolao($bolaoDb);

        $resultados = [];

        $apostadores = $this->apostadorRepository->listEmail($bolaoDb);
        /** @var Apostador $apostador */
        $addresses = array_map(fn($apostador): string => $apostador->getEmail(), $apostadores);

        $bodyHtml = $this->twig->render('bolao/aposta/conferencia_email.html.twig', [
            'bolao' => $bolaoDb,
            'resultados' => $this->formatHtml($apostas),
        ]);

        $bodyText = $this->twig->render('bolao/aposta/conferencia_email.text.twig', [
            'bolao' => $bolaoDb,
            'resultados' => $this->formatText($apostas),
        ]);

        $email = (new Email())
                ->to(...$addresses)
                ->subject($bolaoDb->getNome())
                ->html($bodyHtml)
                ->text($bodyText)
        ;

        $this->mailer->send($email);
    }

    private function formatHtml($apostas)
    {
        $resultados = [];

        /** @var Aposta $aposta */
        foreach ($apostas as $aposta) {
            $dezenas = [];

            foreach ($aposta->getDezena() as $dezena) {
                if (\in_array($dezena, $aposta->getConcurso()->getDezena())) {
                    $dezenas[] = '<span class="sucesso">' . $dezena . '</span>';
                } else {
                    $dezenas[] = $dezena;
                }
            }

            $resultados[] = [
                'dezena' => $dezenas,
                'acerto' => $aposta->getAcerto(),
            ];
        }

        return $resultados;
    }

    private function formatText($apostas)
    {
        $resultados = [];

        /** @var Aposta $aposta */
        foreach ($apostas as $aposta) {
            $dezenas = [];

            foreach ($aposta->getDezena() as $dezena) {
                if (\in_array($dezena, $aposta->getConcurso()->getDezena())) {
                    $dezenas[] = '(' . $dezena . ')';
                } else {
                    $dezenas[] = $dezena;
                }
            }

            $resultados[] = [
                'dezena' => $dezenas,
                'acerto' => $aposta->getAcerto(),
            ];
        }

        return $resultados;
    }
}
