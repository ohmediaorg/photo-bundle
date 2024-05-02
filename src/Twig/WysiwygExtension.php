<?php

namespace OHMedia\PhotoBundle\Twig;

use OHMedia\FileBundle\Service\FileManager;
use OHMedia\PhotoBundle\Repository\GalleryRepository;
use OHMedia\WysiwygBundle\Twig\AbstractWysiwygExtension;
use Symfony\Component\HttpFoundation\UrlHelper;
use Twig\Environment;
use Twig\TwigFunction;

class WysiwygExtension extends AbstractWysiwygExtension
{
    public function __construct(
        private FileManager $fileManager,
        private GalleryRepository $galleryRepository,
        private UrlHelper $urlHelper
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('gallery', [$this, 'gallery'], [
                'is_safe' => ['html'],
                'needs_environment' => true,
            ]),
        ];
    }

    public function gallery(Environment $twig, int $id = null)
    {
        $gallery = $this->galleryRepository->find($id);

        if (!$gallery) {
            return '';
        }

        $photos = $gallery->getPhotos();

        if (!count($photos)) {
            return '';
        }

        $rendered = $twig->render('@OHMediaPhoto/gallery.html.twig', [
            'gallery' => $gallery,
        ]);

        foreach ($photos as $photo) {
            $webPath = $this->fileManager->getWebPath($photo->getImage());

            $schema = [
                '@context' => 'https://schema.org',
                '@type' => 'ImageObject',
                'contentUrl' => $this->urlHelper->getAbsoluteUrl($webPath),
                'caption' => $photo->getCaption(),
            ];

            if ($credit = $photo->getCredit()) {
                $schema['creator'] = [
                    '@type' => 'Person',
                    'name' => $credit,
                ];
            }

            $rendered .= '<script type="application/ld+json">'.json_encode($schema).'</script>';
        }

        return $rendered;
    }
}
