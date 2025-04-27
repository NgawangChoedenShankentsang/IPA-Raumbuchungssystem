<?php

namespace App\Service;

use App\Entity\TelefonBox;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Twig\Environment;

class TelefonBoxNotificationService
{
    private EntityManagerInterface $entityManager;
    private MailerInterface $mailer;
    private Environment $twig;

    public function __construct(EntityManagerInterface $entityManager, MailerInterface $mailer, Environment $twig)
    {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function notifyAdmins(TelefonBox $telefonBox): void
    {
        $adminUsers = $this->entityManager->getRepository(User::class)->findAll();

        $adminEmails = [];

        foreach ($adminUsers as $admin) {
            if (in_array('ROLE_ADMIN', $admin->getRoles(), true)) {
                $adminEmails[] = $admin->getEmail();
            }
        }

        if (empty($adminEmails)) {
            return; // No admins found, do nothing
        }

        $email = (new TemplatedEmail())
            ->from('noreply@telefonbox.ch')
            ->to(...$adminEmails)   
            ->subject('New TelefonBox Reservation')
            ->htmlTemplate('emails/notification.html.twig')
            ->context([
                'telefonBox' => $telefonBox,
            ]);

        $this->mailer->send($email);
    }
}
