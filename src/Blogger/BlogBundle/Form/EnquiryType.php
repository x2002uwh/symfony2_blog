<?php
/**
 * Created by PhpStorm.
 * User: engage
 * Date: 15-01-07
 * Time: 10:19 PM
 */

namespace Blogger\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class EnquiryType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add("name");
        $builder->add("email", "email");
        $builder->add("subject");
        $builder->add("body", "textarea");
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            "data_class"=>"Blogger\BlogBundle\Entity\Enquiry"
        ));
    }

    public function getName(){
        return "contact";
    }
} 