<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\ClientRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ExportClientsCommand extends Command
{
    /**
     * Command name.
     *
     * @var string
     */
    protected static $defaultName = 'app:export-clients';

    /**
     * Command description.
     *
     * @var string
     */
    protected static $defaultDescription = 'Command to export clients to csv file';

    /**
     * Real estate entity repository.
     */
    private ClientRepository $clientRepository;

    /**
     * ImportClientsCommand constructor.
     *
     * @throws LogicException
     */
    public function __construct(ClientRepository $clientRepository)
    {
        parent::__construct();

        $this->clientRepository = $clientRepository;
    }

    protected function configure(): void
    {
        $this
            ->setHelp('This command allows you to export a clients to csv file.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->info('Exporting clients.');

        $csv_path = 'public/private_files/export/clients/clients.csv';
        $clients = $this->clientRepository->findAll();
        $csv = fopen($csv_path, 'r+');
        $header = ['date', 'name', 'phone', 'status', 'client_request'];
        fputcsv($csv, $header);
        foreach ($clients as $row) {
            $data = [
                $row->getDate()->format('Y-m-d'),
                $row->getName(),
                $row->getPhone(),
                $row->getStatus(),
                $row->getClientRequest(),
            ];
            fputcsv($csv, $data);
        }
        fclose($csv);
        $io->success('Exporting finished successfully.');

        return Command::SUCCESS;
    }
}
