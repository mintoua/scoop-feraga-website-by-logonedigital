<?php

namespace App\Services;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{
    private $session;
    private $entityManager;

    public function __construct(SessionInterface $session, EntityManagerInterface $entityManager)
    {
        $this->session = $session;
        $this->entityManager = $entityManager;
    }

    public function add($slug){

        $cart = $this->session->get('cart',[]);

        if(!empty($cart[$slug])){
            $cart[$slug]++;
        }else{
            $cart[$slug]=1;
        }

        $this->session->set('cart',$cart);
    }

    public function decrease($slug){

        $cart = $this->session->get('cart', []);

        if(!empty($cart[$slug])){
            $cart[$slug]--;
        }
        if($cart[$slug]==0)
        {
            unset($cart[$slug]);
        }

        $this->session->set('cart',$cart);
    }

    public function remove($slug){
        $cart = $this->get();

        if(!empty($cart[$slug])){
            unset($cart[$slug]);
        }

        $this->session->set('cart', $cart);
    }


    public function get(){
        return $this->session->get('cart');
    }

    public function getFullCart() :array {

        $fullCart = [];
        if($this->get()){
            foreach($this->get() as $slug => $quantity){
                $product_object =$this->entityManager->getRepository(Product::class)->findOneBySlug($slug);
                if(!$product_object){
                    $this->remove($slug);
                    continue;
                }
                $fullCart[]=[
                    'product' => $product_object,
                    'quantity'=> $quantity
                ];
            }
        }


        return $fullCart;
    }

    public function clearCart(){
        return $this->session->remove('cart');
    }
}