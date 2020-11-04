<?php
namespace Adeliom\WP\Extensions\Blocks;


interface InitializableInterface
{
    public function fileExtension(): string;

    public function isValid(): bool;

    public function renderBlockCallback(array $block): void;

    public function init(): void;
}
