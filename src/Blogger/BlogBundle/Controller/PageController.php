<?php

namespace Blogger\BlogBundle\Controller;

use Blogger\BlogBundle\Form\EnquiryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Swift_Message;

use Blogger\BlogBundle\Entity\Enquiry;

class PageController extends Controller
{
    /**
     * @Route("/", name="blog_home")
     * @Method({"GET"});
     * @Template()
     */
    public function indexAction() {

        $em = $this->getDoctrine()
                   ->getManager();

        $blogs = $em->getRepository("BloggerBlogBundle:Blog")
                    ->getLatestBlogs();

//        $blogs = $em->createQueryBuilder()
//                    ->select("b")
//                    ->from("BloggerBlogBundle:Blog", "b")
//                    ->addOrderBy("b.created", "DESC")
//                    ->getQuery()
//                    ->getResult();

        return array("blogs"=>$blogs);
    }

    /**
     * @Route("/about", name="blog_about")
     * @Method({"GET"})
     * @Template()
     */
    public function aboutAction() {
        return array();
    }

    /**
     * @Route("/contact", name="blog_contact")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function contactAction() {
        $enquiry = new Enquiry();

        $form = $this->createForm(new EnquiryType(), $enquiry);

        $request = $this->getRequest();

        if ($request->getMethod() == "POST") {
            $form->bind($request);

            //$form->handleRequest($request);

            if ($form->isValid()) {
                $this->get('session')->getFlashbag()->set('blogger-notice', 'Your contact enquiry was successfully sent. Thank you!');

                // send email
                $message = Swift_Message::newInstance()
                    ->setSubject('Contact enquiry from symblog')
                    ->setFrom('enquiries@symblog.co.uk')
                    ->setTo($this->container->getParameter('blogger_blog.emails.contact_email'))
                    ->setBody($this->renderView('BloggerBlogBundle:Page:contactEmail.txt.twig',
                        array('enquiry' => $enquiry))
                    );

                $this->get('mailer')->send($message);


//                $std= $form->getData();
//                $em= $this->getDoctrine()->getManager();
//                $em->persist($std);
//                $em->flush();

                return $this->redirect($this->generateUrl("blog_contact"));
            }
        }

        return array('form'=>$form->createView());
    }

    /**
     * @Route("/show/{id}/{slug}", name="blog_show", requirements={"id" = "\d+"})
     * @Method({"GET"})
     * @Template()
     */
    public function showAction($id, $slug) {

        $em = $this->getDoctrine()->getManager();

        $blog = $em->getRepository("BloggerBlogBundle:Blog")->find($id);

        if (!$blog) {
            throw $this->createNotFoundException("Unable to find Blog post.");
        }

        $comments = $em->getRepository("BloggerBlogBundle:Comment")
                       ->getCommentsForBlog($blog->getId());

        return array("blog"=>$blog, "comments"=>$comments);
    }

    /**
     * @Route("/sidebar", name="blog_sidebar")
     * @Method({"GET"})
     * @Template()
     */
    public function sidebarAction() {
        $em = $this->getDoctrine()
            ->getEntityManager();

        $tags = $em->getRepository('BloggerBlogBundle:Blog')
            ->getTags();

        $tagWeights = $em->getRepository('BloggerBlogBundle:Blog')
            ->getTagWeights($tags);

        return array('tags' => $tagWeights);
    }
}
