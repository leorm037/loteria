<?php

/*
 *     This file is part of Loteria.
 *
 *     (c) Leonardo Rodrigues Marques <leonardo@rodriguesmarques.com.br>
 *
 *     This source file is subject to the MIT license that is bundled
 *     with this source code in the file LICENSE.
 */

namespace App\Command;

use App\Entity\Aposta;
use App\Message\BolaoConferidoMessage;
use App\Repository\ApostaRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'loteria:aposta:conferir',
    description: 'Conferir as apostas',
)]
class LoteriaApostaConferirCommand extends Command
{
    private MessageBusInterface $bus;
    private ApostaRepository $apostaRepository;
    private $messages = [];

    public function __construct(
        ApostaRepository $apostaRepository,
        MessageBusInterface $bus
    ) {
        $this->apostaRepository = $apostaRepository;
        $this->bus = $bus;

        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $bolaoApostaConferida = [];

        $apostas = $this->apostaRepository->findNaoConferidoConcursoSorteado();

        /** @var Aposta $aposta */
        foreach ($apostas as $aposta) {
            $dezenasAcertadas = array_intersect($aposta->getDezena(), $aposta->getConcurso()->getDezena());
            $isPremiado = \in_array(\count($dezenasAcertadas), $aposta->getConcurso()->getLoteria()->getPremiar(), true);

            $aposta
                    ->setAcerto(\count($dezenasAcertadas))
                    ->setPremiado($isPremiado)
                    ->setIsConferido(true)
            ;

            $this->apostaRepository->save($aposta, true);

            $this->messages[] = [
                'status' => 'text',
                'message' => sprintf(
                    'Loteria %s, concurso %s, aposta %s.',
                    $aposta->getConcurso()->getLoteria()->getNome(),
                    $aposta->getConcurso()->getNumero(),
                    implode('-', $aposta->getDezena())
                ),
            ];

            if (null !== $aposta->getBolao()) {
                $bolaoApostaConferida[$aposta->getBolao()->getId()->toBase32()] = $aposta->getBolao();
            }
        }

        foreach ($bolaoApostaConferida as $bolao) {
            $this->bus->dispatch(new BolaoConferidoMessage($bolao));
        }

        $this->exibirMensagens($io);

        return Command::SUCCESS;
    }

    private function exibirMensagens(SymfonyStyle $io): void
    {
        foreach ($this->messages as $message) {
            switch ($message['status']) {
                case 'success':
                    $io->success($message['message']);
                    break;
                case 'danger':
                    $io->error($message['message']);
                    break;
                case 'title':
                    $io->title($message['message']);
                    break;
                case 'info':
                    $io->info($message['message']);
                    break;
                default:
                    $io->text($message['message']);
                    break;
            }
        }
    }
}
