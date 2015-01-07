<?php

namespace Icap\BlogBundle\Form;

use Icap\BlogBundle\Form\DataTransformer\IntToBlogTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Zenstruck\Bundle\FormBundle\Form\DataTransformer\AjaxEntityTransformer;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service("icap_blog.form.widget_blog")
 * @DI\FormType(alias = "blog_widget_blog_form")
 */
class WidgetBlogType extends AbstractType
{
    /**
     * @var IntToBlogTransformer
     */
    private $intToBlogTransformer;

    /**
     * @DI\InjectParams({
     *     "intToBlogTransformer" = @DI\Inject("icap_blog.transformer.int_to_blog")
     * })
     */
    public function __construct(IntToBlogTransformer $intToBlogTransformer)
    {
        $this->intToBlogTransformer = $intToBlogTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('resourceNode', 'resourcePicker', array(
                    'theme_options' => array(
                        'label_width'   => 'col-md-6',
                        'control_width' => 'col-md-6'
                    ),
                    'label' => 'blog'
                ));
    }

    public function getName()
    {
        return 'blog_widget_blog_form';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class'         => 'Icap\BlogBundle\Entity\WidgetListBlog',
                'translation_domain' => 'icap_blog'
            )
        );
    }
}