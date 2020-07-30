<?php

namespace App\Controller\Administration;

use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;



class AdminDashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        $package = new Package(new EmptyVersionStrategy());
        return Dashboard::new()
            ->setTitle('<div style="display: flex"><object data="'
                . $package->getUrl("assets/images/OptolithCloud-Combined-SafeSpace.svg")
                . '" type="image/svg+xml" width="auto" height="auto"></object>'
                . '<p style="align-self: center; margin-top: 1rem">&vert;&nbsp;&nbsp;Administration</p></div>');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        // yield MenuItem::linkToCrud('The Label', 'icon class', EntityClass::class);
    }
}
