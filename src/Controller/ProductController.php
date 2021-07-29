<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
class ProductController extends AbstractController
{
    /**
     * @Route("/", name="product_index")
     */
    public function index(ProductRepository $productRepository,SessionInterface $session)

    { 
        if (! $session->has("id")) { 
               $session->set('id',-1);

        }

        

        //dd($productRepository->findAll());
        return $this->render('product/index.html.twig', [
            
            'products' => $productRepository->findBy(['visible'=>true]),
            'user' =>$session->get('id')
        ]);
    }
}
