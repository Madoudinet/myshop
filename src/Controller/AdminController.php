<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Produit;
use App\Form\ProduitType;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/backoffice', name: 'admin_backoffice')]
    public function index()
    {
        return $this->render('admin/index.html.twig');
    }


    #[Route('/modify/{id}', name: 'admin_modify')]
    #[Route('/ajout', name: 'admin_ajout')]
    public function form(Request $globals, EntityManagerInterface $manager, Produit $produit = null, SluggerInterface $slugger): Response
    {

        if($produit == null)
        {
            $produit = new Produit;
            $produit->setDateEnregistrement(new \DateTime());
        }
        
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($globals);
        
        if($form->isSubmitted() && $form->isValid())
        {
            // ! Début traitement de l'image
            $imageFile = $form->get('photo')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($imageFile) {
                // * Permet de récuperer le nom de notre fichier de base
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                // * On enleve se qui gene dans le nom du fichier si on l'utilise en URL
                $safeFilename = $slugger->slug($originalFilename);

                // * On crée un nouveau nom de fichier pour notre image qui sera : nomSafeDuFichier-idUnique.extensionDuFichier
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $imageFile->move(
                        $this->getParameter('img_upload'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $produit->setPhoto($newFilename);
            }
            // ! fin du traitement de l'image
            $manager->persist($produit);
            $manager->flush();
            return $this->redirectToRoute('admin_gestion');
        }
        return $this->render('admin/formproduit.html.twig', [
            'form' => $form,
            'editMode' => $produit->getId() !== null,
        ]);
    }

    #[Route('/gestion', name:'admin_gestion')]
    public function gestion(ProduitRepository $repo): Response
    {
        $produits = $repo->findAll();
        
        return $this->render('admin/gestion.html.twig', [
            'produits' => $produits,
        ]);
    }

    #[Route('/membre', name:'admin_membre')]
    public function membre(UserRepository $repo): Response
    {
        $users = $repo->findAll();
        
        return $this->render('admin/membre.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/membre/modify/{id}', name:'admin_membre_modify')]
    public function modify(Request $request, EntityManagerInterface $manager, User $user = null)
    {
        if($user == null)
        {
            $user = new User;
        }
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('admin_membre');
        }

        return $this->render('registration/role.html.twig', [
            'form' => $form,
            'user' => $user,
        ]);
    }

    #[Route('/register/supprimer/{id}', name:'admin_supprimer')]
    public function supprimer(User $user, EntityManagerInterface $manager)
    {
        $manager->remove($user);
        $manager->flush();
        return $this->redirectToRoute('admin_membre');
    }

    #[Route('/delete/{id}', name:'admin_delete')]
    public function delete(Produit $produit, EntityManagerInterface $manager): Response
    {
        $manager->remove($produit);
        $manager->flush();
        return $this->redirectToRoute('admin_gestion');
    }

}
