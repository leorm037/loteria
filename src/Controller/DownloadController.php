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

use App\Repository\BolaoRepository;
use Exception;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Uid\Uuid;

#[Route('/download', name: 'app_download_')]
class DownloadController extends AbstractController
{

    private BolaoRepository $bolaoRepository;
    private LoggerInterface $logger;
    private SluggerInterface $slug;

    public function __construct(
            BolaoRepository $bolaoRepository,
            LoggerInterface $logger,
            SluggerInterface $slug
    )
    {
        $this->bolaoRepository = $bolaoRepository;
        $this->logger = $logger;
        $this->slug = $slug;
    }

    #[Route('/bolao/{id}/comprovante', name: 'bolao_comprovante')]
    public function bolaoComprovante(Request $request): BinaryFileResponse
    {
        try {
            $idBolao = $request->get('id');

            $uuidBolao = Uuid::fromString($idBolao);

            $bolao = $this->bolaoRepository->findByUuid($uuidBolao);

            if (!file_exists($bolao->getComprovanteAposta())) {
                
            }

            $fileName = $this->slug->slug($bolao->getNome());
            $fileExtension = pathinfo($bolao->getComprovanteAposta(), \PATHINFO_EXTENSION);
            $fileNameDownload = $fileName . '.' . $fileExtension;

            $response = new BinaryFileResponse(
                    $bolao->getComprovanteAposta(),
                    BinaryFileResponse::HTTP_OK
            );

            $response->setContentDisposition(HeaderUtils::DISPOSITION_ATTACHMENT, $fileNameDownload);

            return $response;
        } catch (InvalidArgumentException $iae) {
            $this->logger->error($iae->getMessage());
            
            throw $this->createNotFoundException("Arquivo não encontrado");
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            
            throw $this->createNotFoundException("O arquivo não existe mais.");
        }
    }
}
