<?php

namespace App\Controller\Administration;

use App\Entity\Character;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Contracts\Translation\TranslatorInterface;


class AdminDashboardController extends AbstractDashboardController
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        $this->translator->setLocale($this->getUser()->getLocale());
        $package = new Package(new EmptyVersionStrategy());
        return Dashboard::new()
            ->setTitle('<div style="display: flex"><object data="'
                . $package->getUrl("assets/images/OptolithCloud-Combined-SafeSpace.svg")
                . '" type="image/svg+xml"></object>'
                . '<span style="align-self: center;">&vert;&nbsp;Administration</span></div>')
            ->setTranslationDomain("administration")
            ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard("menu.label.item.dashboard", 'fa fa-circle');
        yield MenuItem::section("menu.label.section.cloudData", 'fa fa-cloud');
        yield MenuItem::linkToCrud("menu.label.item.users", "fa fa-user", User::class);
        yield MenuItem::linkToCrud("menu.label.item.characters", "fa fa-mask", Character::class);
        yield MenuItem::section("menu.label.section.system", 'fa fa-info-circle');
    }
}
