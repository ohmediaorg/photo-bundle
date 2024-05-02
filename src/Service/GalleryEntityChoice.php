<?php

namespace OHMedia\PhotoBundle\Service;

use OHMedia\PhotoBundle\Entity\Gallery;
use OHMedia\SecurityBundle\Service\EntityChoiceInterface;

class GalleryEntityChoice implements EntityChoiceInterface
{
    public function getLabel(): string
    {
        return 'Photos';
    }

    public function getEntities(): array
    {
        return [Gallery::class];
    }
}
