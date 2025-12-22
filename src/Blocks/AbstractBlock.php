<?php

namespace Adeliom\WP\Extensions\Blocks;

use Timber\Timber;

class AbstractBlock extends Block implements InitializableInterface
{
    /**
     * @var mixed|string
     */
    public $dir_preview;
    /**
     * @var mixed|string
     */
    public $dir_icon;
    public string $template = "";

    public string $preview = "";

    public function __construct(array $settings)
    {
        parent::__construct($settings);


        $this->dir = $settings['dir'] ?? "views/blocks";
        $this->dir_preview = $settings['dir_preview'] ?? "assets/images/admin/gutenberg-blocks";
        $this->dir_icon = $settings['dir_icon'] ?? "assets/images/admin/gutenberg-blocks";
        $tpl = $this->name;
        $tpl = str_replace("-block", "", $tpl);

        $this->template = sprintf('%s/%s%s', $this->dir, $tpl, $this->fileExtension());
        $this->preview = sprintf('%s/%s/preview%s', $this->dir_preview, $tpl, $this->previewExtension());

        $iconFile = get_template_directory() . sprintf('/%s/%s/picto%s', $this->dir_icon, $tpl, $this->iconExtension());
        $this->icon = file_exists($iconFile) ? file_get_contents($iconFile) : parent::getIcon();
    }

    public function iconExtension(): string
    {
        return '.svg';
    }

    public function previewExtension(): string
    {
        return '.jpg';
    }

    public function fileExtension(): string
    {
        return '.html.twig';
    }

    public function isValid(): bool
    {
        return class_exists("Timber");
    }

    public function renderBlockCallback(array $block, string $content = '', bool $is_preview = false, int $post_id = 0): void
    {
        $path = apply_filters(
            'acf_gutenblocks/render_block_frontend_path',
            $this->template,
            $this
        );

        $block['slug'] = str_replace('acf/', '', $block['name']);
        $block['classes'] = sanitize_html_class([
            $block['slug'],
            $block['className'] ?? '',
            $block['align'] ?? '',
        ]);

        $controller = $this;

        if (is_admin() && isset($block['data']['content']) && !empty($block['data']['content']['img_preview'])) {
            $path_preview = locate_template($this->preview);
            if (!empty($path_preview)) {
                echo "<img src='" . get_template_directory_uri() . "/" . $this->preview . "' />";
                return;
            }
        }

        $context = Timber::context();
        $context['controller'] = $controller;
        $context['post_id'] = $post_id;
        $context['is_preview'] = $is_preview;
        $context['content'] = $content;
        $context['block'] = $block;

        if (method_exists($this, "addToContext")) {
            $context['context_block'] = $this->addToContext();
        }

        if (method_exists($this, "with")) {
            $context['fields'] = $this->with();
        } elseif (get_fields()) {
            $context['fields'] = get_fields();
        } else {
            $field_groups = acf_get_field_groups(['block' => $block['name']]);
            $fields = acf_get_block_fields($block);

            if (!empty($fields)) {
                $calculatedFields = $this->map_acf_values_to_structure($fields, $block['data']);

                if (!empty($calculatedFields)) {
                    $context['fields'] = $calculatedFields;
                }
            } else {
                $context['fields'] = $block['data'];
            }
        }

        Timber::render($path, $context);
    }

    function map_acf_values_to_structure($fields, $data, $prefix = '')
    {
        $result = [];

        foreach ($fields as $field) {
            $name = $field['name'] ?? '';
            if (!$name) continue;

            // On construit la clé complète (ex: introduction_title_tag)
            $full_key = $prefix . $name;

            // 1. Cas GROUPE
            if ($field['type'] === 'group' && !empty($field['sub_fields'])) {
                $result[$name] = $this->map_acf_values_to_structure($field['sub_fields'], $data, $full_key . '_');
            } // 2. Cas RÉPÉTEUR
            elseif ($field['type'] === 'repeater' && !empty($field['sub_fields'])) {
                $result[$name] = [];

                // On récupère le nombre de lignes via la clé du repeater (ex: "items" => 3)
                $count = isset($data[$full_key]) ? (int)$data[$full_key] : 0;

                for ($i = 0; $i < $count; $i++) {
                    // On cherche dynamiquement le préfixe de la ligne (0, 1 ou suffixe nommé)
                    $row_prefix = $this->find_dynamic_row_prefix($full_key, $i, $field['sub_fields'], $data);

                    if ($row_prefix) {
                        $result[$name][] = $this->map_acf_values_to_structure($field['sub_fields'], $data, $row_prefix);
                    }
                }
            } // 3. Cas CHAMP SIMPLE
            else {
                // On ne prend que la valeur, on ignore les clés "_name" (field keys)
                $result[$name] = $data[$full_key] ?? null;
            }
        }

        return $result;
    }

    /**
     * Identifie dynamiquement le préfixe d'une ligne de répéteur
     */
    private function find_dynamic_row_prefix($full_key, $index, $sub_fields, $data)
    {
        if (empty($sub_fields)) return null;

        $first_sub_name = $sub_fields[0]['name'];

        // Test 1 : Format standard (index 0, 1, 2...) -> items_0_label
        $standard = $full_key . '_' . $index . '_';
        if (isset($data[$standard . $first_sub_name])) return $standard;

        // Test 2 : Format nommé ou décalé (index 1, 2, 3...) -> items_row_1_label ou items_1_label
        // On scanne les clés pour trouver celle qui contient l'index et finit par le premier sous-champ
        foreach ($data as $key => $val) {
            // Regex : commence par la clé du repeater, contient l'index, finit par le nom du premier sous-champ
            $pattern = '/^' . preg_quote($full_key) . '_.*' . ($index + 1) . '_' . preg_quote($first_sub_name) . '$/';
            if (preg_match($pattern, $key)) {
                // On retourne le préfixe trouvé en retirant le nom du sous-champ à la fin
                return substr($key, 0, -strlen($first_sub_name));
            }
        }

        return null;
    }
}
