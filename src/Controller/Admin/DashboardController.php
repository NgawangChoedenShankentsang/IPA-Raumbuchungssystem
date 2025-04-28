<?php

namespace App\Controller\Admin;

use App\Entity\Company;
use App\Entity\TelefonBox;
use App\Entity\User;
use App\Entity\Status;
use App\Repository\TelefonBoxRepository;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\IconSet;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    private TelefonBoxRepository $telefonBoxRepository;

    public function __construct(TelefonBoxRepository $telefonBoxRepository)
    {
        $this->telefonBoxRepository = $telefonBoxRepository;
    }

    public function index(): Response
    {
        // return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // 1.1) If you have enabled the "pretty URLs" feature:
        // return $this->redirectToRoute('admin_user_index');
        //
        // 1.2) Same example but using the "ugly URLs" that were used in previous EasyAdmin versions:
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirectToRoute('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        return $this->render('admin/dashboard.html.twig');
    }
    public function configureAssets(): Assets
    {
        return Assets::new()
            ->useCustomIconSet('tabler')
            ->addCssFile('css/admin.css');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Tele Box');
    }

    public function configureMenuItems(): iterable
    {
        // Counter for new requests (status = ‘Pending’) 
        $newRequestsCount = $this->telefonBoxRepository->count(['status_id' => 1]);
        $label = 'Requests';
        if ($newRequestsCount > 0) {
            $label .= ' <span class="badge badge-pill badge-danger">'
                   . $newRequestsCount .
                   '</span>';
        }

        // Menu items for all Authenticated users
        yield MenuItem::linkToDashboard('Dashboard', 'home');
        
        // Menu items for ROLE_ADMIN
        yield MenuItem::linkToCrud($label, 'bell', TelefonBox::class)
            ->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('User', 'users', User::class)
            ->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Company', 'buildings', Company::class)
            ->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Status', 'flag', Status::class)
            ->setPermission('ROLE_ADMIN');
        
         // Menu items for ROLE_USER
        yield MenuItem::linkToCrud('Reserve', 'calendar-plus', TelefonBox::class)
            ->setPermission('ROLE_USER')
            ->setController(ReserveCrudController::class);

    }
}
