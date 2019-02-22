<?php

/* /opt/lampp/htdocs/purple-cms/vendor/cakephp/bake/src/Template/Bake/Plugin/src/Controller/AppController.php.twig */
class __TwigTemplate_6e96d100aaa4dd5ce33754e70fc38dc25a2aa95bf1293c188a526260b04b6ade extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $__internal_770edd655cdeb606afc443e4edb1f19b4248a91788cb82e88bf8b9495a7c5cfa = $this->env->getExtension("WyriHaximus\\TwigView\\Lib\\Twig\\Extension\\Profiler");
        $__internal_770edd655cdeb606afc443e4edb1f19b4248a91788cb82e88bf8b9495a7c5cfa->enter($__internal_770edd655cdeb606afc443e4edb1f19b4248a91788cb82e88bf8b9495a7c5cfa_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "/opt/lampp/htdocs/purple-cms/vendor/cakephp/bake/src/Template/Bake/Plugin/src/Controller/AppController.php.twig"));

        // line 16
        echo "<?php

namespace ";
        // line 18
        echo twig_escape_filter($this->env, ($context["namespace"] ?? null), "html", null, true);
        echo "\\Controller;

use ";
        // line 20
        echo twig_escape_filter($this->env, ($context["baseNamespace"] ?? null), "html", null, true);
        echo "\\Controller\\AppController as BaseController;

class AppController extends BaseController
{

}
";
        
        $__internal_770edd655cdeb606afc443e4edb1f19b4248a91788cb82e88bf8b9495a7c5cfa->leave($__internal_770edd655cdeb606afc443e4edb1f19b4248a91788cb82e88bf8b9495a7c5cfa_prof);

    }

    public function getTemplateName()
    {
        return "/opt/lampp/htdocs/purple-cms/vendor/cakephp/bake/src/Template/Bake/Plugin/src/Controller/AppController.php.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  31 => 20,  26 => 18,  22 => 16,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("{#
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         2.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
#}
<?php

namespace {{ namespace }}\\Controller;

use {{ baseNamespace }}\\Controller\\AppController as BaseController;

class AppController extends BaseController
{

}
", "/opt/lampp/htdocs/purple-cms/vendor/cakephp/bake/src/Template/Bake/Plugin/src/Controller/AppController.php.twig", "/opt/lampp/htdocs/purple-cms/vendor/cakephp/bake/src/Template/Bake/Plugin/src/Controller/AppController.php.twig");
    }
}
