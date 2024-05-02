<?php

namespace OHMedia\PhotoBundle\Service;

use OHMedia\BackendBundle\Service\AbstractNavItemProvider;
use OHMedia\BootstrapBundle\Component\Nav\NavItemInterface;
use OHMedia\BootstrapBundle\Component\Nav\NavLink;
use OHMedia\PhotoBundle\Entity\Gallery;
use OHMedia\PhotoBundle\Security\Voter\GalleryVoter;

class GalleryNavItemProvider extends AbstractNavItemProvider
{
    public function getNavItem(): ?NavItemInterface
    {
        if ($this->isGranted(GalleryVoter::INDEX, new Gallery())) {
            return (new NavLink('Photos', 'gallery_index'))
                ->setIcon('camera-fill');
        }

        return null;
    }
}
