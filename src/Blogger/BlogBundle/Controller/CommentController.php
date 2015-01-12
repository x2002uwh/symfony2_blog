<?php

namespace Blogger\BlogBundle\Controller;

use Blogger\BlogBundle\Entity\Comment;
use Blogger\BlogBundle\Form\CommentType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;



/**
 * Class CommentController
 * Comment controller
 *
 * @package Blogger\BlogBundle\Controller
 */
class CommentController extends Controller
{
    /**
     * @param $blog_id  Blog Id
     *
     * @Route("/new/{blog_id}", name="blog_comment_new",  requirements={"blog_id" = "\d+"})
     * @Method({"GET"})
     * @Template("BloggerBlogBundle:Comment:form.html.twig")
     */
    public function newAction($blog_id) {
        $blog = $this->getBlog($blog_id);

        $comment = new Comment();
        $comment->setBlog($blog);

        $form = $this->createForm(new CommentType(), $comment);

        return array("form"=>$form->createView(), "comment"=>$comment);
    }

    /**
     *
     * @Route("/comment/{blog_id}", name="blog_comment_create", requirements={"blog_id"="\d+"})
     * @Method({"POST"})
     * @Template("BloggerBlogBundle:Comment:create.html.twig")
     */
    public function createAction($blog_id) {
        $blog = $this->getBlog($blog_id);

        $comment = new Comment();
        $comment->setBlog($blog);

        $request = $this->getRequest();
        $form = $this->createForm(new CommentType(), $comment);

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($comment);
            $em->flush();

            return $this->redirect(
                $this->generateUrl("blog_show", array(
                       "id"   => $comment->getBlog()->getId(),
                       "slug" => $comment->getBlog()->getSlug() . "#comment-" . $comment->getId()
                    )
                ));
        }

        return array(
            "comment"=>$comment,
            "form" => $form->createView()
        );
    }

    private function getBlog($blog_id) {
        $em = $this->getDoctrine()->getManager();

        $blog = $em->getRepository("BloggerBlogBundle:Blog")
                   ->find($blog_id);

        if (!$blog) {
            throw $this->createNotFoundException("Unable to find the blog post.");
        }

        return $blog;
    }
}
