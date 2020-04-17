<?php

namespace App\Controller;

use App\Entity\Property;
use App\Entity\PropertySearch;
use App\Form\PropertySearchType;
use App\Repository\PropertyRepository;
use Doctrine\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class PropertyController extends AbstractController
{
    /**
     * @var PropertyRepository
     */
    private  $repository;


    /**
     * @var ObjectManager
     */
    private $em;

    public function __construct(PropertyRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/biens", name="property.index")
     */
    public function index(PaginatorInterface $paginator, Request $request)
    {
        //Create new search
        $search = new PropertySearch();
        $form = $this->createForm(PropertySearchType::class, $search);
        $form->handleRequest($request);

        //All query and Pagiantion
        $properties = $paginator->paginate(
            $this->repository->findAllVisibleQuery($search),
            $request->query->getInt('page', 1),
            12
        );


        //Count row properties
        // 1. Obtain doctrine manager
        $em = $this->getDoctrine()->getManager();

        // 2. Setup repository of some entity
        $props = $em->getRepository(Property::class);

        // 3. Query how many rows are there in the Articles table
        $totalProperties = $props->createQueryBuilder('a')
            // Filter by some parameter if you want
            // ->where('a.published = 1')
            ->select('count(a.id)')
            ->getQuery()
            ->getSingleScalarResult();


        return $this->render('property/index.html.twig', [
            'current_menu' => 'properties',
            'properties'   => $properties,
            'totalProperties' => $totalProperties,
            'form'         => $form->createView()
        ]);
    }

    /*** Sans pagination
     *  public function index()
    {

    $properties = $this->repository->findAllVisible();

    return $this->render('property/index.html.twig', [
    'current_menu' => 'properties',
    'properties'   => $properties
    ]);
    }
     */

    /**
     * Show property
     * @Route("/biens/{slug}-{id}", name="property.show", requirements={"slug": "[a-z0-9\-]*"})
     */
    public function show(Property $property, string $slug)
    {
        //Si on saisi une mauvaise url
        if($property->getSlug() !== $slug){
            return $this->redirectToRoute('property.show', [
                'id' => $property->getId(),
                'slug' => $property->getSlug()
            ], 301);
        }

        //$property = $this->repository->find($id);

        return $this->render('property/show.html.twig', [
            'property' => $property,
            'current_menu' => 'properties'
        ]);
    }
}
