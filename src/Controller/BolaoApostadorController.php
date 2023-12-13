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

use App\Entity\Apostador;
use App\Form\ApostadorFormType;
use App\Repository\ApostadorRepository;
use App\Repository\BolaoRepository;
use App\Security\Voter\BolaoApostadorVoter;
use App\Service\BolaoComprovateApostadorUploaderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Uid\Uuid;

#[Route('/bolao/apostador', name: 'app_bolao_apostador_')]
class BolaoApostadorController extends AbstractController
{
    private BolaoRepository $bolaoRepository;
    private ApostadorRepository $apostadorRepository;
    private BolaoComprovateApostadorUploaderService $fileUpload;
    private SluggerInterface $slugger;

    public function __construct(
        BolaoRepository $bolaoRepository,
        ApostadorRepository $apostadorRepository,
        BolaoComprovateApostadorUploaderService $fileUpload,
        SluggerInterface $slugger
    ) {
        $this->bolaoRepository = $bolaoRepository;
        $this->apostadorRepository = $apostadorRepository;
        $this->fileUpload = $fileUpload;
        $this->slugger = $slugger;
    }

    #[Route('/{id}', name: 'index')]
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $uuid = Uuid::fromString($request->get('id'));
        $usuario = $this->getUser();

        $bolao = $this->bolaoRepository->findById($uuid, $usuario);

        $this->denyAccessUnlessGranted(
            BolaoApostadorVoter::LIST,
            $bolao,
            'Você só pode visualiar os seus próprios bolões'
        );

        $apostadores = $this->apostadorRepository->list($bolao);

        return $this->render('bolao/apostador/index.html.twig', [
                    'apostadores' => $apostadores,
                    'bolao' => $bolao,
        ]);
    }

    #[Route('/{id}/new', name: 'new')]
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $uuid = Uuid::fromString($request->get('id'));

        $usuario = $this->getUser();

        $bolao = $this->bolaoRepository->findById($uuid, $usuario);

        $this->denyAccessUnlessGranted(
            BolaoApostadorVoter::NEW,
            $bolao,
            'Você só pode cadastrar apostadores nos seus próprios bolões'
        );

        $apostador = new Apostador();
        $apostador->setBolao($bolao);

        $form = $this->createForm(ApostadorFormType::class, $apostador);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comprovantePagaentoFile = $form->get('comprovante')->getData();

            if ($comprovantePagaentoFile) {
                $comprovantePagaento = $this->fileUpload->upload($comprovantePagaentoFile);
                $apostador->setComprovante($comprovantePagaento);
            }

            $this->apostadorRepository->save($apostador, true);

            $this->addFlash('success', 'O apostador foi salvo com sucesso.');

            return $this->redirectToRoute('app_bolao_apostador_index', ['id' => $bolao->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('bolao/apostador/new.html.twig', [
                    'form' => $form,
                    'bolao' => $bolao,
                    'apostador' => $apostador,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit')]
    public function edit(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $uuid = Uuid::fromString($request->get('id'));

        $usuario = $this->getUser();

        /** @var Apostador $apostador */
        $apostador = $this->apostadorRepository->findById($uuid);

        $this->denyAccessUnlessGranted(
            BolaoApostadorVoter::NEW,
            $apostador->getBolao(),
            'Você só pode editar apostadores nos seus próprios bolões'
        );

        $form = $this->createForm(ApostadorFormType::class, $apostador);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comprovantePagamentoFile = $form->get('comprovante')->getData();

            if ($comprovantePagamentoFile) {
                $comprovantePagaento = $this->fileUpload->upload($comprovantePagamentoFile);

                if (file_exists($apostador->getComprovante())) {
                    $this->fileUpload->delete($apostador->getComprovante());
                }

                $apostador->setComprovante($comprovantePagaento);
            }

            $this->apostadorRepository->save($apostador, true);

            $this->addFlash('success', 'O apostador foi atualizado com sucesso.');

            return $this->redirectToRoute('app_bolao_apostador_index', ['id' => $apostador->getBolao()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('bolao/apostador/edit.html.twig', [
                    'form' => $form,
                    'apostador' => $apostador,
                    'bolao' => $apostador->getBolao(),
        ]);
    }

    #[Route('/{id}/download', name: 'download', methods: ['GET'], requirements: ['id' => '[0-9a-f]{8}-[0-9a-f]{4}-6[0-9a-f]{3}-[0-9a-f]{4}-[0-9a-f]{12}'])]
    public function download(Request $request): Response
    {
        $uuid = Uuid::fromString($request->get('id'));
        $usuario = $this->getUser();

        $apostador = $this->apostadorRepository->findById($uuid);

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted(
            BolaoApostadorVoter::NEW,
            $apostador->getBolao(),
            'Você só pode fazer download do comprovante de apostas dos apostadores dos seus próprios bolões'
        );

        $dateTime = new \DateTime();
        $fileName = strtolower($this->slugger->slug($apostador->getNome())).'_'.$dateTime->format('Ymdhis');
        $fileExtension = pathinfo($apostador->getComprovante(), \PATHINFO_EXTENSION);
        $fileNameDownload = $fileName.'.'.$fileExtension;

        $fileContent = file_get_contents($apostador->getComprovante(), \FILE_USE_INCLUDE_PATH);
        $response = new Response($fileContent);

        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $fileNameDownload
        );

        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
