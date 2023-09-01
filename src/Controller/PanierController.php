<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function add($id, RequestStack $rs)
    {
        $session = $rs->getSession();

        $cart = $session->get('cart', []);
        $qt = $session->get('qt', 0);

        if(!empty($cart[$id]))
        {
            $cart[$id]++;
            $qt++;
        }else
        {
            $qt++;
            $cart[$id] = 1;
        }
        $session->set('cart', $cart);
        $session->set('qt', $qt);
        return $this->redirectToRoute('home');
    }

    #[Route('/cart/add/gestion/{id}', name: 'cart_add_gestion')]
    public function addGestion($id, RequestStack $rs)
    {
        $session = $rs->getSession();

        $cart = $session->get('cart', []);
        $qt = $session->get('qt', 0);

        if(!empty($cart[$id]))
        {
            $cart[$id]++;
            $qt++;
        }else
        {
            $qt++;
            $cart[$id] = 1;
        }
        $session->set('cart', $cart);
        $session->set('qt', $qt);
        return $this->redirectToRoute('cart');
    }


    #[Route('/cart/remove/{id}', name: 'cart_remove')]
    public function remove($id, RequestStack $rs)
    {
        $session = $rs->getSession();

        $cart = $session->get('cart', []);
        $qt = $session->get('qt', 0);

        if(!empty($cart[$id]))
        {
            if($cart[$id] > 1){
                $cart[$id]--;
                $qt--;
            } else {
                unset($cart[$id]);
                $qt--;
                
            }
        }
        $session->set('cart', $cart);
        $session->set('qt', $qt);
        return $this->redirectToRoute('cart');
    }

    #[Route('/cart', name:'cart')]
    public function index(RequestStack $rs, ProduitRepository $repo)
    {
        $session = $rs->getSession();
        $cart = $session->get('cart', []);

        $cartWithData = [];

        foreach($cart as $id => $quantity)
        {
            $cartWithData[] = [
                'product' => $repo->find($id),
                'quantity' => $quantity,
            ];
        }

        $total = 0;

        foreach($cartWithData as $item)
        {
            $sousTotal = $item['product']->getPrix() * $item['quantity'];
            $total += $sousTotal;
        }

        return $this->render('panier/index.html.twig', [
            'items' => $cartWithData,
            'total' => $total,
        ]);
    }

    #[Route('cart/delete/{id}', name:'cart_delete')]
    public function delete($id, RequestStack $rs)
    {
        $session = $rs->getSession();
        $cart = $session->get('cart', []);
        $qt = $session->get('qt', 0);

        if(!empty($cart[$id]))
        {
            $qt -= $cart[$id];
            unset($cart[$id]);
        }

        if($qt < 0)
        {
            $qt = 0;
        }
        $session->set('qt', $qt);
        $session->set('cart', $cart);

        return $this->redirectToRoute('cart');
    }
}
