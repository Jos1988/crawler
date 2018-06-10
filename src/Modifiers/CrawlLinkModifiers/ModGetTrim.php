<?php

namespace App\Modifiers\CrawlLinkModifiers;

use App\Entity\CrawlLink;
use App\Modifiers\AbstractModifier;
use App\Modifiers\ModifierInterface;

class ModGetTrim implements ModifierInterface
{
    /**
     * @param CrawlLink $input
     *
     * @return mixed
     */
    public function modify($input)
    {
        $url = $input->getLink();
        $parsedUrl = parse_url($url);
        $trimmedLink = $url;
        if (isset($parsedUrl['query'])) {
            $trimmedLink = str_replace('?'.$parsedUrl['query'], '', $url);
        }

        $input->setLink($trimmedLink);

        return $input;
    }

    /**
     * @inheritdoc
     */
    public function validateInput($input): bool
    {
        return is_a($input, CrawlLink::class);
    }

}