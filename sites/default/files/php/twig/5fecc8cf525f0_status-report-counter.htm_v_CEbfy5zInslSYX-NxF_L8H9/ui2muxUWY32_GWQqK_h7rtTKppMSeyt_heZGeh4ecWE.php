<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* core/themes/seven/templates/status-report-counter.html.twig */
class __TwigTemplate_8f52c1be75e76ad141b7f1b4c5f3aa5e68eb021b59e15ca52b0b6cf24abc6816 extends \Twig\Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
        $this->sandbox = $this->env->getExtension('\Twig\Extension\SandboxExtension');
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 15
        $context["classes"] = [0 => "system-status-counter", 1 => ("system-status-counter--" . $this->sandbox->ensureToStringAllowed(        // line 17
($context["severity"] ?? null), 17, $this->source))];
        // line 20
        echo "<span";
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["attributes"] ?? null), "addClass", [0 => ($context["classes"] ?? null)], "method", false, false, true, 20), 20, $this->source), "html", null, true);
        echo ">
  <span class=\"system-status-counter__status-icon system-status-counter__status-icon--";
        // line 21
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["severity"] ?? null), 21, $this->source), "html", null, true);
        echo "\"></span>
  <span class=\"system-status-counter__status-title\">
    <span class=\"system-status-counter__title-count\">";
        // line 23
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["amount"] ?? null), 23, $this->source), "html", null, true);
        echo " ";
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["text"] ?? null), 23, $this->source), "html", null, true);
        echo "</span>
    <span class=\"system-status-counter__details\"><a href=\"#";
        // line 24
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["severity"] ?? null), 24, $this->source), "html", null, true);
        echo "\" ><span class=\"visually-hidden\">";
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["text"] ?? null), 24, $this->source), "html", null, true);
        echo " </span>Details</a></span>
  </span>
</span>
";
    }

    public function getTemplateName()
    {
        return "core/themes/seven/templates/status-report-counter.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  58 => 24,  52 => 23,  47 => 21,  42 => 20,  40 => 17,  39 => 15,);
    }

    public function getSourceContext()
    {
        return new Source("", "core/themes/seven/templates/status-report-counter.html.twig", "C:\\xampp\\htdocs\\saad-dp9\\saudi-distribution\\core\\themes\\seven\\templates\\status-report-counter.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("set" => 15);
        static $filters = array("escape" => 20);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['set'],
                ['escape'],
                []
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->source);

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

    }
}
