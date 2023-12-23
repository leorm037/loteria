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

use App\DTO\BolaoDTO;
use App\Entity\Apostador;
use App\Entity\Bolao;
use App\Entity\Concurso;
use App\Entity\Loteria;
use App\Form\BolaoFormType;
use App\Repository\ApostadorRepository;
use App\Repository\BolaoRepository;
use App\Repository\ConcursoRepository;
use App\Security\Voter\BolaoVoter;
use App\Security\Voter\UserEmailIsVerifiedVoter;
use App\Service\BolaoComprovateApostaUploaderService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Uid\Uuid;

#[Route('/bolao', name: 'app_bolao_')]
class BolaoController extends AbstractController
{
    private BolaoRepository $bolaoRepository;
    private ApostadorRepository $apostadorRepository;
    private ConcursoRepository $concursoRepository;
    private BolaoComprovateApostaUploaderService $fileUpload;
    private SluggerInterface $slugger;

    public function __construct(
        BolaoRepository $bolaoRepository,
        ApostadorRepository $apostadorRepository,
        ConcursoRepository $concursoRepository,
        BolaoComprovateApostaUploaderService $fileUpload,
        SluggerInterface $slugger
    ) {
        $this->bolaoRepository = $bolaoRepository;
        $this->apostadorRepository = $apostadorRepository;
        $this->concursoRepository = $concursoRepository;
        $this->fileUpload = $fileUpload;
        $this->slugger = $slugger;
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $usuario = $this->getUser();

        $boloes = $this->bolaoRepository->list($usuario);

        return $this->render('bolao/index.html.twig', [
                    'boloes' => $boloes,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->denyAccessUnlessGranted(
            UserEmailIsVerifiedVoter::EMAIL_IS_VERIFIED,
            null,
            'Verifique o seu e-mail e valide a sua conta de acesso.'
        );

        $bolaoDTO = new BolaoDTO();

        $form = $this->createForm(BolaoFormType::class, $bolaoDTO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $usuario = $this->getUser();

            $concurso = $this->cadastraConcursoSeNaoExistir(
                $bolaoDTO->getLoteria(),
                $bolaoDTO->getConcursoNumero()
            );

            $comprovanteApostaFile = $form->get('comprovanteAposta')->getData();

            $bolao = new Bolao();

            $bolao
                    ->setUsuario($usuario)
                    ->setConcurso($concurso)
                    ->setValorCota($bolaoDTO->getValorCota())
                    ->setNome($bolaoDTO->getNome())
            ;

            if ($comprovanteApostaFile) {
                $comprovanteAposta = $this->fileUpload->upload($comprovanteApostaFile);
                $bolao->setComprovanteAposta($comprovanteAposta);
            }

            $this->bolaoRepository->save($bolao, true);

            $this->addFlash('success', 'O bolão foi salvo com sucesso.');

            return $this->redirectToRoute('app_bolao_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('bolao/new.html.twig', [
                    'form' => $form,
                    'bolao' => $bolaoDTO,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => '[0-9a-f]{8}-[0-9a-f]{4}-6[0-9a-f]{3}-[0-9a-f]{4}-[0-9a-f]{12}'])]
    public function edit(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $uuid = Uuid::fromString($request->get('id'));
        $usuario = $this->getUser();

        $bolao = $this->bolaoRepository->findById($uuid, $usuario);

        $this->denyAccessUnlessGranted(BolaoVoter::EDIT, $bolao);

        $bolaoDTO = new BolaoDTO();
        $bolaoDTO
                ->setNome($bolao->getNome())
                ->setValorCota($bolao->getValorCota())
                ->setLoteria($bolao->getConcurso()->getLoteria())
                ->setComprovante($bolao->getComprovanteAposta())
                ->setConcursoNumero($bolao->getConcurso()->getNumero())
        ;

        $form = $this->createForm(BolaoFormType::class, $bolaoDTO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $concurso = $this->cadastraConcursoSeNaoExistir(
                $bolaoDTO->getLoteria(),
                $bolaoDTO->getConcursoNumero()
            );

            $comprovanteApostaFile = $form->get('comprovanteAposta')->getData();

            $bolao
                    ->setConcurso($concurso)
                    ->setValorCota($bolaoDTO->getValorCota())
                    ->setNome($bolaoDTO->getNome())
            ;

            if ($comprovanteApostaFile) {
                $comprovanteAposta = $this->fileUpload->upload($comprovanteApostaFile);

                if (file_exists($bolao->getComprovanteAposta())) {
                    $this->fileUpload->delete($bolao->getComprovanteAposta());
                }

                $bolao->setComprovanteAposta($comprovanteAposta);
            }

            $this->bolaoRepository->save($bolao, true);

            $this->addFlash('success', 'O bolão foi atualizado com sucesso.');

            return $this->redirectToRoute('app_bolao_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('bolao/edit.html.twig', [
                    'form' => $form,
                    'bolao' => $bolaoDTO,
        ]);
    }

    #[Route('/{id}/download', name: 'download', methods: ['GET'], requirements: ['id' => '[0-9a-f]{8}-[0-9a-f]{4}-6[0-9a-f]{3}-[0-9a-f]{4}-[0-9a-f]{12}'])]
    public function download(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $uuid = Uuid::fromString($request->get('id'));
        $usuario = $this->getUser();

        $bolao = $this->bolaoRepository->findById($uuid, $usuario);

        $this->denyAccessUnlessGranted(BolaoVoter::DOWNLOAD, $bolao);

        $dateTime = new DateTime();
        $fileName = strtolower($this->slugger->slug($bolao->getNome())).'_'.$dateTime->format('Ymdhis');
        $fileExtension = pathinfo($bolao->getComprovanteAposta(), \PATHINFO_EXTENSION);
        $fileNameDownload = $fileName.'.'.$fileExtension;

        $fileContent = file_get_contents($bolao->getComprovanteAposta(), \FILE_USE_INCLUDE_PATH);
        $response = new Response($fileContent);

        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $fileNameDownload
        );

        $response->headers->set('Content-Disposition', $disposition);

        $mimeTypes = new MimeTypes();
        $mimeType = $mimeTypes->guessMimeType($bolao->getComprovanteAposta());

        $response->headers->set('Content-type', $mimeType);

        return $response;
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['GET'], requirements: ['id' => '[0-9a-f]{8}-[0-9a-f]{4}-6[0-9a-f]{3}-[0-9a-f]{4}-[0-9a-f]{12}'])]
    public function delete(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $usuario = $this->getUser();

        $idBolao = $request->get('id');
        $uuidBolao = Uuid::fromString($idBolao);

        $bolao = $this->bolaoRepository->findById($uuidBolao, $usuario);

        if (null === $bolao) {
            return $this->json(['message' => 'error'], JsonResponse::HTTP_NOT_FOUND);
        }

        $this->denyAccessUnlessGranted(BolaoVoter::DELETE, $bolao);

        $this->excluirApostadorComprovatePagamento($bolao);
        $this->excluirBolaoComprovanteAposta($bolao);

        $this->bolaoRepository->remove($bolao, true);

        $this->addFlash('success', sprintf('O bolão "%s" foi excluído com sucesso.', $bolao->getNome()));

        return $this->json(['message' => 'success'], JsonResponse::HTTP_OK);
    }

    private function excluirBolaoComprovanteAposta(Bolao $bolao): void
    {
        $filename = $bolao->getComprovanteAposta();

        if (file_exists($filename)) {
            $this->fileUpload->delete($filename);
        }
    }

    private function excluirApostadorComprovatePagamento(Bolao $bolao): void
    {
        $apostadores = $this->apostadorRepository->list($bolao);

        /** @var Apostador $apostador */
        foreach ($apostadores as $apostador) {
            $filename = $apostador->getComprovante();

            if (file_exists($filename)) {
                $this->fileUpload->delete($filename);
            }
        }
    }

    private function cadastraConcursoSeNaoExistir(Loteria $loteria, int $concursoNumero): Concurso
    {
        $concurso = $this->concursoRepository->findByLoteriaAndNumero($loteria, $concursoNumero);

        if (null == $concurso) {
            $concurso = new Concurso();
            $concurso
                    ->setLoteria($loteria)
                    ->setNumero($concursoNumero)
            ;

            $this->concursoRepository->save($concurso, true);
        }

        return $concurso;
    }
}
