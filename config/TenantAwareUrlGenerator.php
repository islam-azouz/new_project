<?php

use Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator;

class TenantAwareUrlGenerator extends DefaultUrlGenerator
{
    public function getUrl(): string
    {
        // $url = asset($this->getPathRelativeToRoot());
        $url = url('/storage/tenant' . tenant()->id . '/app/public/' . $this->getPathRelativeToRoot());

        $url = $this->versionUrl($url);

        return $url;
    }
}
