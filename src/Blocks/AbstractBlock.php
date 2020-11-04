<?php
namespace Adeliom\WP\Extensions\Blocks;


use Timber\Timber;

class AbstractBlock extends Block implements InitializableInterface
{
    public function __construct(array $settings)
    {
        parent::__construct($settings);

        $this->dir = $settings['dir'] ?? "views/blocks";
    }

    public function fileExtension(): string
    {
        return '.html.twig';
    }

    public function isValid(): bool
    {
        return class_exists("Timber");
    }

    public function renderBlockCallback(array $block): void
    {
        $frontend = apply_filters(
            'acf_gutenblocks/render_block_frontend_path',
            "{$this->dir}/{$this->name}{$this->fileExtension()}",
            $this
        );

        if (file_exists($frontend)) {
            $path = $frontend;
        } else {
            $path = locate_template($frontend);
        }

        if (empty($path)) {
            return;
        }

        $block['slug'] = str_replace('acf/', '', $block['name']);
        $block['classes'] = sanitize_html_class([
            $block['slug'],
            $block['className'] ?? '',
            $block['align'] ?? '',
        ]);

        $controller = $this;

        $context = Timber::context();
        $context['controller'] = $controller;
        $context['block'] = $block;
        $context['fields'] = $this->with();


        Timber::render($path, $context);
    }
}
