<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('admin/commande')]
class CommandeController extends AbstractController
{
    #[Route('/', name:'admin_commande')]
    public function index(CommandeRepository $repo): Response
    {
        $coms = $repo->findAll();
        return $this->render('commande/index.html.twig', [
            'coms' => $coms,
        ]);
    }

    #[Route('delete/{id}', name:'admin_commande_delete')]
    public function delete(Commande $commande, EntityManagerInterface $manager): Response
    {
        $manager->remove($commande);
        $manager->flush();
        return $this->redirectToRoute('admin_commande');
    }

    #[Route('/modify/{id}', name:'admin_commande_modify')]
    public function modify(Request $request, EntityManagerInterface $manager, Commande $commande = null)
    {
        if($commande == null)
        {
            $commande = new Commande;
        }
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($commande);
            $manager->flush();
            return $this->redirectToRoute('admin_commande');
        }

        return $this->render('order/index.html.twig', [
            'form' => $form,
        ]);
    }
}
