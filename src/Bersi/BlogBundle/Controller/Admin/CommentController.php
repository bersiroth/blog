<?php

namespace Bersi\BlogBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class CommentController extends Controller
{
    /**
     * @Route("/admin/article/comment/{id}", name="admin_bersi_blog_comment_article")
     */
    public function commentArticleAction($id)
    {
        $repository = $this->getDoctrine()->getRepository('BersiBlogBundle:Comment');
        $comments = $repository->findBy(
            array('article' => $id));
        return $this->render('BersiBlogBundle:Admin:comment.html.twig', array(
            'comments' => $comments,
            'articleId' => $id
        ));
    }

    /**
     * @Route("/admin/comment/publish/{id}", name="admin_bersi_blog_publish_comment")
     */
    public function publishCommentAction($id, Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $article = $this->getDoctrine()->getRepository('BersiBlogBundle:Comment')->find($id);
            $state = $article->getPublished() == true ? false : true;
            $article->setPublished($state);
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
            return new JsonResponse(array('id' => $id));
        };
    }

    /**
     * @Route("/admin/comment/edit/{articleId}/{commentId}", requirements={"artcileId" = "\d+"}, name="admin_bersi_blog_edit_comment")
     */
    public function editCommentAction($articleId = null, $commentId = null, Request $request)
    {
        $comment = $this->getDoctrine()->getRepository('BersiBlogBundle:Comment')->find($commentId);
        $form = $this->get('form.factory')->createBuilder('form', $comment)
            ->add('pseudo', 'text')
            ->add('content', 'textarea',[
                'max_length' => 1000, ])
            ->add('article_id', 'hidden',[
                'mapped' => false,
                'data' => $articleId ])
            ->add('Envoyer', 'submit')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $comment->setContent(substr($comment->getContent(),0 ,1000));
            $articleId = $request->get('form')['article_id'];
            $repository = $this->getDoctrine()->getRepository('BersiBlogBundle:Article');
            $article = $repository->find($articleId);
            $comment->setArticle($article);
                $em = $this->getDoctrine()->getManager();
                $em->persist($comment);
                $em->flush();
                return $this->redirect($this->generateUrl('admin_bersi_blog_comment_article', ['id' => $articleId]));
        }
        return $this->render('BersiBlogBundle:Admin/Forms:comment.html.twig', [
            'form' => $form->createView()
        ]);

    }
}
