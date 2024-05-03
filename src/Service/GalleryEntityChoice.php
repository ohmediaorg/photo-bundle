<?php

namespace OHMedia\PhotoBundle\Service;

use OHMedia\PhotoBundle\Entity\Gallery;
use OHMedia\PhotoBundle\Entity\Photo;
use OHMedia\SecurityBundle\Service\EntityChoiceInterface;

class GalleryEntityChoice implements EntityChoiceInterface
{
    public function getLabel(): string
    {
        return 'Galleries';
    }

    public function getEntities(): array
    {
        return [Gallery::class, Photo::class];
    }
}
