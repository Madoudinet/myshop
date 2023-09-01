<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Produit;
use App\Form\CommandeType;
use App\Repository\ProduitRepository;
use Requests;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ProduitRepository $repo): Response
    {
        $produits = $repo->findAll();
        return $this->render('app/index.html.twig', [
            'produits' => $produits,
        ]);
    }

    // #[Route('/panier/{id}', name:'app_validate')]
    // public function validate(Requests $rq, Produit $produit ,ProduitRepository $repo)
    // {
    //     $commande = new Commande;
    //     $form = $this->createForm(CommandeType::class, $commande);
    //     $form->handleRequest($rq);

    //     if($form->isSubmitted() && $form->isValid())
    //     {
    //         $commande->setMembre($this->getUser());
    //         $commande->setDateEnregistrement(new \DateTime());
    //         $commande->setProduit($produit);

    //         return $this->redirectToRoute('home');
    //     }

    //     return $this->render('panier/index.html.twig');
    // }

    // #[Route('/commandes/ajout', name:'app_commande_ajout')]
    // public function add(): Response
    // {
    //     $this->denyAccessUnlessGranted('ROLE_USER')
    //     return $this->render()

    // }
}
