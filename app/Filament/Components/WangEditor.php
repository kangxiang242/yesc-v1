<?php

namespace App\Filament\Components;

use Filament\Forms\Components\Field;

class WangEditor extends Field
{
    protected string $view = 'filament.forms.wang-editor';

    protected string $mode = 'default';

    protected string $uploadUrl = '';

    protected array $toolbarButtons = [];

    public function mode(string $mode): static
    {
        $this->mode = $mode;
        return $this;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function uploadUrl(string $url): static
    {
        $this->uploadUrl = $url;
        return $this;
    }

    public function getUploadUrl(): string
    {
        if ($this->uploadUrl) {
            return $this->uploadUrl;
        }
        return route('admin.wang-editor.upload');
    }

    public function toolbarButtons(array $buttons): static
    {
        $this->toolbarButtons = $buttons;
        return $this;
    }

    public function getToolbarButtons(): array
    {
        return $this->toolbarButtons;
    }
}
