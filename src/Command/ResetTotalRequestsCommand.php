<?php

namespace App\Command;

use App\Repository\SettingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:reset-total-requests',
    description: 'Add a short description for your command',
)]
class ResetTotalRequestsCommand extends Command
{
    protected static $defaultName = 'app:reset-total-requests';


    public function __construct(private EntityManagerInterface $entityManager, private SettingRepository $settingRepository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Réinitialisation à 0 de total_requests_sent pour tous les utilisateurs.')
            ->setHelp('This command allows you to reset the total_requests_sent field in the Setting table for all users...');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $settings = $this->settingRepository->findAll();
        foreach ($settings as $setting) {
            $setting->setTotalRequestSent(0);
        }

        $this->entityManager->flush();

        $output->writeln('All total_requests_sent values have been reset.');

        $io->progressStart($this->settingRepository->count([]));

        $io->progressFinish();

        return Command::SUCCESS;
    }
}
