<?php

namespace App\Filament\Components;

use Filament\Forms\Components\Field;

class WangEditor extends Field
{
    protected string $view = 'filament.forms.wang-editor';

    protected string $mode = 'default';

    protected string $uploadUrl = '';

    protected array $toolbarButtons = [];

    protected ?bool $hasCustomToolbar = null;

    /**
     * 默认完整工具栏键列表（wangEditor5 标准键）
     *
     * @see https://www.wangeditor.com/v5/toolbar-config.html
     */
    public const FULL_TOOLBAR = [
        'headerSelect',
        '|',
        'bold', 'underline', 'italic', 'through',
        'code', 'sub', 'sup', 'clearStyle',
        'color', 'bgColor',
        'fontSize', 'fontFamily',
        'lineHeight',
        '|',
        'indent', 'delIndent',
        'justifyLeft', 'justifyCenter', 'justifyRight', 'justifyJustify',
        '|',
        'bulletedList', 'numberedList', 'todo',
        'blockquote',
        'codeBlock',
        'codeSelectLang',
        '|',
        'insertTable',
        'insertImage', 'uploadImage',
        'insertLink', 'unLink',
        'insertVideo',
        'emotion',
        'divider',
        '|',
        'undo', 'redo',
        'fullScreen',
    ];

    /**
     * 简单模式工具栏（常用基本工具）
     */
    public const SIMPLE_TOOLBAR = [
        'bold', 'italic', 'underline', 'through',
        'color', 'bgColor',
        'fontSize',
        '|',
        'justifyLeft', 'justifyCenter', 'justifyRight',
        'bulletList', 'orderedList',
        'blockquote',
        '|',
        'insertLink',
        'insertImage', 'uploadImage',
        '|',
        'undo', 'redo',
    ];

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
        $this->hasCustomToolbar = true;
        return $this;
    }

    public function getToolbarButtons(): array
    {
        // 有自定义工具栏则直接返回
        if ($this->hasCustomToolbar) {
            return $this->toolbarButtons;
        }

        // 无自定义时，按 mode 返回默认值
        if ($this->mode === 'simple') {
            return self::SIMPLE_TOOLBAR;
        }

        return self::FULL_TOOLBAR;
    }
}
