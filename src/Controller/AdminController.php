<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\ProductRepository;


class AdminController extends AbstractController
{
   
    /**
     * @Route("/admin", name="admin_panel")
     */
    public function index(ProductRepository $productRepository,sessionInterface $session)
    { $owner=$session->get('id',0);


        $products = $productRepository->findBy(['owner'=> $owner]);
 

        return $this->render('admin/index.html.twig', [
           'products'=>$products,
           'user' =>$session->get('id')
        ]);
    }
  

     /**
     * @Route("/deletProduct{id}", name="admin_del")
     */

    public function del(ProductRepository $productRepository,$id,SessionInterface $session)
    { $product=$productRepository->findOneBy(['id'=>$id]);


       $em =$this->getDoctrine()->getManager();

        $em->remove($product);
$em->flush();

return $this-> redirectToRoute('admin_panel',['user' =>$session->get('id')]);
        
 

    
    }



     /**
     * @Route("/editPage{id}", name="admin_edit")
     */
    public function edit(ProductRepository $productRepository,$id ,SessionInterface $session)
    { 


        $product = $productRepository->findOneBy(['id'=> $id]);
 

        return $this->render('admin/edit.html.twig', ['user' =>$session->get('id'),
           'product'=>$product
        ]);
    }





 /**
     * @Route("/editProduct{id}", name="admin_edit_product")
     */
    public function editProduct(Request $request,ProductRepository $productRepository,$id,SessionInterface $session)
    { 



        $product = $productRepository->findOneBy(['id'=> $id]);
          
        $product->setVisible(false);
        if($request->request->has('visible')){
            $product->setVisible(true);
           }
         

         $product->setTitle($request->request->get('title'));
         $product->setImage($request->request->get('image'));
         $product->setPrice($request->request->get('price'));


        $em =$this->getDoctrine()->getManager();
        $em->persist($product)  ;
        $em->flush();

        return $this-> redirectToRoute('admin_panel');
    }

 


  
 /**
     * @Route("/ByProduct", name="user_buy")
     */
    public function Buy(ProductRepository $productRepository,SessionInterface $session)
    { 


         if($session->get('id') == -1){
            return $this-> redirectToRoute('login_user');
             
         }


        $em =$this->getDoctrine()->getManager();

        $panier=$session->get('panier',[] );
      //  dd($session);
   
        foreach($panier as $id =>$quantity ){
            $product = $productRepository->findOneBy(['id'=> $id]);
           

          
            $product->setVisible(false);
            $product->setOwner($session->get('id'));
           

           
            $em->flush();

       // dd($product);

        }


       $session->set('panier',[]);
      // 

    

      return $this-> redirectToRoute('cart_index',['user' =>$session->get('id')]);
    }

 



   /**
     * @Route("/adminAdd", name="admin_add")
     */
    public function add(Request $request,SessionInterface $session)
    
    
    {$product =new Product(); 
        $product->setVisible(true);
        $product->setOwner($session->get('id'));
        $form=$this->createFormBuilder($product)->add('title',TextType::class)
        ->add('image',TextType::class)
        ->add('price',TextType::class)
        ->add('save',SubmitType::class)
        ->getForm()

        ;
if($request->isMethod('POST')){
$form->handleRequest($request);


if($form->isValid())
{
    
    $em =$this->getDoctrine()->getManager();
    $em->persist($product)  ;
    $em->flush();
   // $session->getFlashBag()->add('notice',"produit bien ajouté");

   return $this-> redirectToRoute('admin_panel',['user' =>$session->get('id')]);
}

}


        return $this->render('admin/add.html.twig', ['user' =>$session->get('id'),
            'form' => $form->createView(),
        ]);
    }


      
   /**
     * @Route("/auth", name="admin_auth")
     */
    public function auth (Request $request,SessionInterface $session)
    {
        $repo = $this->getDoctrine()->getRepository(User::class);
//echo $request->get('usernname') ;
$current=$repo->findOneBy
(['username'=>$request->request->get('username'),'password'=>$request->request->get('password')]);


    $session->set('id',$current->getId());
//dd($session);
return $this-> redirectToRoute('admin_panel',['user' =>$session->get('id')]);



//dd($current);
//dd($request->request->get('password'));

      /*  $current=$repo->findBy(['username'=>$request->get('usernname'),'password'=>$request->get('password')]);
             
        if($current==[])
        {echo $current['username'];}
        else return $this->render('admin/login.html.twig');*/
            
        //   if($current==[]) return $this->render('admin/login.html.twig');
          // return $this->render('admin/index.html.twig');
        //  return $this->render('admin/login.html.twig',['user'=> $current]);
        }

         /**
     * @Route("/login", name="login_user")
     */
    public function au(Request $request)
    {
       
          return $this->render('admin/login.html.twig',['user'=>["username"=>"" , "password"=>""]]);
        }

  
        
  /**
     * @Route("/logout", name="logOut_user")
     */
    public function logout(SessionInterface $session)
    {
        $session->set('id',-1);
        $session->set('panier',[]);
       
          return $this->render('base.html.twig',['user'=>-1]);
        }

        
        
        


 }


















