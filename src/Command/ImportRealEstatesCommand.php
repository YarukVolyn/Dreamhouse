<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\RealEstate;
use App\Repository\RealEstatesRepository;
use League\Csv\Exception;
use League\Csv\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportRealEstatesCommand extends Command
{
    /**
     * Command name.
     *
     * @var string
     */
    protected static $defaultName = 'app:import-real-estate';

    /**
     * Command description.
     *
     * @var string
     */
    protected static $defaultDescription = 'Command to import real estate from csv file';

    /**
     * List of possible real estate types.
     *
     * @var array|string[]
     */
    private array $real_estate_types = ['apartment', 'commerce', 'house'];

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
     * List of possible operations with real estate.
     *
     * @var array|string[]
     */
    private array $real_estate_operations = ['rent', 'sell'];

    /**
     * Real estate entity repository.
     */
    private RealEstatesRepository $realEstatesRepository;

    /**
     * ImportRealEstateCommand constructor.
     *
     * @throws LogicException
     */
    public function __construct(RealEstatesRepository $realEstatesRepository)
    {
        parent::__construct();

        $this->realEstatesRepository = $realEstatesRepository;
    }

    protected function configure(): void
    {
        $this
            ->setHelp(
                'This command allows you to create a real estate from csv file.'
            )
            ->addArgument(
                'real_estate_type',
                InputArgument::OPTIONAL,
                'Type of real estate to import'
            )
            ->addArgument(
                'real_estate_operation',
                InputArgument::OPTIONAL,
                'Type of operation with real estate to import'
            )
            ->addOption(
                'full_import',
                null,
                InputOption::VALUE_OPTIONAL,
                'Import real estates with all types and operations',
                false
            )
        ;
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        if ($input->getOption('full_import')) {
            $io->info('Importing real estates.');
            $csv_path = 'public/private_files/import/real_estate/all.csv';
        } else {
            if ($input->getArgument('real_estate_type')) {
                $real_estate_type = $input->getArgument('real_estate_type');
            } else {
                $real_estate_type = $io->ask('Which type of real estate you will import?', 'apartment');
            }
            if (!\in_array($real_estate_type, $this->real_estate_types, true)) {
                $io->error('Typed real estate type argument not possible');

                return Command::INVALID;
            }
            if ($input->getArgument('real_estate_operation')) {
                $real_estate_operation = $input->getArgument('real_estate_operation');
            } else {
                $real_estate_operation = $io->ask('Which operation with real estate you will import?', 'rent');
            }
            if (!\in_array($real_estate_operation, $this->real_estate_operations, true)) {
                $io->error('Typed operation with real estate not possible');

                return Command::INVALID;
            }
            $io->info('Importing real estates with type '.$real_estate_type.' and operation '.$real_estate_operation.'.');
            $csv_path = 'public/private_files/import/real_estate/'.$real_estate_operation.'/'.$real_estate_type.'.csv';
        }

        $reader = Reader::createFromPath($csv_path)->setHeaderOffset(0);
        $results = $reader->jsonSerialize();
        if ($results) {
            foreach ($results as $key => $row) {
                // Changes with date.
                $converted_date = strtotime(str_replace(array_keys($this->months), array_values($this->months), strtolower($row['date'])));
                if ($converted_date) {
                    $date = new \DateTime(date('Y-m-d', $converted_date));
                } else {
                    $date = null;
                }
                $location = $row['location'] ?? '';
                $title = $row['title'] ?? 'Title example';
                $real_estate_operation_row = $real_estate_operation ?? $row['real_estate_operation'];
                $real_estate_type_row = $real_estate_type ?? $row['real_estate_type'];
                $details = $row['details'] ?? '';
                $contact = $row['contact'] ?? '';
                $contact_type = $row['contact_type'] ?? '';
                $price = $row['price'] ?? '';
                $price_type = $row['price_type'] ?? '';
                $rooms = $row['rooms'] ?? '';
                $area = $row['area'] ?? '';
                $floor = $row['floor'] ?? '';
                $object_type = $row['object_type'] ?? '';
                $house_type = $row['house_type'] ?? '';
                $bathroom = $row['bathroom'] ?? '';
                $heating = $row['heating'] ?? '';
                // create new real estate.
                $real_estate = (new RealEstate())
                    ->setRealEstateOperation($real_estate_operation_row)
                    ->setRealEstateType($real_estate_type_row)
                    ->setDate($date)
                    ->setTitle($title)
                    ->setLocation($location)
                    ->setDetails($details)
                    ->setPrice($price)
                    ->setPriceType($price_type)
                    ->setContact($contact)
                    ->setContactType($contact_type)
                    ->setRooms($rooms)
                    ->setArea($area)
                    ->setFloor($floor)
                    ->setObjectType($object_type)
                    ->setHouseType($house_type)
                    ->setBathroom($bathroom)
                    ->setHeating($heating)
                ;
                $this->realEstatesRepository->add($real_estate, true);
                $io->info('Created '.($key + 1).' of '.$reader->count().' real estates.');
            }
        } else {
            $io->error('Importing failed. CSV file empty.');

            return Command::FAILURE;
        }

        $io->success('Importing finished successfully.');

        return Command::SUCCESS;
    }
}
