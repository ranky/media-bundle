<?php
declare(strict_types=1);

namespace Ranky\MediaBundle\Presentation\BackOffice;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ranky/media/embed', name: 'ranky_media_embed', methods: ['GET'], priority: 5)]
class EmbedMediaBackOfficeController extends BaseMediaBackOfficeController
{

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(Request $request): Response
    {
        return $this->render(
            '@RankyMedia/embed.html.twig',
            [
                'title' => $this->trans('title'),
            ]
        );
    }

}
