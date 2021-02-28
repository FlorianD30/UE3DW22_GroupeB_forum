<?php

namespace App\Controller;

use App\Entity\Like;
use App\Entity\Posts;
use App\Form\LikeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
     * @Route("/user/like/{id}", name="like_post", methods={"GET"})
     */
    public function like(Posts $post): Response
    {
        $like = new Like();
        $user = $this->getUser();
        $isLike=false;
        foreach ($post->getLikes() as $like) {
            if ($like->getUser()==$user) {
                $isLike=true;
            }
        }
        $entityManager = $this->getDoctrine()->getManager();

        if ($isLike) {
            $like=$this->getDoctrine()->getManager()->getRepository(Like::class)->findOneBy(['user'=>$user]);
            $entityManager->remove($like);
        }else{
            $like = new Like();
            $like->setUser($user)->setPost($post);
            $entityManager->persist($like);
        }
        $entityManager->flush();
        return $this->redirectToRoute('posts_index');
    }

}
