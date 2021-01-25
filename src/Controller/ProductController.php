<?php

namespace App\Controller;

use App\Classe\Search;
use App\Entity\Product;
use App\Form\SearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    /**
 * @Route("/nos-produits", name="products")
 */
    public function index(Request $request): Response
    {
//        $products = $this->entityManager->getRepository(Product::class)->findAll(); //on le replace en bas dans le else
//        dd($products);

        $search = new Search();
        $form =$this->createForm(SearchType::class, $search);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $products = $this->entityManager->getRepository(Product::class)->findWithSearch($search);
//            $search = $form->getData();
//            dd($search);
        } else {
            $products = $this->entityManager->getRepository(Product::class)->findAll();
        }

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/produit/{slug}", name="product")
     */
    public function show($slug): Response
    {
        //si je debug mon slug, il le recupere dans la variable->super
//        dd($slug);
        $product = $this->entityManager->getRepository(Product::class)->findOneBySlug($slug);
//        dd($products);

        if (!$product) {
            return $this->redirectToRoute("product");
        }

        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }
}
