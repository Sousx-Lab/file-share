<?php

namespace App\Controller\File;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\FileServices\UploadFileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class UploadFileController extends AbstractController
{
    /**
     * @Route("/upload", name="route_file_upload", methods="POST")
     * @return Response
     */
    public function upload(Request $request, UploadFileService $uploadService): Response
    {

        $error = null;
        $token = $request->request->get('_token');
        $uploadedFiles = $request->files->get('files');

        if(null === $uploadedFiles)
        {
           $this->addFlash('danger', "The file exceeds the allowed limit of " . ini_get("upload_max_filesize"));
           return $this->redirectToRoute("route_homepage");
        }

        if (false === $this->isCsrfTokenValid('upload', $token)) {
            throw new UnauthorizedHttpException('Invalid Csrf token', null, null, 401);
            
        }

        $uploadedFiles = $request->files->get('files');
        if (empty($uploadedFiles)) {
            throw new UnprocessableEntityHttpException('No file has been uploaded', null, 422);
        
        }

        try {
            $files = $uploadService->UploadFile($uploadedFiles, $this->getUser());
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        }

        return $this->render('file/upload/uploaded.file.download.html.twig', [
            'files' => $files,
            'error' => $error
        ]);
    }
}
