<?php

namespace App\Controller;

use App\Repository\FiltersRepository;
use App\Repository\FilterSubtypesRepository;
use App\Repository\FilterTypesRepository;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class Controller extends AbstractController
{
    private FilterTypesRepository $filterTypesRepository;
    private FiltersRepository $filtersRepository;
    private FilterSubtypesRepository $filterSubTypesRepository;

    protected $connection;

    public function __construct(FiltersRepository $filtersRepository, FilterTypesRepository $filterTypesRepository, FilterSubtypesRepository $filterSubTypesRepository, Connection $connection)
    {
        $this->filtersRepository = $filtersRepository;
        $this->filterTypesRepository = $filterTypesRepository;
        $this->filterSubTypesRepository = $filterSubTypesRepository;
        $this->connection = $connection;
    }
    #[Route('//', name: 'app__')]
    public function index(): Response
    {
        $filtersRepository = $this->filtersRepository;
        $filterTypesRepository = $this->filterTypesRepository;
        $filterSubTypesRepository = $this->filterSubTypesRepository;

        $filterEntities = $filtersRepository->findAll();
        $filters = [];
        foreach ($filterEntities as $filter) {
            $criteria = [];
            foreach ($filter->getCriteria() as $criterion) {
                $type = $criterion->getType()->getName();
                $subtype = $criterion->getSubtype()->getName();
                $value = $criterion->getValue();
                if (!is_numeric($value)) {
                    $value = "'$value'";
                }
                $criteria[] = "$type $subtype $value";
            }

            $criteria = implode(', ', $criteria);
            $filters[] = [
                'id' => $filter->getId(),
                'name' => $filter->getName(),
                'entity' => $filter,
                'criteria' => $criteria
            ];
        }

        $data = [
            'filters' => $filters
        ];


        return $this->render('MainBundle/index.html.twig', $data);
    }

    #[Route('/api/filters', name: 'api_filters', methods: ['GET'])]
    public function getFilters(): JsonResponse
    {
        $filters = $this->filtersRepository->findAll();
        return $this->json($filters);
    }

    #[Route('/modal', name: 'app_modal')]
    public function modal(): Response
    {
        return $this->render('modal.html.twig', [
            'title' => 'Dynamic Modal Title',
            'content' => 'This is dynamic content from the controller'
        ]);
    }
}
