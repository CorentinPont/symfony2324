<?php
// src/Controller/ProduitController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProduitRepository;
use App\Entity\Commentaire;
use App\Form\CommentaireType;

class ProduitController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function home (ProduitRepository $produitRepository)
    {
        $produits = $produitRepository->findBy([], ['dateAjout' => 'DESC'], 5);

        return $this->render('index.html.twig', [
            'produits' => $produits,
        ]);
    }

    #[Route("/produits", name:"produits")]
    public function produits(ProduitRepository $produitRepository)
    {
        $produits = $produitRepository->findAll();

        return $this->render('produit/produits.html.twig', [
            'produits' => $produits,
        ]);
    }

    #[Route("/produit/{id}", name:"produit")]
    public function produit($id, ProduitRepository $produitRepository, Request $request, EntityManagerInterface $em)
    {
        $produit = $produitRepository->find($id);

        // Créer un nouveau commentaire
        $commentaire = new Commentaire();

        // Créer le formulaire
        $form = $this->createForm(CommentaireType::class, $commentaire);

        // Traiter la soumission du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Associer le commentaire au produit
            $commentaire->setProduit($produit);

            // Enregistrer le commentaire dans la base de données
            $em->persist($commentaire);
            $em->flush();

            // Rediriger l'utilisateur vers la même page
            return $this->redirectToRoute('produit', ['id' => $produit->getId()]);
        }

        // Récupérer les commentaires du produit
        $commentaires = $produit->getCommentaires();

        return $this->render('produit/produit.html.twig', [
            'produit' => $produit,
            'commentaires' => $commentaires,
            'form' => $form->createView(),
        ]);
    }
}
