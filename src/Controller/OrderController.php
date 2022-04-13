<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartProducts;
use App\Entity\Orders;
use App\Entity\Products;
use App\Form\OrdersType;
use App\Repository\CartRepository;
use App\Repository\OrdersRepository;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OrderController extends AbstractController
{
    /**
     * @Route("/order", name="app_order")
     */
    public function index(ValidatorInterface $validator, Security $security, Request $request, SessionInterface $session, EntityManagerInterface $entityManager, ProductsRepository $productsRepository, OrdersRepository $ordersRepository): Response
    {
        $cartDatabase = new Cart();
        $cartDatabase->setUserId($security->getUser());
        $entityManager->persist($cartDatabase);


        $cart = $session->get('cart', []);

        $cartWithData = [];

        foreach ($cart as $id => $quantity) {
            $cartWithData[] = [
                'product' => $productsRepository->find($id),
                'quantity' => $quantity
            ];
        }

        $total = 0;
        $cartProducts = new CartProducts();
        foreach ($cartWithData as $item) {
            $cartProducts->setCart($cartDatabase);
            $cartProducts->setProduct($item['product']);
            $cartProducts->setQuantity($item['quantity']);
            $entityManager->persist($cartProducts);
            $totalItem = $item['product']->getPrice() * $item['quantity'];
            $total += $totalItem;
        }

        $order = new Orders();

        $user = $security->getUser();

        $form = $this->createForm(OrdersType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $order->setUserId($user->getUserIdentifier());
            $order->setCart($cartDatabase);
            $order->setCreatedAt(new \DateTimeImmutable());
            $order->setTotalPrice($total);
            $order->setStatus('payed');
            foreach ($cart as $id => $quantity) {
                $product = $productsRepository->find($id);
                $product->setStock($product->getStock() - $quantity);
                $errors = $validator->validate($product);
                if(count($errors) > 0) {
                    $this->addFlash('error', 'La quantité commandée est suppérieur au stock');
                    return $this->redirectToRoute('app_order', [], Response::HTTP_SEE_OTHER);
                }
                $entityManager->flush();
            }
            $ordersRepository->add($order);
            $session->set('cart', []);
            return $this->redirectToRoute('app_products_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('order/index.html.twig', [
            'controller_name' => 'OrderController',
            'items' => $cartWithData,
            'total' => $total,
            'form' => $form->createView(),
        ]);
    }

    public function order(): Response
    {

        return $this->render('order/index.html.twig', [
            'controller_name' => 'OrderController',
        ]);
    }
}
