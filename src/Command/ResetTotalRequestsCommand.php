<?php

namespace App\Command;

use App\Repository\SettingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'ResetTotalRequestsCommand',
    description: 'Add a short description for your command',
)]
class ResetTotalRequestsCommand extends Command
{
    protected static $defaultName = 'app:reset-total-requests';

    private $entityManager;
    private $settingRepository;

    public function __construct(EntityManagerInterface $entityManager, SettingRepository $settingRepository)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->settingRepository = $settingRepository;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Resets the total_requests_sent for all users to 0.')
            ->setHelp('This command allows you to reset the total_requests_sent field in the Setting table for all users...');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $settings = $this->settingRepository->findAll();
        foreach ($settings as $setting) {
            $setting->setTotalRequestSent(0);
        }

        $this->entityManager->flush();

        $output->writeln('All total_requests_sent values have been reset.');

        return Command::SUCCESS;
    }
}
