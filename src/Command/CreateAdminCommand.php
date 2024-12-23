<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Créer un administrateur avec un rôle ROLE_ADMIN.',
)]
class CreateAdminCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('firstname', null, InputOption::VALUE_REQUIRED, 'Prénom de l\'administrateur')
            ->addOption('lastname', null, InputOption::VALUE_REQUIRED, 'Nom de l\'administrateur')
            ->addOption('email', null, InputOption::VALUE_REQUIRED, 'Email de l\'administrateur')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'Mot de passe de l\'administrateur');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $firstName = $input->getOption('firstname') ?? $helper->ask($input, $output, new Question('Entrez le prénom de l\'administrateur : '));
        $lastName = $input->getOption('lastname') ?? $helper->ask($input, $output, new Question('Entrez le nom de l\'administrateur : '));
        $email = $input->getOption('email') ?? $helper->ask($input, $output, new Question('Entrez l\'email de l\'administrateur : '));
        $password = $input->getOption('password') ?? $helper->ask($input, $output, new Question('Entrez le mot de passe : '));

        $admin = new User();
        $admin->setFirstname($firstName);
        $admin->setLastname($lastName);
        $admin->setEmail($email);
        $hashedPassword = $this->passwordHasher->hashPassword($admin, $password);
        $admin->setPassword($hashedPassword);
        // Ajout du rôle ROLE_ADMIN
        $admin->setRoles(['ROLE_ADMIN']);

        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        $output->writeln('<info>Administrateur créé avec succès !</info>');
        $output->writeln(sprintf('Email : %s', $email));
        $output->writeln(sprintf('Prénom : %s', $firstName));
        $output->writeln(sprintf('Nom : %s', $lastName));
        return Command::SUCCESS;

//        php bin/console app:create-admin --email="admin@example.com" --password="securepassword" --firstname="John" --lastname="Doe"
    }
}