<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;

class ImageCarousel extends Field
{
    protected string $view = 'forms.components.image-carousel';

    public function setImages(array $images): static
    {
        $this->state($images);
        return $this;
    }
}
