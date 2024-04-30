<?php
// src/Controller/AdminProductController.php
namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route("/product", name:"admin_products_")]
class AdminProductController extends AbstractController
{
    #[Route("/", name:"admin_products_index")]
    public function index(ProductRepository $productRepository)
    {
        $products = $productRepository->findAll();

        return $this->render('product/index.html.twig', [ // change '/product/index.html.twig' to 'product/index.html.twig'
            'products' => $products,
        ]);
    }

    #[Route("/new", name:"admin_products_new")]
    public function new(Request $request, EntityManagerInterface $em)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('admin_products_index');
        }

        return $this->render('admin/products/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
?>
