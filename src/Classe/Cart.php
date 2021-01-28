<?php

namespace App\Classe;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{
    private $session;
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager, SessionInterface $session)
    {
        $this->session = $session;
        $this->entityManager = $entityManager;
    }

    public function add($id)
    {
        $cart = $this->session->get('cart', []);

        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        $this->session->set('cart', $cart

//on a pus besoin de cette parti, car on l'a perfectionné en haut gràce àn l'incrémentation des produits en panier
//            [
//            [
//                'id'=> $id,
//                'quantity' => 1
//            ]
//            ]
);
    }

    public function get()
    {
        return $this->session->get('cart');
    }

    public function remove()
    {
        return $this->session->remove('cart');
    }

    public function delete($id)
    {
        $cart = $this->session->get('cart', []);

        unset($cart[$id]);

        return $this->session->set('cart', $cart);

    }

    public function decrease($id)
    {
        $cart = $this->session->get('cart', []);
        //vérifier si la quantité de notre produit =1, ce n'est plus une diminution , mais c'est une suppression
        if($cart[$id] > 1) {

            //retirer une quantité
            $cart[$id]--;
        }else{
            //suprimer mon produit
            unset($cart[$id]);
        }
        $this->session->set('cart', $cart);
    }

    public function getFull(){
        $cartComplete = [];

        if ($this->get()){
            foreach ($this->get() as $id => $quantity )
            {
                $product_object = $this->entityManager->getRepository(Product::class)->findOneById($id);

                if (!$product_object) {
                    $this->delete($id);
                    continue;
                }

                $cartComplete[] = [
                    'product' => $product_object,
                    'quantity' => $quantity
                ];
            }


        }
        return $cartComplete;
    }

}