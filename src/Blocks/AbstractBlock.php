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
            $context['fields'] = $block['data'];
        }


        Timber::render($path, $context);
    }
}
