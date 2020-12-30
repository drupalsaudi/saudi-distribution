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

/* core/themes/seven/templates/status-report-general-info.html.twig */
class __TwigTemplate_5727cfd7609cf7262985565ae1c3d10febf0a043a177a0975a23451b6ad1c7c7 extends \Twig\Template
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
        // line 32
        echo "<div class=\"system-status-general-info\">
  <h2 class=\"system-status-general-info__header\">";
        // line 33
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("General System Information"));
        echo "</h2>
  <div class=\"system-status-general-info__items\">
    <div class=\"system-status-general-info__item\">
      <span class=\"system-status-general-info__item-icon system-status-general-info__item-icon--drupal\"></span>
      <div class=\"system-status-general-info__item-details\">
        <h3 class=\"system-status-general-info__item-title\">";
        // line 38
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Drupal Version"));
        echo "</h3>
        ";
        // line 39
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["drupal"] ?? null), "value", [], "any", false, false, true, 39), 39, $this->source), "html", null, true);
        echo "
        ";
        // line 40
        if (twig_get_attribute($this->env, $this->source, ($context["drupal"] ?? null), "description", [], "any", false, false, true, 40)) {
            // line 41
            echo "          <div class=\"description\">";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["drupal"] ?? null), "description", [], "any", false, false, true, 41), 41, $this->source), "html", null, true);
            echo "</div>
        ";
        }
        // line 43
        echo "      </div>
    </div>
    <div class=\"system-status-general-info__item\">
      <span class=\"system-status-general-info__item-icon system-status-general-info__item-icon--clock\"></span>
      <div class=\"system-status-general-info__item-details\">
        <h3 class=\"system-status-general-info__item-title\">";
        // line 48
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Last Cron Run"));
        echo "</h3>
        ";
        // line 49
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["cron"] ?? null), "value", [], "any", false, false, true, 49), 49, $this->source), "html", null, true);
        echo "
        ";
        // line 50
        if (twig_get_attribute($this->env, $this->source, ($context["cron"] ?? null), "run_cron", [], "any", false, false, true, 50)) {
            // line 51
            echo "          <div class=\"system-status-general-info__run-cron\">";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["cron"] ?? null), "run_cron", [], "any", false, false, true, 51), 51, $this->source), "html", null, true);
            echo "</div>
        ";
        }
        // line 53
        echo "        ";
        if (twig_get_attribute($this->env, $this->source, ($context["cron"] ?? null), "description", [], "any", false, false, true, 53)) {
            // line 54
            echo "          <div class=\"system-status-general-info__description\">";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["cron"] ?? null), "description", [], "any", false, false, true, 54), 54, $this->source), "html", null, true);
            echo "</div>
        ";
        }
        // line 56
        echo "      </div>
    </div>
    <div class=\"system-status-general-info__item\">
      <span class=\"system-status-general-info__item-icon system-status-general-info__item-icon--server\"></span>
      <div class=\"system-status-general-info__item-details\">
        <h3 class=\"system-status-general-info__item-title\">";
        // line 61
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Web Server"));
        echo "</h3>
        ";
        // line 62
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["webserver"] ?? null), "value", [], "any", false, false, true, 62), 62, $this->source), "html", null, true);
        echo "
        ";
        // line 63
        if (twig_get_attribute($this->env, $this->source, ($context["webserver"] ?? null), "description", [], "any", false, false, true, 63)) {
            // line 64
            echo "          <div class=\"description\">";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["webserver"] ?? null), "description", [], "any", false, false, true, 64), 64, $this->source), "html", null, true);
            echo "</div>
        ";
        }
        // line 66
        echo "      </div>
    </div>
    <div class=\"system-status-general-info__item\">
      <span class=\"system-status-general-info__item-icon system-status-general-info__item-icon--php\"></span>
      <div class=\"system-status-general-info__item-details\">
        <h3 class=\"system-status-general-info__item-title\">";
        // line 71
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("PHP"));
        echo "</h3>
        <h4 class=\"system-status-general-info__sub-item-title\">";
        // line 72
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Version"));
        echo "</h4>";
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["php"] ?? null), "value", [], "any", false, false, true, 72), 72, $this->source), "html", null, true);
        echo "
        ";
        // line 73
        if (twig_get_attribute($this->env, $this->source, ($context["php"] ?? null), "description", [], "any", false, false, true, 73)) {
            // line 74
            echo "          <div class=\"description\">";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["php"] ?? null), "description", [], "any", false, false, true, 74), 74, $this->source), "html", null, true);
            echo "</div>
        ";
        }
        // line 76
        echo "
        <h4 class=\"system-status-general-info__sub-item-title\">";
        // line 77
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Memory limit"));
        echo "</h4>";
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["php_memory_limit"] ?? null), "value", [], "any", false, false, true, 77), 77, $this->source), "html", null, true);
        echo "
        ";
        // line 78
        if (twig_get_attribute($this->env, $this->source, ($context["php_memory_limit"] ?? null), "description", [], "any", false, false, true, 78)) {
            // line 79
            echo "          <div class=\"description\">";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["php_memory_limit"] ?? null), "description", [], "any", false, false, true, 79), 79, $this->source), "html", null, true);
            echo "</div>
        ";
        }
        // line 81
        echo "      </div>
    </div>
    <div class=\"system-status-general-info__item\">
      <span class=\"system-status-general-info__item-icon system-status-general-info__item-icon--database\"></span>
      <div class=\"system-status-general-info__item-details\">
        <h3 class=\"system-status-general-info__item-title\">";
        // line 86
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Database"));
        echo "</h3>
        <h4 class=\"system-status-general-info__sub-item-title\">";
        // line 87
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Version"));
        echo "</h4>";
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["database_system_version"] ?? null), "value", [], "any", false, false, true, 87), 87, $this->source), "html", null, true);
        echo "
        ";
        // line 88
        if (twig_get_attribute($this->env, $this->source, ($context["database_system_version"] ?? null), "description", [], "any", false, false, true, 88)) {
            // line 89
            echo "          <div class=\"description\">";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["database_system_version"] ?? null), "description", [], "any", false, false, true, 89), 89, $this->source), "html", null, true);
            echo "</div>
        ";
        }
        // line 91
        echo "
        <h4 class=\"system-status-general-info__sub-item-title\">";
        // line 92
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("System"));
        echo "</h4>";
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["database_system"] ?? null), "value", [], "any", false, false, true, 92), 92, $this->source), "html", null, true);
        echo "
        ";
        // line 93
        if (twig_get_attribute($this->env, $this->source, ($context["database_system"] ?? null), "description", [], "any", false, false, true, 93)) {
            // line 94
            echo "          <div class=\"description\">";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["database_system"] ?? null), "description", [], "any", false, false, true, 94), 94, $this->source), "html", null, true);
            echo "</div>
        ";
        }
        // line 96
        echo "      </div>
    </div>
  </div>
</div>
";
    }

    public function getTemplateName()
    {
        return "core/themes/seven/templates/status-report-general-info.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  205 => 96,  199 => 94,  197 => 93,  191 => 92,  188 => 91,  182 => 89,  180 => 88,  174 => 87,  170 => 86,  163 => 81,  157 => 79,  155 => 78,  149 => 77,  146 => 76,  140 => 74,  138 => 73,  132 => 72,  128 => 71,  121 => 66,  115 => 64,  113 => 63,  109 => 62,  105 => 61,  98 => 56,  92 => 54,  89 => 53,  83 => 51,  81 => 50,  77 => 49,  73 => 48,  66 => 43,  60 => 41,  58 => 40,  54 => 39,  50 => 38,  42 => 33,  39 => 32,);
    }

    public function getSourceContext()
    {
        return new Source("", "core/themes/seven/templates/status-report-general-info.html.twig", "C:\\xampp\\htdocs\\saad-dp9\\saudi-distribution\\core\\themes\\seven\\templates\\status-report-general-info.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("if" => 40);
        static $filters = array("t" => 33, "escape" => 39);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['if'],
                ['t', 'escape'],
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
