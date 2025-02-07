<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\RealEstatesRepository;
use League\Csv\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ExportrtRealEstatesCommand extends Command
{
    /**
     * Command name.
     *
     * @var string
     */
    protected static $defaultName = 'app:export-real-estate';

    /**
     * Command description.
     *
     * @var string
     */
    protected static $defaultDescription = 'Command to export real estates to csv file';

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
                'This command allows you to export a real estates to csv file.'
            )
            ->addArgument(
                'real_estate_type',
                InputArgument::OPTIONAL,
                'Type of real estate to export'
            )
            ->addArgument(
                'real_estate_operation',
                InputArgument::OPTIONAL,
                'Type of operation with real estate to export'
            )
            ->addOption(
                'full_export',
                null,
                InputOption::VALUE_OPTIONAL,
                'Export real estates with all types and operations',
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
        if ($input->getOption('full_export')) {
            $io->info('Exporting real estates.');
            $csv_path = 'public/private_files/export/real_estate/all.csv';
        } else {
            if ($input->getArgument('real_estate_type')) {
                $real_estate_type = $input->getArgument('real_estate_type');
            } else {
                $real_estate_type = $io->ask('Which type of real estate you will export?', 'apartment');
            }
            if (!\in_array($real_estate_type, $this->real_estate_types, true)) {
                $io->error('Typed real estate type argument not possible');

                return Command::INVALID;
            }
            if ($input->getArgument('real_estate_operation')) {
                $real_estate_operation = $input->getArgument('real_estate_operation');
            } else {
                $real_estate_operation = $io->ask('Which operation with real estate you will export?', 'rent');
            }
            if (!\in_array($real_estate_operation, $this->real_estate_operations, true)) {
                $io->error('Typed operation with real estate not possible');

                return Command::INVALID;
            }
            $io->info('Exporting real estates with type '.$real_estate_type.' and operation '.$real_estate_operation.'.');
            $csv_path = 'public/private_files/export/real_estate/'.$real_estate_operation.'/'.$real_estate_type.'.csv';
        }
        $real_estates = $this->realEstatesRepository->findAll();
        $csv = fopen($csv_path, 'r+');
        $header = ['date', 'title', 'real_estate_operation', 'real_estate_type', 'details', 'contact', 'contact_type', 'price', 'price_type', 'rooms', 'area', 'floor', 'object_type', 'house_type', 'bathroom', 'heating', 'location'];
        fputcsv($csv, $header);
        foreach ($real_estates as $row) {
            $data = [
                $row->getDate()->format('Y-m-d'),
                $row->getTitle(),
                $row->getRealEstateOperation(),
                $row->getRealEstateType(),
                $row->getDetails(),
                $row->getContact(),
                $row->getContactType(),
                $row->getPrice(),
                $row->getPriceType(),
                $row->getRooms(),
                $row->getArea(),
                $row->getFloor(),
                $row->getObjectType(),
                $row->getHouseType(),
                $row->getBathroom(),
                $row->getHeating(),
                $row->getLocation(),
            ];
            fputcsv($csv, $data);
        }
        fclose($csv);
        $io->success('Exporting finished successfully.');

        return Command::SUCCESS;
    }
}
