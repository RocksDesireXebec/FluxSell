<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
class ProduitController extends AbstractController
{
    private $produitRepository;

    public function __construct(ProduitRepository $produitRepository) {
        $this->produitRepository = $produitRepository;
    }

    public function __invoke() : mixed
    {    
        return $this->produitRepository->mostPopularProducts();
    }

    #[Route('/produit', name: 'app_produit_index', methods: ['GET'])]
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    #[Route('/produit/new', name: 'app_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProduitRepository $produitRepository): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $produitRepository->add($produit);
            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/produit/{id}', name: 'app_produit_show', methods: ['GET'])]
    public function show(Produit $produit): Response
    {
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/produit/{id}/edit', name: 'app_produit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produit $produit, ProduitRepository $produitRepository): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $produitRepository->add($produit);
            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/produit/{id}', name: 'app_produit_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, ProduitRepository $produitRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $produitRepository->remove($produit);
        }

        return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/mainCategories', name: 'app_mainCategories', methods: ['GET'])]
    public function mainCategories(Request $request, CategorieRepository $categorieRepository) : JsonResponse
    {
        return new JsonResponse($categorieRepository->findMainCategories(),200,['content-type'=>'application/ld+json;charset=utf-8']);       
    }

    #[Route('/categorie/{id}/categories', name: 'app_categories_de_categorie', methods: ['GET'])]
    public function categoriesDeCategorie(Categorie $categorie, SerializerInterface $serializer) : Response
    {
        for ($i=0; $i < count($categorie->getCategories()); $i++) { 
            $data[] = $categorie->getCategories()->get($i);
        }
        $json = $serializer->serialize($data,'json',[AbstractNormalizer::ATTRIBUTES => ['id','libelle','description']]);
        
        return new Response($json, 200, ['content-type','application/ld+json;charset=utf-8']);

    }

    #[Route('/categorie/{id}/produits', name: 'app_produits_de_categorie', methods: ['GET'])]
    public function produitsDeCategorie(Categorie $categorie, SerializerInterface $serializer) : Response
    {
        for ($i=0; $i < count($categorie->getProduits()); $i++) { 
            $data[] = $categorie->getProduits()->get($i);
        }
        $json = $serializer->serialize($data,'json',[AbstractNormalizer::ATTRIBUTES => ['id', 'idProduit','libelle','prix','etoile','marque','etat','note','qteEnStock','description']]);
        
        return new Response($json, 200, ['content-type','application/ld+json;charset=utf-8']);

    }

    #[Route('/categorie/{id}/produits/{idProduit}', name: 'app_produit_de_categorie', methods: ['GET'])]
    public function produitDeCategorie($id, $idProduit, SerializerInterface $serializer, ProduitRepository $produitRepository, CategorieRepository $categorieRepository) : Response
    {
        $categorie = $categorieRepository->find($id);
        /**
         * Ici il faudra tester que le produit appartient effectivement à la categorie
         */
        $produit = $produitRepository->find($idProduit);
        
        $json = $serializer->serialize($produit,'json',[AbstractNormalizer::ATTRIBUTES => ['id', 'idProduit','libelle','prix','etoile','marque','etat','note','qteEnStock','description']]);
        
        return new Response($json, 200, ['content-type','application/ld+json;charset=utf-8']);

    }
}
