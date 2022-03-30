<?php

namespace App\Controller;

use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="app_cart")
     */
    public function index(SessionInterface $session, ProductsRepository $productsRepository): Response
    {
        $cart = $session->get('cart', []);

        $cartWithData = [];

        foreach ($cart as $id => $quantity) {
            $cartWithData[] = [
                'product' => $productsRepository->find($id),
                'quantity' => $quantity
            ];
        }

        $total = 0;
        foreach ($cartWithData as $item) {
            $totalItem = $item['product']->getPrice() * $item['quantity'];
            $total += $totalItem;
        }

        return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
            'items' => $cartWithData,
            'total' => $total
        ]);
    }

    /**
     * @Route("/cart/add/{id}", name="add_cart")
     */
    public function add($id, SessionInterface $session)
    {

        $cart = $session->get('cart', []);

        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        $session->set('cart', $cart);
        return $this->redirectToRoute("app_cart");
    }


    /**
     * @Route("/cart/remove/{id}", name="remove_cart")
     */
    public function remove($id, SessionInterface $session)
    {
        $cart = $session->get('cart', []);

        if (!empty($cart[$id])) {
            unset($cart[$id]);
        }

        $session->set('cart', $cart);

        return $this->redirect("/cart");
    }

    /**
     * @Route("/cart/remove_quantity/{id}", name="remove_quantity_cart")
     */
    public function removeQuantity($id, SessionInterface $session, ProductsRepository $productsRepository)
    {
        $cart = $session->get('cart', []);

        if($cart[$id] == 1){
            $this->addFlash('notice', "Supprimez l'article");
        }else {
            $cart[$id]--;
        }

        $session->set('cart', $cart);

        return $this->redirect("/cart");
    }
}
