<?php

/* error/404.twig */
class __TwigTemplate_3c21e0e116129805edc5925404d0322a6b48df9c1c7a256066be561df2e7f01e extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("layout/layout.twig");

        $this->blocks = array(
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "layout/layout.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = array())
    {
        // line 4
        echo "<div class=\"page-header\">
    <h1>404 <small>page not found</small></h1>
</div>

";
        // line 8
        if ((isset($context["type"]) ? $context["type"] : null)) {
            $template = $this->env->resolveTemplate((("error/404-" . (isset($context["type"]) ? $context["type"] : null)) . ".twig"));
            $template->display($context);
        }
    }

    public function getTemplateName()
    {
        return "error/404.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  37 => 8,  31 => 4,  28 => 3,);
    }
}
