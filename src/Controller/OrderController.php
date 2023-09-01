<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route('/ajout', name: 'app_order')]
    public function add(SessionInterface $session, ProduitRepository $repo, EntityManagerInterface $manager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        $panier = $session->get('cart', []);
        
        if($panier === []){
            $this->addFlash('message', 'Votre panier est vide');
            return $this->redirectToRoute('home');
        }
       
        foreach($panier as $item => $quantity)
        {
            $commande = new Commande;
            
            
            $produit = $repo->find($item);

            $stock = $produit->getStock();
            
            $price = $produit->getPrix();
            
            $commande->setProduit($produit);
            $commande->setMontant($price);
            $commande->setQuantite($quantity);
            $commande->setEtat('En cours de traitement');
            $commande->setDateEnregistrement(new \DateTime());
            $commande->setMembre($this->getUser());
            $produit->setStock($stock - $quantity);
            
            
            $manager->persist($commande);

        }

        $manager->flush();

        $cart = $session->get('cart', []);
        if(!empty($cart)){        
            $session->remove('cart');
            $session->remove('qt');
        }

        $this->addFlash('message', 'Votre commande a bien été envoyé');
        return $this->redirectToRoute('home');
    }
}
