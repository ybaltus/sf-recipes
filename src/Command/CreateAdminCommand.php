<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Create and administrator',
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em
    )
    {
        parent::__construct("CreateAdminCommand");
    }

    protected function configure(): void
    {
        $this
            ->addArgument('fullName', InputArgument::OPTIONAL, 'Fullname of administrator')
            ->addArgument('email', InputArgument::OPTIONAL, 'Email of administrator')
            ->addArgument('password', InputArgument::OPTIONAL, 'Password of administrator')
            ->addArgument('pseudo', InputArgument::OPTIONAL, 'Pseudo of administrator')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $io = new SymfonyStyle($input, $output);

        $fullname = $input->getArgument('fullName');
        if(!$fullname) {
            $questionFullName = new Question('Quel est le Nom Prénom de l\'utilisateur ? :');
            $fullname = $helper->ask($input, $output, $questionFullName);
        }

        $email = $input->getArgument('email');
        if(!$email) {
            $questionEmail = new Question('Quel est l\'email de l\'utilisateur? :');
            $email = $helper->ask($input, $output, $questionEmail);
        }
        $password = $input->getArgument('password');
        if(!$password) {
            $questionPassword = new Question('Quel est le mot de passe de l\'utilisateur? :');
            $password = $helper->ask($input, $output, $questionPassword);
        }
        $pseudo = $input->getArgument('pseudo');
        if(!$pseudo) {
            $questionConfirmPseudo = new ConfirmationQuestion('Voulez-vous renseigner un pseudo pour l\'utilisateur ? :', true);
            $repAsk = $helper->ask($input, $output, $questionConfirmPseudo);
            if($repAsk){
                $questionPseudo = new Question('Quel est le pseudo de l\'utilisateur? :');
                $pseudo = $helper->ask($input, $output, $questionPseudo);
            }
        }

        if(!$fullname || !$email || !$password) {
            $io->error('Le nom, l\'email et le mot de passe sont obligatoire.');
            return Command::FAILURE;
        } else {
            $user = (new User())
            ->setEmail($email)
                ->setPlainPassword($password)
                ->setRoles(['ROLE_USER', 'ROLE_ADMIN'])
                ->setFullName($fullname)
                ;
            if($pseudo){
                $user->setPseudo($pseudo);
            }

            $this->em->persist($user);
            $this->em->flush();
        }

        $io->success("L'dministrateur $fullname à bien été créé !");

        return Command::SUCCESS;
    }
}
