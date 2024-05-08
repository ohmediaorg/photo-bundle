<?php

namespace OHMedia\PhotoBundle\Service;

use OHMedia\BackendBundle\Shortcodes\AbstractShortcodeProvider;
use OHMedia\BackendBundle\Shortcodes\Shortcode;
use OHMedia\PhotoBundle\Repository\GalleryRepository;

class GalleryShortcodeProvider extends AbstractShortcodeProvider
{
    public function __construct(private GalleryRepository $galleryRepository)
    {
    }

    public function getTitle(): string
    {
        return 'Galleries';
    }

    public function buildShortcodes(): void
    {
        $galleries = $this->galleryRepository->createQueryBuilder('g')
            ->orderBy('g.name', 'asc')
            ->getQuery()
            ->getResult();

        foreach ($galleries as $gallery) {
            $id = $gallery->getId();

            $this->addShortcode(new Shortcode(
                sprintf('%s (ID:%s)', $gallery, $id),
                'gallery('.$id.')'
            ));
        }
    }
}
