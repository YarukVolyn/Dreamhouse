<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\RealEstate;
use App\Form\ImageType;
use Dompdf\Dompdf;
use Dompdf\Options;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Mime\FileinfoMimeTypeGuesser;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @IsGranted("ROLE_USER")
 */
class RealEstateCrudController extends AbstractCrudController
{
    protected TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public static function getEntityFqcn(): string
    {
        return RealEstate::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $generatePdf = Action::new('generate_pdf', $this->translator->trans('Generate PDF'), 'fa fa-file-pdf')
            ->linkToCrudAction('generatePdf')
        ;

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_DETAIL, $generatePdf)
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            Field::new('id')
                ->hideOnForm(),
            Field::new('date')
                ->setLabel($this->translator->trans('Date')),
            Field::new('title')
                ->setLabel($this->translator->trans('Title')),
            ChoiceField::new('location')
                ->setChoices([
                    $this->translator->trans('Center') => 'center',
                    $this->translator->trans('LPZ') => 'lpz',
                    $this->translator->trans('Sugar') => 'sugar',
                    $this->translator->trans('Kichkarivka') => 'kichkarivka',
                    $this->translator->trans('Old town') => 'old_town',
                    $this->translator->trans('33rd') => '33',
                    $this->translator->trans('40rd') => '40',
                    $this->translator->trans('55rd') => '55',
                    $this->translator->trans('Kyiv maidan') => 'kyiv_maidan,',
                    $this->translator->trans('Renaissance') => 'renaissance',
                    $this->translator->trans('Rivne') => 'rivne',
                    $this->translator->trans('Lviv') => 'lviv',
                    $this->translator->trans('Gnidova') => 'gnidova',
                    $this->translator->trans('Other') => 'other',
                ])
                ->setLabel($this->translator->trans('Location')),
            Field::new('price')
                ->setLabel($this->translator->trans('Price')),
            ChoiceField::new('price_type')
                ->setChoices([
                    $this->translator->trans('UAH') => 'UAH',
                    $this->translator->trans('USD') => 'USD',
                    $this->translator->trans('EUR') => 'EUR',
                ])
                ->setLabel($this->translator->trans('Price type')),
            TextField::new('contact')
                ->setLabel($this->translator->trans('Contact')),
            ChoiceField::new('contact_type')
                ->setChoices([
                    $this->translator->trans('Owner') => 'owner',
                    $this->translator->trans('Real Estate Agency') => 'real_estate',
                ])
                ->setLabel($this->translator->trans('Contact type')),
            ChoiceField::new('real_estate_operation')
                ->setChoices([
                    $this->translator->trans('Rent') => 'rent',
                    $this->translator->trans('Sell') => 'sell',
                ])
                ->setLabel($this->translator->trans('Real estate operation')),
            ChoiceField::new('real_estate_type')
                ->setChoices([
                    $this->translator->trans('Apartment') => 'apartment',
                    $this->translator->trans('Commerce') => 'commerce',
                    $this->translator->trans('House') => 'house',
                ])
                ->setLabel($this->translator->trans('Real estate type')),
            Field::new('details')
                ->hideOnIndex()
                ->setLabel($this->translator->trans('Details')),
            Field::new('rooms')
                ->hideOnIndex()
                ->setLabel($this->translator->trans('Number of rooms')),
            Field::new('area')
                ->hideOnIndex()
                ->setLabel($this->translator->trans('Area')),
            Field::new('floor')
                ->hideOnIndex()
                ->setLabel($this->translator->trans('Floor / Storey')),
            ChoiceField::new('object_type')
                ->hideOnIndex()
                ->setChoices([
                    $this->translator->trans('New building') => 'new_building',
                    $this->translator->trans('Secondary market') => 'secondary_market',
                ])
                ->setLabel($this->translator->trans('Object type')),
            Field::new('house_type')
                ->hideOnIndex()
                ->setLabel($this->translator->trans('House type')),
            Field::new('bathroom')
                ->hideOnIndex()
                ->setLabel($this->translator->trans('Bathroom')),
            Field::new('heating')
                ->hideOnIndex()
                ->setLabel($this->translator->trans('Heating')),
            CollectionField::new('images')
                ->setEntryType(ImageType::class)
                ->setCustomOption('type', 'image')
                ->setEntryIsComplex(true)
                ->setFormTypeOption('by_reference', false)
                ->hideOnIndex()
                ->setLabel($this->translator->trans('Images')),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural($this->translator->trans('Real estates'))
            ->setEntityLabelInSingular($this->translator->trans('Real estate'))
            ->setPageTitle('index', $this->translator->trans('Real estate'))
            ->setPageTitle('new', $this->translator->trans('Create Real estate'))
            ->setPageTitle('edit', $this->translator->trans('Edit Real estate'))
            ->setSearchFields(['title', 'details'])
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(
                ChoiceFilter::new('real_estate_operation')
                    ->setChoices([
                        $this->translator->trans('Rent') => 'rent',
                        $this->translator->trans('Sell') => 'sell',
                    ])
                    ->setLabel($this->translator->trans('Real estate operation'))
            )
            ->add(
                ChoiceFilter::new('real_estate_type')
                    ->setChoices([
                        $this->translator->trans('Apartment') => 'apartment',
                        $this->translator->trans('Commerce') => 'commerce',
                        $this->translator->trans('House') => 'house',
                    ])
                    ->setLabel($this->translator->trans('Real estate type'))
            )
            ->add(
                ChoiceFilter::new('contact_type')
                    ->setChoices([
                        $this->translator->trans('Owner') => 'owner',
                        $this->translator->trans('Real Estate Agency') => 'real_estate',
                    ])
                    ->setLabel($this->translator->trans('Contact type'))
            )
            ->add(
                ChoiceFilter::new('price_type')
                    ->setChoices([
                        $this->translator->trans('UAH') => 'UAH',
                        $this->translator->trans('USD') => 'USD',
                        $this->translator->trans('EUR') => 'EUR',
                    ])
                    ->setLabel($this->translator->trans('Price type'))
            )
            ->add(
                NumericFilter::new('price')
                    ->setLabel($this->translator->trans('Price'))
            )
            ->add(
                ChoiceFilter::new('location')
                    ->setChoices([
                        $this->translator->trans('Center') => 'center',
                        $this->translator->trans('LPZ') => 'lpz',
                        $this->translator->trans('Sugar') => 'sugar',
                        $this->translator->trans('Kichkarivka') => 'kichkarivka',
                        $this->translator->trans('Old town') => 'old_town',
                        $this->translator->trans('33rd') => '33',
                        $this->translator->trans('40rd') => '40',
                        $this->translator->trans('55rd') => '55',
                        $this->translator->trans('Kyiv maidan') => 'kyiv_maidan,',
                        $this->translator->trans('Renaissance') => 'renaissance',
                        $this->translator->trans('Rivne') => 'rivne',
                        $this->translator->trans('Lviv') => 'lviv',
                        $this->translator->trans('Gnidova') => 'gnidova',
                        $this->translator->trans('Other') => 'other',
                    ])
                    ->setLabel($this->translator->trans('Location')),
            )
            ;
    }

    public function generatePdf(AdminContext $context): BinaryFileResponse
    {
        $entity = $this->getContext()->getEntity()->getInstance();
        $real_estate_operation_value = $entity->getRealEstateOperation();
        if ($real_estate_operation_value === 'rent') {
            $real_estate_operation = $this->translator->trans('Rent');
        } elseif ($real_estate_operation_value === 'sell') {
            $real_estate_operation = $this->translator->trans('Sell');
        } else {
            $real_estate_operation = '';
        }
        $real_estate_type_value = $entity->getRealEstateType();
        if ($real_estate_type_value === 'apartment') {
            $real_estate_type = $this->translator->trans('Apartment');
        } elseif ($real_estate_type_value === 'commerce') {
            $real_estate_type = $this->translator->trans('Commerce');
        } elseif ($real_estate_type_value === 'house') {
            $real_estate_type = $this->translator->trans('House');
        } else {
            $real_estate_type = '';
        }
        $contact_type_value = $entity->getContactType();
        if ($contact_type_value === 'owner') {
            $contact_type = $this->translator->trans('Owner');
        } elseif ($contact_type_value === 'real_estate') {
            $contact_type = $this->translator->trans('Real Estate Agency');
        } else {
            $contact_type = '';
        }
        $object_type_value = $entity->getObjectType();
        if ($object_type_value === 'new_building') {
            $object_type = $this->translator->trans('New building');
        } elseif ($object_type_value === 'secondary_market') {
            $object_type = $this->translator->trans('Secondary market');
        } else {
            $object_type = '';
        }
        $location_value = $entity->getLocation();
        if ($location_value === 'center') {
            $location = $this->translator->trans('Center');
        } elseif ($location_value === 'lpz') {
            $location = $this->translator->trans('LPZ');
        } elseif ($location_value === 'sugar') {
            $location = $this->translator->trans('Sugar');
        } elseif ($location_value === 'kichkarivka') {
            $location = $this->translator->trans('Kichkarivka');
        } elseif ($location_value === 'old_town') {
            $location = $this->translator->trans('Old town');
        } elseif ($location_value === '33') {
            $location = $this->translator->trans('33rd');
        } elseif ($location_value === '40') {
            $location = $this->translator->trans('40rd');
        } elseif ($location_value === '55') {
            $location = $this->translator->trans('55rd');
        } elseif ($location_value === 'kyiv_maidan') {
            $location = $this->translator->trans('Kyiv maidan');
        } elseif ($location_value === 'renaissance') {
            $location = $this->translator->trans('Renaissance');
        } elseif ($location_value === 'rivne') {
            $location = $this->translator->trans('Rivne');
        } elseif ($location_value === 'lviv') {
            $location = $this->translator->trans('Lviv');
        } elseif ($location_value === 'gnidova') {
            $location = $this->translator->trans('Gnidova');
        } elseif ($location_value === 'other') {
            $location = $this->translator->trans('Other');
        } else {
            $location = '';
        }
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'DejaVu Sans');
        $pdfOptions->set('isRemoteEnabled', true);

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('real_estate_pdf.html.twig', [
            'date' => $entity->getDate()->format('Y-m-d'),
            'title' => $entity->getTitle(),
            'real_estate_operation' => $real_estate_operation,
            'real_estate_type' => $real_estate_type,
            'details' => $entity->getDetails(),
            'contact' => $entity->getContact(),
            'contact_type' => $contact_type,
            'price' => $entity->getPrice(),
            'price_type' => $entity->getPriceType(),
            'rooms' => $entity->getRooms(),
            'area' => $entity->getArea(),
            'floor' => $entity->getFloor(),
            'object_type' => $object_type,
            'house_type' => $entity->getHouseType(),
            'bathroom' => $entity->getBathroom(),
            'heating' => $entity->getHeating(),
            'location' => $location,
            'images' => $entity->getImages(),
        ]);
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');
        // Render the HTML as PDF
        $dompdf->render();
        // Output the generated PDF to Browser (force download)

        $output = $dompdf->output();

        // e.g /var/www/project/public/mypdf.pdf
        $FolderPath = 'files/pdf_files/';
        $filename = $entity->getTitle().'-'.$entity->getId().'-presentation.pdf';

        // Write file to the desired path
        file_put_contents($FolderPath.$filename, $output);
        // This should return the file to the browser as response
        $response = new BinaryFileResponse($FolderPath.$filename);

        // To generate a file download, you need the mimetype of the file
        $mimeTypeGuesser = new FileinfoMimeTypeGuesser();

        // Set the mimetype with the guesser or manually
        if ($mimeTypeGuesser->isGuesserSupported()) {
            // Guess the mimetype of the file according to the extension of the file
            $response->headers->set('Content-Type', $mimeTypeGuesser->guessMimeType($FolderPath.$filename));
        } else {
            // Set the mimetype of the file manually, in this case for a text file is text/plain
            $response->headers->set('Content-Type', 'application/pdf');
        }

        // Set content disposition inline of the file
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );

        return $response->deleteFileAfterSend();
    }
}
