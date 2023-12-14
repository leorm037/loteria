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
use App\Entity\Loteria;
use App\Message\BolaoConferidoMessage;
use App\Repository\ApostaRepository;
use App\Repository\ConcursoRepository;
use App\Repository\LoteriaRepository;
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
    private ConcursoRepository $concursoRepository;
    private LoteriaRepository $loteriaRepository;
    private ApostaRepository $apostaRepository;
    private $messages = [];

    public function __construct(
        ConcursoRepository $concursoRepository,
        LoteriaRepository $loteriaRepository,
        ApostaRepository $apostaRepository,
        MessageBusInterface $bus
    ) {
        $this->concursoRepository = $concursoRepository;
        $this->loteriaRepository = $loteriaRepository;
        $this->apostaRepository = $apostaRepository;
        $this->bus = $bus;

        parent::__construct();
    }

    protected function configure(): void
    {
        //        $this
        //                ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
        //                ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        //        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $loterias = $this->loteriaRepository->list();

        /** @var Loteria $loteria */
        foreach ($loterias as $loteria) {
            $this->messages[] = [
                'status' => 'title',
                'message' => sprintf('Loteria %s', $loteria->getNome()),
            ];

            $concurso = $this->concursoRepository->findLast($loteria);

            $this->messages[] = [
                'status' => 'info',
                'message' => sprintf('Concurso %s', $concurso->getNumero()),
            ];

            /** @var Aposta $aposta */
            $apostas = $this->apostaRepository->findNaoApurado($concurso);

            $bolaoConferido = [];

            foreach ($apostas as $aposta) {
                if (null !== $aposta->getBolao()) {
                    $bolaoConferido[] = $aposta->getBolao();
                }

                $acertoArray = array_intersect($aposta->getDezena(), $concurso->getDezena());
                $isPremiado = \in_array(\count($acertoArray), $loteria->getPremiar(), true);

                $aposta
                        ->setAcerto(\count($acertoArray))
                        ->setPremiado($isPremiado)
                        ->setIsConferido(true)
                ;

                $this->apostaRepository->save($aposta, true);

                $this->messages[] = [
                    'status' => 'text',
                    'message' => sprintf(
                        'Dezenas apostadas "%s" com %s acerto(s).',
                        implode(', ', $aposta->getDezena()),
                        $aposta->getAcerto()
                    ),
                ];
            }

            $bolaoConferido = array_unique($bolaoConferido, \SORT_REGULAR);

            foreach ($bolaoConferido as $bolao) {
                $this->bus->dispatch(new BolaoConferidoMessage($bolao));
            }
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
