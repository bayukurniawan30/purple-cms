<?php

/* /opt/lampp/htdocs/purple-cms/vendor/cakephp/bake/src/Template/Bake/Plugin/phpunit.xml.dist.twig */
class __TwigTemplate_6fcf404414cf522c7035a5576f5ea2691a2ba93dd60d287058693d23ba943672 extends Twig_Template
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
        $__internal_770edd655cdeb606afc443e4edb1f19b4248a91788cb82e88bf8b9495a7c5cfa->enter($__internal_770edd655cdeb606afc443e4edb1f19b4248a91788cb82e88bf8b9495a7c5cfa_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "/opt/lampp/htdocs/purple-cms/vendor/cakephp/bake/src/Template/Bake/Plugin/phpunit.xml.dist.twig"));

        // line 16
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<phpunit
    colors=\"true\"
    processIsolation=\"false\"
    stopOnFailure=\"false\"
    syntaxCheck=\"false\"
    bootstrap=\"tests/bootstrap.php\"
    >
    <php>
        <ini name=\"memory_limit\" value=\"-1\"/>
        <ini name=\"apc.enable_cli\" value=\"1\"/>
    </php>

    <!-- Add any additional test suites you want to run here -->
    <testsuites>
        <testsuite name=\"";
        // line 31
        echo twig_escape_filter($this->env, ($context["plugin"] ?? null), "html", null, true);
        echo "\">
            <directory>tests/TestCase/</directory>
        </testsuite>
    </testsuites>

    <!-- Setup a listener for fixtures -->
    <listeners>
        <listener class=\"\\Cake\\TestSuite\\Fixture\\FixtureInjector\">
            <arguments>
                <object class=\"\\Cake\\TestSuite\\Fixture\\FixtureManager\"/>
            </arguments>
        </listener>
    </listeners>

    <filter>
        <whitelist>
            <directory suffix=\".php\">src/</directory>
        </whitelist>
    </filter>

</phpunit>
";
        
        $__internal_770edd655cdeb606afc443e4edb1f19b4248a91788cb82e88bf8b9495a7c5cfa->leave($__internal_770edd655cdeb606afc443e4edb1f19b4248a91788cb82e88bf8b9495a7c5cfa_prof);

    }

    public function getTemplateName()
    {
        return "/opt/lampp/htdocs/purple-cms/vendor/cakephp/bake/src/Template/Bake/Plugin/phpunit.xml.dist.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  39 => 31,  22 => 16,);
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
<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<phpunit
    colors=\"true\"
    processIsolation=\"false\"
    stopOnFailure=\"false\"
    syntaxCheck=\"false\"
    bootstrap=\"tests/bootstrap.php\"
    >
    <php>
        <ini name=\"memory_limit\" value=\"-1\"/>
        <ini name=\"apc.enable_cli\" value=\"1\"/>
    </php>

    <!-- Add any additional test suites you want to run here -->
    <testsuites>
        <testsuite name=\"{{ plugin }}\">
            <directory>tests/TestCase/</directory>
        </testsuite>
    </testsuites>

    <!-- Setup a listener for fixtures -->
    <listeners>
        <listener class=\"\\Cake\\TestSuite\\Fixture\\FixtureInjector\">
            <arguments>
                <object class=\"\\Cake\\TestSuite\\Fixture\\FixtureManager\"/>
            </arguments>
        </listener>
    </listeners>

    <filter>
        <whitelist>
            <directory suffix=\".php\">src/</directory>
        </whitelist>
    </filter>

</phpunit>
", "/opt/lampp/htdocs/purple-cms/vendor/cakephp/bake/src/Template/Bake/Plugin/phpunit.xml.dist.twig", "/opt/lampp/htdocs/purple-cms/vendor/cakephp/bake/src/Template/Bake/Plugin/phpunit.xml.dist.twig");
    }
}
