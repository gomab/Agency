<?php

namespace App\Controller;

use App\Entity\Property;
use App\Repository\PropertyRepository;
use Doctrine\Persistence\ObjectManager;
use http\Env\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function index()
    {
        //Create Entity
        /*
        $property = new Property();
        $property->setTitle('Mon premier bien')
            ->setPrice(2000000)
            ->setRooms(4)
            ->setBedrooms(3)
            ->setDescription('Une petit description')
            ->setSurface(60)
            ->setFloor(4)
            ->setHeat(1)
            ->setCity('Paris')
            ->setAddress('9 rue Jorge semprun')
            ->setPostalCode('75012');

        $em = $this->getDoctrine()->getManager();
        $em->persist($property);
        $em->flush();
        */

       // $repository = $this->getDoctrine()->getRepository(Property::class);

        /*$property = $this->repository->findAllVisible();

        $this->em->flush();*/
        return $this->render('property/index.html.twig', [
            'current_menu' => 'properties'
        ]);
    }

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
