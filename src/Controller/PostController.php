<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    /**
     * @Route("/", name="post_index")
     * @param PostRepository $postRepository
     * @return Response
     */
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findAllOrderedByCreatedAt()
        ]);
    }

    /**
     * @Route("post/new", name="post_add")
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function add(Request $request, EntityManagerInterface $manager): Response
    {
        $post = new Post();

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Le slug du post ainsi que le champ createdAt et le champ user sont générés automatiquement avant de persist() (Event/PostSubscriber.php)
            $manager->persist($post);
            $manager->flush();

            return $this->redirectToRoute('post_show', [
                'slug' => $post->getSlug()
            ]);
        }

        return $this->render('post/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/post/{slug}", name="post_show")
     * @param Post $post Entité Post obtenue grâce au ParamConverter de Symfony
     * @return Response
     */
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post
        ]);
    }

    /**
     * @Route("post/{id}/edit", name="post_edit")
     * @param Post $post Entité Post obtenue grâce au ParamConverter de Symfony
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(Post $post,
                         Request $request,
                         EntityManagerInterface $manager): Response
    {
        // Autorise l'accès à cette route seulement à l'auteur du post ou à un admin (Security/PostVoter.php)
        $this->denyAccessUnlessGranted('edit', $post);

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Le slug du post est regénéré automatiquement avant de flush() (Event/PostSubscriber.php)
            $manager->flush();

            return $this->redirectToRoute('post_show', [
                'slug' => $post->getSlug()
            ]);
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/post/{id}/delete", name="post_delete")
     * @param Post $post
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Post $post, EntityManagerInterface $manager): Response
    {
        // Autorise l'accès à cette route seulement à l'auteur du post ou à un admin (Security/PostVoter.php)
        $this->denyAccessUnlessGranted('delete', $post);

        $manager->remove($post);
        $manager->flush();

        return $this->redirectToRoute('user_index');
    }
}
