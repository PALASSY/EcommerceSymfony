<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\AdminCommentType;
use App\Repository\CommentRepository;
use App\Service\Pagination\Pagination;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentController extends AbstractController
{
    /**
     * @Route("/admin/comments/{page<\d+>?1}", name="admin_comments_list")
     */
    public function index(CommentRepository $commentrepo,$page,Pagination $pagination): Response
    {
        //Setter la (l'Entity et la page actuelle)
        $pagination->setEntityClass(Comment::class)
                   ->setCurrentpage($page)
                   //->setRoute('admin_comments_list')
                   ;

        return $this->render('admin/comment/index.html.twig', [
            'pagination' => $pagination
        ]);
    }



    /**
     * Page qui modifie le commentaire
     * @Route("/admin/comment/{id}/edit", name="admin_comment_edit")
     * @param Comment $comment
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function edit(Comment $comment, Request $request, ObjectManager $manager)
    {
        $form = $this->createForm(AdminCommentType::class,$comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash("success","Votre commentaire a été modifié");

            return $this->redirectToRoute("admin_comments_list");
        }
        return $this->render("admin/comment/edit.html.twig", [
           "comment" => $comment,
           "form" => $form->createView()
        ]);
    }


    

    /**
     * Page qui supprime le commentaire
     * @Route("/admin/comment/{id}/delete", name="admin_comment_delete")
     *
     * @param Comment $comment
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(Comment $comment, ObjectManager $manager)
    {
        $commentid = $comment->getId();
        $manager->remove($comment);
        $manager->flush();

        $this->addFlash("success", "Votre commentaire N° <strong>{$commentid}</strong> a été supprimé");

        return $this->redirectToRoute("admin_comments_list");
    }
}
