<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Client;
use App\Repository\ClientRepository;
use DateTime;
use League\Csv\Exception;
use League\Csv\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportClientsCommand extends Command
{
    /**
     * Command name.
     *
     * @var string
     */
    protected static $defaultName = 'app:import-clients';

    /**
     * Command description.
     *
     * @var string
     */
    protected static $defaultDescription = 'Command to import clients from csv file';

    /**
     * Real estate entity repository.
     */
    private ClientRepository $clientRepository;

    private array $months = [
        'cічень' => 'january',
        'лютий' => 'february',
        'березень' => 'march',
        'квітень' => 'april',
        'травень' => 'may',
        'червень' => 'june',
        'липень' => 'july',
        'серпень' => 'august',
        'вересень' => 'september',
        'жовтень' => 'october',
        'листопад' => 'november',
        'грудень' => 'december',
    ];

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
            ->setHelp('This command allows you to create a clients from csv file.')
        ;
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->info('Importing clients.');
        $csv_path = 'public/private_files/import/clients/clients.csv';
        $reader = Reader::createFromPath($csv_path)->setHeaderOffset(0);
        $results = $reader->jsonSerialize();
        foreach ($results as $key => $row) {
            // Changes with date.
            $converted_date = strtotime(str_replace(array_keys($this->months), array_values($this->months), strtolower($row['date'])));
            if ($converted_date) {
                $date = new DateTime(date('Y-m-d', $converted_date));
            } else {
                $date = null;
            }
            $name = $row['name'] ?? '';
            $phone = $row['phone'] ?? '';
            $status = $row['status'] ?? '';
            $client_request = $row['client_request'] ?? '';
            // create new client.
            $client = (new Client())
                ->setDate($date)
                ->setName($name)
                ->setPhone($phone)
                ->setStatus($status)
                ->setClientRequest($client_request)
            ;
            $this->clientRepository->add($client, true);
            $io->info('Created '.($key + 1).' of '.$reader->count().' clients.');
        }

        $io->success('Importing finished successfully.');

        return Command::SUCCESS;
    }
}
