<?php

/*
 *     This file is part of Loteria.
 *
 *     (c) Leonardo Rodrigues Marques <leonardo@rodriguesmarques.com.br>
 *
 *     This source file is subject to the MIT license that is bundled
 *     with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\DTO\BolaoApostaDTO;
use App\DTO\BolaoApostaImportDTO;
use App\DTO\PaginatorDTO;
use App\Entity\Aposta;
use App\Entity\Bolao;
use App\Form\BolaoApostaFormType;
use App\Form\BolaoApostaImportFormType;
use App\Helper\CsvReaderHelper;
use App\Repository\ApostaRepository;
use App\Repository\BolaoRepository;
use App\Security\Voter\BolaoApostaVoter;
use App\Validator\ArrayValueNotRepeat;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/bolao', name: 'app_bolao_aposta_')]
class BolaoApostaController extends AbstractController
{

    private BolaoRepository $bolaoRepository;
    private ApostaRepository $apostaRepository;
    private ValidatorInterface $validator;

    public function __construct(
            BolaoRepository $bolaoRepository,
            ValidatorInterface $validator,
            ApostaRepository $apostaRepository
    )
    {
        $this->bolaoRepository = $bolaoRepository;
        $this->apostaRepository = $apostaRepository;
        $this->validator = $validator;
    }

    #[Route('/{idBolao}/aposta', name: 'index')]
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $uuid = Uuid::fromString($request->get('idBolao'));
        $usuario = $this->getUser();

        $bolao = $this->bolaoRepository->findById($uuid, $usuario);

        $page = $request->get('page', 1);
        $maxResult = $request->get('maxResult', 10);

        $paginator = new PaginatorDTO();
        $paginator
                ->setPage($page)
                ->setMaxResult($maxResult)
        ;

        $apostas = $this->apostaRepository->listPesquisar(
                $bolao,
                $paginator->getFirstResult(),
                $paginator->getMaxResult()
        );

        $paginator->setResult($apostas);

        return $this->render('bolao/aposta/index.html.twig', [
                    'paginator' => $paginator,
                    'bolao' => $bolao,
        ]);
    }

    #[Route(
                '{idBolao}/aposta/{idAposta}/edit',
                name: 'edit',
                requirements: [
            'idBolao' => '[0-9a-f]{8}-[0-9a-f]{4}-6[0-9a-f]{3}-[0-9a-f]{4}-[0-9a-f]{12}',
            'idAposta' => '[0-9a-f]{8}-[0-9a-f]{4}-6[0-9a-f]{3}-[0-9a-f]{4}-[0-9a-f]{12}',
                ]
        )]
    public function edit(Request $request): Response
    {
        $idBolao = $request->get('idBolao');
        $uuidBolao = Uuid::fromString($idBolao);
        $bolao = $this->bolaoRepository->findByUuid($uuidBolao);

        $idAposta = $request->get('idAposta');
        $uuidAposta = Uuid::fromString($idAposta);
        $aposta = $this->apostaRepository->findByUuid($uuidAposta);

        $bolaoApostaDTO = new BolaoApostaDTO();
        $bolaoApostaDTO
                ->setIdLoteria($bolao->getConcurso()->getLoteria()->getId())
                ->setDezenas($aposta->getDezena())
                ->setDezenasMarcar(count($aposta->getDezena()))
        ;

        $form = $this->createForm(BolaoApostaFormType::class, $bolaoApostaDTO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $aposta->setDezena($bolaoApostaDTO->getDezenas());
            
            $this->apostaRepository->save($aposta, true);

            $this->addFlash('success', 'A aposta "%s" foi atualizada com sucesso.');

            return $this->redirectToRoute('app_bolao_aposta_index', ['idBolao' => $bolao->getId()]);
        }

        return $this->renderForm('bolao/aposta/edit.html.twig', [
                    'bolao' => $bolao,
                    'form' => $form,
        ]);
    }

    #[Route('/{idBolao}/aposta/new', name: 'new')]
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $idBolao = $request->get('idBolao');
        $uuidBolao = Uuid::fromString($idBolao);
        $bolao = $this->bolaoRepository->findByUuid($uuidBolao);

        $aposta = new Aposta();
        $aposta->setBolao($bolao);
        $form = $this->createForm(BolaoApostaFormType::class, $aposta);

        $this->denyAccessUnlessGranted(BolaoApostaVoter::NEW, $aposta);

        return $this->renderForm('bolao/aposta/new.html.twig', [
                    'bolao' => $bolao,
                    'form' => $form,
        ]);
    }

    #[Route('/{id}/aposta/import', name: 'import')]
    public function import(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $uuid = Uuid::fromString($request->get('idBolao'));
        $usuario = $this->getUser();

        $bolao = $this->bolaoRepository->findById($uuid, $usuario);

        $this->denyAccessUnlessGranted(BolaoApostaVoter::EDIT, $aposta);

        $bolaoApostaImport = new BolaoApostaImportDTO();
        $bolaoApostaImport->setBolao($bolao);
        $form = $this->createForm(BolaoApostaImportFormType::class, $bolaoApostaImport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $csvFile */
            $fileCsv = $form->get('fileCsv')->getData();

            $csvReaderHelp = new CsvReaderHelper($fileCsv->getRealPath());

            foreach ($csvReaderHelp->getIterator() as $row) {
                $dezenas = array_map('intval', $row);

                if (\count($dezenas) < min($bolao->getConcurso()->getLoteria()->getMarcar())) {
                    continue;
                }

                $aposta = new Aposta();
                $aposta
                        ->setBolao($bolao)
                        ->setUsuario($usuario)
                        ->setDezena($dezenas)
                        ->setConcurso($bolao->getConcurso())
                ;

                $violations = $this->validator->validate(
                        $aposta->getDezena(),
                        [new ArrayValueNotRepeat()]
                );

                if ($violations->count() > 0) {
                    /** @var ConstraintViolationInterface $violation */
                    foreach ($violations as $violation) {
                        $this->addFlash('danger', $violation->getMessage());
                    }
                    continue;
                }

                if ($this->isDezenasCadastradasBolao($aposta)) {
                    $this->addFlash('danger', sprintf('As dezenas %s já foram cadastradas no bolão.', implode(', ', $aposta->getDezena())));
                    continue;
                }

                $this->apostaRepository->save($aposta, true);
            }

            $this->addFlash('info', sprintf('Importação do arquivo "%s" concluída.', $fileCsv->getClientOriginalName()));

            return $this->redirectToRoute('app_bolao_aposta_index', [
                        'idBolao' => $bolao->getId(),
                            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('bolao/aposta/import.html.twig', [
                    'bolao' => $bolao,
                    'form' => $form,
        ]);
    }

    private function isDezenasCadastradasBolao(Aposta $aposta): bool
    {
        $apostaDb = $this->apostaRepository->findByDezenasOnBolao($aposta);

        if (null !== $apostaDb) {
            return true;
        }

        return false;
    }

    #[Route(
                '/{idBolao}/aposta/{idAposta}/delete',
                name: 'delete',
                methods: ['GET'],
                requirements: [
            'idBolao' => '[0-9a-f]{8}-[0-9a-f]{4}-6[0-9a-f]{3}-[0-9a-f]{4}-[0-9a-f]{12}',
            'idAposta' => '[0-9a-f]{8}-[0-9a-f]{4}-6[0-9a-f]{3}-[0-9a-f]{4}-[0-9a-f]{12}',
                ]
        )]
    public function delete(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $idBolao = $request->get('idBolao');
        $idAposta = $request->get('idAposta');
        $uuidAposta = Uuid::fromString($idAposta);

        $aposta = $this->apostaRepository->findByUuid($uuidAposta);

        if (null === $aposta) {
            $this->addFlash('warning', 'Esta aposta não foi encontrada para excluir.');

            return $this->redirectToRoute('app_bolao_aposta_index', ['idBolao' => $idBolao], Response::HTTP_SEE_OTHER);
        }

        $this->denyAccessUnlessGranted(BolaoApostaVoter::DELETE, $aposta);

        $this->apostaRepository->remove($aposta, true);

        $this->addFlash('success', sprintf('A aposta "%s" foi excluída com sucesso.', implode(', ', $aposta->getDezena())));

        return $this->redirectToRoute('app_bolao_aposta_index', ['idBolao' => $idBolao], Response::HTTP_SEE_OTHER);
    }
}
