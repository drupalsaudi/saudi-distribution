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

/* core/themes/seven/templates/status-report-page.html.twig */
class __TwigTemplate_33a73cbd1da04f31e44b5aeb49297171b204e979d3671447858f1e9c1600c3dc extends \Twig\Template
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
        // line 14
        if ((twig_length_filter($this->env, ($context["counters"] ?? null)) == 3)) {
            // line 15
            echo "  ";
            $context["element_width_class"] = " system-status-report-counters__item--third-width";
        } elseif ((twig_length_filter($this->env,         // line 16
($context["counters"] ?? null)) == 2)) {
            // line 17
            echo "  ";
            $context["element_width_class"] = " system-status-report-counters__item--half-width";
        }
        // line 19
        echo "<div class=\"system-status-report-counters\">
  ";
        // line 20
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["counters"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["counter"]) {
            // line 21
            echo "    <div class=\"system-status-report-counters__item";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["element_width_class"] ?? null), 21, $this->source), "html", null, true);
            echo "\">
      ";
            // line 22
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed($context["counter"], 22, $this->source), "html", null, true);
            echo "
    </div>
  ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['counter'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 25
        echo "</div>

";
        // line 27
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["general_info"] ?? null), 27, $this->source), "html", null, true);
        echo "
";
        // line 28
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["requirements"] ?? null), 28, $this->source), "html", null, true);
        echo "
";
    }

    public function getTemplateName()
    {
        return "core/themes/seven/templates/status-report-page.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  79 => 28,  75 => 27,  71 => 25,  62 => 22,  57 => 21,  53 => 20,  50 => 19,  46 => 17,  44 => 16,  41 => 15,  39 => 14,);
    }

    public function getSourceContext()
    {
        return new Source("", "core/themes/seven/templates/status-report-page.html.twig", "C:\\xampp\\htdocs\\saad-dp9\\saudi-distribution\\core\\themes\\seven\\templates\\status-report-page.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("if" => 14, "set" => 15, "for" => 20);
        static $filters = array("length" => 14, "escape" => 21);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['if', 'set', 'for'],
                ['length', 'escape'],
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
